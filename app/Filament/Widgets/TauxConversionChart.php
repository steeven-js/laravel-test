<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Devis;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TauxConversionChart extends ChartWidget
{
    protected static ?string $heading = 'Taux de conversion des devis';

    protected static ?string $description = 'Évolution du pourcentage de devis acceptés sur les 6 derniers mois';

    protected static string $color = 'warning';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $routeRecord = request()->route('record');
        $client = $routeRecord instanceof Client ? $routeRecord : Client::find($routeRecord);

        if (!$client) {
            return [
                'datasets' => [
                    [
                        'label' => 'Taux de conversion (%)',
                        'data' => [],
                        'borderColor' => '#F59E0B',
                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                        'borderWidth' => 3,
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ],
                'labels' => [],
            ];
        }

        $labels = [];
        $values = [];

        // Construit 6 mois glissants (du plus ancien au plus récent)
        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = Carbon::now()->subMonths($i)->endOfMonth();
            $labels[] = $start->translatedFormat('M Y');

            $totalDevis = Devis::query()
                ->where('client_id', $client->getKey())
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $devisAcceptes = Devis::query()
                ->where('client_id', $client->getKey())
                ->where('statut', 'accepte')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            // Calculer le taux de conversion
            $tauxConversion = $totalDevis > 0 ? ($devisAcceptes / $totalDevis) * 100 : 0;
            $values[] = round($tauxConversion, 1);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Taux de conversion (%)',
                    'data' => $values,
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#F59E0B',
                    'pointBorderColor' => '#FFFFFF',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 6,
                    'pointHoverRadius' => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.dataset.label + ": " + context.parsed.y + "%";
                        }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 100,
                    'ticks' => [
                        'callback' => 'function(value) {
                            return value + "%";
                        }',
                    ],
                ],
            ],
            'elements' => [
                'point' => [
                    'hoverBackgroundColor' => '#F59E0B',
                ],
            ],
        ];
    }
}
