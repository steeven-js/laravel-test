<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use App\Models\Client;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;

class HistoriqueActions extends Page
{
    protected static string $resource = ClientResource::class;

    protected static string $view = 'filament.pages.historique-actions';

    public ?Client $record = null;

    public function mount(Client $record): void
    {
        $this->record = $record;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Historique des actions')
                    ->description("Suivi de toutes les actions effectuées sur ce client ({$this->record->historiques()->count()})")
                    ->icon('heroicon-o-clock')
                    ->schema([
                        $this->createHistoriqueList(),
                    ])
                    ->collapsible(false),
            ]);
    }

    private function createHistoriqueList(): Grid
    {
        $historiques = $this->record->historiques()->orderBy('created_at', 'desc')->get();
        $schema = [];

        foreach ($historiques as $historique) {
            $schema[] = $this->createHistoriqueEntry($historique);
        }

        return Grid::make(1)->schema($schema);
    }

    private function createHistoriqueEntry($historique): Grid
    {
        $actionColor = $this->getActionColor($historique->action);
        $actionIcon = $this->getActionIcon($historique->action);

        return Grid::make(1)
            ->schema([
                // En-tête de l'action
                Grid::make(3)
                    ->schema([
                        TextEntry::make('action')
                            ->label('Action')
                            ->badge()
                            ->color($actionColor)
                            ->icon($actionIcon)
                            ->default($historique->action)
                            ->columnSpan(1),

                        TextEntry::make('user_nom')
                            ->label('Par')
                            ->default($historique->user_nom)
                            ->columnSpan(1),

                        TextEntry::make('created_at')
                            ->label('Date')
                            ->default($historique->created_at?->format('d/m/Y H:i'))
                            ->columnSpan(1),
                    ]),

                // Description
                TextEntry::make('description')
                    ->label('Description')
                    ->default($historique->description)
                    ->columnSpanFull(),

                // Bouton pour voir les détails
                Action::make('voir_details')
                    ->label('▼ Voir les détails')
                    ->icon('heroicon-o-chevron-down')
                    ->color('gray')
                    ->size('sm')
                    ->action(function () {
                        // Cette action sera gérée par JavaScript
                    })
                    ->extraAttributes([
                        'class' => 'historique-toggle-details',
                        'data-historique-id' => $historique->id,
                    ]),

                // Section des détails (masquée par défaut)
                $this->createDetailsSection($historique),
            ])
            ->extraAttributes([
                'class' => 'historique-entry',
                'data-historique-id' => $historique->id,
            ]);
    }

    private function createDetailsSection($historique): Grid
    {
        $schema = [];

        // Données avant (si disponibles)
        if ($historique->donnees_avant && ! empty($historique->donnees_avant)) {
            $schema[] = TextEntry::make('donnees_avant')
                ->label('Avant:')
                ->default(json_encode($historique->donnees_avant, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                ->fontFamily('mono')
                ->size('sm')
                ->color('gray')
                ->columnSpanFull();
        }

        // Données après (si disponibles)
        if ($historique->donnees_apres && ! empty($historique->donnees_apres)) {
            $schema[] = TextEntry::make('donnees_apres')
                ->label('Après:')
                ->default(json_encode($historique->donnees_apres, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                ->fontFamily('mono')
                ->size('sm')
                ->color('gray')
                ->columnSpanFull();
        }

        // Données supplémentaires (si disponibles)
        if ($historique->donnees_supplementaires && ! empty($historique->donnees_supplementaires)) {
            $schema[] = TextEntry::make('donnees_supplementaires')
                ->label('Informations supplémentaires:')
                ->default(json_encode($historique->donnees_supplementaires, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                ->fontFamily('mono')
                ->size('sm')
                ->color('gray')
                ->columnSpanFull();
        }

        return Grid::make(1)
            ->schema($schema)
            ->extraAttributes([
                'class' => 'historique-details',
                'style' => 'display: none;',
                'data-historique-id' => $historique->id,
            ]);
    }

    private function getActionColor(string $action): string
    {
        return match ($action) {
            'creation' => 'success',
            'modification' => 'primary',
            'suppression' => 'danger',
            'changement_statut' => 'warning',
            'envoi_email' => 'info',
            'paiement_stripe' => 'success',
            default => 'gray',
        };
    }

    private function getActionIcon(string $action): string
    {
        return match ($action) {
            'creation' => 'heroicon-o-document-plus',
            'modification' => 'heroicon-o-pencil',
            'suppression' => 'heroicon-o-trash',
            'changement_statut' => 'heroicon-o-arrow-path',
            'envoi_email' => 'heroicon-o-envelope',
            'paiement_stripe' => 'heroicon-o-credit-card',
            default => 'heroicon-o-clock',
        };
    }

    public function getTitle(): string
    {
        return "Historique des actions - {$this->record->nom} {$this->record->prenom}";
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.clients.index') => 'Clients',
            route('filament.admin.resources.clients.view', ['record' => $this->record]) => "{$this->record->nom} {$this->record->prenom}",
            'Historique des actions',
        ];
    }
}
