<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Devis;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TotalDevisChart extends ChartWidget
{
    protected static ?string $heading = 'Total des devis par mois';

    protected static ?string $description = 'Évolution du nombre de devis créés sur les 6 derniers mois';

    protected static string $color = 'success';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $routeRecord = request()->route('record');
        $client = $routeRecord instanceof Client ? $routeRecord : Client::find($routeRecord);

        if (!$client) {
            return [
                'datasets' => [
                    [
                        'label' => 'Devis créés',
                        'data' => [],
                        'backgroundColor' => '#10B981',
                        'borderColor' => '#059669',
                        'borderWidth' => 2,
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

            $count = Devis::query()
                ->where('client_id', $client->getKey())
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $values[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Devis créés',
                    'data' => $values,
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#059669',
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                    'fill' => true,
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
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
