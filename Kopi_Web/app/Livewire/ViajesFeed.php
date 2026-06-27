<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ViajesFeed extends Component
{
    public $todosLosViajes = [];
    public $search = ''; // Propiedad reactiva para el buscador

    public function mount()
    {
        $this->cargarViajes();
    }

    // Separado en un método para poder recargar la lista después de reservar
    public function cargarViajes()
    {
        $token = Session::get('jwt_token');
        $response = Http::withToken($token)->get(env('FASTAPI_URL') . '/viajes');
        
        if ($response->successful()) {
            $this->todosLosViajes = $response->json();
        } else {
            $this->todosLosViajes = [];
        }
    }

    // LA LÓGICA DE LA RESERVACIÓN
    public function solicitarAsiento($viajeId)
    {
        $token = Session::get('jwt_token');
        
        // Buscamos el viaje seleccionado en nuestra lista para extraer su primera parada
        $viaje = collect($this->todosLosViajes)->firstWhere('id', $viajeId);
        // Si la API devuelve paradas, tomamos el ID de la primera. Si no, enviamos 1 por defecto.
        $paradaId = $viaje['paradas'][0]['id'] ?? 1; 

        // Armamos el payload exacto que probaste en Swagger
        $payload = [
            'viaje_id' => $viajeId,
            'parada_subida_id' => $paradaId,
            'asientos_solicitados' => 1,
            'estatus_reserva' => 'solicitado'
        ];

        // Disparamos la petición a FastAPI
        $response = Http::withToken($token)->post(env('FASTAPI_URL') . '/reservaciones', $payload);

        if ($response->successful()) {
            session()->flash('success', '¡Asiento solicitado con éxito! Notificación enviada al conductor.');
            $this->cargarViajes(); // Recargamos para actualizar el contador de asientos disponibles
        } else {
            // Si FastAPI lanza un HTTPException, lo atrapamos y mostramos el detalle
            $error = $response->json()['detail'] ?? 'Hubo un error al procesar tu solicitud.';
            session()->flash('error', $error);
        }
    }

    public function render()
    {
        // LA LÓGICA DEL BUSCADOR EN TIEMPO REAL
        $viajesFiltrados = collect($this->todosLosViajes)->filter(function ($viaje) {
            // Si el buscador está vacío, mostramos todos
            if (empty($this->search)) return true;
            
            $busqueda = strtolower($this->search);
            // Filtramos revisando si el texto coincide con el Origen o el Destino
            return str_contains(strtolower($viaje['origen']), $busqueda) || 
                   str_contains(strtolower($viaje['destino']), $busqueda);
        })->all();

        return view('livewire.viajes-feed', [
            'viajes' => $viajesFiltrados
        ])->layout('layouts.app');
    }
}