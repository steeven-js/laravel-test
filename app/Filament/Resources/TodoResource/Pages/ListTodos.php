<?php

declare(strict_types=1);

namespace App\Filament\Resources\TodoResource\Pages;

use App\Filament\Resources\TodoResource;
use App\Models\Client;
use App\Models\Todo;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTodos extends ListRecords
{
    protected static string $resource = TodoResource::class;

    protected static ?string $breadcrumb = 'Liste';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouvelle'),
            Actions\Action::make('generateFakeTodos')
                ->label('Générer des tâches factices')
                ->icon('heroicon-o-check-circle')
                ->visible(fn (): bool => Auth::user()?->userRole?->name === 'super_admin')
                ->form([
                    Forms\Components\TextInput::make('count')
                        ->label('Quantité')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(50)
                        ->default(15)
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
                            ->body("Créez d'abord des clients et des utilisateurs pour générer des tâches.")
                            ->danger()
                            ->send();

                        return;
                    }

                    $faker = FakerFactory::create('fr_FR');
                    $priorites = ['faible', 'normale', 'haute', 'critique'];
                    $created = 0;

                    for ($i = 0; $i < $count; $i++) {
                        $priorite = $faker->randomElement($priorites);
                        $termine = $faker->boolean(30); // 30% de chance d'être terminé
                        $ordre = $i + 1;

                        // Date d'échéance selon la priorité
                        $dateEcheance = match ($priorite) {
                            'critique' => $faker->dateTimeBetween('now', '+3 days'),
                            'haute' => $faker->dateTimeBetween('now', '+1 week'),
                            'normale' => $faker->dateTimeBetween('now', '+2 weeks'),
                            'faible' => $faker->dateTimeBetween('now', '+1 month'),
                            default => $faker->dateTimeBetween('now', '+1 week'),
                        };

                        // Pour les tâches terminées, ajuster la date d'échéance dans le passé
                        if ($termine) {
                            $dateEcheance = $faker->dateTimeBetween('-2 weeks', 'now');
                        }

                        $todoTitles = [
                            'Appeler le client pour suivi',
                            'Préparer la proposition commerciale',
                            'Réviser la documentation technique',
                            'Organiser la réunion de lancement',
                            'Tester les nouvelles fonctionnalités',
                            'Envoyer le devis au client',
                            'Préparer la présentation',
                            'Mettre à jour le planning',
                            'Contacter le fournisseur',
                            'Rédiger le rapport mensuel',
                            'Former l\'équipe client',
                            'Vérifier la conformité',
                            'Optimiser les performances',
                            'Préparer la livraison',
                            'Archiver les documents',
                            'Analyser les besoins client',
                            'Créer les maquettes',
                            'Effectuer les tests de régression',
                            'Préparer la documentation utilisateur',
                            'Planifier la maintenance',
                        ];

                        $descriptions = [
                            'Contacter le client pour faire le point sur le projet en cours.',
                            'Rédiger la proposition commerciale pour le nouveau projet.',
                            'Mettre à jour la documentation technique du projet.',
                            'Planifier et organiser la réunion de lancement du projet.',
                            'Effectuer les tests des nouvelles fonctionnalités développées.',
                            'Finaliser et envoyer le devis au client.',
                            'Créer la présentation pour la réunion client.',
                            'Actualiser le planning du projet avec les nouvelles échéances.',
                            'Appeler le fournisseur pour commander le matériel nécessaire.',
                            'Préparer le rapport mensuel d\'activité pour le client.',
                            'Organiser une session de formation pour l\'équipe client.',
                            'Contrôler la conformité du projet aux normes en vigueur.',
                            'Analyser et optimiser les performances de l\'application.',
                            'Finaliser la préparation de la livraison du projet.',
                            'Archiver les documents du projet terminé.',
                            'Analyser en détail les besoins exprimés par le client.',
                            'Créer les maquettes de l\'interface utilisateur.',
                            'Effectuer une série complète de tests de régression.',
                            'Rédiger la documentation utilisateur finale.',
                            'Planifier les interventions de maintenance préventive.',
                        ];

                        Todo::create([
                            'titre' => $faker->randomElement($todoTitles),
                            'description' => $faker->randomElement($descriptions),
                            'termine' => $termine,
                            'ordre' => $ordre,
                            'priorite' => $priorite,
                            'date_echeance' => $dateEcheance,
                            'client_id' => $faker->randomElement($clientIds),
                            'user_id' => $faker->randomElement($userIds),
                        ]);

                        $created++;
                    }

                    $recipient = Filament::auth()->user();

                    Notification::make()
                        ->title($created . ' tâches factices créées')
                        ->success()
                        ->sendToDatabase($recipient);

                    Notification::make()->title($created . ' tâches factices créées')->success()->send();
                })
                ->requiresConfirmation(),
        ];
    }
}
