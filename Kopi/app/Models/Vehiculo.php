<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehiculo extends Model
{
    use SoftDeletes;

    protected $table = 'vehiculos';

    protected $fillable = [
        'conductor_id', 'placas', 'marca', 'modelo', 'color', 'asientos_totales'
    ];

    // Relaciones
    public function conductor() {
        return $this->belongsTo(Usuario::class, 'conductor_id');
    }

    public function viajes() {
        return $this->hasMany(Viaje::class);
    }
}