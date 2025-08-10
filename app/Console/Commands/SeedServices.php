<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\ServiceSeeder;
use Illuminate\Console\Command;

class SeedServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:seed {--force : Forcer l\'exécution sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importer les services depuis le fichier CSV services_rows (2).csv';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $csvPath = base_path('services_rows (2).csv');
        
        if (! file_exists($csvPath)) {
            $this->error("Fichier CSV introuvable: {$csvPath}");
            $this->info('Veuillez placer le fichier services_rows (2).csv à la racine du projet.');
            return 1;
        }

        if (! $this->option('force')) {
            if (! $this->confirm('Voulez-vous importer les services depuis le CSV ? Cela peut écraser les données existantes.')) {
                $this->info('Import annulé.');
                return 0;
            }
        }

        $this->info('Import des services en cours...');
        
        try {
            $seeder = new ServiceSeeder();
            $seeder->setCommand($this);
            $seeder->run();
            
            $this->info('✅ Services importés avec succès !');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'import: ' . $e->getMessage());
            return 1;
        }
    }
}
