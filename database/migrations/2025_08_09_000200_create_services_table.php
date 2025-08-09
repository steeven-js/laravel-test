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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique()->nullable()->comment('Code unique du service');
            $table->text('description')->nullable();
            $table->decimal('prix_ht', 10, 2)->nullable()->comment('Prix unitaire hors taxes');
            $table->integer('qte_defaut')->default(1)->nullable()->comment('Quantité par défaut');
            $table->enum('unite', [
                'heure',
                'journee',
                'semaine',
                'mois',
                'unite',
                'forfait',
                'licence',
            ])->default('heure')->comment('Unité de mesure du service');
            $table->boolean('actif')->default(true)->nullable()->comment('Service disponible');
            $table->timestamps();
            $table->softDeletes();

            // Index pour optimiser les recherches
            $table->index(['actif', 'nom']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
