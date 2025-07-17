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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained()->cascadeOnDelete();
            $table->string('code'); // Ej: PEN, USD, EUR
            $table->string('symbol'); // Ej: S/, $, €
            $table->boolean('is_default')->default(false); // Para definir moneda por defecto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
