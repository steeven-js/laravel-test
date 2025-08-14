<?php

declare(strict_types=1);

namespace App\Filament\Resources\FactureResource\Pages;

use App\Filament\Resources\FactureResource;
use App\Filament\Widgets\factures\FacturesStats;
use App\Models\Client;
use App\Models\Devis;
use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\NumeroSequence;
use App\Models\Service;
use App\Models\User;
use App\Traits\EnvironmentProtection;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListFactures extends ListRecords
{
    use EnvironmentProtection;
    
    protected static string $resource = FactureResource::class;

    protected static ?string $breadcrumb = 'Liste';

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make()->label('Nouvelle'),
        ];

        // Afficher le bouton de génération seulement en environnement de développement
        if ($this->shouldShowGenerationButtons()) {
            $actions[] = Actions\Action::make('generate_test_data')
                ->label('🎲 Générer factures de test')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->form([
                    Forms\Components\TextInput::make('count')
                        ->label('Nombre de factures')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(100)
                        ->default(10)
                        ->required(),
                    Forms\Components\TextInput::make('min_lines')
                        ->label('Lignes min par facture')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10)
                        ->default(2)
                        ->required(),
                    Forms\Components\TextInput::make('max_lines')
                        ->label('Lignes max par facture')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10)
                        ->default(4)
                        ->required(),
                    Forms\Components\Toggle::make('link_to_devis')
                        ->label('Lier à des devis existants')
                        ->default(true),
                ])
                ->action(function (array $data): void {
                    // Vérifier l'environnement avant de générer des données
                    if (!$this->isDataGenerationAllowed()) {
                        Notification::make()
                            ->title('🚫 Génération bloquée')
                            ->body($this->getEnvironmentErrorMessage())
                            ->danger()
                            ->send();
                        return;
                    }
                    
                    // Eviter les timeouts et réduire l'overhead mémoire pendant la génération
                    @set_time_limit(0);
                    @ini_set('memory_limit', '512M');
                    \Illuminate\Support\Facades\DB::connection()->disableQueryLog();

                    $count = (int) ($data['count'] ?? 0);
                    $minLines = max(1, (int) ($data['min_lines'] ?? 1));
                    $maxLines = max($minLines, (int) ($data['max_lines'] ?? $minLines));
                    $linkToDevis = (bool) ($data['link_to_devis'] ?? true);

                    if ($count < 1) {
                        Notification::make()->title('Quantité invalide')->danger()->send();

                        return;
                    }

                    $clientIds = Client::query()->pluck('id')->all();
                    $serviceIds = Service::query()->pluck('id')->all();
                    $admins = User::query()
                        ->whereHas('userRole', fn ($q) => $q->whereIn('name', ['admin', 'super_admin']))
                        ->pluck('id')
                        ->all();

                    if (empty($clientIds) || empty($serviceIds)) {
                        Notification::make()
                            ->title('Données insuffisantes')
                            ->body("Créez d'abord des clients et des services pour générer des factures.")
                            ->danger()
                            ->send();

                        return;
                    }

                    $tva = 8.5;
                    $created = 0;

                    for ($i = 0; $i < $count; $i++) {
                        // Numéro FC-YY-XXX via compteur centralisé
                        $year = (int) now()->format('y');
                        $current = null;
                        DB::transaction(function () use ($year, &$current) {
                            $seq = NumeroSequence::query()
                                ->lockForUpdate()
                                ->firstOrCreate(['type' => 'facture', 'year' => $year], ['next_number' => 1]);
                            $current = (int) $seq->next_number;
                            $seq->next_number = $current + 1;
                            $seq->save();
                        });
                        $numero = sprintf('FC-%02d-%03d', $year, $current);

                        $selectedDevis = null;
                        if ($linkToDevis) {
                            // On choisit un devis qui a des lignes pour garantir la copie
                            $selectedDevis = Devis::query()->whereHas('lignes')->inRandomOrder()->first();
                        }

                        $clientId = $selectedDevis?->client_id ?? $clientIds[array_rand($clientIds)];
                        $adminId = ! empty($admins) ? $admins[array_rand($admins)] : null;

                        $dateFacture = Carbon::today();
                        $dateEcheance = (clone $dateFacture)->addDays(30);

                        $facture = Facture::create([
                            'numero_facture' => $numero,
                            'devis_id' => $selectedDevis?->id,
                            'client_id' => $clientId,
                            'administrateur_id' => $adminId,
                            'date_facture' => $dateFacture,
                            'date_echeance' => $dateEcheance,
                            'statut' => 'en_attente',
                            'statut_envoi' => 'non_envoyee',
                            'objet' => 'Facture ' . Str::title(Str::random(8)),
                            'description' => 'Facture générée automatiquement pour tests.',
                            'montant_ht' => 0,
                            'taux_tva' => $tva,
                            'montant_tva' => 0,
                            'montant_ttc' => 0,
                            'conditions_paiement' => 'Règlement à 30 jours. TVA 8,5%.',
                            'notes' => 'Générée par outil super admin.',
                            'archive' => false,
                            'mode_paiement_propose' => 'virement',
                        ]);

                        $ligneOrder = 1;
                        $sumHt = 0.0;
                        $sumTva = 0.0;
                        $sumTtc = 0.0;

                        if ($selectedDevis && $selectedDevis->lignes()->exists()) {
                            foreach ($selectedDevis->lignes as $ligneDevis) {
                                $ligne = LigneFacture::create([
                                    'facture_id' => $facture->id,
                                    'service_id' => $ligneDevis->service_id,
                                    'quantite' => $ligneDevis->quantite,
                                    'unite' => $ligneDevis->unite,
                                    'prix_unitaire_ht' => $ligneDevis->prix_unitaire_ht,
                                    'remise_pourcentage' => $ligneDevis->remise_pourcentage,
                                    'taux_tva' => $tva,
                                    'ordre' => $ligneOrder++,
                                    'description_personnalisee' => $ligneDevis->description_personnalisee,
                                ]);

                                $sumHt += (float) $ligne->montant_ht;
                                $sumTva += (float) $ligne->montant_tva;
                                $sumTtc += (float) $ligne->montant_ttc;
                            }
                        } else {
                            $numLines = random_int($minLines, $maxLines);
                            for ($j = 0; $j < $numLines; $j++) {
                                $serviceId = $serviceIds[array_rand($serviceIds)];
                                $service = Service::find($serviceId);
                                if (! $service) {
                                    continue;
                                }

                                $quantite = random_int(1, 5);
                                $prixUnitaire = (float) ($service->prix_ht ?? 0);
                                $remise = random_int(0, 20);

                                $ligne = LigneFacture::create([
                                    'facture_id' => $facture->id,
                                    'service_id' => $service->id,
                                    'quantite' => $quantite,
                                    'unite' => $service->unite ?? 'unite',
                                    'prix_unitaire_ht' => $prixUnitaire,
                                    'remise_pourcentage' => $remise,
                                    'taux_tva' => $tva,
                                    'ordre' => $ligneOrder++,
                                    'description_personnalisee' => null,
                                ]);

                                $sumHt += (float) $ligne->montant_ht;
                                $sumTva += (float) $ligne->montant_tva;
                                $sumTtc += (float) $ligne->montant_ttc;
                            }
                        }

                        $facture->update([
                            'montant_ht' => round($sumHt, 2),
                            'montant_tva' => round($sumTva, 2),
                            'montant_ttc' => round($sumTtc, 2),
                        ]);

                        $created++;

                        if ($created % 10 === 0 || $created === $count) {
                            Notification::make()
                                ->title("Progression: $created / $count factures générées")
                                ->success()
                                ->send(); // Notification immédiate au lieu de sendToDatabase
                        }
                    }

                    $recipient = Filament::auth()->user();

                    Notification::make()
                        ->title($count . ' factures de test créées')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation();
        }

        return $actions;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FacturesStats::class,
        ];
    }
}
