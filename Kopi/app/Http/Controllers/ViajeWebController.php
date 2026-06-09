<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ViajeWebController extends Controller
{
    /**
     * Muestra la página principal con la lista de viajes disponibles.
     */
    public function index()
    {
        try {
            // Hacemos una petición GET a tu API en FastAPI (asegúrate de que esté corriendo en el puerto 8000)
            $response = Http::timeout(5)->get('http://127.0.0.1:8000/viajes');
            
            // Convertimos la respuesta JSON de Python a un arreglo de PHP
            $viajes = $response->successful() ? $response->json() : [];
            
        } catch (\Exception $e) {
            // Si FastAPI está apagado, evitamos que la pantalla de Laravel explote
            $viajes = [];
            session()->flash('error', 'No se pudo conectar con el servidor de rutas.');
        }

        // Inyectamos los viajes a tu vista blade existente
        return view('welcome', compact('viajes'));
    }

    /**
     * Muestra la vista para publicar un nuevo viaje (Conductor).
     */
    public function create()
    {
        return view('driver');
    }

    /**
     * Recibe los datos del formulario de Laravel y los envía a FastAPI.
     */
    public function store(Request $request)
    {
        // Nota: Más adelante aquí agregaremos el Token del usuario para que FastAPI sepa quién es.
        $response = Http::post('http://127.0.0.1:8000/viajes', [
            'origen' => $request->origen,
            'destino' => $request->destino,
            'fecha_salida' => $request->fecha_salida,
            'hora_salida' => $request->hora_salida,
            'asientos_disponibles' => $request->asientos_disponibles,
            'costo_por_asiento' => $request->costo_por_asiento,
            // 'vehiculo_id' => $request->vehiculo_id
        ]);

        if ($response->successful()) {
            return redirect('/')->with('success', 'Viaje publicado correctamente para la universidad.');
        }

        return back()->with('error', 'Error al publicar el viaje: ' . $response->json('detail'));
    }
}