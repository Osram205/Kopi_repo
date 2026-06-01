<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParadaViaje extends Model
{
    protected $table = 'paradas_viaje';

    protected $fillable = [
        'viaje_id', 'nombre_parada', 'coordenadas', 'orden'
    ];

    public function viaje() {
        return $this->belongsTo(Viaje::class);
    }
}
