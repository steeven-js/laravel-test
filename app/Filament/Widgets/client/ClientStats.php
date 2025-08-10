<?php

namespace App\Filament\Widgets\client;

use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStats extends BaseWidget
{
    public ?Client $record = null;

    protected function getStats(): array
    {
        $client = $this->record;

        if (! $client) {
            return [];
        }

        $totalDevis = $client->devis()->count();
        $totalFactures = $client->factures()->count();
        $caTotal = $client->factures()->sum('montant_ttc');

        return [
            Stat::make('Devis', (string) $totalDevis)
                ->icon('heroicon-m-document-text'),
            Stat::make('Factures', (string) $totalFactures)
                ->icon('heroicon-m-document-text'),
            Stat::make('CA total', (string) $caTotal)
                ->icon('heroicon-m-currency-euro'),
        ];
    }
}
