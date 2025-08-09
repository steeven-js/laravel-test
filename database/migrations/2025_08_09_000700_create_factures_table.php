<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero_facture')->unique();
            $table->foreignId('devis_id')->nullable()->constrained('devis')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('administrateur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date_facture');
            $table->date('date_echeance');
            $table->enum('statut', ['brouillon', 'en_attente', 'envoyee', 'payee', 'en_retard', 'annulee'])->default('en_attente');
            $table->enum('statut_envoi', ['non_envoyee', 'envoyee', 'echec_envoi'])
                ->default('non_envoyee')
                ->comment("Statut d'envoi de la facture au client");
            $table->string('pdf_file')->nullable();
            $table->string('pdf_url')->nullable()->comment('URL publique Supabase du PDF');
            $table->string('objet')->nullable();
            $table->text('description')->nullable();
            $table->decimal('montant_ht', 12, 2)->default(0);
            $table->decimal('taux_tva', 6, 2)->default(8.5);
            $table->decimal('montant_tva', 12, 2)->default(0);
            $table->decimal('montant_ttc', 12, 2)->default(0);
            $table->text('conditions_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->date('date_paiement')->nullable();
            $table->string('mode_paiement')->nullable();
            $table->text('reference_paiement')->nullable();
            $table->boolean('archive')->default(false);
            $table->timestamp('date_envoi_client')->nullable();
            $table->timestamp('date_envoi_admin')->nullable();

            // === CHAMPS STRIPE INTÉGRÉS ===
            $table->enum('mode_paiement_propose', ['virement', 'stripe'])
                ->default('virement')
                ->comment('Mode de paiement proposé au client');
            $table->string('stripe_payment_url', 2048)->nullable()->comment('URL de paiement Stripe');
            $table->string('stripe_session_id', 512)->nullable()->comment('ID de session Stripe');
            $table->string('stripe_payment_intent_id', 512)->nullable()->comment('ID PaymentIntent Stripe');
            $table->string('stripe_invoice_id')->nullable()->comment('ID de la facture Stripe');
            $table->string('stripe_customer_id')->nullable()->comment('ID du client Stripe');
            $table->string('stripe_receipt_url')->nullable()->comment('URL du reçu de paiement Stripe');
            $table->enum('stripe_status', ['pending', 'succeeded', 'canceled', 'expired'])->nullable()->comment('Statut du paiement Stripe');
            $table->json('stripe_metadata')->nullable()->comment('Métadonnées Stripe (frais, etc.)');
            $table->timestamp('stripe_created_at')->nullable()->comment('Date de création de la session Stripe');

            $table->timestamps();
            $table->softDeletes();

            // Index pour optimiser les recherches
            $table->index(['statut', 'date_facture']);
            $table->index(['client_id', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};


