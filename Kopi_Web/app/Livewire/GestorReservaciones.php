<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class GestorReservaciones extends Component
{
    public $reservaciones = [];

    public function mount()
    {
        if (!Session::get('es_conductor') && Session::get('estatus_verificacion') !== 'aprobado') {
            return redirect()->route('viajes.index')->with('error', 'Acceso denegado. No tienes permisos de conductor autorizado.');
        }

        $this->cargarReservaciones();
    }

    public function cargarReservaciones()
    {
        $token = Session::get('jwt_token');
        $response = Http::withToken($token)->get(config('services.fastapi.url') . '/reservaciones?rol=conductor');

        if ($response->successful()) {
            $this->reservaciones = $response->json();
        } else {
            $this->reservaciones = [];
        }
    }

    // Método para consumir el PUT /{id}/estatus
    public function responderSolicitud($reservacionId, $nuevoEstatus)
    {
        $token = Session::get('jwt_token');
        
        $response = Http::withToken($token)->put(config('services.fastapi.url') . '/reservaciones/' . $reservacionId . '/estatus', [
            'estatus_reserva' => $nuevoEstatus // Puede ser 'aceptado' o 'rechazado'
        ]);

        if ($response->successful()) {
            session()->flash('success', 'La solicitud de viaje ha sido ' . $nuevoEstatus . ' correctamente.');
            $this->cargarReservaciones(); // Recargamos la lista automáticamente
        } else {
            $errDetail = $response->json()['detail'] ?? 'Hubo un problema al procesar la solicitud.'; $error = is_array($errDetail) ? (isset($errDetail[0]['msg']) ? $errDetail[0]['msg'] : json_encode($errDetail)) : $errDetail;
            session()->flash('error', $error);
        }
    }

    public function render()
    {
        return view('livewire.gestor-reservaciones')->layout('layouts.app');
    }
}