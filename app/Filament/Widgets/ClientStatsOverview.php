<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $routeRecord = request()->route('record');
        $client = $routeRecord instanceof Client ? $routeRecord : Client::find($routeRecord);
        if (!$client) {
            return [];
        }

        $devisCount = $client->devis()->count();
        $devisTotal = $client->devis()->sum('montant_ttc');
        $opportunitiesCount = $client->opportunities()->count();
        $ticketsCount = $client->tickets()->count();
        $todosCount = $client->todos()->where('termine', false)->count();

        return [
            Stat::make('Total devis', (string) $devisCount)
                ->description('Nombre total de devis')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('Montant total devis', number_format((float) $devisTotal, 2) . ' €')
                ->description('Valeur totale des devis')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('info'),

            Stat::make('Opportunités', (string) $opportunitiesCount)
                ->description("Nombre d'opportunités")
                ->descriptionIcon('heroicon-m-light-bulb')
                ->color('warning'),

            Stat::make('Tickets ouverts', (string) $ticketsCount)
                ->description('Nombre de tickets actifs')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('danger'),

            Stat::make('Tâches en cours', (string) $todosCount)
                ->description('Tâches non terminées')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('warning'),
        ];
    }
}


