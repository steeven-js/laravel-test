<?php

declare(strict_types=1);

namespace App\Filament\Components;

use App\Models\Historique;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Collection;

class HistoriqueActions
{
    /**
     * Créer une section d'historique des actions
     */
    public static function make(string $label = 'Historique des actions', ?string $description = null): Section
    {
        return Section::make($label)
            ->description($description)
            ->icon('heroicon-o-clock')
            ->schema([
                // Cette section sera remplie dynamiquement
            ]);
    }

    /**
     * Créer une section d'historique avec des données
     */
    public static function withData(Collection $historiques, string $label = 'Historique des actions', ?string $description = null): Section
    {
        $schema = [];

        foreach ($historiques as $historique) {
            $schema[] = self::createHistoriqueEntry($historique);
        }

        return Section::make($label)
            ->description($description)
            ->icon('heroicon-o-clock')
            ->schema($schema);
    }

    /**
     * Créer une entrée d'historique individuelle
     */
    private static function createHistoriqueEntry(Historique $historique): Grid
    {
        $actionColor = self::getActionColor($historique->action);
        $actionIcon = self::getActionIcon($historique->action);

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
                        // Cette action sera gérée par JavaScript pour afficher/masquer les détails
                    })
                    ->extraAttributes([
                        'class' => 'historique-toggle-details',
                        'data-historique-id' => $historique->id,
                    ]),

                // Section des détails (masquée par défaut)
                self::createDetailsSection($historique),
            ])
            ->extraAttributes([
                'class' => 'historique-entry',
                'data-historique-id' => $historique->id,
            ]);
    }

    /**
     * Créer la section des détails avec les données JSON
     */
    private static function createDetailsSection(Historique $historique): Grid
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

    /**
     * Obtenir la couleur pour le type d'action
     */
    private static function getActionColor(string $action): string
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

    /**
     * Obtenir l'icône pour le type d'action
     */
    private static function getActionIcon(string $action): string
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

    /**
     * Créer un composant d'historique simple pour les tables
     */
    public static function tableEntry(Historique $historique): array
    {
        return [
            'action' => $historique->action,
            'titre' => $historique->titre,
            'description' => $historique->description,
            'user_nom' => $historique->user_nom,
            'created_at' => $historique->created_at?->format('d/m/Y H:i'),
            'donnees_avant' => $historique->donnees_avant,
            'donnees_apres' => $historique->donnees_apres,
        ];
    }
}
