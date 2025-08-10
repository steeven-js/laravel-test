<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class OptimizeFilamentForms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament:optimize-forms {--resource= : Nom de la ressource spécifique à optimiser}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimise les formulaires Filament avec un affichage en deux colonnes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Optimisation des formulaires Filament...');

        $resourcesPath = app_path('Filament/Resources');
        
        if (!File::exists($resourcesPath)) {
            $this->error('Le dossier des ressources Filament n\'existe pas.');
            return 1;
        }

        $resourceName = $this->option('resource');
        
        if ($resourceName) {
            // Optimiser une ressource spécifique
            $this->optimizeSpecificResource($resourceName);
        } else {
            // Optimiser toutes les ressources
            $this->optimizeAllResources($resourcesPath);
        }

        $this->info('✅ Optimisation terminée !');
        return 0;
    }

    /**
     * Optimise une ressource spécifique.
     */
    private function optimizeSpecificResource(string $resourceName): void
    {
        $resourcePath = app_path("Filament/Resources/{$resourceName}.php");
        
        if (!File::exists($resourcePath)) {
            $this->error("La ressource {$resourceName} n'existe pas.");
            return;
        }

        $this->info("🔧 Optimisation de {$resourceName}...");
        $this->optimizeResourceFile($resourcePath);
    }

    /**
     * Optimise toutes les ressources.
     */
    private function optimizeAllResources(string $resourcesPath): void
    {
        $resourceFiles = File::glob($resourcesPath . '/*.php');
        
        $this->info("📁 Trouvé " . count($resourceFiles) . " ressources à analyser...");

        foreach ($resourceFiles as $resourceFile) {
            $resourceName = basename($resourceFile, '.php');
            $this->info("🔧 Analyse de {$resourceName}...");
            
            if ($this->needsOptimization($resourceFile)) {
                $this->info("  ⚡ Optimisation nécessaire pour {$resourceName}");
                $this->optimizeResourceFile($resourceFile);
            } else {
                $this->info("  ✅ {$resourceName} est déjà optimisé");
            }
        }
    }

    /**
     * Vérifie si une ressource a besoin d'optimisation.
     */
    private function needsOptimization(string $filePath): bool
    {
        $content = File::get($filePath);
        
        // Vérifier si le fichier contient déjà des composants Grid
        if (Str::contains($content, 'Forms\\Components\\Grid::make')) {
            return false;
        }

        // Vérifier si le fichier contient des champs de formulaire
        if (Str::contains($content, 'Forms\\Components\\TextInput::make') ||
            Str::contains($content, 'Forms\\Components\\Select::make') ||
            Str::contains($content, 'Forms\\Components\\Textarea::make')) {
            return true;
        }

        return false;
    }

    /**
     * Optimise un fichier de ressource.
     */
    private function optimizeResourceFile(string $filePath): void
    {
        $content = File::get($filePath);
        $originalContent = $content;

        // Ajouter des sections et des grilles basiques
        $content = $this->addBasicSections($content);
        
        // Sauvegarder le fichier original
        $backupPath = $filePath . '.backup.' . date('Y-m-d-H-i-s');
        File::put($backupPath, $originalContent);
        
        // Sauvegarder le fichier optimisé
        File::put($filePath, $content);
        
        $this->info("  💾 Fichier optimisé et sauvegardé dans {$backupPath}");
    }

    /**
     * Ajoute des sections et des grilles basiques au formulaire.
     */
    private function addBasicSections(string $content): string
    {
        // Rechercher la méthode form
        if (!Str::contains($content, 'public static function form(Form $form): Form')) {
            return $content;
        }

        // Ajouter des imports si nécessaire
        if (!Str::contains($content, 'use Filament\\Forms\\Components\\Section;')) {
            $content = str_replace(
                'use Filament\\Forms;',
                "use Filament\\Forms;\nuse Filament\\Forms\\Components\\Section;",
                $content
            );
        }

        // Remplacer le schéma basique par des sections
        $content = preg_replace(
            '/->schema\(\[(.*?)\]\)/s',
            '->schema([
                Section::make(\'Informations générales\')
                    ->description(\'Détails principaux de l\'entité\')
                    ->icon(\'heroicon-o-information-circle\')
                    ->schema([
                        Forms\\Components\\Grid::make(2)
                            ->schema([
                                $1
                            ]),
                    ]),
            ])',
            $content
        );

        return $content;
    }

    /**
     * Affiche des conseils d'optimisation.
     */
    private function showOptimizationTips(): void
    {
        $this->newLine();
        $this->info('💡 Conseils d\'optimisation :');
        $this->line('  • Utilisez Forms\\Components\\Grid::make(2) pour les paires de champs');
        $this->line('  • Organisez les champs en sections logiques');
        $this->line('  • Ajoutez des descriptions et des icônes aux sections');
        $this->line('  • Utilisez columnSpanFull pour les champs longs');
        $this->line('  • Privilégiez les grilles de 2 colonnes pour la plupart des cas');
    }
}
