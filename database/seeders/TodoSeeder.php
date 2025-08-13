<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Création des tâches...');

        $clients = Client::all();
        $users = User::all();

        if ($clients->isEmpty() || $users->isEmpty()) {
            $this->command?->warn('Aucun client ou utilisateur trouvé. Impossible de créer des tâches.');

            return;
        }

        $todos = [
            [
                'titre' => 'Appeler le client pour suivi',
                'description' => 'Contacter le client pour faire le point sur le projet en cours.',
                'termine' => false,
                'ordre' => 1,
                'priorite' => 'haute',
                'date_echeance' => now()->addDays(2),
            ],
            [
                'titre' => 'Préparer la proposition commerciale',
                'description' => 'Rédiger la proposition commerciale pour le nouveau projet.',
                'termine' => false,
                'ordre' => 2,
                'priorite' => 'normale',
                'date_echeance' => now()->addWeek(),
            ],
            [
                'titre' => 'Réviser la documentation technique',
                'description' => 'Mettre à jour la documentation technique du projet.',
                'termine' => true,
                'ordre' => 3,
                'priorite' => 'normale',
                'date_echeance' => now()->subDays(3),
            ],
            [
                'titre' => 'Organiser la réunion de lancement',
                'description' => 'Planifier et organiser la réunion de lancement du projet.',
                'termine' => false,
                'ordre' => 4,
                'priorite' => 'haute',
                'date_echeance' => now()->addDays(5),
            ],
            [
                'titre' => 'Tester les nouvelles fonctionnalités',
                'description' => 'Effectuer les tests des nouvelles fonctionnalités développées.',
                'termine' => false,
                'ordre' => 5,
                'priorite' => 'normale',
                'date_echeance' => now()->addDays(3),
            ],
            [
                'titre' => 'Envoyer le devis au client',
                'description' => 'Finaliser et envoyer le devis au client.',
                'termine' => true,
                'ordre' => 6,
                'priorite' => 'critique',
                'date_echeance' => now()->subWeek(),
            ],
            [
                'titre' => 'Préparer la présentation',
                'description' => 'Créer la présentation pour la réunion client.',
                'termine' => false,
                'ordre' => 7,
                'priorite' => 'normale',
                'date_echeance' => now()->addDays(4),
            ],
            [
                'titre' => 'Mettre à jour le planning',
                'description' => 'Actualiser le planning du projet avec les nouvelles échéances.',
                'termine' => false,
                'ordre' => 8,
                'priorite' => 'faible',
                'date_echeance' => now()->addWeeks(2),
            ],
            [
                'titre' => 'Contacter le fournisseur',
                'description' => 'Appeler le fournisseur pour commander le matériel nécessaire.',
                'termine' => false,
                'ordre' => 9,
                'priorite' => 'normale',
                'date_echeance' => now()->addDays(7),
            ],
            [
                'titre' => 'Rédiger le rapport mensuel',
                'description' => 'Préparer le rapport mensuel d\'activité pour le client.',
                'termine' => true,
                'ordre' => 10,
                'priorite' => 'normale',
                'date_echeance' => now()->subDays(5),
            ],
            [
                'titre' => 'Former l\'équipe client',
                'description' => 'Organiser une session de formation pour l\'équipe client.',
                'termine' => false,
                'ordre' => 11,
                'priorite' => 'haute',
                'date_echeance' => now()->addWeeks(2),
            ],
            [
                'titre' => 'Vérifier la conformité',
                'description' => 'Contrôler la conformité du projet aux normes en vigueur.',
                'termine' => false,
                'ordre' => 12,
                'priorite' => 'normale',
                'date_echeance' => now()->addWeeks(3),
            ],
            [
                'titre' => 'Optimiser les performances',
                'description' => 'Analyser et optimiser les performances de l\'application.',
                'termine' => false,
                'ordre' => 13,
                'priorite' => 'normale',
                'date_echeance' => now()->addWeeks(4),
            ],
            [
                'titre' => 'Préparer la livraison',
                'description' => 'Finaliser la préparation de la livraison du projet.',
                'termine' => false,
                'ordre' => 14,
                'priorite' => 'critique',
                'date_echeance' => now()->addWeeks(2),
            ],
            [
                'titre' => 'Archiver les documents',
                'description' => 'Archiver les documents du projet terminé.',
                'termine' => true,
                'ordre' => 15,
                'priorite' => 'faible',
                'date_echeance' => now()->subWeeks(2),
            ],
        ];

        foreach ($todos as $todoData) {
            $todo = Todo::create([
                'titre' => $todoData['titre'],
                'description' => $todoData['description'],
                'termine' => $todoData['termine'],
                'ordre' => $todoData['ordre'],
                'priorite' => $todoData['priorite'],
                'date_echeance' => $todoData['date_echeance'],
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
            ]);
        }

        $this->command?->info('✅ Tâches créées avec succès !');
    }
}
