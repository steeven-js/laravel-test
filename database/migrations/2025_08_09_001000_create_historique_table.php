<?php

declare(strict_types=1);

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
        Schema::create('historique', function (Blueprint $table) {
            $table->id();

            // Référence polymorphe pour associer à n'importe quelle entité
            $table->string('entite_type');
            $table->unsignedBigInteger('entite_id');

            // Informations sur l'action (incluant les actions Stripe)
            $table->enum('action', [
                'creation',
                'modification',
                'changement_statut',
                'envoi_email',
                'suppression',
                'archivage',
                'restauration',
                'transformation', // Pour devis -> facture
                'paiement_stripe',
                'stripe_cancel',
                'stripe_failed',
                'stripe_expire',
                'migration_numero',
            ]);

            // Détails de l'action
            $table->string('titre');
            $table->text('description')->nullable();

            // Données avant/après pour les modifications
            $table->json('donnees_avant')->nullable();
            $table->json('donnees_apres')->nullable();
            $table->json('donnees_supplementaires')->nullable();

            // Utilisateur qui a effectué l'action
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('user_nom');
            $table->string('user_email');

            // Informations techniques
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at');

            // Index
            $table->index(['entite_type', 'entite_id']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('created_at');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique');
    }
};
