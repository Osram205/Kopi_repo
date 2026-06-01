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
        // Migración: create_usuarios_table
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('matricula', 20)->unique();
            $table->string('correo_institucional', 100)->unique();
            $table->string('contrasena');
            $table->string('telefono', 15);
            $table->string('foto_credencial')->nullable();
            $table->enum('estatus_verificacion', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            
            // [NUEVO] Rol explícito para renderizado condicional rápido
            $table->boolean('es_conductor')->default(false); 
            
            // [NUEVO] Timestamps y Soft Deletes
            $table->timestamps(); 
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
