<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Devis;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class CATotalChart extends ChartWidget
{
    protected static ?string $heading = 'Chiffre d\'affaires total';

    protected static ?string $description = 'Évolution du CA total sur les 6 derniers mois';

    protected static string $color = 'primary';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $routeRecord = request()->route('record');
        $client = $routeRecord instanceof Client ? $routeRecord : Client::find($routeRecord);

        if (!$client) {
            return [
                'datasets' => [
                    [
                        'label' => 'CA Total (€)',
                        'data' => [],
                        'backgroundColor' => '#3B82F6',
                        'borderColor' => '#1D4ED8',
                        'borderWidth' => 2,
                        'borderRadius' => 4,
                        'fill' => true,
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

            $caTotal = Devis::query()
                ->where('client_id', $client->getKey())
                ->where('statut', 'accepte')
                ->whereBetween('created_at', [$start, $end])
                ->sum('montant_ttc');

            $values[] = round($caTotal, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'CA Total (€)',
                    'data' => $values,
                    'backgroundColor' => '#3B82F6',
                    'borderColor' => '#1D4ED8',
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                    'fill' => true,
                    'barThickness' => 'flex',
                    'maxBarThickness' => 50,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
                            return context.dataset.label + ": " + new Intl.NumberFormat("fr-FR", {
                                style: "currency",
                                currency: "EUR"
                            }).format(context.parsed.y);
                        }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) {
                            return new Intl.NumberFormat("fr-FR", {
                                style: "currency",
                                currency: "EUR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            }).format(value);
                        }',
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
