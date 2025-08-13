<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration de l'historique des actions
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient la configuration pour le système d'historique
    | des actions dans l'application.
    |
    */

    // Modèles qui doivent avoir un historique automatique
    'models' => [
        'App\Models\Client',
        'App\Models\Entreprise',
        'App\Models\Devis',
        'App\Models\Facture',
        'App\Models\Service',
        'App\Models\Ticket',
        'App\Models\Todo',
        'App\Models\Opportunity',
        'App\Models\ClientEmail',
        'App\Models\EmailTemplate',
        'App\Models\Notification',
        'App\Models\Madinia',
        'App\Models\SecteurActivite',
        'App\Models\User',
        'App\Models\UserRole',
    ],

    // Actions à traquer automatiquement
    'actions' => [
        'creation' => [
            'label' => 'Création',
            'color' => 'success',
            'icon' => 'heroicon-o-document-plus',
        ],
        'modification' => [
            'label' => 'Modification',
            'color' => 'primary',
            'icon' => 'heroicon-o-pencil',
        ],
        'suppression' => [
            'label' => 'Suppression',
            'color' => 'danger',
            'icon' => 'heroicon-o-trash',
        ],
        'changement_statut' => [
            'label' => 'Changement de statut',
            'color' => 'warning',
            'icon' => 'heroicon-o-arrow-path',
        ],
        'envoi_email' => [
            'label' => 'Envoi d\'email',
            'color' => 'info',
            'icon' => 'heroicon-o-envelope',
        ],
        'paiement_stripe' => [
            'label' => 'Paiement Stripe',
            'color' => 'success',
            'icon' => 'heroicon-o-credit-card',
        ],
        'transformation' => [
            'label' => 'Transformation',
            'color' => 'warning',
            'icon' => 'heroicon-o-arrow-path',
        ],
        'archivage' => [
            'label' => 'Archivage',
            'color' => 'gray',
            'icon' => 'heroicon-o-archive-box',
        ],
        'restauration' => [
            'label' => 'Restauration',
            'color' => 'info',
            'icon' => 'heroicon-o-arrow-uturn-left',
        ],
    ],

    // Champs à exclure de l'historique des modifications
    'exclude_fields' => [
        'updated_at',
        'created_at',
        'deleted_at',
        'remember_token',
        'email_verified_at',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ],

    // Nombre maximum d'historiques à conserver par entité
    'max_records_per_entity' => 1000,

    // Activer/désactiver l'historique automatique
    'auto_tracking' => env('HISTORIQUE_AUTO_TRACKING', true),

    // Activer/désactiver l'historique des modifications
    'track_modifications' => env('HISTORIQUE_TRACK_MODIFICATIONS', true),

    // Activer/désactiver l'historique des créations
    'track_creations' => env('HISTORIQUE_TRACK_CREATIONS', true),

    // Activer/désactiver l'historique des suppressions
    'track_deletions' => env('HISTORIQUE_TRACK_DELETIONS', true),
];
