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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservacion_id')->constrained('reservaciones');
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['tarjeta', 'transferencia']);
            $table->enum('estatus_pago', ['pendiente', 'completado', 'reembolsado'])->default('pendiente');
            $table->timestamp('fecha_pago')->nullable(); // Se conserva para marcar el momento exacto del cobro/depósito
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
