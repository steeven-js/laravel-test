<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super administrateur',
                'description' => 'Accès complet à toute l\'interface d\'administration avec tous les droits.',
                'permissions' => [
                    'users' => ['view', 'create', 'edit', 'delete', 'manage_roles'],
                    'clients' => ['view', 'create', 'edit', 'delete', 'export'],
                    'devis' => ['view', 'create', 'edit', 'delete', 'send', 'export', 'transform_to_facture'],
                    'factures' => ['view', 'create', 'edit', 'delete', 'send', 'export'],
                    'opportunities' => ['view', 'create', 'edit', 'delete', 'export'],
                    'tickets' => ['view', 'create', 'edit', 'delete', 'assign', 'export'],
                    'todos' => ['view', 'create', 'edit', 'delete', 'assign', 'export'],
                    'services' => ['view', 'create', 'edit', 'delete', 'import_csv'],
                    'entreprises' => ['view', 'create', 'edit', 'delete'],
                    'secteurs_activite' => ['view', 'create', 'edit', 'delete'],
                    'settings' => ['view', 'edit'],
                    'historique' => ['view', 'export'],
                    'dashboard' => ['view_all_stats'],
                    'generation' => ['generate_test_data'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrateur',
                'description' => 'Accès d\'administration standard avec gestion des utilisateurs et des données principales.',
                'permissions' => [
                    'users' => ['view', 'create', 'edit'],
                    'clients' => ['view', 'create', 'edit', 'delete', 'export'],
                    'devis' => ['view', 'create', 'edit', 'delete', 'send', 'export', 'transform_to_facture'],
                    'factures' => ['view', 'create', 'edit', 'delete', 'send', 'export'],
                    'opportunities' => ['view', 'create', 'edit', 'delete', 'export'],
                    'tickets' => ['view', 'create', 'edit', 'delete', 'assign', 'export'],
                    'todos' => ['view', 'create', 'edit', 'delete', 'assign', 'export'],
                    'services' => ['view', 'create', 'edit', 'delete'],
                    'entreprises' => ['view', 'create', 'edit', 'delete'],
                    'secteurs_activite' => ['view', 'create', 'edit', 'delete'],
                    'settings' => ['view'],
                    'historique' => ['view'],
                    'dashboard' => ['view_all_stats'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Gestionnaire avec accès aux clients, devis, factures et opportunités.',
                'permissions' => [
                    'clients' => ['view', 'create', 'edit', 'export'],
                    'devis' => ['view', 'create', 'edit', 'send', 'export', 'transform_to_facture'],
                    'factures' => ['view', 'create', 'edit', 'send', 'export'],
                    'opportunities' => ['view', 'create', 'edit', 'export'],
                    'tickets' => ['view', 'create', 'edit', 'assign'],
                    'todos' => ['view', 'create', 'edit', 'assign'],
                    'services' => ['view'],
                    'entreprises' => ['view', 'create', 'edit'],
                    'secteurs_activite' => ['view'],
                    'historique' => ['view'],
                    'dashboard' => ['view_own_stats'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'commercial',
                'display_name' => 'Commercial',
                'description' => 'Commercial avec accès aux clients, devis et opportunités.',
                'permissions' => [
                    'clients' => ['view', 'create', 'edit'],
                    'devis' => ['view', 'create', 'edit', 'send'],
                    'opportunities' => ['view', 'create', 'edit'],
                    'tickets' => ['view', 'create'],
                    'todos' => ['view', 'create'],
                    'services' => ['view'],
                    'entreprises' => ['view'],
                    'secteurs_activite' => ['view'],
                    'dashboard' => ['view_own_stats'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'support',
                'display_name' => 'Support',
                'description' => 'Équipe support avec accès aux tickets et tâches.',
                'permissions' => [
                    'clients' => ['view'],
                    'tickets' => ['view', 'create', 'edit', 'assign'],
                    'todos' => ['view', 'create', 'edit', 'assign'],
                    'services' => ['view'],
                    'dashboard' => ['view_own_stats'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'viewer',
                'display_name' => 'Lecteur',
                'description' => 'Accès en lecture seule pour consultation des données.',
                'permissions' => [
                    'clients' => ['view'],
                    'devis' => ['view'],
                    'factures' => ['view'],
                    'opportunities' => ['view'],
                    'tickets' => ['view'],
                    'todos' => ['view'],
                    'services' => ['view'],
                    'entreprises' => ['view'],
                    'secteurs_activite' => ['view'],
                    'dashboard' => ['view_own_stats'],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $data) {
            UserRole::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
