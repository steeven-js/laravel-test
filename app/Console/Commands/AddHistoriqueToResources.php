<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AddHistoriqueToResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'historique:add-to-resources {--force : Forcer la modification même si le trait existe déjà}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ajouter le trait HasHistoriqueResource à tous les resources Filament';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Ajout du trait HasHistoriqueResource aux resources Filament...');

        $resourcesPath = app_path('Filament/Resources');
        $successCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        // Parcourir tous les fichiers de resources
        $resourceFiles = File::glob($resourcesPath . '/*.php');

        foreach ($resourceFiles as $resourceFile) {
            $resourceName = basename($resourceFile, '.php');

            // Ignorer les fichiers de configuration et les traits
            if (Str::contains($resourceName, 'Trait') || Str::contains($resourceName, 'Config')) {
                continue;
            }

            $result = $this->addTraitToResource($resourceFile, $resourceName);

            switch ($result) {
                case 'success':
                    $successCount++;
                    break;
                case 'skipped':
                    $skippedCount++;
                    break;
                case 'error':
                    $errorCount++;
                    break;
            }
        }

        $this->newLine();
        $this->info('📊 Résumé de l\'opération :');
        $this->info("✅ Resources modifiés : {$successCount}");
        $this->info("⏭️  Resources ignorés : {$skippedCount}");
        $this->info("❌ Erreurs : {$errorCount}");

        if ($errorCount > 0) {
            $this->error('⚠️  Certains resources n\'ont pas pu être modifiés. Vérifiez les erreurs ci-dessus.');

            return 1;
        }

        $this->info('🎉 Traitement terminé avec succès !');

        return 0;
    }

    /**
     * Ajouter le trait HasHistoriqueResource à un resource
     */
    private function addTraitToResource(string $resourceFile, string $resourceName): string
    {
        $content = File::get($resourceFile);

        // Vérifier si le trait existe déjà
        if (Str::contains($content, 'HasHistoriqueResource') && ! $this->option('force')) {
            $this->line("⏭️  Le trait HasHistoriqueResource existe déjà dans {$resourceName}");

            return 'skipped';
        }

        // Vérifier si c'est bien un resource Filament
        if (! Str::contains($content, 'extends Resource')) {
            $this->line("⏭️  {$resourceName} n'est pas un resource Filament, ignoré");

            return 'skipped';
        }

        // Ajouter le trait dans la classe
        $pattern = '/(class ' . $resourceName . ' extends Resource\s*\{)/';
        $replacement = '$1' . PHP_EOL . '    use \\App\\Filament\\Resources\\Traits\\HasHistoriqueResource;';

        $newContent = preg_replace($pattern, $replacement, $content);

        if ($newContent === $content) {
            $this->warn("⚠️  Impossible de modifier le resource {$resourceName} - structure inattendue");

            return 'error';
        }

        // Sauvegarder le fichier
        try {
            File::put($resourceFile, $newContent);
            $this->info("✅ Trait HasHistoriqueResource ajouté à {$resourceName}");

            return 'success';
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la sauvegarde de {$resourceName} : " . $e->getMessage());

            return 'error';
        }
    }
}
