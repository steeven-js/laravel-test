<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\EnvironmentProtection;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    use EnvironmentProtection;

    public function run(): void
    {
        // Vérifier l'environnement avant de générer des données
        $this->ensureDataGenerationAllowed();
        
        $this->command?->info('Création des tickets...');
        $this->createTickets();
    }

    private function createTickets(): void
    {
        $clients = Client::all();
        $users = User::all();

        if ($clients->isEmpty() || $users->isEmpty()) {
            $this->command?->warn('Aucun client ou utilisateur trouvé. Impossible de créer des tickets.');

            return;
        }

        $tickets = [
            [
                'titre' => 'Problème de connexion à l\'application',
                'description' => 'L\'utilisateur ne parvient pas à se connecter à l\'application. Erreur 403 affichée.',
                'priorite' => 'haute',
                'statut' => 'en_cours',
                'type' => 'incident',
                'temps_estime' => 4,
                'temps_passe' => 2,
                'progression' => 50,
                'date_echeance' => now()->addDays(2),
                'notes_internes' => 'Problème lié aux permissions utilisateur. Vérification en cours.',
                'solution' => null,
                'visible_client' => true,
            ],
            [
                'titre' => 'Demande d\'ajout de fonctionnalité',
                'description' => 'Le client souhaite ajouter un système d\'export PDF des rapports.',
                'priorite' => 'normale',
                'statut' => 'ouvert',
                'type' => 'demande',
                'temps_estime' => 8,
                'temps_passe' => 0,
                'progression' => 0,
                'date_echeance' => now()->addWeeks(2),
                'notes_internes' => 'Fonctionnalité demandée par plusieurs clients. À prioriser.',
                'solution' => null,
                'visible_client' => true,
            ],
            [
                'titre' => 'Bug dans le calcul des totaux',
                'description' => 'Les totaux ne s\'affichent pas correctement dans le tableau de bord.',
                'priorite' => 'critique',
                'statut' => 'resolu',
                'type' => 'bug',
                'temps_estime' => 6,
                'temps_passe' => 5,
                'progression' => 100,
                'date_echeance' => now()->subDay(),
                'date_resolution' => now()->subDay(),
                'notes_internes' => 'Problème de formatage des nombres décimaux.',
                'solution' => 'Correction du formatage des nombres décimaux dans le calcul des totaux. Test effectué et validé.',
                'visible_client' => true,
            ],
            [
                'titre' => 'Question sur l\'utilisation de l\'API',
                'description' => 'Le client a des questions sur l\'utilisation de l\'API REST.',
                'priorite' => 'faible',
                'statut' => 'ferme',
                'type' => 'question',
                'temps_estime' => 2,
                'temps_passe' => 1,
                'progression' => 100,
                'date_echeance' => now()->subDays(3),
                'date_resolution' => now()->subDays(3),
                'notes_internes' => 'Documentation envoyée au client.',
                'solution' => 'Envoi de la documentation complète de l\'API avec exemples d\'utilisation.',
                'visible_client' => true,
            ],
            [
                'titre' => 'Problème de performance',
                'description' => 'L\'application est lente lors du chargement des données.',
                'priorite' => 'haute',
                'statut' => 'en_cours',
                'type' => 'incident',
                'temps_estime' => 12,
                'temps_passe' => 6,
                'progression' => 50,
                'date_echeance' => now()->addWeek(),
                'notes_internes' => 'Optimisation des requêtes en cours. Indexation de la base de données.',
                'solution' => null,
                'visible_client' => true,
            ],
            [
                'titre' => 'Demande de modification du design',
                'description' => 'Le client souhaite modifier la couleur du thème de l\'interface.',
                'priorite' => 'normale',
                'statut' => 'ouvert',
                'type' => 'demande',
                'temps_estime' => 4,
                'temps_passe' => 0,
                'progression' => 0,
                'date_echeance' => now()->addWeeks(3),
                'notes_internes' => 'Changement de couleur simple à implémenter.',
                'solution' => null,
                'visible_client' => true,
            ],
            [
                'titre' => 'Erreur lors de l\'upload de fichiers',
                'description' => 'Les utilisateurs ne peuvent pas télécharger des fichiers de plus de 10MB.',
                'priorite' => 'normale',
                'statut' => 'resolu',
                'type' => 'bug',
                'temps_estime' => 3,
                'temps_passe' => 3,
                'progression' => 100,
                'date_echeance' => now()->subDays(5),
                'date_resolution' => now()->subDays(5),
                'notes_internes' => 'Limite de taille de fichier à augmenter.',
                'solution' => 'Augmentation de la limite de taille de fichier à 50MB. Configuration serveur mise à jour.',
                'visible_client' => true,
            ],
            [
                'titre' => 'Problème d\'envoi d\'emails',
                'description' => 'Les emails automatiques ne sont pas envoyés.',
                'priorite' => 'critique',
                'statut' => 'en_cours',
                'type' => 'incident',
                'temps_estime' => 8,
                'temps_passe' => 4,
                'progression' => 50,
                'date_echeance' => now()->addDays(3),
                'notes_internes' => 'Problème avec le serveur SMTP. Configuration à vérifier.',
                'solution' => null,
                'visible_client' => false,
            ],
            [
                'titre' => 'Demande d\'intégration avec un service tiers',
                'description' => 'Le client souhaite intégrer l\'application avec son CRM Salesforce.',
                'priorite' => 'normale',
                'statut' => 'ouvert',
                'type' => 'demande',
                'temps_estime' => 16,
                'temps_passe' => 0,
                'progression' => 0,
                'date_echeance' => now()->addMonths(2),
                'notes_internes' => 'Intégration complexe nécessitant l\'API Salesforce.',
                'solution' => null,
                'visible_client' => true,
            ],
            [
                'titre' => 'Bug dans l\'affichage des graphiques',
                'description' => 'Les graphiques ne s\'affichent pas correctement sur mobile.',
                'priorite' => 'haute',
                'statut' => 'resolu',
                'type' => 'bug',
                'temps_estime' => 6,
                'temps_passe' => 6,
                'progression' => 100,
                'date_echeance' => now()->subWeek(),
                'date_resolution' => now()->subWeek(),
                'notes_internes' => 'Problème de responsive design sur les graphiques.',
                'solution' => 'Correction du CSS pour l\'affichage responsive des graphiques sur mobile.',
                'visible_client' => true,
            ],
        ];

        foreach ($tickets as $ticketData) {
            $ticket = Ticket::create([
                'titre' => $ticketData['titre'],
                'description' => $ticketData['description'],
                'priorite' => $ticketData['priorite'],
                'statut' => $ticketData['statut'],
                'type' => $ticketData['type'],
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'created_by' => $users->random()->id,
                'temps_estime' => $ticketData['temps_estime'],
                'temps_passe' => $ticketData['temps_passe'],
                'progression' => $ticketData['progression'],
                'date_echeance' => $ticketData['date_echeance'],
                'date_resolution' => $ticketData['date_resolution'] ?? null,
                'notes_internes' => $ticketData['notes_internes'],
                'solution' => $ticketData['solution'],
                'visible_client' => $ticketData['visible_client'],
            ]);
        }

        $this->command?->info('✅ Tickets créés avec succès !');
    }
}
