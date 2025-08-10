<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Devis;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ClientDevisEvolutionChart extends ChartWidget
{
    protected static ?string $heading = 'Évolution des devis (6 derniers mois)';

    protected function getData(): array
    {
        $routeRecord = request()->route('record');
        $client = $routeRecord instanceof Client ? $routeRecord : Client::find($routeRecord);

        if (! $client) {
            return [
                'datasets' => [
                    [
                        'label' => 'Devis',
                        'data' => [],
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
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}


