<?php

declare(strict_types=1);

namespace App\Filament\Resources\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HasPermissions
{
    /**
     * Vérifier si l'utilisateur peut voir la ressource
     */
    protected static function canViewResource(): bool
    {
        $user = Auth::user();
        if (! $user || ! ($user instanceof User)) {
            return false;
        }

        $resourceName = static::getResourceName();

        return $user->canView($resourceName);
    }

    /**
     * Vérifier si l'utilisateur peut créer la ressource
     */
    protected static function canCreateResource(): bool
    {
        $user = Auth::user();
        if (! $user || ! ($user instanceof User)) {
            return false;
        }

        $resourceName = static::getResourceName();

        return $user->canCreate($resourceName);
    }

    /**
     * Vérifier si l'utilisateur peut modifier la ressource
     */
    protected static function canEditResource(): bool
    {
        $user = Auth::user();
        if (! $user || ! ($user instanceof User)) {
            return false;
        }

        $resourceName = static::getResourceName();

        return $user->canEdit($resourceName);
    }

    /**
     * Vérifier si l'utilisateur peut supprimer la ressource
     */
    protected static function canDeleteResource(): bool
    {
        $user = Auth::user();
        if (! $user || ! ($user instanceof User)) {
            return false;
        }

        $resourceName = static::getResourceName();

        return $user->canDelete($resourceName);
    }

    /**
     * Vérifier si l'utilisateur peut exporter la ressource
     */
    protected static function canExportResource(): bool
    {
        $user = Auth::user();
        if (! $user || ! ($user instanceof User)) {
            return false;
        }

        $resourceName = static::getResourceName();

        return $user->canExport($resourceName);
    }

    /**
     * Vérifier si l'utilisateur peut envoyer la ressource
     */
    protected static function canSendResource(): bool
    {
        $user = Auth::user();
        if (! $user || ! ($user instanceof User)) {
            return false;
        }

        $resourceName = static::getResourceName();

        return $user->canSend($resourceName);
    }

    /**
     * Vérifier si l'utilisateur peut assigner la ressource
     */
    protected static function canAssignResource(): bool
    {
        $user = Auth::user();
        if (! $user || ! ($user instanceof User)) {
            return false;
        }

        $resourceName = static::getResourceName();

        return $user->canAssign($resourceName);
    }

    /**
     * Vérifier si l'utilisateur peut générer des données de test
     */
    protected static function canGenerateTestData(): bool
    {
        $user = Auth::user();
        if (! $user || ! ($user instanceof User)) {
            return false;
        }

        return $user->canGenerateTestData();
    }

    /**
     * Vérifier si l'utilisateur peut voir toutes les statistiques
     */
    protected static function canViewAllStats(): bool
    {
        $user = Auth::user();
        if (! $user || ! ($user instanceof User)) {
            return false;
        }

        return $user->canViewAllStats();
    }

    /**
     * Obtenir le nom de la ressource pour les permissions
     */
    protected static function getResourceName(): string
    {
        $className = class_basename(static::class);

        return strtolower(str_replace('Resource', '', $className));
    }

    /**
     * Configurer les actions de base avec permissions
     */
    protected static function configureBaseActions(): array
    {
        $actions = [];

        if (static::canCreateResource()) {
            $actions[] = \Filament\Actions\CreateAction::make()->label('Nouveau');
        }

        return $actions;
    }

    /**
     * Configurer les actions de ligne avec permissions
     */
    protected static function configureRowActions(): array
    {
        $actions = [];

        if (static::canViewResource()) {
            $actions[] = \Filament\Tables\Actions\ViewAction::make()
                ->label('Aperçu')
                ->modal()
                ->url(null)
                ->modalCancelActionLabel('Fermer');
        }

        if (static::canEditResource()) {
            $actions[] = \Filament\Tables\Actions\EditAction::make();
        }

        if (static::canDeleteResource()) {
            $actions[] = \Filament\Tables\Actions\DeleteAction::make();
        }

        return $actions;
    }

    /**
     * Configurer les actions de page avec permissions
     */
    protected static function configurePageActions(): array
    {
        $actions = [];

        if (static::canEditResource()) {
            $actions[] = \Filament\Actions\EditAction::make();
        }

        if (static::canDeleteResource()) {
            $actions[] = \Filament\Actions\DeleteAction::make();
        }

        return $actions;
    }
}
