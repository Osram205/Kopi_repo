<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservacion extends Model
{
    protected $table = 'reservaciones';

    protected $fillable = [
        'viaje_id', 'pasajero_id', 'parada_subida_id', 'asientos_solicitados', 'estatus_reserva'
    ];

    public function viaje() {
        return $this->belongsTo(Viaje::class);
    }

    public function pasajero() {
        return $this->belongsTo(Usuario::class, 'pasajero_id');
    }

    public function paradaSubida() {
        return $this->belongsTo(ParadaViaje::class, 'parada_subida_id');
    }

    public function pago() {
        return $this->hasOne(Pago::class);
    }
}