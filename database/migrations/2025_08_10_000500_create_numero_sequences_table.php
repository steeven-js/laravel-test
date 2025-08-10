<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numero_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // devis | facture
            $table->unsignedSmallInteger('year'); // 2 digits (e.g. 25)
            $table->unsignedInteger('next_number')->default(1);
            $table->timestamps();
            $table->unique(['type', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numero_sequences');
    }
};
