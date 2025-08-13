<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RemoveHistoriqueTrait extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'historique:remove-trait';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprimer temporairement le trait HasHistoriqueResource de tous les resources';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🗑️  Suppression temporaire du trait HasHistoriqueResource...');

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

            $result = $this->removeTrait($resourceFile, $resourceName);
            
            if ($result === 'success') {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info('📊 Résumé de l\'opération :');
        $this->info("✅ Traits supprimés : {$successCount}");
        $this->info("❌ Erreurs : {$errorCount}");

        if ($errorCount > 0) {
            $this->error('⚠️  Certains resources n\'ont pas pu être modifiés.');
            return 1;
        }

        $this->info('🎉 Tous les traits ont été supprimés avec succès !');
        $this->info('💡 Vous pourrez les remettre plus tard avec la commande historique:add-to-resources');
        return 0;
    }

    /**
     * Supprimer le trait d'un resource
     */
    private function removeTrait(string $resourceFile, string $resourceName): string
    {
        $content = File::get($resourceFile);
        
        // Vérifier si le resource utilise HasHistoriqueResource
        if (!Str::contains($content, 'HasHistoriqueResource')) {
            $this->line("⏭️  {$resourceName} n'utilise pas HasHistoriqueResource, ignoré");
            return 'success';
        }

        // Supprimer le trait
        $newContent = str_replace(
            "use \\App\\Filament\\Resources\\Traits\\HasHistoriqueResource;\n\n    ",
            '',
            $content
        );

        $newContent = str_replace(
            "use \\App\\Filament\\Resources\\Traits\\HasHistoriqueResource;\n    ",
            '',
            $newContent
        );

        $newContent = str_replace(
            "use \\App\\Filament\\Resources\\Traits\\HasHistoriqueResource;",
            '',
            $newContent
        );

        // Supprimer les lignes vides en double
        $newContent = preg_replace('/\n\s*\n\s*\n/', "\n\n", $newContent);

        // Sauvegarder le fichier
        try {
            File::put($resourceFile, $newContent);
            $this->info("✅ Trait supprimé de {$resourceName}");
            return 'success';
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la sauvegarde de {$resourceName} : " . $e->getMessage());
            return 'error';
        }
    }
}
