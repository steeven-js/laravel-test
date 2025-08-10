<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Madinia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MadiniaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider la table avant de la remplir
        Madinia::truncate();

        // Créer ou récupérer l'utilisateur de contact pour Madin.IA
        $contactUser = User::firstOrCreate(
            ['email' => 'd.brault@madin-ia.com'],
            [
                'name' => 'David Brault',
                'email' => 'd.brault@madin-ia.com',
                'password' => Hash::make('password'), // Mot de passe temporaire
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("Utilisateur de contact créé/récupéré: {$contactUser->name} (ID: {$contactUser->id})");

        // Chemin vers le fichier CSV
        $csvFile = database_path('seeders/madinia_rows (2).csv');

        if (! file_exists($csvFile)) {
            $this->command->error("Le fichier CSV 'madinia_rows (2).csv' n'existe pas dans database/seeders/");

            return;
        }

        // Lire le fichier CSV
        $handle = fopen($csvFile, 'r');
        if (! $handle) {
            $this->command->error("Impossible d'ouvrir le fichier CSV");

            return;
        }

        // Lire l'en-tête
        $headers = fgetcsv($handle);
        if (! $headers) {
            $this->command->error("Impossible de lire l'en-tête du CSV");
            fclose($handle);

            return;
        }

        // Lire et traiter chaque ligne
        $rowNumber = 1; // Commencer à 1 car l'en-tête est la ligne 0
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            try {
                // Créer un tableau associatif avec les en-têtes
                $data = array_combine($headers, $row);

                // Remplacer l'ID du contact par l'utilisateur créé
                $data['contact_principal_id'] = $contactUser->id;

                // Traiter les champs spéciaux
                if (isset($data['reseaux_sociaux']) && ! empty($data['reseaux_sociaux'])) {
                    // Le CSV contient déjà du JSON valide, on peut le décoder directement
                    $reseauxSociaux = json_decode($data['reseaux_sociaux'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data['reseaux_sociaux'] = $reseauxSociaux;
                    } else {
                        $this->command->warn("Erreur JSON dans les réseaux sociaux à la ligne {$rowNumber}: " . json_last_error_msg());
                        $data['reseaux_sociaux'] = null;
                    }
                }

                // Créer l'enregistrement Madinia
                Madinia::create($data);

                $this->command->info("Ligne {$rowNumber} importée avec succès: {$data['name']}");

            } catch (\Exception $e) {
                $this->command->error("Erreur à la ligne {$rowNumber}: " . $e->getMessage());
                $this->command->error('Données: ' . json_encode($row));
            }
        }

        fclose($handle);

        $this->command->info('Seeding de la table madinia terminé.');
    }
}
