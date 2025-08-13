<?php

declare(strict_types=1);

namespace App\Filament\Resources\Traits;

use App\Filament\Resources\RelationManagers\HistoriquesRelationManager;

trait HasHistoriqueResource
{
    /**
     * Ajouter la relation historique aux relations du resource
     */
    public static function getRelations(): array
    {
        $relations = static::getDefaultRelations();

        // Ajouter la relation historique si elle n'existe pas déjà
        if (! in_array(HistoriquesRelationManager::class, $relations)) {
            $relations[] = HistoriquesRelationManager::class;
        }

        return $relations;
    }

    /**
     * Obtenir les relations par défaut du resource
     * Cette méthode doit être implémentée par chaque resource
     */
    abstract protected static function getDefaultRelations(): array;
}
