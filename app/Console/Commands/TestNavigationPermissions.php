<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Console\Command;

class TestNavigationPermissions extends Command
{
    protected $signature = 'navigation:test-permissions {--user-id= : ID de l\'utilisateur à tester} {--role= : Nom du rôle à tester}';

    protected $description = 'Teste le système de masquage de navigation selon les permissions';

    public function handle(): int
    {
        $this->info('🧭 Test du système de masquage de navigation selon les permissions');
        $this->newLine();

        // Déterminer l'utilisateur à tester
        $user = $this->getUserToTest();
        
        if (!$user) {
            $this->error('❌ Aucun utilisateur trouvé pour le test');
            return 1;
        }

        $this->info("👤 Utilisateur testé : {$user->name} ({$user->email})");
        $this->info("🔑 Rôle : {$user->getRoleDisplayName()}");
        $this->newLine();

        // Tester les permissions de navigation
        $this->testNavigationPermissions($user);

        return 0;
    }

    private function getUserToTest(): ?User
    {
        $userId = $this->option('user-id');
        $roleName = $this->option('role');

        if ($userId) {
            return User::find($userId);
        }

        if ($roleName) {
            $role = UserRole::where('name', $roleName)->first();
            if ($role) {
                return $role->users()->first();
            }
        }

        // Par défaut, prendre le premier utilisateur
        return User::first();
    }

    private function testNavigationPermissions(User $user): void
    {
        $this->info('📋 Éléments de navigation visibles :');
        $this->newLine();

        // Dashboard (toujours visible)
        $this->line('✅ Dashboard (toujours visible)');

        // Groupe CRM
        $crmItems = [];
        if ($user->canView('clients')) {
            $crmItems[] = 'Clients';
        }
        if ($user->canView('entreprises')) {
            $crmItems[] = 'Entreprises';
        }
        if ($user->canView('opportunities')) {
            $crmItems[] = 'Opportunités';
        }

        if (!empty($crmItems)) {
            $this->line('📊 Groupe CRM :');
            foreach ($crmItems as $item) {
                $this->line("   ✅ {$item}");
            }
        } else {
            $this->line('❌ Groupe CRM : Masqué (aucune permission)');
        }

        // Groupe Ventes
        $ventesItems = [];
        if ($user->canView('devis')) {
            $ventesItems[] = 'Devis';
        }
        if ($user->canView('factures')) {
            $ventesItems[] = 'Factures';
        }

        if (!empty($ventesItems)) {
            $this->line('💰 Groupe Ventes :');
            foreach ($ventesItems as $item) {
                $this->line("   ✅ {$item}");
            }
        } else {
            $this->line('❌ Groupe Ventes : Masqué (aucune permission)');
        }

        // Groupe Communication
        $communicationItems = [];
        if ($user->canView('emailtemplates')) {
            $communicationItems[] = 'Templates d\'emails';
        }
        if ($user->canView('clientemails')) {
            $communicationItems[] = 'Emails clients';
        }
        if ($user->canView('notifications')) {
            $communicationItems[] = 'Notifications';
        }

        if (!empty($communicationItems)) {
            $this->line('📧 Groupe Communication :');
            foreach ($communicationItems as $item) {
                $this->line("   ✅ {$item}");
            }
        } else {
            $this->line('❌ Groupe Communication : Masqué (aucune permission)');
        }

        // Groupe Référentiels
        $referentielsItems = [];
        if ($user->canView('services')) {
            $referentielsItems[] = 'Services';
        }
        if ($user->canView('secteursactivite')) {
            $referentielsItems[] = 'Secteurs d\'activité';
        }

        if (!empty($referentielsItems)) {
            $this->line('📚 Groupe Référentiels :');
            foreach ($referentielsItems as $item) {
                $this->line("   ✅ {$item}");
            }
        } else {
            $this->line('❌ Groupe Référentiels : Masqué (aucune permission)');
        }

        // Groupe Support
        $supportItems = [];
        if ($user->canView('tickets')) {
            $supportItems[] = 'Tickets';
        }
        if ($user->canView('todos')) {
            $supportItems[] = 'Tâches';
        }

        if (!empty($supportItems)) {
            $this->line('🛠️ Groupe Support :');
            foreach ($supportItems as $item) {
                $this->line("   ✅ {$item}");
            }
        } else {
            $this->line('❌ Groupe Support : Masqué (aucune permission)');
        }

        // Groupe Réglages
        $reglagesItems = [];
        if ($user->canView('madinia')) {
            $reglagesItems[] = 'Paramètres Madinia';
        }
        if ($user->canView('settings')) {
            $reglagesItems[] = 'Numéros de séquence';
        }

        if (!empty($reglagesItems)) {
            $this->line('⚙️ Groupe Réglages :');
            foreach ($reglagesItems as $item) {
                $this->line("   ✅ {$item}");
            }
        } else {
            $this->line('❌ Groupe Réglages : Masqué (aucune permission)');
        }

        // Groupe Administration
        $adminItems = [];
        if ($user->canView('userroles') || $user->canManageRoles()) {
            $adminItems[] = 'Rôles et permissions';
        }
        if ($user->canView('users')) {
            $adminItems[] = 'Utilisateurs';
        }

        if (!empty($adminItems)) {
            $this->line('🔐 Groupe Administration :');
            foreach ($adminItems as $item) {
                $this->line("   ✅ {$item}");
            }
        } else {
            $this->line('❌ Groupe Administration : Masqué (aucune permission)');
        }

        $this->newLine();
        $this->info('🎯 Résumé :');
        $this->line("   • Éléments visibles : " . $this->countVisibleItems($user));
        $this->line("   • Groupes visibles : " . $this->countVisibleGroups($user));
        
        if ($user->isSuperAdmin()) {
            $this->line("   • Statut : Super administrateur (accès complet)");
        } else {
            $this->line("   • Statut : Utilisateur avec permissions filtrées");
        }
    }

    private function countVisibleItems(User $user): int
    {
        $count = 1; // Dashboard toujours visible

        $resources = [
            'clients', 'entreprises', 'opportunities',
            'devis', 'factures',
            'emailtemplates', 'clientemails', 'notifications',
            'services', 'secteursactivite',
            'tickets', 'todos',
            'madinia', 'settings',
            'userroles', 'users'
        ];

        foreach ($resources as $resource) {
            if ($user->canView($resource)) {
                $count++;
            }
        }

        return $count;
    }

    private function countVisibleGroups(User $user): int
    {
        $groups = 0;

        // CRM
        if ($user->canView('clients') || $user->canView('entreprises') || $user->canView('opportunities')) {
            $groups++;
        }

        // Ventes
        if ($user->canView('devis') || $user->canView('factures')) {
            $groups++;
        }

        // Communication
        if ($user->canView('emailtemplates') || $user->canView('clientemails') || $user->canView('notifications')) {
            $groups++;
        }

        // Référentiels
        if ($user->canView('services') || $user->canView('secteursactivite')) {
            $groups++;
        }

        // Support
        if ($user->canView('tickets') || $user->canView('todos')) {
            $groups++;
        }

        // Réglages
        if ($user->canView('madinia') || $user->canView('settings')) {
            $groups++;
        }

        // Administration
        if ($user->canView('userroles') || $user->canManageRoles() || $user->canView('users')) {
            $groups++;
        }

        return $groups;
    }
}
