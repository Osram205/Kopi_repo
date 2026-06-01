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
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viaje_id')->constrained('viajes');
            $table->foreignId('evaluador_id')->constrained('usuarios');
            $table->foreignId('evaluado_id')->constrained('usuarios');
            $table->enum('rol_evaluador', ['conductor', 'pasajero']);
            $table->integer('puntuacion');
            $table->text('comentarios')->nullable();
            
            // [NUEVO] Reemplaza fecha_calificacion
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
