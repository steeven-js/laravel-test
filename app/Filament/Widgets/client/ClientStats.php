<?php

declare(strict_types=1);

namespace App\Filament\Widgets\client;

use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        // Récupérer le client depuis la route
        $routeRecord = request()->route('record');
        $client = $routeRecord instanceof Client ? $routeRecord : Client::find($routeRecord);

        if (! $client) {
            return [
                Stat::make('Total devis', 'N/A')
                    ->description('Aucun client sélectionné')
                    ->color('gray'),
                Stat::make('Devis acceptés', 'N/A')
                    ->description('Aucun client sélectionné')
                    ->color('gray'),
                Stat::make('CA total', 'N/A')
                    ->description('Aucun client sélectionné')
                    ->color('gray'),
            ];
        }

        $totalDevis = $client->devis()->count();
        $devisAcceptes = $client->devis()->where('statut', 'accepte')->count();
        $caTotal = $client->factures()->sum('montant_ttc');

        return [
            Stat::make('Total devis', $totalDevis)
                ->description('Nombre total de devis créés')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Devis acceptés', $devisAcceptes)
                ->description('Devis convertis en factures')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('CA total', number_format($caTotal, 2, ',', ' ') . ' €')
                ->description('Chiffre d\'affaires total')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('warning'),

            Stat::make('Taux de conversion', $totalDevis > 0 ? number_format(($devisAcceptes / $totalDevis) * 100, 1, ',', ' ') . ' %' : '0,0 %')
                ->description('Pourcentage de devis acceptés')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }
}
