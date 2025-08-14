<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Entreprise;
use App\Models\SecteurActivite;
use App\Traits\EnvironmentProtection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientProductionSeeder extends Seeder
{
    use EnvironmentProtection;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier l'environnement avant de générer des données
        $this->ensureDataGenerationAllowed();
        
        $this->command->info('🚀 Début de la migration des clients de production...');

        // Vérifier que le fichier CSV existe
        $csvPath = base_path('clients_rows.csv');
        if (! file_exists($csvPath)) {
            $this->command->error('❌ Fichier clients_rows.csv non trouvé dans le répertoire racine');

            return;
        }

        // Lire le fichier CSV
        $csvData = $this->readCsvFile($csvPath);
        if (empty($csvData)) {
            $this->command->error('❌ Aucune donnée trouvée dans le fichier CSV');

            return;
        }

        $this->command->info("📊 {$csvData['count']} clients trouvés dans le CSV");

        // Démarrer la transaction
        DB::beginTransaction();

        try {
            $stats = [
                'clients_crees' => 0,
                'clients_ignores' => 0,
                'entreprises_crees' => 0,
                'erreurs' => 0,
            ];

            foreach ($csvData['data'] as $row) {
                try {
                    $result = $this->processClientRow($row);

                    switch ($result['status']) {
                        case 'created':
                            $stats['clients_crees']++;
                            break;
                        case 'ignored':
                            $stats['clients_ignores']++;
                            break;
                        case 'error':
                            $stats['erreurs']++;
                            $this->command->warn("⚠️  Erreur pour le client {$row['nom']} {$row['prenom']}: {$result['message']}");
                            break;
                    }
                } catch (\Exception $e) {
                    $stats['erreurs']++;
                    $this->command->warn("⚠️  Erreur critique pour le client {$row['nom']} {$row['prenom']}: " . $e->getMessage());

                    // Continuer avec le client suivant au lieu d'arrêter tout le processus
                    continue;
                }
            }

            // Valider la transaction
            DB::commit();

            // Afficher les statistiques
            $this->displayStats($stats);

            $this->command->info('✅ Migration des clients terminée avec succès !');

        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();

            $this->command->error('❌ Erreur lors de la migration: ' . $e->getMessage());
            Log::error('Erreur ClientProductionSeeder: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Lit le fichier CSV et retourne les données
     */
    private function readCsvFile(string $path): array
    {
        $data = [];
        $count = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            // Lire l'en-tête
            $headers = fgetcsv($handle);

            if ($headers === false) {
                fclose($handle);

                return ['data' => [], 'count' => 0];
            }

            // Lire les données
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === count($headers)) {
                    $data[] = array_combine($headers, $row);
                    $count++;
                }
            }

            fclose($handle);
        }

        return ['data' => $data, 'count' => $count];
    }

    /**
     * Traite une ligne de client du CSV
     */
    private function processClientRow(array $row): array
    {
        try {
            // Vérifier si le client existe déjà (par email ou nom+prenom)
            $existingClient = $this->findExistingClient($row);
            if ($existingClient) {
                return [
                    'status' => 'ignored',
                    'message' => 'Client déjà existant',
                    'client_id' => $existingClient->id,
                ];
            }

            // Traiter l'entreprise
            $entreprise = $this->processEntreprise($row);

            // Créer le client
            $clientData = $this->mapClientData($row, $entreprise);
            $client = Client::create($clientData);

            return [
                'status' => 'created',
                'message' => 'Client créé avec succès',
                'client_id' => $client->id,
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'client_id' => null,
            ];
        }
    }

    /**
     * Trouve un client existant
     */
    private function findExistingClient(array $row): ?Client
    {
        // Recherche par email si disponible
        if (! empty($row['email'])) {
            $client = Client::where('email', $row['email'])->first();
            if ($client) {
                return $client;
            }
        }

        // Recherche par nom + prénom
        if (! empty($row['nom']) && ! empty($row['prenom'])) {
            $client = Client::where('nom', $row['nom'])
                ->where('prenom', $row['prenom'])
                ->first();
            if ($client) {
                return $client;
            }
        }

        return null;
    }

    /**
     * Traite l'entreprise du client
     */
    private function processEntreprise(array $row): ?Entreprise
    {
        // Si pas d'entreprise_id, essayer de trouver une entreprise existante basée sur la ville
        if (empty($row['entreprise_id'])) {
            return $this->findExistingEntreprise($row);
        }

        // Vérifier si l'entreprise existe
        $entreprise = Entreprise::find($row['entreprise_id']);
        if ($entreprise) {
            return $entreprise;
        }

        // Si l'ID n'existe pas, essayer de trouver une entreprise existante
        return $this->findExistingEntreprise($row);
    }

    /**
     * Trouve une entreprise existante basée sur les données du client
     */
    private function findExistingEntreprise(array $row): ?Entreprise
    {
        // Essayer de trouver une entreprise existante basée sur la ville
        if (! empty($row['ville'])) {
            $existingEntreprise = Entreprise::where('ville', $row['ville'])
                ->where('actif', true)
                ->first();
            if ($existingEntreprise) {
                return $existingEntreprise;
            }
        }

        // Essayer de trouver une entreprise existante basée sur le pays
        if (! empty($row['pays'])) {
            $existingEntreprise = Entreprise::where('pays', $row['pays'])
                ->where('actif', true)
                ->first();
            if ($existingEntreprise) {
                return $existingEntreprise;
            }
        }

        // Si aucune entreprise trouvée, retourner null (pas d'entreprise)
        return null;
    }

    /**
     * Mappe les données du CSV vers la structure de la table clients
     */
    private function mapClientData(array $row, ?Entreprise $entreprise): array
    {
        return [
            'nom' => $row['nom'] ?: 'Nom non renseigné',
            'prenom' => $row['prenom'] ?: null,
            'email' => $row['email'] ?: null,
            'telephone' => $row['telephone'] ?: null,
            'adresse' => $row['adresse'] ?: null,
            'ville' => $row['ville'] ?: null,
            'code_postal' => $row['code_postal'] ?: null,
            'pays' => $row['pays'] ?: 'France',
            'actif' => $this->parseBoolean($row['actif']),
            'notes' => $this->formatNotes($row),
            'entreprise_id' => $entreprise ? $entreprise->id : null,
            'created_at' => $this->parseDateTime($row['created_at']),
            'updated_at' => $this->parseDateTime($row['updated_at']),
        ];
    }

    /**
     * Parse une valeur booléenne
     */
    private function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));

            return in_array($value, ['true', '1', 'yes', 'oui', 'vrai']);
        }

        return (bool) $value;
    }

    /**
     * Parse une date/heure
     */
    private function parseDateTime($value): string
    {
        if (empty($value)) {
            return now()->toDateTimeString();
        }

        try {
            $date = \Carbon\Carbon::parse($value);

            return $date->toDateTimeString();
        } catch (\Exception $e) {
            return now()->toDateTimeString();
        }
    }

    /**
     * Formate les notes du client
     */
    private function formatNotes(array $row): ?string
    {
        $notes = [];

        // Ajouter les notes existantes
        if (! empty($row['notes'])) {
            $notes[] = $row['notes'];
        }

        // Ajouter des informations supplémentaires utiles
        if (! empty($row['type_client'])) {
            $notes[] = "Type client: {$row['type_client']}";
        }

        if (! empty($row['assujetti_tva'])) {
            $notes[] = 'Assujetti TVA: ' . ($this->parseBoolean($row['assujetti_tva']) ? 'Oui' : 'Non');
        }

        if (! empty($row['numero_tva'])) {
            $notes[] = "Numéro TVA: {$row['numero_tva']}";
        }

        if (! empty($row['siren_client'])) {
            $notes[] = "SIREN: {$row['siren_client']}";
        }

        if (! empty($row['siret_client'])) {
            $notes[] = "SIRET: {$row['siret_client']}";
        }

        if (! empty($row['accepte_e_facture'])) {
            $notes[] = 'Accepte e-facture: ' . ($this->parseBoolean($row['accepte_e_facture']) ? 'Oui' : 'Non');
        }

        if (! empty($row['preference_format'])) {
            $notes[] = "Format préféré: {$row['preference_format']}";
        }

        if (! empty($row['deleted_at'])) {
            $notes[] = "⚠️ CLIENT SUPPRIMÉ dans l'ancien système le: {$row['deleted_at']}";
        }

        return empty($notes) ? null : implode("\n", $notes);
    }

    /**
     * Affiche les statistiques de migration
     */
    private function displayStats(array $stats): void
    {
        $this->command->info("\n📊 Statistiques de migration:");
        $this->command->info("   ✅ Clients créés: {$stats['clients_crees']}");
        $this->command->info("   ⏭️  Clients ignorés (déjà existants): {$stats['clients_ignores']}");
        $this->command->info("   🏢 Entreprises créées: {$stats['entreprises_crees']}");

        if ($stats['erreurs'] > 0) {
            $this->command->warn("   ❌ Erreurs: {$stats['erreurs']}");
        }

        $this->command->info("\n💡 Utilisez 'php artisan db:seed --class=ClientProductionSeeder' pour relancer la migration");
    }
}
