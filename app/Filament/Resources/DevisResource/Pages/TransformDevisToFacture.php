<?php

declare(strict_types=1);

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use App\Models\Devis;
use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\NumeroSequence;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;

class TransformDevisToFacture extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = DevisResource::class;

    protected static string $view = 'filament.resources.devis-resource.pages.transform-to-invoice';

    public Devis $record;

    public ?array $data = [];

    public function mount(Devis $record): void
    {
        $this->record = $record;

        $this->form->fill([
            'date_facture' => Carbon::today()->toDateString(),
            'date_echeance' => Carbon::today()->addDays(30)->toDateString(),
            'mode_paiement_propose' => 'virement',
            'objet' => $record->objet,
            'conditions_paiement' => null,
            'archiver_devis' => true,
        ]);
    }

    public function form(Form $form): Form
    {
        $devis = $this->record;

        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Résumé devis')
                        ->icon('heroicon-m-document-text')
                        ->schema([
                            Forms\Components\Placeholder::make('numero_devis')
                                ->label('Numéro de devis')
                                ->content($devis->numero_devis),
                            Forms\Components\Placeholder::make('client')
                                ->label('Client')
                                ->content($devis->client?->nom ?? ''),
                            Forms\Components\Placeholder::make('montant_ht')
                                ->label('Montant HT')
                                ->content(number_format((float) $devis->montant_ht, 2, ',', ' ') . ' €'),
                            Forms\Components\Placeholder::make('taux_tva')
                                ->label('Taux TVA')
                                ->content(number_format((float) $devis->taux_tva, 2, '.', '') . ' %'),
                            Forms\Components\Placeholder::make('montant_ttc')
                                ->label('Montant TTC')
                                ->content(number_format((float) $devis->montant_ttc, 2, ',', ' ') . ' €'),
                        ])
                        ->columns(3),

                    Wizard\Step::make('Paramètres facture')
                        ->icon('heroicon-m-cog-6-tooth')
                        ->schema([
                            Forms\Components\DatePicker::make('date_facture')->label('Date de facture')->required(),
                            Forms\Components\DatePicker::make('date_echeance')->label("Date d'échéance")->required(),
                            Forms\Components\Select::make('mode_paiement_propose')
                                ->label('Mode de paiement proposé')
                                ->options([
                                    'virement' => 'Virement',
                                    'cheque' => 'Chèque',
                                    'carte' => 'Carte',
                                ])->required(),
                            Forms\Components\TextInput::make('objet')
                                ->label('Objet')
                                ->maxLength(255),
                            Forms\Components\Textarea::make('conditions_paiement')
                                ->label('Conditions de paiement')
                                ->default('Paiement à réception, délai 30 jours.')
                                ->rows(4),
                        ])->columns(2),

                    Wizard\Step::make('Confirmation')
                        ->icon('heroicon-m-check-circle')
                        ->schema([
                            Forms\Components\Placeholder::make('confirm_text')
                                ->content("Le statut du devis sera automatiquement mis à ‘Accepté’, un numéro de facture sera généré, et les lignes du devis seront copiées vers la facture. Vous pourrez ensuite l'éditer si besoin."),
                            Forms\Components\Toggle::make('archiver_devis')
                                ->label('Archiver le devis après transformation')
                                ->default(true),
                        ]),
                ])
                    ->skippable(false)
                    ->submitAction(
                        \Filament\Actions\Action::make('submit')
                            ->label('Créer la facture')
                            ->icon('heroicon-m-check')
                            ->color('primary')
                            ->submit('submit')
                    ),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $devis = $this->record;

        $facture = null;

        DB::transaction(function () use (&$facture, $devis, $data) {
            if ($devis->statut !== 'accepte') {
                $devis->statut = 'accepte';
                if (empty($devis->date_acceptation)) {
                    $devis->date_acceptation = Carbon::today();
                }
                $devis->save();
            }

            $year = (int) now()->format('y');
            $seq = NumeroSequence::query()
                ->lockForUpdate()
                ->firstOrCreate(['type' => 'facture', 'year' => $year], ['next_number' => 1]);
            $current = (int) $seq->next_number;
            $seq->next_number = $current + 1;
            $seq->save();
            $numeroFacture = sprintf('FA-%02d-%03d', $year, $current);

            $facture = Facture::create([
                'numero_facture' => $numeroFacture,
                'devis_id' => $devis->id,
                'client_id' => $devis->client_id,
                'administrateur_id' => $devis->administrateur_id,
                'date_facture' => Carbon::parse($data['date_facture'] ?? Carbon::today()),
                'date_echeance' => Carbon::parse($data['date_echeance'] ?? Carbon::today()->addDays(30)),
                'objet' => $data['objet'] ?? $devis->objet,
                'description' => $devis->description,
                'montant_ht' => $devis->montant_ht,
                'taux_tva' => $devis->taux_tva,
                'montant_tva' => $devis->montant_tva,
                'montant_ttc' => $devis->montant_ttc,
                'conditions_paiement' => $data['conditions_paiement'] ?? null,
                'notes' => $devis->notes,
                'mode_paiement_propose' => $data['mode_paiement_propose'] ?? 'virement',
                'archive' => false,
            ]);

            $order = 1;
            foreach ($devis->lignes as $ligneDevis) {
                LigneFacture::create([
                    'facture_id' => $facture->id,
                    'service_id' => $ligneDevis->service_id,
                    'quantite' => $ligneDevis->quantite,
                    'unite' => $ligneDevis->unite,
                    'prix_unitaire_ht' => $ligneDevis->prix_unitaire_ht,
                    'remise_pourcentage' => $ligneDevis->remise_pourcentage ?? 0,
                    'taux_tva' => $ligneDevis->taux_tva,
                    'montant_ht' => $ligneDevis->montant_ht,
                    'montant_tva' => $ligneDevis->montant_tva,
                    'montant_ttc' => $ligneDevis->montant_ttc,
                    'ordre' => $order++,
                    'description_personnalisee' => $ligneDevis->description_personnalisee,
                ]);
            }

            if (! empty($data['archiver_devis'])) {
                $devis->archive = true;
                $devis->save();
            }
        });

        Notification::make()
            ->title('Facture créée avec succès')
            ->success()
            ->send();

        if ($facture) {
            $this->redirect(route('filament.admin.resources.factures.edit', ['record' => $facture]));
        }
    }
}
