<?php

declare(strict_types=1);

namespace App\Filament\Widgets\client;

use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStats extends BaseWidget
{
    public ?Client $record = null;

    protected static bool $shouldPoll = false;

    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        // Ne s'affiche que sur les pages de détail des clients
        return request()->routeIs('filament.admin.resources.clients.view');
    }

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
