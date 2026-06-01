<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'reservacion_id', 'monto', 'metodo_pago', 'estatus_pago', 'fecha_pago'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime', // Permite manejar la fecha con Carbon fácilmente
    ];

    // Relaciones
    public function reservacion() {
        return $this->belongsTo(Reservacion::class);
    }
}