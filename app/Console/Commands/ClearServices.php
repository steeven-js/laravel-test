<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

class ClearServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:clear {--force : Forcer la suppression sans confirmation} {--soft : Suppression douce (soft delete)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoyer tous les services de la base de données';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $totalServices = Service::count();
        $servicesActifs = Service::where('actif', true)->count();
        $servicesInactifs = Service::where('actif', false)->count();
        $servicesSupprimes = Service::withTrashed()->whereNotNull('deleted_at')->count();

        $this->info('📊 Statistiques actuelles des services:');
        $this->info("   • Total: {$totalServices}");
        $this->info("   • Actifs: {$servicesActifs}");
        $this->info("   • Inactifs: {$servicesInactifs}");
        $this->info("   • Supprimés: {$servicesSupprimes}");

        if ($totalServices === 0) {
            $this->info('✅ Aucun service à nettoyer.');

            return 0;
        }

        $mode = $this->option('soft') ? 'soft delete' : 'suppression définitive';

        if (! $this->option('force')) {
            if (! $this->confirm("Voulez-vous vraiment supprimer tous les services ? Mode: {$mode}")) {
                $this->info('Nettoyage annulé.');

                return 0;
            }
        }

        $this->info('🧹 Nettoyage des services en cours...');

        try {
            if ($this->option('soft')) {
                // Soft delete tous les services
                Service::query()->update(['deleted_at' => now()]);
                $this->info("✅ {$totalServices} services supprimés en soft delete.");
            } else {
                // Suppression définitive
                Service::query()->delete();
                $this->info("✅ {$totalServices} services supprimés définitivement.");
            }

            // Vérification post-nettoyage
            $servicesRestants = Service::count();
            $this->info("📊 Services restants: {$servicesRestants}");

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du nettoyage: ' . $e->getMessage());

            return 1;
        }
    }
}
