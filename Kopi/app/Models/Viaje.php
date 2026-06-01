<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Viaje extends Model
{
    use SoftDeletes;

    protected $table = 'viajes';

    protected $fillable = [
        'conductor_id', 'vehiculo_id', 'origen', 'destino', 
        'fecha_salida', 'hora_salida', 'asientos_disponibles', 
        'costo_por_asiento', 'estatus'
    ];

    protected $casts = [
        'fecha_salida' => 'date',
        // Esto ayuda a manejar correctamente las horas y decimales en las respuestas JSON
        'hora_salida' => 'datetime:H:i', 
        'costo_por_asiento' => 'decimal:2',
    ];

    // Relaciones
    public function conductor() {
        return $this->belongsTo(Usuario::class, 'conductor_id');
    }

    public function vehiculo() {
        return $this->belongsTo(Vehiculo::class);
    }

    public function paradas() {
        return $this->hasMany(ParadaViaje::class)->orderBy('orden', 'asc');
    }

    public function reservaciones() {
        return $this->hasMany(Reservacion::class);
    }
}