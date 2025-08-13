<?php

declare(strict_types=1);

namespace App\Filament\Widgets\devis;

use App\Enums\DevisStatus;
use App\Models\Devis;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DevisStats extends BaseWidget
{
    protected static bool $shouldPoll = false;

    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        // Ne s'affiche que sur la page de liste des devis
        return request()->routeIs('filament.admin.resources.devis.index');
    }

    protected function getStats(): array
    {
        $totalDevis = Devis::query()->count();
        $devisEnAttente = Devis::query()->where('status', DevisStatus::EnAttente)->count();
        $devisAcceptes = Devis::query()->where('status', DevisStatus::Accepte)->count();
        $devisRefuses = Devis::query()->where('status', DevisStatus::Refuse)->count();
        $devisExpires = Devis::query()->where('status', DevisStatus::Expire)->count();
        $montantTotalDevis = (float) Devis::query()->sum('montant_ttc');
        $devisConvertis = Devis::query()->whereHas('factures')->count();

        return [
            Stat::make('Total devis', (string) $totalDevis)
                ->description('Nombre total de devis')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('En attente', (string) $devisEnAttente)
                ->description('Devis en attente de réponse')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Acceptés', (string) $devisAcceptes)
                ->description('Devis acceptés')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Refusés', (string) $devisRefuses)
                ->description('Devis refusés')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
            Stat::make('Expirés', (string) $devisExpires)
                ->description('Devis expirés')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('gray'),
            Stat::make('Convertis', (string) $devisConvertis)
                ->description('Devis convertis en factures')
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color('info'),
            Stat::make('Montant total', number_format($montantTotalDevis, 2, ',', ' ') . ' €')
                ->description('Montant total des devis')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('success'),
        ];
    }
}
