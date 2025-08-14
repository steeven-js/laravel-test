<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestRlsAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rls:test {table? : Table name to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test RLS access on tables in laravel schema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->argument('table');

        if ($table) {
            $this->testTableAccess($table);
        } else {
            $this->testAllTables();
        }
    }

    private function testAllTables()
    {
        $this->info('Testing RLS access on all tables in laravel schema...');

        $tables = [
            'users', 'clients', 'devis', 'factures', 'services',
            'notifications', 'entreprises', 'opportunities', 'tickets', 'todos',
        ];

        foreach ($tables as $table) {
            $this->testTableAccess($table);
        }
    }

    private function testTableAccess($table)
    {
        $this->info("\nTesting table: {$table}");

        try {
            // Test SELECT access
            $count = DB::table("laravel.{$table}")->count();
            $this->info("  ✅ SELECT: {$count} rows accessible");

            // Test if table has RLS enabled
            $rlsEnabled = DB::select("
                SELECT rowsecurity 
                FROM pg_tables 
                WHERE schemaname = 'laravel' 
                AND tablename = ?
            ", [$table]);

            if (! empty($rlsEnabled)) {
                $this->info('  🔒 RLS: ' . ($rlsEnabled[0]->rowsecurity ? 'ENABLED' : 'DISABLED'));
            }

        } catch (\Exception $e) {
            $this->error("  ❌ Error accessing {$table}: " . $e->getMessage());
        }
    }
}
