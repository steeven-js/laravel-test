<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\DevisStatus;
use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientQuickOverview extends BaseWidget
{
    protected ?string $heading = null;

    protected function getStats(): array
    {
        $routeRecord = request()->route('record');
        $client = $routeRecord instanceof Client ? $routeRecord : Client::find($routeRecord);
        if (!$client) {
            return [];
        }

        $totalDevis = $client->devis()->count();
        $devisAcceptes = $client->devis()->where('statut', DevisStatus::Accepte->value)->count();
        $caTotal = (float) $client->factures()->sum('montant_ttc');

        // Eviter division par zéro
        $tauxConversion = $totalDevis > 0 ? round(($devisAcceptes / $totalDevis) * 100, 1) : 0.0;
        $panierMoyen = $devisAcceptes > 0 ? round((float) $client->devis()->where('statut', DevisStatus::Accepte->value)->avg('montant_ttc'), 2) : 0.0;

        return [
            Stat::make('Total devis', (string) $totalDevis)
                ->icon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Devis acceptés', (string) $devisAcceptes)
                ->icon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Taux de conversion', number_format($tauxConversion, 1, ',', ' ') . '%')
                ->icon('heroicon-m-chart-bar')
                ->color('warning'),

            Stat::make('CA total', number_format($caTotal, 2, ',', ' ') . ' €')
                ->icon('heroicon-m-currency-euro')
                ->color('success'),
        ];
    }
}


