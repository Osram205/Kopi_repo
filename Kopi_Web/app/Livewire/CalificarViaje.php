<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CalificarViaje extends Component
{
    public $viaje_id;
    public $viaje;
    public $evaluado_id;
    public $nombre_evaluado = '';
    
    // Campos del formulario
    public $puntuacion = 5; // Por defecto 5 estrellas
    public $comentarios = '';

    public function mount($viaje_id, $evaluado_id)
    {
        $this->viaje_id = $viaje_id;
        $this->evaluado_id = $evaluado_id;
        
        $token = Session::get('jwt_token');
        
        // Obtenemos detalles del viaje para mostrar el contexto
        $response = Http::withToken($token)->get(config('services.fastapi.url') . '/viajes/' . $this->viaje_id);
        
        if ($response->successful()) {
            $this->viaje = $response->json();
            // Si el evaluado es el conductor, ponemos una etiqueta amigable
            if ($this->viaje['conductor_id'] == $this->evaluado_id) {
                $this->nombre_evaluado = "Conductor del Viaje #" . $this->viaje_id;
            } else {
                $this->nombre_evaluado = "Pasajero (ID: " . $this->evaluado_id . ")";
            }
        } else {
            return redirect()->route('viajes.index')->with('error', 'No se pudo cargar la información del viaje.');
        }
    }

    public function enviarCalificacion()
    {
        $this->validate([
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentarios' => 'nullable|string|max:500'
        ]);

        $token = Session::get('jwt_token');

        // Consumimos tu endpoint POST /calificaciones/
        $response = Http::withToken($token)->post(config('services.fastapi.url') . '/calificaciones/', [
            'viaje_id' => (int) $this->viaje_id,
            'evaluado_id' => (int) $this->evaluado_id,
            'puntuacion' => (int) $this->puntuacion,
            'comentarios' => $this->comentarios ?: null
        ]);

        if ($response->successful()) {
            session()->flash('success', '⭐ ¡Reputación registrada con éxito! Gracias por hacer de KOPI una comunidad segura.');
            return redirect()->route('viajes.mios');
        } else {
            $errDetail = $response->json()['detail'] ?? 'Hubo un error al enviar tu calificación.'; $error = is_array($errDetail) ? (isset($errDetail[0]['msg']) ? $errDetail[0]['msg'] : json_encode($errDetail)) : $errDetail;
            session()->flash('error', $error);
        }
    }

    public function render()
    {
        return view('livewire.calificar-viaje')->layout('layouts.app');
    }
}