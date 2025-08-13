<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\TicketSeeder;
use Illuminate\Console\Command;

class SeedTickets extends Command
{
    protected $signature = 'seed:tickets {--force : Forcer l\'exécution même si des données existent}';

    protected $description = 'Créer des tickets de test';

    public function handle(): int
    {
        $this->info('Début de la création des tickets...');

        try {
            $seeder = new TicketSeeder;
            $seeder->run();

            $this->info('✅ Tickets créés avec succès !');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la création des tickets : ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
