<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre', 'matricula', 'correo_institucional', 'contrasena', 'telefono',
        'foto_credencial', 'estatus_verificacion', 'es_conductor'
    ];

    protected $hidden = [
        'contrasena', 
    ];

    protected $casts = [
        'es_conductor' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Relaciones
    public function vehiculos() {
        return $this->hasMany(Vehiculo::class, 'conductor_id');
    }

    public function viajesComoConductor() {
        return $this->hasMany(Viaje::class, 'conductor_id');
    }

    public function reservacionesComoPasajero() {
        return $this->hasMany(Reservacion::class, 'pasajero_id');
    }

    public function apiTokens() {
        return $this->hasMany(ApiToken::class);
    }
}
