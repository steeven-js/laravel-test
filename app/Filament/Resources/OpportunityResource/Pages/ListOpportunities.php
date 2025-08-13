<?php

declare(strict_types=1);

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Filament\Resources\OpportunityResource;
use App\Models\Client;
use App\Models\Opportunity;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListOpportunities extends ListRecords
{
    protected static string $resource = OpportunityResource::class;

    protected static ?string $breadcrumb = 'Liste';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouvelle'),
            Actions\Action::make('generateFakeOpportunities')
                ->label('Générer des opportunités factices')
                ->icon('heroicon-o-chart-bar')
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
                            ->body("Créez d'abord des clients et des utilisateurs pour générer des opportunités.")
                            ->danger()
                            ->send();

                        return;
                    }

                    $faker = FakerFactory::create('fr_FR');
                    $etapes = ['prospection', 'qualification', 'proposition', 'negociation', 'fermeture', 'gagnee', 'perdue'];
                    $created = 0;

                    for ($i = 0; $i < $count; $i++) {
                        $etape = $faker->randomElement($etapes);
                        $probabilite = match ($etape) {
                            'prospection' => $faker->numberBetween(10, 30),
                            'qualification' => $faker->numberBetween(30, 50),
                            'proposition' => $faker->numberBetween(60, 80),
                            'negociation' => $faker->numberBetween(70, 90),
                            'fermeture' => $faker->numberBetween(85, 95),
                            'gagnee' => 100,
                            'perdue' => 0,
                            default => $faker->numberBetween(0, 100),
                        };

                        $montant = $faker->numberBetween(5000, 50000);
                        $dateCloturePrevue = $faker->dateTimeBetween('now', '+6 months');

                        // Pour les opportunités gagnées ou perdues, ajouter une date de clôture réelle
                        $dateClotureReelle = null;
                        if (in_array($etape, ['gagnee', 'perdue'])) {
                            $dateClotureReelle = $faker->dateTimeBetween('-2 months', 'now');
                        }

                        $opportunityNames = [
                            'Refonte site web e-commerce',
                            'Application mobile de gestion',
                            'Système de facturation automatisé',
                            'Plateforme de formation en ligne',
                            'Intégration API de paiement',
                            'Dashboard analytique',
                            'Système de réservation en ligne',
                            'Application de livraison',
                            'Site vitrine professionnel',
                            'Système de gestion des stocks',
                            'CRM personnalisé',
                            'Application de suivi client',
                            'Plateforme de e-learning',
                            'Système de gestion des commandes',
                            'Application de comptabilité',
                        ];

                        $descriptions = [
                            'Développement d\'une solution complète pour optimiser les processus métier.',
                            'Création d\'une application moderne et intuitive pour améliorer la productivité.',
                            'Mise en place d\'un système automatisé pour simplifier les tâches administratives.',
                            'Conception d\'une plateforme innovante pour répondre aux besoins spécifiques.',
                            'Intégration de technologies avancées pour moderniser l\'infrastructure existante.',
                        ];

                        $notes = [
                            'Client très intéressé par notre approche. Réunion de présentation prévue.',
                            'Négociation en cours sur le délai de livraison et les fonctionnalités incluses.',
                            'Besoins à préciser lors de la prochaine réunion.',
                            'Premier contact établi. Envoi de documentation prévu.',
                            'Contrat en cours de finalisation. Signature prévue cette semaine.',
                            'Projet gagné ! Démarrage prévu dans 2 semaines.',
                            'Projet perdu au profit d\'un concurrent. Budget insuffisant.',
                            'Proposition technique en cours de préparation.',
                            'Réunion de qualification prévue la semaine prochaine.',
                            'Négociation sur les délais et le prix final.',
                        ];

                        Opportunity::create([
                            'nom' => $faker->randomElement($opportunityNames),
                            'description' => $faker->randomElement($descriptions),
                            'etape' => $etape,
                            'probabilite' => $probabilite,
                            'montant' => $montant,
                            'date_cloture_prevue' => $dateCloturePrevue,
                            'date_cloture_reelle' => $dateClotureReelle,
                            'client_id' => $faker->randomElement($clientIds),
                            'user_id' => $faker->randomElement($userIds),
                            'notes' => $faker->randomElement($notes),
                            'active' => true,
                        ]);

                        $created++;
                    }

                    $recipient = Filament::auth()->user();

                    Notification::make()
                        ->title($created . ' opportunités factices créées')
                        ->success()
                        ->sendToDatabase($recipient);

                    Notification::make()->title($created . ' opportunités factices créées')->success()->send();
                })
                ->requiresConfirmation(),
        ];
    }
}
