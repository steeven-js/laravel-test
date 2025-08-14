<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Traits\EnvironmentProtection;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class EnvironmentProtectionDemo extends Page
{
    use EnvironmentProtection;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationGroup = 'Administration';
    
    protected static ?string $title = 'Démonstration Protection Environnement';
    
    protected static ?string $slug = 'environment-protection-demo';
    
    protected static ?int $navigationSort = 100;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Bouton toujours visible
        $actions[] = Action::make('info')
            ->label('ℹ️ Informations')
            ->icon('heroicon-o-information-circle')
            ->color('info')
            ->action(function () {
                $this->showEnvironmentInfo();
            });

        // Bouton de génération seulement en développement
        if ($this->shouldShowGenerationButtons()) {
            $actions[] = Action::make('generate_demo_data')
                ->label('🎲 Générer données de démonstration')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->action(function () {
                    $this->generateDemoData();
                });
        }

        // Bouton d'alerte seulement en production
        if ($this->shouldHideGenerationButtons()) {
            $actions[] = Action::make('production_warning')
                ->label('🚫 Mode Production')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->action(function () {
                    $this->showProductionWarning();
                });
        }

        return $actions;
    }

    protected function getViewData(): array
    {
        return [
            'environment' => app()->environment(),
            'isDataGenerationAllowed' => $this->isDataGenerationAllowed(),
            'shouldShowButtons' => $this->shouldShowGenerationButtons(),
            'shouldHideButtons' => $this->shouldHideGenerationButtons(),
            'isProduction' => $this->isProduction(),
            'isStaging' => $this->isStaging(),
            'errorMessage' => $this->getEnvironmentErrorMessage(),
        ];
    }

    public function showEnvironmentInfo(): void
    {
        $environment = app()->environment();
        $message = "Environnement actuel : {$environment}\n\n";
        
        if ($this->isDataGenerationAllowed()) {
            $message .= "✅ Génération de données : AUTORISÉE\n";
            $message .= "✅ Boutons de génération : VISIBLES";
        } else {
            $message .= "❌ Génération de données : BLOQUÉE\n";
            $message .= "❌ Boutons de génération : MASQUÉS";
        }

        Notification::make()
            ->title('Informations Environnement')
            ->body($message)
            ->info()
            ->send();
    }

    public function generateDemoData(): void
    {
        // Vérifier à nouveau l'environnement (double sécurité)
        if (!$this->isDataGenerationAllowed()) {
            Notification::make()
                ->title('🚫 Génération bloquée')
                ->body($this->getEnvironmentErrorMessage())
                ->danger()
                ->send();
            return;
        }

        // Simuler la génération de données
        Notification::make()
            ->title('✅ Données générées')
            ->body('Données de démonstration créées avec succès !')
            ->success()
            ->send();
    }

    public function showProductionWarning(): void
    {
        Notification::make()
            ->title('🚫 Mode Production')
            ->body('Les boutons de génération de données sont masqués en production pour des raisons de sécurité.')
            ->warning()
            ->send();
    }
}
