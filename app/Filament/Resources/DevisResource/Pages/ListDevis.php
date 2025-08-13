<?php

declare(strict_types=1);

namespace App\Filament\Resources\DevisResource\Pages;

use App\Filament\Resources\DevisResource;
use App\Filament\Widgets\devis\DevisStats;
use App\Models\Client;
use App\Models\Devis;
use App\Models\LigneDevis;
use App\Models\NumeroSequence;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListDevis extends ListRecords
{
    protected static string $resource = DevisResource::class;

    protected static ?string $breadcrumb = 'Liste';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouveau'),
            Actions\Action::make('generateFakeDevis')
                ->label('Générer des devis factices')
                ->icon('heroicon-o-document-text')
                ->visible(fn (): bool => Auth::user()?->userRole?->name === 'super_admin')
                ->form([
                    Forms\Components\TextInput::make('count')
                        ->label('Quantité de devis')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(100)
                        ->default(10)
                        ->required(),
                    Forms\Components\TextInput::make('min_lines')
                        ->label('Lignes min/devis')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10)
                        ->default(1)
                        ->required(),
                    Forms\Components\TextInput::make('max_lines')
                        ->label('Lignes max/devis')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10)
                        ->default(4)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    // Eviter les timeouts et réduire l’overhead mémoire pendant la génération
                    @set_time_limit(0);
                    @ini_set('memory_limit', '512M');
                    DB::connection()->disableQueryLog();

                    $count = (int) ($data['count'] ?? 0);
                    $minLines = max(1, (int) ($data['min_lines'] ?? 1));
                    $maxLines = max($minLines, (int) ($data['max_lines'] ?? $minLines));

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
                            ->body("Créez d'abord des clients et des services pour générer des devis.")
                            ->danger()
                            ->send();

                        return;
                    }

                    $tva = 8.5;
                    $created = 0;

                    for ($i = 0; $i < $count; $i++) {
                        // Numéro DV-YY-XXX via compteur centralisé
                        $year = (int) now()->format('y');
                        $current = null;
                        DB::transaction(function () use ($year, &$current) {
                            $seq = NumeroSequence::query()
                                ->lockForUpdate()
                                ->firstOrCreate(['type' => 'devis', 'year' => $year], ['next_number' => 1]);
                            $current = (int) $seq->next_number;
                            $seq->next_number = $current + 1;
                            $seq->save();
                        });
                        $numero = sprintf('DV-%02d-%03d', $year, $current);

                        $clientId = $clientIds[array_rand($clientIds)];
                        $adminId = ! empty($admins) ? $admins[array_rand($admins)] : null;

                        $dateDevis = Carbon::today();
                        $dateValidite = (clone $dateDevis)->addDays(30);

                        $devis = Devis::create([
                            'numero_devis' => $numero,
                            'client_id' => $clientId,
                            'administrateur_id' => $adminId,
                            'date_devis' => $dateDevis,
                            'date_validite' => $dateValidite,
                            'statut' => 'en_attente',
                            'statut_envoi' => 'non_envoye',
                            'objet' => 'Devis ' . Str::title(Str::random(8)),
                            'description' => 'Devis généré automatiquement pour tests.',
                            'montant_ht' => 0,
                            'taux_tva' => $tva,
                            'montant_tva' => 0,
                            'montant_ttc' => 0,
                            'conditions' => 'TVA 8,5% - validité 30 jours.',
                            'notes' => 'Généré par outil super admin.',
                            'archive' => false,
                        ]);

                        $ligneOrder = 1;
                        $sumHt = 0.0;
                        $sumTva = 0.0;
                        $sumTtc = 0.0;

                        $numLines = random_int($minLines, $maxLines);
                        for ($j = 0; $j < $numLines; $j++) {
                            $serviceId = $serviceIds[array_rand($serviceIds)];
                            $service = Service::find($serviceId);
                            if (! $service) {
                                continue;
                            }

                            $quantite = random_int(1, 5);
                            $prixUnitaire = (float) ($service->prix_ht ?? 0);
                            $remise = random_int(0, 20); // %

                            $ligne = LigneDevis::create([
                                'devis_id' => $devis->id,
                                'service_id' => $service->id,
                                'quantite' => $quantite,
                                'unite' => $service->unite ?? 'unite',
                                'prix_unitaire_ht' => $prixUnitaire,
                                'remise_pourcentage' => $remise,
                                'taux_tva' => $tva,
                                'ordre' => $ligneOrder++,
                                'description_personnalisee' => $service->description,
                            ]);

                            $sumHt += (float) $ligne->montant_ht;
                            $sumTva += (float) $ligne->montant_tva;
                            $sumTtc += (float) $ligne->montant_ttc;
                        }

                        $devis->update([
                            'montant_ht' => round($sumHt, 2),
                            'montant_tva' => round($sumTva, 2),
                            'montant_ttc' => round($sumTtc, 2),
                        ]);

                        $created++;

                        // Notifications de progression seulement à intervalles ou à la fin
                        if ($created % 10 === 0 || $created === $count) {
                            Notification::make()
                                ->title("Progression: $created / $count devis générés")
                                ->success()
                                ->sendToDatabase(Filament::auth()->user());
                        }
                    }

                    Notification::make()->title($created . ' devis factices créés')->success()->send();
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DevisStats::class,
        ];
    }
}
