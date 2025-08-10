<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Devis;
use Filament\Widgets\ChartWidget;

class DevisAcceptesChart extends ChartWidget
{
    protected static ?string $heading = 'Répartition des devis';

    protected static ?string $description = 'Proportion des devis acceptés vs refusés';

    protected static string $color = 'info';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $routeRecord = request()->route('record');
        $client = $routeRecord instanceof Client ? $routeRecord : Client::find($routeRecord);

        if (!$client) {
            return [
                'datasets' => [
                    [
                        'label' => 'Devis',
                        'data' => [],
                        'backgroundColor' => [],
                        'borderColor' => [],
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => [],
            ];
        }

        // Compter les devis par statut
        $devisAcceptes = Devis::query()
            ->where('client_id', $client->getKey())
            ->where('statut', 'accepte')
            ->count();

        $devisRefuses = Devis::query()
            ->where('client_id', $client->getKey())
            ->where('statut', 'refuse')
            ->count();

        $devisEnAttente = Devis::query()
            ->where('client_id', $client->getKey())
            ->where('statut', 'en_attente')
            ->count();

        $devisExpires = Devis::query()
            ->where('client_id', $client->getKey())
            ->where('statut', 'expire')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Devis',
                    'data' => [$devisAcceptes, $devisRefuses, $devisEnAttente, $devisExpires],
                    'backgroundColor' => [
                        '#10B981', // Vert pour acceptés
                        '#EF4444', // Rouge pour refusés
                        '#F59E0B', // Orange pour en attente
                        '#6B7280', // Gris pour expirés
                    ],
                    'borderColor' => [
                        '#059669',
                        '#DC2626',
                        '#D97706',
                        '#4B5563',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                'Acceptés',
                'Refusés',
                'En attente',
                'Expirés',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ": " + context.parsed + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'cutout' => '60%',
            'maintainAspectRatio' => false,
        ];
    }
}
