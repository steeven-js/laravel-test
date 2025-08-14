<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Traits\EnvironmentProtection;
use Database\Seeders\EmailTemplateSeeder;
use Illuminate\Console\Command;

class SeedEmailTemplates extends Command
{
    use EnvironmentProtection;

    protected $signature = 'seed:email-templates {--force : Forcer l\'exécution même si des données existent}';

    protected $description = 'Créer des modèles d\'email de test';

    public function handle(): int
    {
        try {
            // Vérifier l'environnement avant de générer des données
            $this->ensureDataGenerationAllowed();
            
            $this->info('Début de la création des modèles d\'email...');

            $seeder = new EmailTemplateSeeder;
            $seeder->run();

            $this->info('✅ Modèles d\'email créés avec succès !');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la création des modèles d\'email : ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
