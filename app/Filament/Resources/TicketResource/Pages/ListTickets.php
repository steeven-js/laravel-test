<?php

declare(strict_types=1);

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected static ?string $breadcrumb = 'Liste';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouveau'),
            Actions\Action::make('generateFakeTickets')
                ->label('Générer des tickets factices')
                ->icon('heroicon-o-lifebuoy')
                ->visible(fn (): bool => Auth::user()?->userRole?->name === 'super_admin')
                ->form([
                    Forms\Components\TextInput::make('count')
                        ->label('Quantité')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(50)
                        ->default(10)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $count = (int) ($data['count'] ?? 0);
                    if ($count < 1) {
                        Notification::make()->title('Quantité invalide')->danger()->send();

                        return;
                    }

                    $clientIds = Client::query()->pluck('id')->all();
                    $userIds = User::query()->pluck('id')->all();

                    if (empty($clientIds) || empty($userIds)) {
                        Notification::make()
                            ->title('Données insuffisantes')
                            ->body("Créez d'abord des clients et des utilisateurs pour générer des tickets.")
                            ->danger()
                            ->send();

                        return;
                    }

                    $faker = FakerFactory::create('fr_FR');
                    $priorites = ['faible', 'normale', 'haute', 'critique'];
                    $statuts = ['ouvert', 'en_cours', 'resolu', 'ferme'];
                    $types = ['bug', 'demande', 'incident', 'question', 'autre'];
                    $created = 0;

                    for ($i = 0; $i < $count; $i++) {
                        $statut = $faker->randomElement($statuts);
                        $priorite = $faker->randomElement($priorites);
                        $type = $faker->randomElement($types);

                        // Ajuster la progression selon le statut
                        $progression = match ($statut) {
                            'ouvert' => 0,
                            'en_cours' => $faker->numberBetween(10, 80),
                            'resolu' => 100,
                            'ferme' => 100,
                            default => 0,
                        };

                        // Temps estimé et passé selon la priorité
                        $tempsEstime = match ($priorite) {
                            'critique' => $faker->numberBetween(2, 8),
                            'haute' => $faker->numberBetween(4, 12),
                            'normale' => $faker->numberBetween(6, 16),
                            'faible' => $faker->numberBetween(2, 8),
                            default => $faker->numberBetween(4, 12),
                        };

                        $tempsPasse = $statut === 'resolu' || $statut === 'ferme'
                            ? $tempsEstime
                            : $faker->numberBetween(0, $tempsEstime);

                        // Date d'échéance selon la priorité
                        $dateEcheance = match ($priorite) {
                            'critique' => $faker->dateTimeBetween('now', '+2 days'),
                            'haute' => $faker->dateTimeBetween('now', '+1 week'),
                            'normale' => $faker->dateTimeBetween('now', '+2 weeks'),
                            'faible' => $faker->dateTimeBetween('now', '+1 month'),
                            default => $faker->dateTimeBetween('now', '+1 week'),
                        };

                        // Date de résolution pour les tickets résolus
                        $dateResolution = null;
                        if ($statut === 'resolu' || $statut === 'ferme') {
                            $dateResolution = $faker->dateTimeBetween('-1 week', 'now');
                        }

                        $ticketTitles = [
                            'Problème de connexion à l\'application',
                            'Demande d\'ajout de fonctionnalité',
                            'Bug dans le calcul des totaux',
                            'Question sur l\'utilisation de l\'API',
                            'Problème de performance',
                            'Demande de modification du design',
                            'Erreur lors de l\'upload de fichiers',
                            'Problème d\'envoi d\'emails',
                            'Demande d\'intégration avec un service tiers',
                            'Bug dans l\'affichage des graphiques',
                            'Problème d\'authentification',
                            'Demande de formation utilisateur',
                            'Erreur dans l\'export des données',
                            'Problème de synchronisation',
                            'Demande de personnalisation',
                        ];

                        $descriptions = [
                            'L\'utilisateur ne parvient pas à accéder aux fonctionnalités principales.',
                            'Nouvelle fonctionnalité demandée pour améliorer l\'expérience utilisateur.',
                            'Calculs incorrects dans les rapports financiers.',
                            'Clarification nécessaire sur l\'utilisation des endpoints.',
                            'Lenteur excessive lors du chargement des données.',
                            'Modification de l\'interface utilisateur demandée.',
                            'Impossible de télécharger des fichiers volumineux.',
                            'Les notifications automatiques ne sont pas envoyées.',
                            'Intégration avec un système externe nécessaire.',
                            'Affichage incorrect sur les appareils mobiles.',
                        ];

                        $notesInternes = [
                            'Problème lié aux permissions utilisateur. Vérification en cours.',
                            'Fonctionnalité demandée par plusieurs clients. À prioriser.',
                            'Problème de formatage des nombres décimaux.',
                            'Documentation envoyée au client.',
                            'Optimisation des requêtes en cours. Indexation de la base de données.',
                            'Changement de couleur simple à implémenter.',
                            'Limite de taille de fichier à augmenter.',
                            'Problème avec le serveur SMTP. Configuration à vérifier.',
                            'Intégration complexe nécessitant l\'API externe.',
                            'Problème de responsive design sur les graphiques.',
                        ];

                        $solutions = [
                            'Correction du formatage des nombres décimaux dans le calcul des totaux. Test effectué et validé.',
                            'Envoi de la documentation complète de l\'API avec exemples d\'utilisation.',
                            'Augmentation de la limite de taille de fichier à 50MB. Configuration serveur mise à jour.',
                            'Correction du CSS pour l\'affichage responsive des graphiques sur mobile.',
                            'Réinitialisation du mot de passe utilisateur.',
                            'Formation dispensée par visioconférence.',
                            'Correction du script d\'export avec gestion des caractères spéciaux.',
                            'Synchronisation manuelle effectuée. Automatisation en cours.',
                            'Personnalisation implémentée selon les spécifications.',
                        ];

                        Ticket::create([
                            'titre' => $faker->randomElement($ticketTitles),
                            'description' => $faker->randomElement($descriptions),
                            'priorite' => $priorite,
                            'statut' => $statut,
                            'type' => $type,
                            'client_id' => $faker->randomElement($clientIds),
                            'user_id' => $faker->randomElement($userIds),
                            'created_by' => $faker->randomElement($userIds),
                            'temps_estime' => $tempsEstime,
                            'temps_passe' => $tempsPasse,
                            'progression' => $progression,
                            'date_echeance' => $dateEcheance,
                            'date_resolution' => $dateResolution,
                            'notes_internes' => $faker->randomElement($notesInternes),
                            'solution' => $statut === 'resolu' ? $faker->randomElement($solutions) : null,
                            'visible_client' => $faker->boolean(80), // 80% visible par le client
                        ]);

                        $created++;
                    }

                    $recipient = Filament::auth()->user();

                    Notification::make()
                        ->title($created . ' tickets factices créés')
                        ->success()
                        ->sendToDatabase($recipient);

                    Notification::make()->title($created . ' tickets factices créés')->success()->send();
                })
                ->requiresConfirmation(),
        ];
    }
}
