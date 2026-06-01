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
        Schema::create('viajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conductor_id')->constrained('usuarios');
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->string('origen', 255);
            $table->string('destino', 255);
            $table->date('fecha_salida');
            $table->time('hora_salida');
            $table->integer('asientos_disponibles');
            $table->decimal('costo_por_asiento', 8, 2);
            $table->enum('estatus', ['programado', 'en_curso', 'completado', 'cancelado'])->default('programado');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viajes');
    }
};
