<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Devis;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\Opportunity;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected static bool $shouldPoll = false;

    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        // Ne s'affiche que sur le dashboard
        return request()->routeIs('filament.admin.pages.dashboard');
    }

    protected function getStats(): array
    {
        $totalClients = Client::query()->count();
        $totalEntreprises = Entreprise::query()->count();
        $totalDevis = Devis::query()->count();
        $totalFactures = Facture::query()->count();
        $caTotal = (float) Facture::query()->sum('montant_ttc');
        $totalOpportunities = Opportunity::query()->count();
        $totalTickets = Ticket::query()->count();

        return [
            Stat::make('Clients', (string) $totalClients)
                ->description('Total des clients')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Entreprises', (string) $totalEntreprises)
                ->description('Total des entreprises')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),
            Stat::make('Devis', (string) $totalDevis)
                ->description('Total des devis')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),
            Stat::make('Factures', (string) $totalFactures)
                ->description('Total des factures')
                ->descriptionIcon('heroicon-m-receipt-refund')
                ->color('success'),
            Stat::make('CA total', number_format($caTotal, 2, ',', ' ') . ' €')
                ->description('Chiffre d\'affaires total')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('success'),
            Stat::make('Opportunités', (string) $totalOpportunities)
                ->description('Total des opportunités')
                ->descriptionIcon('heroicon-m-light-bulb')
                ->color('warning'),
            Stat::make('Tickets', (string) $totalTickets)
                ->description('Total des tickets')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('danger'),
        ];
    }
}
