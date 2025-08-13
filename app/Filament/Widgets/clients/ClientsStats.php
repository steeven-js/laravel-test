<?php

declare(strict_types=1);

namespace App\Filament\Widgets\clients;

use App\Models\Client;
use App\Models\Devis;
use App\Models\Facture;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientsStats extends BaseWidget
{
    protected static bool $shouldPoll = false;

    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        // Ne s'affiche que sur la page de liste des clients
        return request()->routeIs('filament.admin.resources.clients.index');
    }

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
