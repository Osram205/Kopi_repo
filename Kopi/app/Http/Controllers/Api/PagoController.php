<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Reservacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PagoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'reservacion_id' => ['required', 'integer', 'exists:reservaciones,id', 'unique:pagos,reservacion_id'],
            'metodo_pago' => ['required', Rule::in(['tarjeta', 'transferencia'])],
        ]);

        $reservacion = Reservacion::with('viaje')->findOrFail($data['reservacion_id']);

        if ($reservacion->pasajero_id !== $request->user()->id) {
            return response()->json(['message' => 'Solo el pasajero puede registrar el pago.'], 403);
        }

        if ($reservacion->estatus_reserva !== 'aceptado') {
            return response()->json(['message' => 'La reservación debe estar aceptada para registrar pago.'], 422);
        }

        $pago = Pago::create([
            'reservacion_id' => $reservacion->id,
            'monto' => $reservacion->asientos_solicitados * $reservacion->viaje->costo_por_asiento,
            'metodo_pago' => $data['metodo_pago'],
            'estatus_pago' => 'pendiente',
        ]);

        return response()->json($pago, 201);
    }

    public function actualizarEstatus(Request $request, string $id)
    {
        $data = $request->validate([
            'estatus_pago' => ['required', Rule::in(['pendiente', 'completado', 'reembolsado'])],
        ]);

        $pago = Pago::with('reservacion.viaje')->findOrFail($id);

        if ($pago->reservacion->viaje->conductor_id !== $request->user()->id && $pago->reservacion->pasajero_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $pago->update($data + [
            'fecha_pago' => $data['estatus_pago'] === 'completado' ? now() : $pago->fecha_pago,
        ]);

        return response()->json($pago);
    }
}
