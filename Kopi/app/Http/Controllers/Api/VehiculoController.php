<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Vehiculo::where('conductor_id', $request->user()->id)->latest()->paginate(15)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'placas' => ['required', 'string', 'max:10', 'unique:vehiculos,placas'],
            'marca' => ['required', 'string', 'max:50'],
            'modelo' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:30'],
            'asientos_totales' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $vehiculo = Vehiculo::create($data + ['conductor_id' => $request->user()->id]);
        $request->user()->forceFill(['es_conductor' => true])->save();

        return response()->json($vehiculo, 201);
    }

    public function update(Request $request, string $id)
    {
        $vehiculo = Vehiculo::where('conductor_id', $request->user()->id)->findOrFail($id);

        $data = $request->validate([
            'placas' => ['sometimes', 'string', 'max:10', Rule::unique('vehiculos', 'placas')->ignore($vehiculo->id)],
            'marca' => ['sometimes', 'string', 'max:50'],
            'modelo' => ['sometimes', 'string', 'max:50'],
            'color' => ['sometimes', 'string', 'max:30'],
            'asientos_totales' => ['sometimes', 'integer', 'min:1', 'max:12'],
        ]);

        $vehiculo->update($data);

        return response()->json($vehiculo);
    }

    public function destroy(Request $request, string $id)
    {
        $vehiculo = Vehiculo::where('conductor_id', $request->user()->id)->findOrFail($id);
        $vehiculo->delete();

        return response()->json(['message' => 'Vehículo eliminado correctamente.']);
    }
}
