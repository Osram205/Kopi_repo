<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Reservacion;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalificacionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'viaje_id' => ['required', 'integer', 'exists:viajes,id'],
            'evaluado_id' => ['required', 'integer', 'exists:usuarios,id'],
            'puntuacion' => ['required', 'integer', 'min:1', 'max:5'],
            'comentarios' => ['nullable', 'string'],
        ]);

        $usuario = $request->user();
        $viaje = Viaje::findOrFail($data['viaje_id']);
        $esConductor = $viaje->conductor_id === $usuario->id;
        $esPasajero = Reservacion::where('viaje_id', $viaje->id)
            ->where('pasajero_id', $usuario->id)
            ->where('estatus_reserva', 'aceptado')
            ->exists();

        if (! $esConductor && ! $esPasajero) {
            return response()->json(['message' => 'Solo participantes del viaje pueden calificar.'], 403);
        }

        if ($data['evaluado_id'] === $usuario->id) {
            return response()->json(['message' => 'No puedes calificarte a ti mismo.'], 422);
        }

        $calificacion = Calificacion::create($data + [
            'evaluador_id' => $usuario->id,
            'rol_evaluador' => $esConductor ? 'conductor' : 'pasajero',
        ]);

        return response()->json($calificacion, 201);
    }
}
