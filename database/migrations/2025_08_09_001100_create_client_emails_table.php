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
        Schema::create('client_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('objet');
            $table->text('contenu');
            $table->text('cc')->nullable();
            $table->json('attachments')->nullable()->comment('Informations des pièces jointes en JSON');
            $table->enum('statut', ['envoye', 'echec'])->default('envoye');
            $table->timestamp('date_envoi');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'user_id']);
            $table->index('date_envoi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_emails');
    }
};
