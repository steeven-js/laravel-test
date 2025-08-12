<?php

declare(strict_types=1);

namespace App\Filament\Widgets\entreprises;

use App\Models\Entreprise;
use App\Models\Facture;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EntreprisesStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEntreprises = Entreprise::query()->count();
        // Nombre d'entreprises avec au moins un devis (via les clients)
        $totalEntreprisesAvecDevis = Entreprise::query()->whereHas('clients.devis')->count();
        // Nombre d'entreprises avec au moins une facture (via les clients)
        $totalEntreprisesAvecFacture = Entreprise::query()->whereHas('clients.factures')->count();
        // Nombre d'entreprises avec au moins un devis et une facture (via les clients)
        $totalEntreprisesAvecDevisEtFacture = Entreprise::query()
            ->whereHas('clients.devis')
            ->whereHas('clients.factures')
            ->count();
        // Nombre d'entreprises avec au moins un devis et une facture
        $caTotal = (float) Facture::query()->sum('montant_ttc');

        return [
            Stat::make('Entreprises', (string) $totalEntreprises)
                ->icon('heroicon-m-user-group'),
            Stat::make('Entreprises avec devis', (string) $totalEntreprisesAvecDevis)
                ->icon('heroicon-m-document-text'),
            Stat::make('Entreprises avec facture', (string) $totalEntreprisesAvecFacture)
                ->icon('heroicon-m-document-text'),
            Stat::make('Entreprises avec devis et facture', (string) $totalEntreprisesAvecDevisEtFacture)
                ->icon('heroicon-m-document-text'),
            Stat::make('CA total', number_format($caTotal, 2, ',', ' '))
                ->icon('heroicon-m-currency-euro'),
        ];
    }
}
