<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\EmailTemplateSeeder;
use Illuminate\Console\Command;

class SeedEmailTemplates extends Command
{
    protected $signature = 'seed:email-templates {--force : Forcer l\'exécution même si des données existent}';

    protected $description = 'Importer les modèles d\'email depuis le fichier CSV';

    public function handle(): int
    {
        $this->info('Début de l\'import des modèles d\'email...');

        try {
            $seeder = new EmailTemplateSeeder;
            $seeder->run();

            $this->info('✅ Import des modèles d\'email terminé avec succès !');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'import des modèles d\'email : ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
