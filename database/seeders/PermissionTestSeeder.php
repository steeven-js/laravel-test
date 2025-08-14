<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧪 Test du système de permissions...');

        // Récupérer les utilisateurs de test
        $users = [
            'super@admin.com' => 'Super Admin',
            'admin@admin.com' => 'Admin Principal',
            'manager@admin.com' => 'Manager Commercial',
            'commercial@admin.com' => 'Commercial Senior',
            'support@admin.com' => 'Support Niveau 1',
            'viewer@admin.com' => 'Lecteur Consultant',
        ];

        foreach ($users as $email => $name) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                $this->command->warn("Utilisateur {$email} non trouvé");

                continue;
            }

            $this->command->info("\n👤 {$name} ({$user->getRoleDisplayName()})");
            $this->command->info("📧 {$user->email}");

            // Tester les permissions principales
            $this->testUserPermissions($user);
        }

        $this->command->info("\n✅ Tests de permissions terminés !");
    }

    private function testUserPermissions(User $user): void
    {
        $resources = ['clients', 'devis', 'factures', 'opportunities', 'tickets', 'todos', 'users'];
        $actions = ['view', 'create', 'edit', 'delete', 'export'];

        $this->command->info('🔐 Permissions :');

        foreach ($resources as $resource) {
            $permissions = [];

            foreach ($actions as $action) {
                if ($user->hasPermission($resource, $action)) {
                    $permissions[] = $action;
                }
            }

            if (! empty($permissions)) {
                $this->command->info("   • {$resource}: " . implode(', ', $permissions));
            }
        }

        // Tester les permissions spéciales
        $specialPermissions = [];

        if ($user->canSend('devis')) {
            $specialPermissions[] = 'envoyer devis';
        }

        if ($user->canTransformDevisToFacture()) {
            $specialPermissions[] = 'transformer devis en facture';
        }

        if ($user->canAssign('tickets')) {
            $specialPermissions[] = 'assigner tickets';
        }

        if ($user->canManageRoles()) {
            $specialPermissions[] = 'gérer les rôles';
        }

        if ($user->canGenerateTestData()) {
            $specialPermissions[] = 'générer données de test';
        }

        if ($user->canViewAllStats()) {
            $specialPermissions[] = 'voir toutes les stats';
        }

        if (! empty($specialPermissions)) {
            $this->command->info('   • Permissions spéciales: ' . implode(', ', $specialPermissions));
        }

        // Afficher toutes les permissions
        $allPermissions = $user->getAllPermissions();
        if ($user->isSuperAdmin()) {
            $this->command->info('   • Toutes les permissions (Super Admin)');
        } else {
            $this->command->info('   • Permissions totales: ' . count($allPermissions) . ' ressources');
        }
    }
}
