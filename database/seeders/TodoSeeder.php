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

        // Tâches de base pour chaque client
        $baseTodos = [
            [
                'titre' => 'Appeler le client pour suivi',
                'description' => 'Contacter le client pour faire le point sur le projet en cours.',
                'termine' => false,
                'priorite' => 'haute',
                'date_echeance' => now()->addDays(2),
            ],
            [
                'titre' => 'Préparer la proposition commerciale',
                'description' => 'Rédiger la proposition commerciale pour le nouveau projet.',
                'termine' => false,
                'priorite' => 'normale',
                'date_echeance' => now()->addWeek(),
            ],
            [
                'titre' => 'Réviser la documentation technique',
                'description' => 'Mettre à jour la documentation technique du projet.',
                'termine' => true,
                'priorite' => 'normale',
                'date_echeance' => now()->subDays(3),
            ],
            [
                'titre' => 'Organiser la réunion de lancement',
                'description' => 'Planifier et organiser la réunion de lancement du projet.',
                'termine' => false,
                'priorite' => 'haute',
                'date_echeance' => now()->addDays(5),
            ],
            [
                'titre' => 'Tester les nouvelles fonctionnalités',
                'description' => 'Effectuer les tests des nouvelles fonctionnalités développées.',
                'termine' => false,
                'priorite' => 'normale',
                'date_echeance' => now()->addDays(3),
            ],
            [
                'titre' => 'Envoyer le devis au client',
                'description' => 'Finaliser et envoyer le devis au client.',
                'termine' => true,
                'priorite' => 'critique',
                'date_echeance' => now()->subWeek(),
            ],
            [
                'titre' => 'Préparer la présentation',
                'description' => 'Créer la présentation pour la réunion client.',
                'termine' => false,
                'priorite' => 'normale',
                'date_echeance' => now()->addDays(4),
            ],
            [
                'titre' => 'Mettre à jour le planning',
                'description' => 'Actualiser le planning du projet avec les nouvelles échéances.',
                'termine' => false,
                'priorite' => 'faible',
                'date_echeance' => now()->addWeeks(2),
            ],
            [
                'titre' => 'Contacter le fournisseur',
                'description' => 'Appeler le fournisseur pour commander le matériel nécessaire.',
                'termine' => false,
                'priorite' => 'normale',
                'date_echeance' => now()->addDays(7),
            ],
            [
                'titre' => 'Rédiger le rapport mensuel',
                'description' => 'Préparer le rapport mensuel d\'activité pour le client.',
                'termine' => true,
                'priorite' => 'normale',
                'date_echeance' => now()->subDays(5),
            ],
            [
                'titre' => 'Former l\'équipe client',
                'description' => 'Organiser une session de formation pour l\'équipe client.',
                'termine' => false,
                'priorite' => 'haute',
                'date_echeance' => now()->addWeeks(2),
            ],
            [
                'titre' => 'Vérifier la conformité',
                'description' => 'Contrôler la conformité du projet aux normes en vigueur.',
                'termine' => false,
                'priorite' => 'normale',
                'date_echeance' => now()->addWeeks(3),
            ],
            [
                'titre' => 'Optimiser les performances',
                'description' => 'Analyser et optimiser les performances de l\'application.',
                'termine' => false,
                'priorite' => 'normale',
                'date_echeance' => now()->addWeeks(4),
            ],
            [
                'titre' => 'Préparer la livraison',
                'description' => 'Finaliser la préparation de la livraison du projet.',
                'termine' => false,
                'priorite' => 'critique',
                'date_echeance' => now()->addWeeks(2),
            ],
            [
                'titre' => 'Archiver les documents',
                'description' => 'Archiver les documents du projet terminé.',
                'termine' => true,
                'priorite' => 'faible',
                'date_echeance' => now()->subWeeks(2),
            ],
            [
                'titre' => 'Analyser les besoins client',
                'description' => 'Analyser en détail les besoins exprimés par le client.',
                'termine' => false,
                'priorite' => 'haute',
                'date_echeance' => now()->addDays(1),
            ],
            [
                'titre' => 'Créer les maquettes',
                'description' => 'Créer les maquettes de l\'interface utilisateur.',
                'termine' => false,
                'priorite' => 'normale',
                'date_echeance' => now()->addWeeks(1),
            ],
            [
                'titre' => 'Effectuer les tests de régression',
                'description' => 'Effectuer une série complète de tests de régression.',
                'termine' => false,
                'priorite' => 'normale',
                'date_echeance' => now()->addWeeks(3),
            ],
            [
                'titre' => 'Préparer la documentation utilisateur',
                'description' => 'Rédiger la documentation utilisateur finale.',
                'termine' => false,
                'priorite' => 'faible',
                'date_echeance' => now()->addWeeks(4),
            ],
            [
                'titre' => 'Planifier la maintenance',
                'description' => 'Planifier les interventions de maintenance préventive.',
                'termine' => false,
                'priorite' => 'normale',
                'date_echeance' => now()->addWeeks(6),
            ],
            [
                'titre' => 'Configurer l\'environnement',
                'description' => 'Mettre en place l\'environnement de développement.',
                'termine' => true,
                'priorite' => 'critique',
                'date_echeance' => now()->subWeeks(1),
            ],
            [
                'titre' => 'Réaliser l\'audit de sécurité',
                'description' => 'Effectuer un audit complet de la sécurité du système.',
                'termine' => false,
                'priorite' => 'haute',
                'date_echeance' => now()->addWeeks(2),
            ],
            [
                'titre' => 'Mettre en place le monitoring',
                'description' => 'Configurer les outils de monitoring et d\'alertes.',
                'termine' => false,
                'priorite' => 'normale',
                'date_echeance' => now()->addWeeks(3),
            ],
            [
                'titre' => 'Former les utilisateurs finaux',
                'description' => 'Organiser la formation des utilisateurs finaux.',
                'termine' => false,
                'priorite' => 'haute',
                'date_echeance' => now()->addWeeks(4),
            ],
            [
                'titre' => 'Finaliser la documentation technique',
                'description' => 'Compléter la documentation technique du projet.',
                'termine' => false,
                'priorite' => 'normale',
                'date_echeance' => now()->addWeeks(5),
            ],
        ];

        $createdCount = 0;

        // Créer des tâches pour chaque client
        foreach ($clients as $client) {
            // Nombre aléatoire de tâches par client (entre 8 et 15)
            $numTodos = rand(8, 15);

            // Sélectionner aléatoirement des tâches de base
            $selectedTodos = collect($baseTodos)->shuffle()->take($numTodos);

            $ordre = 1;

            foreach ($selectedTodos as $todoData) {
                // Modifier légèrement les données pour éviter les doublons
                $modifiedTodo = array_merge($todoData, [
                    'titre' => $todoData['titre'] . ' - ' . $client->nom,
                    'ordre' => $ordre,
                    'client_id' => $client->id,
                    'user_id' => $users->random()->id,
                ]);

                Todo::create($modifiedTodo);
                $ordre++;
                $createdCount++;
            }
        }

        $this->command?->info("✅ {$createdCount} tâches créées avec succès pour {$clients->count()} clients !");
        $this->command?->info('📊 Moyenne : ' . round($createdCount / $clients->count(), 1) . ' tâches par client');
    }
}
