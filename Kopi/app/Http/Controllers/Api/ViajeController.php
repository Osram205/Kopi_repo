<?php

namespace App\Http\Controllers\Api;

use App\Models\ParadaViaje;
use App\Models\Vehiculo;
use App\Models\Viaje;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ViajeController extends Controller
{
    public function index(Request $request)
    {
        $query = Viaje::with(['conductor:id,nombre,correo_institucional,telefono', 'vehiculo', 'paradas'])
            ->where('estatus', 'programado')
            ->where('asientos_disponibles', '>', 0);

        foreach (['origen', 'destino'] as $field) {
            $query->when($request->filled($field), fn ($q) => $q->where($field, 'like', '%'.$request->string($field).'%'));
        }

        $query->when($request->filled('fecha_salida'), fn ($q) => $q->whereDate('fecha_salida', $request->date('fecha_salida')));

        return response()->json($query->orderBy('fecha_salida')->orderBy('hora_salida')->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehiculo_id' => ['required', 'integer', 'exists:vehiculos,id'],
            'origen' => ['required', 'string', 'max:255'],
            'destino' => ['required', 'string', 'max:255', 'different:origen'],
            'fecha_salida' => ['required', 'date', 'after_or_equal:today'],
            'hora_salida' => ['required', 'date_format:H:i'],
            'asientos_disponibles' => ['required', 'integer', 'min:1'],
            'costo_por_asiento' => ['required', 'numeric', 'min:0'],
            'paradas' => ['sometimes', 'array'],
            'paradas.*.nombre_parada' => ['required_with:paradas', 'string', 'max:150'],
            'paradas.*.coordenadas' => ['required_with:paradas', 'string', 'max:100'],
            'paradas.*.orden' => ['required_with:paradas', 'integer', 'min:1'],
        ]);

        $usuario = $request->user();
        $vehiculo = Vehiculo::where('id', $data['vehiculo_id'])->where('conductor_id', $usuario->id)->first();

        if (! $vehiculo) {
            return response()->json(['message' => 'El vehículo no pertenece al conductor autenticado.'], 403);
        }

        if ($data['asientos_disponibles'] > $vehiculo->asientos_totales) {
            return response()->json(['message' => 'Los asientos disponibles exceden los asientos del vehículo.'], 422);
        }

        $viaje = DB::transaction(function () use ($data, $usuario) {
            $paradas = $data['paradas'] ?? [];
            unset($data['paradas']);

            $viaje = Viaje::create($data + ['conductor_id' => $usuario->id]);

            foreach ($paradas as $parada) {
                $viaje->paradas()->create($parada);
            }

            return $viaje->load(['vehiculo', 'paradas']);
        });

        $usuario->forceFill(['es_conductor' => true])->save();

        return response()->json($viaje, 201);
    }

    public function show(string $id)
    {
        return response()->json(Viaje::with(['conductor:id,nombre,correo_institucional,telefono', 'vehiculo', 'paradas', 'reservaciones'])->findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $viaje = Viaje::where('conductor_id', $request->user()->id)->findOrFail($id);

        $data = $request->validate([
            'origen' => ['sometimes', 'string', 'max:255'],
            'destino' => ['sometimes', 'string', 'max:255'],
            'fecha_salida' => ['sometimes', 'date', 'after_or_equal:today'],
            'hora_salida' => ['sometimes', 'date_format:H:i'],
            'asientos_disponibles' => ['sometimes', 'integer', 'min:0'],
            'costo_por_asiento' => ['sometimes', 'numeric', 'min:0'],
            'estatus' => ['sometimes', Rule::in(['programado', 'en_curso', 'completado', 'cancelado'])],
        ]);

        $viaje->update($data);

        return response()->json($viaje->load(['vehiculo', 'paradas']));
    }

    public function destroy(string $id)
    {
        $viaje = Viaje::where('conductor_id', request()->user()->id)->findOrFail($id);
        $viaje->update(['estatus' => 'cancelado']);
        $viaje->delete();

        return response()->json(['message' => 'Viaje cancelado correctamente.']);
    }
}
