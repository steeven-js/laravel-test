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
        Schema::create('lignes_factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->constrained('factures')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->cascadeOnDelete();
            $table->integer('quantite')->default(1);
            $table->enum('unite', ['heure', 'jour', 'semaine', 'mois', 'unite', 'forfait', 'licence'])
                ->default('heure')
                ->comment('Unité de mesure pour la quantité');
            $table->decimal('prix_unitaire_ht', 12, 2)->comment('Prix unitaire HT au moment de la facture');
            $table->decimal('remise_pourcentage', 5, 2)->default(0)->comment('Remise appliquée en pourcentage (0-100)');
            $table->decimal('taux_tva', 6, 2)->default(8.5)->comment('Taux TVA applicable');
            $table->decimal('montant_ht', 12, 2)->comment('Montant total HT de la ligne (quantite * prix_unitaire_ht)');
            $table->decimal('montant_tva', 12, 2)->comment('Montant TVA de la ligne');
            $table->decimal('montant_ttc', 12, 2)->comment('Montant total TTC de la ligne');
            $table->integer('ordre')->default(1)->comment("Ordre d'affichage de la ligne");
            $table->text('description_personnalisee')->nullable()->comment('Description spécifique pour cette ligne');
            $table->timestamps();

            // Index pour optimiser les performances
            $table->index(['facture_id', 'ordre']);
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lignes_factures');
    }
};


