<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FixHistoriqueResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'historique:fix-resources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corriger tous les resources qui utilisent HasHistoriqueResource';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔧 Correction des resources HasHistoriqueResource...');

        $resourcesPath = app_path('Filament/Resources');
        $successCount = 0;
        $errorCount = 0;

        // Parcourir tous les fichiers de resources
        $resourceFiles = File::glob($resourcesPath . '/*.php');
        
        foreach ($resourceFiles as $resourceFile) {
            $resourceName = basename($resourceFile, '.php');
            
            // Ignorer les fichiers de configuration et les traits
            if (Str::contains($resourceName, 'Trait') || Str::contains($resourceName, 'Config')) {
                continue;
            }

            $result = $this->fixResource($resourceFile, $resourceName);
            
            if ($result === 'success') {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info('📊 Résumé de l\'opération :');
        $this->info("✅ Resources corrigés : {$successCount}");
        $this->info("❌ Erreurs : {$errorCount}");

        if ($errorCount > 0) {
            $this->error('⚠️  Certains resources n\'ont pas pu être corrigés.');
            return 1;
        }

        $this->info('🎉 Tous les resources ont été corrigés avec succès !');
        return 0;
    }

    /**
     * Corriger un resource individuel
     */
    private function fixResource(string $resourceFile, string $resourceName): string
    {
        $content = File::get($resourceFile);
        
        // Vérifier si le resource utilise HasHistoriqueResource
        if (!Str::contains($content, 'HasHistoriqueResource')) {
            return 'success'; // Pas besoin de correction
        }

        // Vérifier si getDefaultRelations existe déjà
        if (Str::contains($content, 'getDefaultRelations')) {
            $this->line("⏭️  {$resourceName} a déjà getDefaultRelations, ignoré");
            return 'success';
        }

        // Vérifier si getRelations existe
        if (!Str::contains($content, 'getRelations')) {
            $this->warn("⚠️  {$resourceName} n'a pas de méthode getRelations, impossible à corriger");
            return 'error';
        }

        // Extraire le contenu de getRelations
        if (preg_match('/public static function getRelations\(\): array\s*\{([^}]+)\}/s', $content, $matches)) {
            $relationsContent = trim($matches[1]);
            
            // Créer la méthode getDefaultRelations
            $defaultRelationsMethod = "\n    protected static function getDefaultRelations(): array\n    {\n        return [\n        ];\n    }\n";
            
            // Insérer après getRelations
            $newContent = str_replace(
                'public static function getRelations(): array' . "\n    {\n" . $relationsContent . "\n    }",
                'public static function getRelations(): array' . "\n    {\n" . $relationsContent . "\n    }" . $defaultRelationsMethod,
                $content
            );
            
            // Sauvegarder le fichier
            try {
                File::put($resourceFile, $newContent);
                $this->info("✅ Resource {$resourceName} corrigé");
                return 'success';
            } catch (\Exception $e) {
                $this->error("❌ Erreur lors de la sauvegarde de {$resourceName} : " . $e->getMessage());
                return 'error';
            }
        } else {
            $this->warn("⚠️  Impossible de parser getRelations dans {$resourceName}");
            return 'error';
        }
    }
}
