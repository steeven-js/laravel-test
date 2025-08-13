<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AddHistoriqueToModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'historique:add-to-models {--force : Forcer la modification même si le trait existe déjà}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ajouter le trait HasHistorique à tous les modèles configurés';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Ajout du trait HasHistorique aux modèles...');

        $models = config('historique.models', []);
        $modelsPath = app_path('Models');
        $successCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($models as $modelClass) {
            $modelName = class_basename($modelClass);
            $modelFile = $modelsPath . '/' . $modelName . '.php';

            if (! File::exists($modelFile)) {
                $this->warn("⚠️  Fichier modèle non trouvé : {$modelFile}");
                $errorCount++;

                continue;
            }

            $result = $this->addTraitToModel($modelFile, $modelName);

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
        $this->info("✅ Modèles modifiés : {$successCount}");
        $this->info("⏭️  Modèles ignorés : {$skippedCount}");
        $this->info("❌ Erreurs : {$errorCount}");

        if ($errorCount > 0) {
            $this->error('⚠️  Certains modèles n\'ont pas pu être modifiés. Vérifiez les erreurs ci-dessus.');

            return 1;
        }

        $this->info('🎉 Traitement terminé avec succès !');

        return 0;
    }

    /**
     * Ajouter le trait HasHistorique à un modèle
     */
    private function addTraitToModel(string $modelFile, string $modelName): string
    {
        $content = File::get($modelFile);

        // Vérifier si le trait existe déjà
        if (Str::contains($content, 'HasHistorique') && ! $this->option('force')) {
            $this->line("⏭️  Le trait HasHistorique existe déjà dans {$modelName}");

            return 'skipped';
        }

        // Vérifier si le modèle utilise déjà des traits
        if (Str::contains($content, 'use HasFactory')) {
            // Ajouter le trait après HasFactory
            $pattern = '/(use HasFactory[^;]*;)/';
            $replacement = '$1, \\App\\Models\\Traits\\HasHistorique';

            if (Str::contains($content, 'HasFactory, SoftDeletes')) {
                $pattern = '/(use HasFactory, SoftDeletes)/';
                $replacement = '$1, \\App\\Models\\Traits\\HasHistorique';
            }

            $newContent = preg_replace($pattern, $replacement, $content);

            if ($newContent === $content) {
                $this->warn("⚠️  Impossible de modifier le modèle {$modelName} - structure inattendue");

                return 'error';
            }
        } else {
            // Ajouter le trait dans la classe
            $pattern = '/(class ' . $modelName . ' extends Model\s*\{)/';
            $replacement = '$1' . PHP_EOL . '    use \\App\\Models\\Traits\\HasHistorique;';

            $newContent = preg_replace($pattern, $replacement, $content);

            if ($newContent === $content) {
                $this->warn("⚠️  Impossible de modifier le modèle {$modelName} - structure inattendue");

                return 'error';
            }
        }

        // Sauvegarder le fichier
        try {
            File::put($modelFile, $newContent);
            $this->info("✅ Trait HasHistorique ajouté à {$modelName}");

            return 'success';
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la sauvegarde de {$modelName} : " . $e->getMessage());

            return 'error';
        }
    }
}
