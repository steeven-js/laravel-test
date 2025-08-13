<?php

declare(strict_types=1);

namespace App\Filament\Widgets\factures;

use App\Enums\FactureEnvoiStatus;
use App\Enums\FactureStatus;
use App\Models\Facture;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FacturesStats extends BaseWidget
{
    protected static bool $shouldPoll = false;

    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        // Ne s'affiche que sur la page de liste des factures
        return request()->routeIs('filament.admin.resources.factures.index');
    }

    protected function getStats(): array
    {
        $totalFactures = Facture::query()->count();
        $facturesPayees = Facture::query()->where('statut', FactureStatus::Payee)->count();
        $facturesImpayees = Facture::query()->whereNotIn('statut', [FactureStatus::Payee, FactureStatus::Annulee])->count();
        $montantTotalFactures = (float) Facture::query()->sum('montant_ttc');
        $montantPaye = (float) Facture::query()->where('statut', FactureStatus::Payee)->sum('montant_ttc');
        $montantImpaye = (float) Facture::query()->whereNotIn('statut', [FactureStatus::Payee, FactureStatus::Annulee])->sum('montant_ttc');
        $facturesNonEnvoyees = Facture::query()->where('statut_envoi', FactureEnvoiStatus::NonEnvoyee)->count();
        $facturesEnvoyees = Facture::query()->where('statut_envoi', FactureEnvoiStatus::Envoyee)->count();

        return [
            Stat::make('Total factures', (string) $totalFactures)
                ->description('Nombre total de factures')
                ->descriptionIcon('heroicon-m-receipt-refund')
                ->color('primary'),
            Stat::make('Payées', (string) $facturesPayees)
                ->description('Factures payées')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Impayées', (string) $facturesImpayees)
                ->description('Factures impayées')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('Non envoyées', (string) $facturesNonEnvoyees)
                ->description('Factures non envoyées')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Envoyées', (string) $facturesEnvoyees)
                ->description('Factures envoyées')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('info'),
            Stat::make('CA total', number_format($montantTotalFactures, 2, ',', ' ') . ' €')
                ->description('Chiffre d\'affaires total')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('success'),
            Stat::make('Montant payé', number_format($montantPaye, 2, ',', ' ') . ' €')
                ->description('Montant total payé')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Montant impayé', number_format($montantImpaye, 2, ',', ' ') . ' €')
                ->description('Montant total impayé')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),
        ];
    }
}
