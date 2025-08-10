<?php

declare(strict_types=1);

namespace App\Filament\Resources\Traits;

use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

trait HasStandardActions
{
    /**
     * Configure les actions standardisées pour la table
     */
    public static function configureStandardActions(Table $table, string $resourceClass): Table
    {
        return $table->actions([
            // Action Aperçu (modal)
            Tables\Actions\ViewAction::make()
                ->label('Aperçu')
                ->modal()
                ->url(null)
                ->modalCancelActionLabel('Fermer')
                ->icon('heroicon-o-eye')
                ->color('gray'),
            
            // Action Détail (page complète)
            Tables\Actions\Action::make('detail')
                ->label('Détail')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('info')
                ->url(fn (Model $record): string => $resourceClass::getUrl('view', ['record' => $record]))
                ->openUrlInNewTab(false),
            
            // Action Éditer
            Tables\Actions\EditAction::make()
                ->icon('heroicon-o-pencil')
                ->color('warning'),
        ]);
    }

    /**
     * Configure les actions standardisées avec des actions personnalisées
     */
    public static function configureStandardActionsWithCustom(Table $table, string $resourceClass, array $customActions = []): Table
    {
        $standardActions = [
            // Action Aperçu (modal)
            Tables\Actions\ViewAction::make()
                ->label('Aperçu')
                ->modal()
                ->url(null)
                ->modalCancelActionLabel('Fermer')
                ->icon('heroicon-o-eye')
                ->color('gray'),
            
            // Action Détail (page complète)
            Tables\Actions\Action::make('detail')
                ->label('Détail')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('info')
                ->url(fn (Model $record): string => $resourceClass::getUrl('view', ['record' => $record]))
                ->openUrlInNewTab(false),
        ];

        // Ajouter les actions personnalisées
        foreach ($customActions as $action) {
            $standardActions[] = $action;
        }

        // Ajouter l'action Éditer à la fin
        $standardActions[] = Tables\Actions\EditAction::make()
            ->icon('heroicon-o-pencil')
            ->color('warning');

        return $table->actions($standardActions);
    }

    /**
     * Configure les actions standardisées avec des actions conditionnelles
     */
    public static function configureStandardActionsWithConditions(Table $table, string $resourceClass, array $conditionalActions = []): Table
    {
        $standardActions = [
            // Action Aperçu (modal)
            Tables\Actions\ViewAction::make()
                ->label('Aperçu')
                ->modal()
                ->url(null)
                ->modalCancelActionLabel('Fermer')
                ->icon('heroicon-o-eye')
                ->color('gray'),
            
            // Action Détail (page complète)
            Tables\Actions\Action::make('detail')
                ->label('Détail')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('info')
                ->url(fn (Model $record): string => $resourceClass::getUrl('view', ['record' => $record]))
                ->openUrlInNewTab(false),
        ];

        // Ajouter les actions conditionnelles
        foreach ($conditionalActions as $action) {
            $standardActions[] = $action;
        }

        // Ajouter l'action Éditer à la fin
        $standardActions[] = Tables\Actions\EditAction::make()
            ->icon('heroicon-o-pencil')
            ->color('warning');

        return $table->actions($standardActions);
    }
}
