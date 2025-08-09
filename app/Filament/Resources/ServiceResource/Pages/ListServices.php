<?php

declare(strict_types=1);

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\Service;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected static ?string $breadcrumb = 'Liste';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouveau'),
            Actions\Action::make('generateServicesFromCsv')
                ->label('Générer des services (CSV)')
                ->icon('heroicon-o-briefcase')
                ->visible(fn (): bool => Auth::user()?->userRole?->name === 'super_admin')
                ->form([
                    Forms\Components\TextInput::make('count')
                        ->label('Quantité')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(200)
                        ->default(20)
                        ->required(),
                    Forms\Components\Toggle::make('skip_existing')
                        ->label('Ignorer les codes déjà existants')
                        ->default(true),
                ])
                ->action(function (array $data): void {
                    $count = (int) ($data['count'] ?? 0);
                    if ($count < 1) {
                        Notification::make()->title('Quantité invalide')->danger()->send();

                        return;
                    }

                    $csvPath = base_path('services_rows (2).csv');
                    if (! file_exists($csvPath)) {
                        Notification::make()->title('Fichier CSV introuvable')->danger()->send();

                        return;
                    }

                    $handle = fopen($csvPath, 'r');
                    if (! $handle) {
                        Notification::make()->title('Impossible de lire le CSV')->danger()->send();

                        return;
                    }

                    $header = fgetcsv($handle);
                    if (! $header) {
                        fclose($handle);
                        Notification::make()->title('CSV vide ou invalide')->danger()->send();

                        return;
                    }

                    $rows = [];
                    while (($row = fgetcsv($handle)) !== false) {
                        if (count($row) === count($header)) {
                            $rows[] = array_combine($header, $row);
                        }
                    }
                    fclose($handle);

                    if (empty($rows)) {
                        Notification::make()->title('Aucune ligne valide dans le CSV')->warning()->send();

                        return;
                    }

                    shuffle($rows);
                    $selected = array_slice($rows, 0, min($count, count($rows)));

                    $created = 0;
                    foreach ($selected as $r) {
                        $code = trim($r['code'] ?? '');
                        $payload = [
                            'nom' => $r['nom'] ?? 'Service démo',
                            'description' => $r['description'] ?? null,
                            'prix_ht' => is_numeric($r['prix_ht'] ?? null) ? (float) $r['prix_ht'] : 0,
                            'qte_defaut' => is_numeric($r['qte_defaut'] ?? null) ? (int) $r['qte_defaut'] : 1,
                            'unite' => $r['unite'] ?: 'heure',
                            'actif' => filter_var($r['actif'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
                        ];

                        if ($code !== '') {
                            if (! empty($data['skip_existing']) && Service::where('code', $code)->exists()) {
                                continue;
                            }
                            Service::updateOrCreate(['code' => $code], $payload);
                            $created++;
                        } else {
                            // Pas de code → création simple avec code généré
                            $payload['code'] = 'SRV-' . now()->format('y') . '-' . str_pad((string) (Service::max('id') + 1), 3, '0', STR_PAD_LEFT);
                            Service::create($payload);
                            $created++;
                        }
                    }

                    Notification::make()->title($created . ' services créés/mis à jour depuis CSV')->success()->send();
                })
                ->requiresConfirmation(),
        ];
    }
}
