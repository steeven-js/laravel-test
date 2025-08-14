<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Protection d'environnement pour la génération de données
    |--------------------------------------------------------------------------
    |
    | Ce fichier configure quels environnements sont autorisés pour
    | la génération de données de test et de développement.
    |
    */

    // Environnements autorisés pour la génération de données
    'allowed_environments' => [
        'local',
        'development',
        'testing',
    ],

    // Environnements bloqués (priorité sur allowed_environments)
    'blocked_environments' => [
        'production',
        'staging',
    ],

    // Routes protégées par le middleware
    'protected_routes' => [
        'seed:*',
        'generate:*',
        'test:*',
        'admin/generate-*',
    ],

    // Messages d'erreur personnalisés par environnement
    'error_messages' => [
        'production' => '🚫 Génération de données bloquée en production pour des raisons de sécurité. Cette fonctionnalité est réservée aux environnements de développement.',
        'staging' => '⚠️ Génération de données bloquée en staging. Cette fonctionnalité est réservée aux environnements de développement.',
        'default' => '⚠️ Génération de données non autorisée dans cet environnement. Seuls les environnements de développement sont autorisés.',
    ],

    // Logs de sécurité
    'security_logging' => [
        'enabled' => true,
        'channel' => 'security',
        'level' => 'warning',
    ],
];
