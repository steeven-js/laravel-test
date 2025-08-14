<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;

class PermissionManager extends Field
{
    protected string $view = 'filament.forms.components.permission-manager';

    public static function make(string $name = 'permissions'): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('Gestion des permissions')
                ->description('Configurez les permissions pour chaque ressource')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    // Permissions pour les clients
                    Section::make('Clients')
                        ->description('Gestion des clients et prospects')
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Checkbox::make('clients.view')
                                        ->label('Voir les clients')
                                        ->helperText('Permet de consulter la liste des clients'),
                                    Checkbox::make('clients.create')
                                        ->label('Créer des clients')
                                        ->helperText('Permet d\'ajouter de nouveaux clients'),
                                    Checkbox::make('clients.edit')
                                        ->label('Modifier les clients')
                                        ->helperText('Permet de modifier les informations clients'),
                                    Checkbox::make('clients.delete')
                                        ->label('Supprimer les clients')
                                        ->helperText('Permet de supprimer des clients'),
                                    Checkbox::make('clients.export')
                                        ->label('Exporter les clients')
                                        ->helperText('Permet d\'exporter les données clients'),
                                ]),
                        ]),

                    // Permissions pour les devis
                    Section::make('Devis')
                        ->description('Gestion des devis et propositions')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Checkbox::make('devis.view')
                                        ->label('Voir les devis')
                                        ->helperText('Permet de consulter les devis'),
                                    Checkbox::make('devis.create')
                                        ->label('Créer des devis')
                                        ->helperText('Permet de créer de nouveaux devis'),
                                    Checkbox::make('devis.edit')
                                        ->label('Modifier les devis')
                                        ->helperText('Permet de modifier les devis'),
                                    Checkbox::make('devis.delete')
                                        ->label('Supprimer les devis')
                                        ->helperText('Permet de supprimer des devis'),
                                    Checkbox::make('devis.send')
                                        ->label('Envoyer les devis')
                                        ->helperText('Permet d\'envoyer des devis par email'),
                                    Checkbox::make('devis.export')
                                        ->label('Exporter les devis')
                                        ->helperText('Permet d\'exporter les devis'),
                                    Checkbox::make('devis.transform_to_facture')
                                        ->label('Transformer en facture')
                                        ->helperText('Permet de transformer un devis en facture'),
                                ]),
                        ]),

                    // Permissions pour les factures
                    Section::make('Factures')
                        ->description('Gestion des factures et paiements')
                        ->icon('heroicon-o-currency-euro')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Checkbox::make('factures.view')
                                        ->label('Voir les factures')
                                        ->helperText('Permet de consulter les factures'),
                                    Checkbox::make('factures.create')
                                        ->label('Créer des factures')
                                        ->helperText('Permet de créer de nouvelles factures'),
                                    Checkbox::make('factures.edit')
                                        ->label('Modifier les factures')
                                        ->helperText('Permet de modifier les factures'),
                                    Checkbox::make('factures.delete')
                                        ->label('Supprimer les factures')
                                        ->helperText('Permet de supprimer des factures'),
                                    Checkbox::make('factures.send')
                                        ->label('Envoyer les factures')
                                        ->helperText('Permet d\'envoyer des factures par email'),
                                    Checkbox::make('factures.export')
                                        ->label('Exporter les factures')
                                        ->helperText('Permet d\'exporter les factures'),
                                ]),
                        ]),

                    // Permissions pour les opportunités
                    Section::make('Opportunités')
                        ->description('Gestion du pipeline commercial')
                        ->icon('heroicon-o-chart-bar')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Checkbox::make('opportunities.view')
                                        ->label('Voir les opportunités')
                                        ->helperText('Permet de consulter les opportunités'),
                                    Checkbox::make('opportunities.create')
                                        ->label('Créer des opportunités')
                                        ->helperText('Permet de créer de nouvelles opportunités'),
                                    Checkbox::make('opportunities.edit')
                                        ->label('Modifier les opportunités')
                                        ->helperText('Permet de modifier les opportunités'),
                                    Checkbox::make('opportunities.delete')
                                        ->label('Supprimer les opportunités')
                                        ->helperText('Permet de supprimer des opportunités'),
                                    Checkbox::make('opportunities.export')
                                        ->label('Exporter les opportunités')
                                        ->helperText('Permet d\'exporter les opportunités'),
                                ]),
                        ]),

                    // Permissions pour les tickets
                    Section::make('Tickets')
                        ->description('Gestion du support client')
                        ->icon('heroicon-o-ticket')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Checkbox::make('tickets.view')
                                        ->label('Voir les tickets')
                                        ->helperText('Permet de consulter les tickets'),
                                    Checkbox::make('tickets.create')
                                        ->label('Créer des tickets')
                                        ->helperText('Permet de créer de nouveaux tickets'),
                                    Checkbox::make('tickets.edit')
                                        ->label('Modifier les tickets')
                                        ->helperText('Permet de modifier les tickets'),
                                    Checkbox::make('tickets.delete')
                                        ->label('Supprimer les tickets')
                                        ->helperText('Permet de supprimer des tickets'),
                                    Checkbox::make('tickets.assign')
                                        ->label('Assigner les tickets')
                                        ->helperText('Permet d\'assigner des tickets'),
                                    Checkbox::make('tickets.export')
                                        ->label('Exporter les tickets')
                                        ->helperText('Permet d\'exporter les tickets'),
                                ]),
                        ]),

                    // Permissions pour les tâches
                    Section::make('Tâches')
                        ->description('Gestion des tâches et projets')
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Checkbox::make('todos.view')
                                        ->label('Voir les tâches')
                                        ->helperText('Permet de consulter les tâches'),
                                    Checkbox::make('todos.create')
                                        ->label('Créer des tâches')
                                        ->helperText('Permet de créer de nouvelles tâches'),
                                    Checkbox::make('todos.edit')
                                        ->label('Modifier les tâches')
                                        ->helperText('Permet de modifier les tâches'),
                                    Checkbox::make('todos.delete')
                                        ->label('Supprimer les tâches')
                                        ->helperText('Permet de supprimer des tâches'),
                                    Checkbox::make('todos.assign')
                                        ->label('Assigner les tâches')
                                        ->helperText('Permet d\'assigner des tâches'),
                                    Checkbox::make('todos.export')
                                        ->label('Exporter les tâches')
                                        ->helperText('Permet d\'exporter les tâches'),
                                ]),
                        ]),

                    // Permissions pour les utilisateurs
                    Section::make('Utilisateurs')
                        ->description('Gestion des utilisateurs et rôles')
                        ->icon('heroicon-o-users')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Checkbox::make('users.view')
                                        ->label('Voir les utilisateurs')
                                        ->helperText('Permet de consulter les utilisateurs'),
                                    Checkbox::make('users.create')
                                        ->label('Créer des utilisateurs')
                                        ->helperText('Permet de créer de nouveaux utilisateurs'),
                                    Checkbox::make('users.edit')
                                        ->label('Modifier les utilisateurs')
                                        ->helperText('Permet de modifier les utilisateurs'),
                                    Checkbox::make('users.delete')
                                        ->label('Supprimer les utilisateurs')
                                        ->helperText('Permet de supprimer des utilisateurs'),
                                    Checkbox::make('users.manage_roles')
                                        ->label('Gérer les rôles')
                                        ->helperText('Permet de gérer les rôles utilisateur'),
                                ]),
                        ]),

                    // Permissions pour les services
                    Section::make('Services')
                        ->description('Gestion du catalogue de services')
                        ->icon('heroicon-o-briefcase')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Checkbox::make('services.view')
                                        ->label('Voir les services')
                                        ->helperText('Permet de consulter les services'),
                                    Checkbox::make('services.create')
                                        ->label('Créer des services')
                                        ->helperText('Permet de créer de nouveaux services'),
                                    Checkbox::make('services.edit')
                                        ->label('Modifier les services')
                                        ->helperText('Permet de modifier les services'),
                                    Checkbox::make('services.delete')
                                        ->label('Supprimer les services')
                                        ->helperText('Permet de supprimer des services'),
                                    Checkbox::make('services.import_csv')
                                        ->label('Importer depuis CSV')
                                        ->helperText('Permet d\'importer des services depuis CSV'),
                                ]),
                        ]),

                    // Permissions pour les entreprises
                    Section::make('Entreprises')
                        ->description('Gestion des entreprises clientes')
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Checkbox::make('entreprises.view')
                                        ->label('Voir les entreprises')
                                        ->helperText('Permet de consulter les entreprises'),
                                    Checkbox::make('entreprises.create')
                                        ->label('Créer des entreprises')
                                        ->helperText('Permet de créer de nouvelles entreprises'),
                                    Checkbox::make('entreprises.edit')
                                        ->label('Modifier les entreprises')
                                        ->helperText('Permet de modifier les entreprises'),
                                    Checkbox::make('entreprises.delete')
                                        ->label('Supprimer les entreprises')
                                        ->helperText('Permet de supprimer des entreprises'),
                                ]),
                        ]),

                    // Permissions pour les secteurs d'activité
                    Section::make('Secteurs d\'activité')
                        ->description('Gestion des secteurs d\'activité')
                        ->icon('heroicon-o-tag')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Checkbox::make('secteurs_activite.view')
                                        ->label('Voir les secteurs')
                                        ->helperText('Permet de consulter les secteurs'),
                                    Checkbox::make('secteurs_activite.create')
                                        ->label('Créer des secteurs')
                                        ->helperText('Permet de créer de nouveaux secteurs'),
                                    Checkbox::make('secteurs_activite.edit')
                                        ->label('Modifier les secteurs')
                                        ->helperText('Permet de modifier les secteurs'),
                                    Checkbox::make('secteurs_activite.delete')
                                        ->label('Supprimer les secteurs')
                                        ->helperText('Permet de supprimer des secteurs'),
                                ]),
                        ]),

                    // Permissions pour les paramètres
                    Section::make('Paramètres')
                        ->description('Configuration du système')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Checkbox::make('settings.view')
                                        ->label('Voir les paramètres')
                                        ->helperText('Permet de consulter les paramètres'),
                                    Checkbox::make('settings.edit')
                                        ->label('Modifier les paramètres')
                                        ->helperText('Permet de modifier les paramètres'),
                                ]),
                        ]),

                    // Permissions pour l'historique
                    Section::make('Historique')
                        ->description('Consultation de l\'historique des actions')
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Checkbox::make('historique.view')
                                        ->label('Voir l\'historique')
                                        ->helperText('Permet de consulter l\'historique'),
                                    Checkbox::make('historique.export')
                                        ->label('Exporter l\'historique')
                                        ->helperText('Permet d\'exporter l\'historique'),
                                ]),
                        ]),

                    // Permissions pour le tableau de bord
                    Section::make('Tableau de bord')
                        ->description('Accès aux statistiques')
                        ->icon('heroicon-o-presentation-chart-line')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Checkbox::make('dashboard.view_all_stats')
                                        ->label('Voir toutes les statistiques')
                                        ->helperText('Permet de voir toutes les statistiques'),
                                    Checkbox::make('dashboard.view_own_stats')
                                        ->label('Voir ses propres statistiques')
                                        ->helperText('Permet de voir ses propres statistiques'),
                                ]),
                        ]),

                    // Permissions pour la génération
                    Section::make('Génération de données')
                        ->description('Génération de données de test')
                        ->icon('heroicon-o-beaker')
                        ->schema([
                            Checkbox::make('generation.generate_test_data')
                                ->label('Générer des données de test')
                                ->helperText('Permet de générer des données de test pour le développement'),
                        ]),
                ]),
        ];
    }

    /**
     * Expose les sous-composants au moteur de formulaires de Filament.
     * Ceci permet d'afficher effectivement le contenu dans la vue via
     * $getChildComponentContainer().
     */
    public function getChildComponents(): array
    {
        return $this->getFormSchema();
    }
}
