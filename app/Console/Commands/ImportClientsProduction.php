<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\ClientProductionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportClientsProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:import-production 
                            {--csv=clients_rows.csv : Chemin vers le fichier CSV}
                            {--force : Forcer l\'import même si des clients existent}
                            {--dry-run : Afficher ce qui serait importé sans créer}
                            {--validate-only : Valider uniquement le fichier CSV}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importer les clients de production depuis un fichier CSV';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Import des clients de production');
        $this->newLine();

        // Vérifier le fichier CSV
        $csvPath = $this->option('csv');
        $fullPath = base_path($csvPath);

        if (! File::exists($fullPath)) {
            $this->error("❌ Fichier CSV non trouvé: {$csvPath}");
            $this->error('   Placez le fichier dans le répertoire racine du projet');

            return 1;
        }

        $this->info("📁 Fichier CSV trouvé: {$csvPath}");
        $this->info('📊 Taille: ' . $this->formatBytes(File::size($fullPath)));

        // Validation du fichier CSV
        if ($this->option('validate-only')) {
            return $this->validateCsvFile($fullPath);
        }

        // Mode dry-run
        if ($this->option('dry-run')) {
            return $this->dryRunImport($fullPath);
        }

        // Import réel
        return $this->performImport($fullPath);
    }

    /**
     * Valide le fichier CSV
     */
    private function validateCsvFile(string $path): int
    {
        $this->info('🔍 Validation du fichier CSV...');

        $data = $this->readCsvFile($path);
        if (empty($data)) {
            $this->error('❌ Fichier CSV vide ou invalide');

            return 1;
        }

        $this->info('✅ Fichier CSV valide');
        $this->info("   📊 Lignes de données: {$data['count']}");
        $this->info('   📋 Colonnes: ' . count($data['headers']));

        // Afficher les premières lignes
        $this->newLine();
        $this->info('📋 Aperçu des données:');
        $this->table(
            $data['headers'],
            array_slice($data['data'], 0, 5)
        );

        return 0;
    }

    /**
     * Mode dry-run pour voir ce qui serait importé
     */
    private function dryRunImport(string $path): int
    {
        $this->info('🔍 Mode dry-run - Aucune donnée ne sera créée');

        $data = $this->readCsvFile($path);
        if (empty($data)) {
            $this->error('❌ Fichier CSV vide ou invalide');

            return 1;
        }

        $this->info("📊 {$data['count']} clients seraient importés");

        // Analyser les données
        $stats = $this->analyzeCsvData($data['data']);

        $this->newLine();
        $this->info('📊 Analyse des données:');
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Clients avec email', $stats['avec_email']],
                ['Clients avec téléphone', $stats['avec_telephone']],
                ['Clients avec adresse', $stats['avec_adresse']],
                ['Clients supprimés (deleted_at)', $stats['supprimes']],
                ['Pays représentés', $stats['pays_count']],
                ['Villes représentées', $stats['villes_count']],
            ]
        );

        return 0;
    }

    /**
     * Effectue l'import réel
     */
    private function performImport(string $path): int
    {
        $this->info('🚀 Début de l\'import des clients...');

        // Vérifier la confirmation si pas en mode force
        if (! $this->option('force')) {
            if (! $this->confirm('⚠️  Êtes-vous sûr de vouloir importer les clients ?')) {
                $this->info('❌ Import annulé');

                return 0;
            }
        }

        try {
            // Créer une instance du seeder et l'exécuter
            $seeder = new ClientProductionSeeder;
            $seeder->setCommand($this);
            $seeder->run();

            $this->newLine();
            $this->info('✅ Import terminé avec succès !');

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de l'import: " . $e->getMessage());

            return 1;
        }
    }

    /**
     * Lit le fichier CSV et retourne les données
     */
    private function readCsvFile(string $path): array
    {
        $data = [];
        $headers = [];
        $count = 0;

        if (($handle = fopen($path, 'r')) !== false) {
            // Lire l'en-tête
            $headers = fgetcsv($handle);

            if ($headers === false) {
                fclose($handle);

                return [];
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

        return [
            'data' => $data,
            'headers' => $headers,
            'count' => $count,
        ];
    }

    /**
     * Analyse les données CSV pour les statistiques
     */
    private function analyzeCsvData(array $data): array
    {
        $stats = [
            'avec_email' => 0,
            'avec_telephone' => 0,
            'avec_adresse' => 0,
            'supprimes' => 0,
            'pays' => [],
            'villes' => [],
        ];

        foreach ($data as $row) {
            if (! empty($row['email'])) {
                $stats['avec_email']++;
            }
            if (! empty($row['telephone'])) {
                $stats['avec_telephone']++;
            }
            if (! empty($row['adresse'])) {
                $stats['avec_adresse']++;
            }
            if (! empty($row['deleted_at'])) {
                $stats['supprimes']++;
            }

            if (! empty($row['pays'])) {
                $stats['pays'][$row['pays']] = true;
            }
            if (! empty($row['ville'])) {
                $stats['villes'][$row['ville']] = true;
            }
        }

        $stats['pays_count'] = count($stats['pays']);
        $stats['villes_count'] = count($stats['villes']);

        return $stats;
    }

    /**
     * Formate les bytes en format lisible
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
