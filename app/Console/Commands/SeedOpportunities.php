<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Traits\EnvironmentProtection;
use Database\Seeders\OpportunitySeeder;
use Illuminate\Console\Command;

class SeedOpportunities extends Command
{
    use EnvironmentProtection;

    protected $signature = 'seed:opportunities {--force : Forcer l\'exécution même si des données existent}';

    protected $description = 'Créer des opportunités de test';

    public function handle(): int
    {
        try {
            // Vérifier l'environnement avant de générer des données
            $this->ensureDataGenerationAllowed();
            
            $this->info('Début de la création des opportunités...');

            $seeder = new OpportunitySeeder;
            $seeder->run();

            $this->info('✅ Opportunités créées avec succès !');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la création des opportunités : ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
