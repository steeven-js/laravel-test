<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\TodoSeeder;
use Illuminate\Console\Command;

class SeedTodos extends Command
{
    protected $signature = 'seed:todos {--force : Forcer l\'exécution même si des données existent}';

    protected $description = 'Créer des tâches de test';

    public function handle(): int
    {
        $this->info('Début de la création des tâches...');

        try {
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
