<?php

namespace App\Http\Controllers\Api;

use App\Models\Reservacion;
use App\Models\Viaje;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReservacionController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $reservaciones = Reservacion::with(['viaje.conductor:id,nombre,correo_institucional', 'viaje.vehiculo', 'paradaSubida'])
            ->where('pasajero_id', $usuario->id)
            ->orWhereHas('viaje', fn ($q) => $q->where('conductor_id', $usuario->id))
            ->latest()
            ->paginate(15);

        return response()->json($reservaciones);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'viaje_id' => ['required', 'integer', 'exists:viajes,id'],
            'parada_subida_id' => ['required', 'integer', 'exists:paradas_viaje,id'],
            'asientos_solicitados' => ['required', 'integer', 'min:1'],
        ]);

        $usuario = $request->user();

        $reservacion = DB::transaction(function () use ($data, $usuario) {
            $viaje = Viaje::where('estatus', 'programado')->lockForUpdate()->findOrFail($data['viaje_id']);

            if ($viaje->conductor_id === $usuario->id) {
                abort(response()->json(['message' => 'No puedes reservar tu propio viaje.'], 422));
            }

            if ($data['asientos_solicitados'] > $viaje->asientos_disponibles) {
                abort(response()->json(['message' => 'No hay suficientes asientos disponibles.'], 422));
            }

            if (! $viaje->paradas()->where('id', $data['parada_subida_id'])->exists()) {
                abort(response()->json(['message' => 'La parada no pertenece al viaje seleccionado.'], 422));
            }

            $existente = Reservacion::where('viaje_id', $viaje->id)
                ->where('pasajero_id', $usuario->id)
                ->whereIn('estatus_reserva', ['solicitado', 'aceptado'])
                ->exists();

            if ($existente) {
                abort(response()->json(['message' => 'Ya tienes una reservación activa para este viaje.'], 422));
            }

            return Reservacion::create($data + ['pasajero_id' => $usuario->id])
                ->load(['viaje', 'paradaSubida']);
        });

        return response()->json($reservacion, 201);
    }

    public function show(string $id)
    {
        $reservacion = Reservacion::with(['viaje', 'pasajero', 'paradaSubida'])->findOrFail($id);
        $usuario = request()->user();

        if ($reservacion->pasajero_id !== $usuario->id && $reservacion->viaje->conductor_id !== $usuario->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        return response()->json($reservacion);
    }

    public function update(Request $request, string $id)
    {
        return $this->actualizarEstatus($request, $id);
    }

    public function destroy(string $id)
    {
        $reservacion = Reservacion::where('pasajero_id', request()->user()->id)->findOrFail($id);
        $reservacion->update(['estatus_reserva' => 'cancelado']);

        return response()->json(['message' => 'Reservación cancelada correctamente.']);
    }

    public function actualizarEstatus(Request $request, string $id)
    {
        $data = $request->validate([
            'estatus_reserva' => ['required', Rule::in(['aceptado', 'rechazado', 'cancelado'])],
        ]);

        $reservacion = DB::transaction(function () use ($data, $id, $request) {
            $reservacion = Reservacion::with('viaje')->lockForUpdate()->findOrFail($id);
            $viaje = Viaje::lockForUpdate()->findOrFail($reservacion->viaje_id);

            if ($viaje->conductor_id !== $request->user()->id && $reservacion->pasajero_id !== $request->user()->id) {
                abort(response()->json(['message' => 'No autorizado.'], 403));
            }

            if ($data['estatus_reserva'] !== 'cancelado' && $viaje->conductor_id !== $request->user()->id) {
                abort(response()->json(['message' => 'Solo el conductor puede aceptar o rechazar reservaciones.'], 403));
            }

            if ($data['estatus_reserva'] === 'aceptado' && $reservacion->estatus_reserva !== 'aceptado') {
                if ($reservacion->asientos_solicitados > $viaje->asientos_disponibles) {
                    abort(response()->json(['message' => 'No hay suficientes asientos disponibles.'], 422));
                }
                $viaje->decrement('asientos_disponibles', $reservacion->asientos_solicitados);
            }

            if ($data['estatus_reserva'] === 'cancelado' && $reservacion->estatus_reserva === 'aceptado') {
                $viaje->increment('asientos_disponibles', $reservacion->asientos_solicitados);
            }

            $reservacion->update($data);

            return $reservacion->load(['viaje', 'pasajero', 'paradaSubida']);
        });

        return response()->json($reservacion);
    }
}
