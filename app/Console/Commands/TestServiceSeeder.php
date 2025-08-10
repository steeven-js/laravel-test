<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Service;
use Illuminate\Console\Command;

class TestServiceSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:test-seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester le seeder des services et afficher les statistiques';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Test du seeder des services...');

        // Vérifier le fichier CSV
        $csvPath = base_path('services_rows (2).csv');
        if (! file_exists($csvPath)) {
            $this->error("❌ Fichier CSV introuvable: {$csvPath}");

            return 1;
        }

        $this->info("✅ Fichier CSV trouvé: {$csvPath}");

        // Compter les lignes dans le CSV
        $csvLines = count(file($csvPath)) - 1; // -1 pour l'en-tête
        $this->info("📊 Nombre de services dans le CSV: {$csvLines}");

        // Statistiques des services en base
        $totalServices = Service::count();
        $servicesActifs = Service::where('actif', true)->count();
        $servicesInactifs = Service::where('actif', false)->count();
        $servicesSupprimes = Service::withTrashed()->whereNotNull('deleted_at')->count();

        $this->info("\n📈 Statistiques des services en base:");
        $this->info("   • Total: {$totalServices}");
        $this->info("   • Actifs: {$servicesActifs}");
        $this->info("   • Inactifs: {$servicesInactifs}");
        $this->info("   • Supprimés: {$servicesSupprimes}");

        // Afficher quelques exemples de services
        $this->info("\n📋 Exemples de services:");
        $services = Service::take(5)->get(['id', 'nom', 'code', 'prix_ht', 'unite', 'actif']);

        foreach ($services as $service) {
            $status = $service->actif ? '✅' : '❌';
            $this->info("   {$status} {$service->code} - {$service->nom} ({$service->prix_ht}€/{$service->unite})");
        }

        // Vérifier les unités utilisées
        $unites = Service::distinct()->pluck('unite')->filter()->values();
        $this->info("\n🏷️  Unités utilisées: " . $unites->implode(', '));

        // Vérifier la plage de prix
        $prixMin = Service::min('prix_ht');
        $prixMax = Service::max('prix_ht');
        $this->info("💰 Plage de prix: {$prixMin}€ - {$prixMax}€");

        $this->info("\n✅ Test terminé avec succès !");

        if ($totalServices === 0) {
            $this->warn("⚠️  Aucun service en base. Exécutez 'php artisan services:seed' pour importer les données.");
        }

        return 0;
    }
}
