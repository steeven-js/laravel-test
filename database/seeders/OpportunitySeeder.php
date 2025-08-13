<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Création des opportunités...');

        $clients = Client::all();
        $users = User::all();

        if ($clients->isEmpty() || $users->isEmpty()) {
            $this->command?->warn('Aucun client ou utilisateur trouvé. Impossible de créer des opportunités.');

            return;
        }

        $opportunities = [
            [
                'nom' => 'Refonte site web e-commerce',
                'description' => 'Refonte complète du site web e-commerce avec nouvelle interface utilisateur et intégration de nouveaux moyens de paiement.',
                'etape' => 'proposition',
                'probabilite' => 75,
                'montant' => 25000.00,
                'date_cloture_prevue' => now()->addMonths(2),
                'notes' => 'Client très intéressé par notre approche. Réunion de présentation prévue la semaine prochaine.',
            ],
            [
                'nom' => 'Application mobile de gestion',
                'description' => 'Développement d\'une application mobile pour la gestion des stocks et des commandes.',
                'etape' => 'negociation',
                'probabilite' => 60,
                'montant' => 18000.00,
                'date_cloture_prevue' => now()->addMonth(),
                'notes' => 'Négociation en cours sur le délai de livraison et les fonctionnalités incluses.',
            ],
            [
                'nom' => 'Système de facturation automatisé',
                'description' => 'Mise en place d\'un système de facturation automatisé avec intégration comptable.',
                'etape' => 'qualification',
                'probabilite' => 40,
                'montant' => 12000.00,
                'date_cloture_prevue' => now()->addMonths(3),
                'notes' => 'Besoins à préciser lors de la prochaine réunion.',
            ],
            [
                'nom' => 'Plateforme de formation en ligne',
                'description' => 'Création d\'une plateforme de formation en ligne avec système de suivi des apprenants.',
                'etape' => 'prospection',
                'probabilite' => 25,
                'montant' => 35000.00,
                'date_cloture_prevue' => now()->addMonths(4),
                'notes' => 'Premier contact établi. Envoi de documentation prévu.',
            ],
            [
                'nom' => 'Intégration API de paiement',
                'description' => 'Intégration d\'API de paiement (Stripe, PayPal) dans le système existant.',
                'etape' => 'fermeture',
                'probabilite' => 90,
                'montant' => 8000.00,
                'date_cloture_prevue' => now()->addWeeks(2),
                'notes' => 'Contrat en cours de finalisation. Signature prévue cette semaine.',
            ],
            [
                'nom' => 'Dashboard analytique',
                'description' => 'Développement d\'un dashboard analytique pour le suivi des performances commerciales.',
                'etape' => 'gagnee',
                'probabilite' => 100,
                'montant' => 15000.00,
                'date_cloture_prevue' => now()->subWeek(),
                'date_cloture_reelle' => now()->subWeek(),
                'notes' => 'Projet gagné ! Démarrage prévu dans 2 semaines.',
            ],
            [
                'nom' => 'Système de réservation en ligne',
                'description' => 'Création d\'un système de réservation en ligne pour un hôtel.',
                'etape' => 'perdue',
                'probabilite' => 0,
                'montant' => 22000.00,
                'date_cloture_prevue' => now()->subWeeks(2),
                'date_cloture_reelle' => now()->subWeeks(2),
                'notes' => 'Projet perdu au profit d\'un concurrent. Budget insuffisant.',
            ],
            [
                'nom' => 'Application de livraison',
                'description' => 'Développement d\'une application mobile pour la gestion des livraisons.',
                'etape' => 'proposition',
                'probabilite' => 70,
                'montant' => 28000.00,
                'date_cloture_prevue' => now()->addMonths(2),
                'notes' => 'Proposition technique en cours de préparation.',
            ],
            [
                'nom' => 'Site vitrine professionnel',
                'description' => 'Création d\'un site vitrine moderne et responsive pour une entreprise de services.',
                'etape' => 'qualification',
                'probabilite' => 50,
                'montant' => 6000.00,
                'date_cloture_prevue' => now()->addMonths(2),
                'notes' => 'Réunion de qualification prévue la semaine prochaine.',
            ],
            [
                'nom' => 'Système de gestion des stocks',
                'description' => 'Développement d\'un système de gestion des stocks avec alertes automatiques.',
                'etape' => 'negociation',
                'probabilite' => 65,
                'montant' => 16000.00,
                'date_cloture_prevue' => now()->addMonth(),
                'notes' => 'Négociation sur les délais et le prix final.',
            ],
        ];

        foreach ($opportunities as $opportunityData) {
            $opportunity = Opportunity::create([
                'nom' => $opportunityData['nom'],
                'description' => $opportunityData['description'],
                'etape' => $opportunityData['etape'],
                'probabilite' => $opportunityData['probabilite'],
                'montant' => $opportunityData['montant'],
                'date_cloture_prevue' => $opportunityData['date_cloture_prevue'],
                'date_cloture_reelle' => $opportunityData['date_cloture_reelle'] ?? null,
                'client_id' => $clients->random()->id,
                'user_id' => $users->random()->id,
                'notes' => $opportunityData['notes'],
                'active' => true,
            ]);
        }

        $this->command?->info('✅ Opportunités créées avec succès !');
    }
}
