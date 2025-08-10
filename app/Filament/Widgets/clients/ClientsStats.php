<?php

namespace App\Filament\Widgets\clients;

use App\Models\Client;
use App\Models\Devis;
use App\Models\Facture;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientsStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalClients = Client::query()->count();
        $totalDevis = Devis::query()->count();
        $caTotal = (float) Facture::query()->sum('montant_ttc');

        return [
            Stat::make('Clients', (string) $totalClients)
                ->icon('heroicon-m-user-group'),
            Stat::make('Devis', (string) $totalDevis)
                ->icon('heroicon-m-document-text'),
            Stat::make('CA total', number_format($caTotal, 2, ',', ' '))
                ->icon('heroicon-m-currency-euro'),
        ];
    }
}
