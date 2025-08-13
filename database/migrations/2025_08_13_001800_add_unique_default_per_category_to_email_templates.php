<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Normaliser les données existantes: garder 1 par catégorie, mettre les autres à false
        $duplicates = DB::table('email_templates')
            ->select('category', DB::raw('COUNT(*) as count'))
            ->where('is_default', true)
            ->whereNull('deleted_at')
            ->groupBy('category')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            $keep = DB::table('email_templates')
                ->where('category', $dup->category)
                ->where('is_default', true)
                ->whereNull('deleted_at')
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();

            if ($keep) {
                DB::table('email_templates')
                    ->where('category', $dup->category)
                    ->where('is_default', true)
                    ->whereNull('deleted_at')
                    ->where('id', '!=', $keep->id)
                    ->update(['is_default' => false]);
            }
        }

        // 2) Contrainte niveau DB (PostgreSQL uniquement) : un seul is_default=true par category (en excluant soft deletes)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS email_templates_one_default_per_category ON email_templates (category) WHERE is_default = true AND deleted_at IS NULL');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS email_templates_one_default_per_category');
        }
    }
};
