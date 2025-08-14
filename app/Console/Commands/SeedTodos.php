<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Traits\EnvironmentProtection;
use Database\Seeders\TodoSeeder;
use Illuminate\Console\Command;

class SeedTodos extends Command
{
    use EnvironmentProtection;

    protected $signature = 'seed:todos {--force : Forcer l\'exécution même si des données existent}';

    protected $description = 'Créer des tâches de test';

    public function handle(): int
    {
        try {
            // Vérifier l'environnement avant de générer des données
            $this->ensureDataGenerationAllowed();
            
            $this->info('Début de la création des tâches...');

            $seeder = new TodoSeeder;
            $seeder->run();

            $this->info('✅ Tâches créées avec succès !');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la création des tâches : ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
