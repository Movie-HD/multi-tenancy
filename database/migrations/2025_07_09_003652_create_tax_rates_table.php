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
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Ej: IGV, IVA Reducido
            $table->decimal('percentage', 5, 2); // Ej: 18.00, 21.00
            $table->boolean('is_default')->default(false); // Para autoseleccionar por defecto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
