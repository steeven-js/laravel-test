<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Traits\EnvironmentProtection;
use Database\Seeders\ServiceSeeder;
use Illuminate\Console\Command;

class SeedServices extends Command
{
    use EnvironmentProtection;

    protected $signature = 'seed:services {--force : Forcer l\'exécution même si des données existent}';

    protected $description = 'Créer des services de test';

    public function handle(): int
    {
        try {
            // Vérifier l'environnement avant de générer des données
            $this->ensureDataGenerationAllowed();
            
            $this->info('Début de la création des services...');

            $seeder = new ServiceSeeder;
            $seeder->run();

            $this->info('✅ Services créés avec succès !');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la création des services : ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
