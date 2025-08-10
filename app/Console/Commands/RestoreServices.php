<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

class RestoreServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:restore {--force : Forcer la restauration sans confirmation} {--all : Restaurer tous les services supprimés} {--id=* : IDs spécifiques des services à restaurer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restaurer les services supprimés en soft delete';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $servicesSupprimes = Service::withTrashed()->whereNotNull('deleted_at')->count();

        if ($servicesSupprimes === 0) {
            $this->info('✅ Aucun service supprimé à restaurer.');

            return 0;
        }

        $this->info("📊 Services supprimés disponibles: {$servicesSupprimes}");

        if ($this->option('all')) {
            // Restaurer tous les services supprimés
            if (! $this->option('force')) {
                if (! $this->confirm("Voulez-vous restaurer tous les {$servicesSupprimes} services supprimés ?")) {
                    $this->info('Restauration annulée.');

                    return 0;
                }
            }

            $this->info('🔄 Restauration de tous les services supprimés...');

            try {
                $restored = Service::withTrashed()->whereNotNull('deleted_at')->restore();
                $this->info("✅ {$restored} services restaurés avec succès !");

                return 0;
            } catch (\Exception $e) {
                $this->error('❌ Erreur lors de la restauration: ' . $e->getMessage());

                return 1;
            }
        }

        if ($this->option('id')) {
            // Restaurer des services spécifiques par ID
            $ids = $this->option('id');
            $this->info('🔄 Restauration des services avec IDs: ' . implode(', ', $ids));

            try {
                $restored = Service::withTrashed()->whereIn('id', $ids)->restore();
                $this->info("✅ {$restored} services restaurés avec succès !");

                return 0;
            } catch (\Exception $e) {
                $this->error('❌ Erreur lors de la restauration: ' . $e->getMessage());

                return 1;
            }
        }

        // Mode interactif : afficher la liste des services supprimés
        $this->info("\n📋 Services supprimés disponibles:");
        $services = Service::withTrashed()->whereNotNull('deleted_at')->get(['id', 'nom', 'code', 'deleted_at']);

        foreach ($services as $service) {
            $deletedDate = $service->deleted_at->format('d/m/Y H:i');
            $this->info("   • ID {$service->id}: {$service->code} - {$service->nom} (supprimé le {$deletedDate})");
        }

        $this->info("\n💡 Utilisez --all pour restaurer tous les services ou --id=X,Y,Z pour des IDs spécifiques.");
        $this->info('   Exemple: php artisan services:restore --all');
        $this->info('   Exemple: php artisan services:restore --id=26,27');

        return 0;
    }
}
