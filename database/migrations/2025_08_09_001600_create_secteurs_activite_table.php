<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('secteurs_activite', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('Code NAF (ex: 62.01Z)');
            $table->string('libelle')->comment("Libellé du secteur d'activité");
            $table->string('division', 2)->nullable()->comment('Division NAF (2 premiers chiffres)');
            $table->string('section', 1)->nullable()->comment('Section NAF (lettre)');
            $table->boolean('actif')->default(true)->comment('Secteur actif');
            $table->timestamps();

            $table->index(['actif']);
            $table->index(['division']);
            $table->index(['section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secteurs_activite');
    }
};


