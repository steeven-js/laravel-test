<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = base_path('database/seeders/data/services.csv');
        if (! file_exists($csvPath)) {
            $this->command?->warn("Fichier CSV introuvable: {$csvPath}");

            return;
        }

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command?->error('Impossible d\'ouvrir le fichier CSV.');

            return;
        }

        // Lire l'en-tête
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            $this->command?->warn('Fichier CSV vide.');

            return;
        }

        $now = now();
        $batch = [];
        $chunkSize = 300;

        while (($row = fgetcsv($handle)) !== false) {
            // Colonnes attendues: id,nom,code,description,prix_ht,qte_defaut,unite,actif,created_at,updated_at,deleted_at
            [$id, $nom, $code, $description, $prixHtStr, $qteDefautStr, $unite, $actifStr, $createdAtStr, $updatedAtStr, $deletedAtStr] = array_pad($row, 11, null);

            $createdAt = $createdAtStr ? Carbon::parse($createdAtStr) : $now;
            $updatedAt = $updatedAtStr ? Carbon::parse($updatedAtStr) : $now;
            $deletedAt = $deletedAtStr ? Carbon::parse($deletedAtStr) : null;
            $actif = filter_var($actifStr, FILTER_VALIDATE_BOOL);
            $prixHt = $prixHtStr ? (float) $prixHtStr : 0.0;
            $qteDefaut = $qteDefautStr ? (int) $qteDefautStr : 1;

            $batch[] = [
                'id' => $id ?: null,
                'nom' => $nom,
                'code' => $code,
                'description' => $description ?: null,
                'prix_ht' => $prixHt,
                'qte_defaut' => $qteDefaut,
                'unite' => $unite ?: 'heure',
                'actif' => $actif,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'deleted_at' => $deletedAt,
            ];

            if (count($batch) >= $chunkSize) {
                // upsert sur 'code' (unique) pour éviter les doublons
                Service::upsert($batch, ['code'], ['nom', 'description', 'prix_ht', 'qte_defaut', 'unite', 'actif', 'updated_at', 'deleted_at']);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            Service::upsert($batch, ['code'], ['nom', 'description', 'prix_ht', 'qte_defaut', 'unite', 'actif', 'updated_at', 'deleted_at']);
        }

        fclose($handle);
        $this->command?->info('Services importés depuis le CSV.');
    }
}
