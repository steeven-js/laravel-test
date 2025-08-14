-- Enable RLS on all tables in the laravel schema
-- Migration: 20250115000000_enable_rls_laravel_schema.sql

-- Enable RLS on all tables
ALTER TABLE laravel.users ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.user_roles ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.clients ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.entreprises ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.services ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.devis ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.factures ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.lignes_devis ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.lignes_factures ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.client_emails ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.email_templates ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.opportunities ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.tickets ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.todos ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.historique ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.notifications ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.madinia ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.secteurs_activite ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.numero_sequences ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.migrations ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.cache ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.cache_locks ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.failed_jobs ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.job_batches ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.jobs ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.sessions ENABLE ROW LEVEL SECURITY;
ALTER TABLE laravel.password_reset_tokens ENABLE ROW LEVEL SECURITY;

-- Comment: RLS enabled on all tables in laravel schema
-- Note: Policies will need to be created separately based on your specific requirements
