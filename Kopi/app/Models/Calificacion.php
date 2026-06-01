<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';

    protected $fillable = [
        'viaje_id', 'evaluador_id', 'evaluado_id', 'rol_evaluador', 'puntuacion', 'comentarios'
    ];

    // Relaciones
    public function viaje() {
        return $this->belongsTo(Viaje::class);
    }

    public function evaluador() {
        // El usuario que emite la calificación
        return $this->belongsTo(Usuario::class, 'evaluador_id');
    }

    public function evaluado() {
        // El usuario que recibe la calificación
        return $this->belongsTo(Usuario::class, 'evaluado_id');
    }
}