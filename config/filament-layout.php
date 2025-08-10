<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration de l'affichage en deux colonnes
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient la configuration pour l'affichage en deux colonnes
    | dans les ressources Filament de l'application.
    |
    */

    'default_grid_columns' => 2,
    
    'max_grid_columns' => 3,
    
    'preferred_grid_columns' => [
        'client' => 2,
        'entreprise' => 2,
        'devis' => 2,
        'facture' => 2,
        'service' => 2,
        'user' => 2,
        'opportunity' => 2,
        'ticket' => 2,
        'todo' => 2,
        'notification' => 2,
        'email_template' => 2,
        'secteur_activite' => 2,
        'historique' => 2,
        'user_role' => 2,
        'client_email' => 2,
        'numero_sequence' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Icônes par défaut pour les sections
    |--------------------------------------------------------------------------
    |
    | Icônes Heroicon à utiliser par défaut pour chaque type de section.
    |
    */
    
    'default_section_icons' => [
        'general' => 'heroicon-o-information-circle',
        'contact' => 'heroicon-o-envelope',
        'address' => 'heroicon-o-map-pin',
        'settings' => 'heroicon-o-cog-6-tooth',
        'commercial' => 'heroicon-o-currency-dollar',
        'technical' => 'heroicon-o-wrench-screwdriver',
        'security' => 'heroicon-o-shield-check',
        'notification' => 'heroicon-o-bell',
        'opportunity' => 'heroicon-o-light-bulb',
        'ticket' => 'heroicon-o-ticket',
        'todo' => 'heroicon-o-check-circle',
        'service' => 'heroicon-o-wrench',
        'client' => 'heroicon-o-user-group',
        'entreprise' => 'heroicon-o-building-office',
        'devis' => 'heroicon-o-document-text',
        'facture' => 'heroicon-o-receipt-refund',
        'email' => 'heroicon-o-envelope',
        'historique' => 'heroicon-o-clock',
        'user_role' => 'heroicon-o-key',
        'numero_sequence' => 'heroicon-o-hashtag',
    ],

    /*
    |--------------------------------------------------------------------------
    | Descriptions par défaut pour les sections
    |--------------------------------------------------------------------------
    |
    | Descriptions à utiliser par défaut pour chaque type de section.
    |
    */
    
    'default_section_descriptions' => [
        'general' => 'Informations générales de l\'entité',
        'contact' => 'Coordonnées et informations de contact',
        'address' => 'Adresse et localisation',
        'settings' => 'Paramètres et configuration',
        'commercial' => 'Informations commerciales et financières',
        'technical' => 'Détails techniques et spécifications',
        'security' => 'Paramètres de sécurité et accès',
        'notification' => 'Configuration des notifications',
        'opportunity' => 'Détails de l\'opportunité commerciale',
        'ticket' => 'Informations du ticket de support',
        'todo' => 'Détails de la tâche à effectuer',
        'service' => 'Caractéristiques du service',
        'client' => 'Informations du client',
        'entreprise' => 'Détails de l\'entreprise',
        'devis' => 'Informations du devis',
        'facture' => 'Détails de la facture',
        'email' => 'Configuration du modèle d\'email',
        'historique' => 'Historique des actions',
        'user_role' => 'Rôles et permissions utilisateur',
        'numero_sequence' => 'Configuration des numéros de séquence',
    ],

    /*
    |--------------------------------------------------------------------------
    | Règles de validation pour l'affichage en deux colonnes
    |--------------------------------------------------------------------------
    |
    | Règles pour déterminer quand utiliser 2, 3 ou plus de colonnes.
    |
    */
    
    'grid_rules' => [
        'use_2_columns' => [
            'max_fields_per_section' => 8,
            'field_types' => ['text', 'email', 'tel', 'url', 'number', 'select', 'date', 'datetime'],
        ],
        'use_3_columns' => [
            'max_fields_per_section' => 12,
            'field_types' => ['text', 'email', 'tel', 'url', 'number', 'select', 'date', 'datetime', 'boolean'],
        ],
        'use_full_width' => [
            'field_types' => ['textarea', 'rich_editor', 'markdown', 'file_upload', 'repeater'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Classes CSS personnalisées
    |--------------------------------------------------------------------------
    |
    | Classes CSS à appliquer aux grilles et sections.
    |
    */
    
    'custom_css_classes' => [
        'grid_2_columns' => 'grid-cols-1 md:grid-cols-2 gap-4',
        'grid_3_columns' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4',
        'section_header' => 'text-lg font-semibold text-gray-900 dark:text-white',
        'section_description' => 'text-sm text-gray-600 dark:text-gray-400',
    ],
];
