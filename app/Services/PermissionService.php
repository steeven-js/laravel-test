<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Arr;

class PermissionService
{
    /**
     * Convertir les permissions du formulaire en format JSON pour la base de données
     */
    public static function formatPermissionsForDatabase(array $formPermissions): array
    {
        // Le state du formulaire est de la forme:
        // [ 'clients' => ['view' => true, 'create' => false, ...], 'devis' => [...], ... ]
        // On doit retourner: [ 'clients' => ['view', 'create', ...], 'devis' => [...], ... ]

        $permissionsByResource = [];

        $walker = function (array $node, array $segments = []) use (&$walker, &$permissionsByResource): void {
            foreach ($node as $key => $value) {
                $currentPath = array_merge($segments, [$key]);

                if (is_array($value)) {
                    $walker($value, $currentPath);

                    continue;
                }

                if ($value === true && count($currentPath) === 2) {
                    [$resource, $action] = $currentPath;
                    $permissionsByResource[$resource] = $permissionsByResource[$resource] ?? [];
                    $permissionsByResource[$resource][] = $action;
                }
            }
        };

        $walker($formPermissions, []);

        return $permissionsByResource;
    }

    /**
     * Convertir les permissions de la base de données en format formulaire
     */
    public static function formatPermissionsForForm(array $databasePermissions): array
    {
        // Depuis la base: [ 'clients' => ['view', 'create'], ... ]
        // Vers le state attendu par Filament Field: [ 'clients' => ['view' => true, 'create' => true], ... ]
        $formState = [];

        foreach ($databasePermissions as $resource => $actions) {
            if (! is_array($actions)) {
                continue;
            }

            foreach ($actions as $action) {
                if (is_string($action)) {
                    Arr::set($formState, $resource . '.' . $action, true);
                }
            }
        }

        return $formState;
    }

    /**
     * Obtenir toutes les permissions disponibles
     */
    public static function getAllAvailablePermissions(): array
    {
        return [
            'clients' => [
                'view' => 'Voir les clients',
                'create' => 'Créer des clients',
                'edit' => 'Modifier les clients',
                'delete' => 'Supprimer les clients',
                'export' => 'Exporter les clients',
            ],
            'devis' => [
                'view' => 'Voir les devis',
                'create' => 'Créer des devis',
                'edit' => 'Modifier les devis',
                'delete' => 'Supprimer les devis',
                'send' => 'Envoyer les devis',
                'export' => 'Exporter les devis',
                'transform_to_facture' => 'Transformer en facture',
            ],
            'factures' => [
                'view' => 'Voir les factures',
                'create' => 'Créer des factures',
                'edit' => 'Modifier les factures',
                'delete' => 'Supprimer les factures',
                'send' => 'Envoyer les factures',
                'export' => 'Exporter les factures',
            ],
            'opportunities' => [
                'view' => 'Voir les opportunités',
                'create' => 'Créer des opportunités',
                'edit' => 'Modifier les opportunités',
                'delete' => 'Supprimer les opportunités',
                'export' => 'Exporter les opportunités',
            ],
            'tickets' => [
                'view' => 'Voir les tickets',
                'create' => 'Créer des tickets',
                'edit' => 'Modifier les tickets',
                'delete' => 'Supprimer les tickets',
                'assign' => 'Assigner les tickets',
                'export' => 'Exporter les tickets',
            ],
            'todos' => [
                'view' => 'Voir les tâches',
                'create' => 'Créer des tâches',
                'edit' => 'Modifier les tâches',
                'delete' => 'Supprimer les tâches',
                'assign' => 'Assigner les tâches',
                'export' => 'Exporter les tâches',
            ],
            'users' => [
                'view' => 'Voir les utilisateurs',
                'create' => 'Créer des utilisateurs',
                'edit' => 'Modifier les utilisateurs',
                'delete' => 'Supprimer les utilisateurs',
                'manage_roles' => 'Gérer les rôles',
            ],
            'services' => [
                'view' => 'Voir les services',
                'create' => 'Créer des services',
                'edit' => 'Modifier les services',
                'delete' => 'Supprimer les services',
                'import_csv' => 'Importer depuis CSV',
            ],
            'entreprises' => [
                'view' => 'Voir les entreprises',
                'create' => 'Créer des entreprises',
                'edit' => 'Modifier les entreprises',
                'delete' => 'Supprimer les entreprises',
            ],
            'secteurs_activite' => [
                'view' => 'Voir les secteurs',
                'create' => 'Créer des secteurs',
                'edit' => 'Modifier les secteurs',
                'delete' => 'Supprimer les secteurs',
            ],
            'settings' => [
                'view' => 'Voir les paramètres',
                'edit' => 'Modifier les paramètres',
            ],
            'historique' => [
                'view' => 'Voir l\'historique',
                'export' => 'Exporter l\'historique',
            ],
            'dashboard' => [
                'view_all_stats' => 'Voir toutes les statistiques',
                'view_own_stats' => 'Voir ses propres statistiques',
            ],
            'generation' => [
                'generate_test_data' => 'Générer des données de test',
            ],
        ];
    }

    /**
     * Obtenir les permissions par ressource
     */
    public static function getPermissionsByResource(): array
    {
        $permissions = self::getAllAvailablePermissions();
        $result = [];

        foreach ($permissions as $resource => $actions) {
            $result[$resource] = [];
            foreach ($actions as $action => $label) {
                $result[$resource][] = $action;
            }
        }

        return $result;
    }

    /**
     * Vérifier si une permission existe
     */
    public static function permissionExists(string $resource, string $action): bool
    {
        $permissions = self::getAllAvailablePermissions();

        return isset($permissions[$resource][$action]);
    }

    /**
     * Obtenir le label d'une permission
     */
    public static function getPermissionLabel(string $resource, string $action): ?string
    {
        $permissions = self::getAllAvailablePermissions();

        return $permissions[$resource][$action] ?? null;
    }
}
