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
        Schema::create('reservaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viaje_id')->constrained('viajes');
            $table->foreignId('pasajero_id')->constrained('usuarios');
            $table->foreignId('parada_subida_id')->constrained('paradas_viaje');
            $table->integer('asientos_solicitados')->default(1);
            $table->enum('estatus_reserva', ['solicitado', 'aceptado', 'rechazado', 'cancelado'])->default('solicitado');
            
            // [NUEVO] Reemplaza fecha_solicitud por el estándar del ORM
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservaciones');
    }
};
