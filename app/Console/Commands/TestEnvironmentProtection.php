<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Traits\EnvironmentProtection;
use Illuminate\Console\Command;

class TestEnvironmentProtection extends Command
{
    use EnvironmentProtection;

    protected $signature = 'test:environment-protection {--force : Forcer le test même en production}';

    protected $description = 'Tester la protection d\'environnement pour la génération de données';

    public function handle(): int
    {
        $this->info('🧪 Test de la protection d\'environnement...');
        $this->newLine();

        // Afficher l'environnement actuel
        $environment = app()->environment();
        $this->info("📍 Environnement actuel : {$environment}");
        
        // Tester les méthodes de protection
        $this->info('🔒 Test des méthodes de protection :');
        $this->line("  • isDataGenerationAllowed() : " . ($this->isDataGenerationAllowed() ? '✅ OUI' : '❌ NON'));
        $this->line("  • isProduction() : " . ($this->isProduction() ? '✅ OUI' : '❌ NON'));
        
        // Tester les méthodes d'affichage des boutons
        $this->info('🎛️ Test de l\'affichage des boutons :');
        $this->line("  • shouldShowGenerationButtons() : " . ($this->shouldShowGenerationButtons() ? '✅ OUI' : '❌ NON'));
        $this->line("  • shouldHideGenerationButtons() : " . ($this->shouldHideGenerationButtons() ? '✅ OUI' : '❌ NON'));
        
        $this->newLine();

        // Tester la protection avec force
        if ($this->option('force')) {
            $this->warn('⚠️  Option --force activée, test de la protection ignoré');
            $this->info('✅ Test terminé (force)');
            return self::SUCCESS;
        }

        // Tester la protection normale
        try {
            $this->ensureDataGenerationAllowed();
            $this->info('✅ Protection d\'environnement : AUTORISÉ');
            $this->info('✅ Boutons de génération : VISIBLES');
            $this->info('✅ Test terminé avec succès');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Protection d\'environnement : BLOQUÉ');
            $this->error('📝 Message d\'erreur : ' . $e->getMessage());
            $this->newLine();
            $this->info('✅ Boutons de génération : MASQUÉS');
            $this->info('✅ Test terminé - Protection fonctionne correctement');
            return self::SUCCESS;
        }
    }
}
