<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait EnvironmentProtection
{
    /**
     * Vérifie si la génération de données est autorisée dans l'environnement actuel
     */
    protected function isDataGenerationAllowed(): bool
    {
        $environment = App::environment();
        
        // Vérifier d'abord les environnements bloqués
        $blockedEnvironments = config('environment-protection.blocked_environments', ['production', 'staging']);
        if (in_array($environment, $blockedEnvironments)) {
            return false;
        }
        
        // Vérifier les environnements autorisés
        $allowedEnvironments = config('environment-protection.allowed_environments', ['local', 'development', 'testing']);
        return in_array($environment, $allowedEnvironments);
    }

    /**
     * Vérifie si l'environnement est en production
     */
    protected function isProduction(): bool
    {
        return App::environment('production');
    }

    /**
     * Vérifie si l'environnement est en staging
     */
    protected function isStaging(): bool
    {
        return App::environment('staging');
    }

    /**
     * Lance une exception si la génération de données n'est pas autorisée
     */
    protected function ensureDataGenerationAllowed(): void
    {
        if (!$this->isDataGenerationAllowed()) {
            $environment = App::environment();
            $message = $this->getEnvironmentErrorMessage();
            
            throw new \Exception($message);
        }
    }

    /**
     * Retourne un message d'erreur formaté pour l'environnement
     */
    protected function getEnvironmentErrorMessage(): string
    {
        $environment = App::environment();
        $errorMessages = config('environment-protection.error_messages', []);
        
        // Message spécifique à l'environnement
        if (isset($errorMessages[$environment])) {
            return $errorMessages[$environment];
        }
        
        // Message par défaut
        return $errorMessages['default'] ?? 
               "⚠️ Environnement {$environment} non autorisé pour la génération de données. " .
               "Seuls les environnements de développement sont autorisés.";
    }

    /**
     * Vérifie si les boutons de génération de données doivent être affichés dans l'interface
     * Utile pour masquer complètement les boutons en production
     */
    protected function shouldShowGenerationButtons(): bool
    {
        return $this->isDataGenerationAllowed();
    }

    /**
     * Vérifie si les boutons de génération de données doivent être masqués dans l'interface
     * Utile pour masquer complètement les boutons en production
     */
    protected function shouldHideGenerationButtons(): bool
    {
        return !$this->isDataGenerationAllowed();
    }
}
