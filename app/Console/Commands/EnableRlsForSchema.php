<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnableRlsForSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Options:
     *  --schema=laravel             Schéma cible (par défaut: laravel)
     *  --force                      Utiliser FORCE ROW LEVEL SECURITY
     *  --dry-run                    Afficher les commandes sans exécuter
     *  --exclude=*                  Tables à exclure (option répétable)
     *  --include-notifications      Inclure notifications/test_notifications (exclues par défaut)
     */
    protected $signature = 'supabase:enable-rls
		{--schema=laravel : Schéma cible}
		{--force : Utiliser FORCE ROW LEVEL SECURITY}
		{--dry-run : Afficher les commandes sans exécuter}
		{--exclude=* : Tables à exclure (option répétable)}
		{--include-notifications : Inclure notifications et test_notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Active le Row Level Security (RLS) sur toutes les tables d'un schéma PostgreSQL (Supabase).";

    public function handle(): int
    {
        $schema = (string) $this->option('schema');
        $dryRun = (bool) $this->option('dry-run');
        $useForce = (bool) $this->option('force');
        $excludes = (array) ($this->option('exclude') ?? []);
        $includeNotifications = (bool) $this->option('include-notifications');

        if (! $includeNotifications) {
            // Par sécurité, on exclut les tables de notifications par défaut (convention du projet)
            $excludes = array_values(array_unique(array_merge($excludes, ['notifications', 'test_notifications'])));
        }

        $this->info("Schéma: {$schema}");
        $this->info('Exclusions: ' . (empty($excludes) ? '(aucune)' : implode(', ', $excludes)));
        if ($useForce) {
            $this->info('Mode: FORCE ROW LEVEL SECURITY');
        }
        if ($dryRun) {
            $this->warn('Mode: DRY-RUN (aucune modification appliquée)');
        }

        $tables = $this->getSchemaTables($schema);
        if (empty($tables)) {
            $this->warn("Aucune table trouvée dans le schéma '{$schema}'.");

            return 0;
        }

        $targetTables = array_values(array_filter($tables, function (string $table) use ($excludes): bool {
            return ! in_array($table, $excludes, true);
        }));

        if (empty($targetTables)) {
            $this->warn('Toutes les tables ont été exclues, rien à faire.');

            return 0;
        }

        $enabled = 0;
        $errors = 0;

        foreach ($targetTables as $table) {
            $sql = $this->buildAlterStatement($schema, $table, $useForce);
            if ($dryRun) {
                $this->line($sql);
                $enabled++;

                continue;
            }

            try {
                DB::statement($sql);
                $this->info("✅ RLS activé pour {$schema}.{$table}");
                $enabled++;
            } catch (\Throwable $e) {
                $this->error("❌ Échec RLS pour {$schema}.{$table} : " . $e->getMessage());
                $errors++;
            }
        }

        $this->newLine();
        $this->info('Tables traitées: ' . count($targetTables));
        $this->info("Succès: {$enabled}");
        if ($errors > 0) {
            $this->warn("Échecs: {$errors}");
        }

        return $errors === 0 ? 0 : 1;
    }

    /**
     * @return array<int, string>
     */
    private function getSchemaTables(string $schema): array
    {
        $query = <<<'SQL'
			select table_name
			from information_schema.tables
			where table_schema = :schema
			and table_type = 'BASE TABLE'
			order by table_name
		SQL;

        $rows = DB::select($query, ['schema' => $schema]);

        return array_map(static function ($row): string {
            return (string) $row->table_name;
        }, $rows);
    }

    private function buildAlterStatement(string $schema, string $table, bool $useForce): string
    {
        $quotedSchema = '"' . str_replace('"', '""', $schema) . '"';
        $quotedTable = '"' . str_replace('"', '""', $table) . '"';
        $force = $useForce ? ' FORCE' : '';

        return sprintf('ALTER TABLE %s.%s ENABLE%s ROW LEVEL SECURITY;', $quotedSchema, $quotedTable, $force);
    }
}
