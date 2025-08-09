<?php

namespace Database\Seeders;

use App\Models\SecteurActivite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SecteurActiviteSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = base_path('secteurs_activite_rows.csv');
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
            // Colonnes attendues: id,code,libelle,division,section,actif,created_at,updated_at
            [$id, $code, $libelle, $division, $section, $actifStr, $createdAtStr, $updatedAtStr] = array_pad($row, 8, null);

            $createdAt = $createdAtStr ? Carbon::parse($createdAtStr) : $now;
            $updatedAt = $updatedAtStr ? Carbon::parse($updatedAtStr) : $now;
            $actif = filter_var($actifStr, FILTER_VALIDATE_BOOL);

            $batch[] = [
                'id' => $id ?: null,
                'code' => $code,
                'libelle' => $libelle,
                'division' => $division ?: null,
                'section' => $section ?: null,
                'actif' => $actif,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];

            if (count($batch) >= $chunkSize) {
                // upsert sur 'code' (unique) pour éviter les doublons
                SecteurActivite::upsert($batch, ['code'], ['libelle', 'division', 'section', 'actif', 'updated_at']);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            SecteurActivite::upsert($batch, ['code'], ['libelle', 'division', 'section', 'actif', 'updated_at']);
        }

        fclose($handle);
        $this->command?->info('Secteurs d\'activité importés depuis le CSV.');
    }
}


