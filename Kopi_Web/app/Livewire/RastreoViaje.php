<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class RastreoViaje extends Component
{
    public $viaje_id;
    public $viaje;
    public $es_conductor = false;
    public $token;

    public function mount($id)
    {
        $this->viaje_id = $id;
        $this->token = Session::get('jwt_token');
        
        return $this->cargarDatosViaje();
    }

    // Método reutilizable para refrescar el estatus del viaje
    public function cargarDatosViaje()
    {
        $response = Http::withToken($this->token)->get(config('services.fastapi.url') . '/viajes/' . $this->viaje_id);
        $perfilResponse = Http::withToken($this->token)->get(config('services.fastapi.url') . '/usuarios/perfil');

        if ($response->successful() && $perfilResponse->successful()) {
            $this->viaje = $response->json();
            $perfil = $perfilResponse->json();
            $this->es_conductor = ($perfil['id'] == $this->viaje['conductor_id']);
        } else {
            return redirect()->route('viajes.index')->with('error', 'Viaje no encontrado o acceso denegado.');
        }
    }

    // Cambia el estatus a 'en_curso'
    public function iniciarViaje()
    {
        $response = Http::withToken($this->token)->put(config('services.fastapi.url') . '/viajes/' . $this->viaje_id, [
            'estatus' => 'en_curso'
        ]);

        if ($response->successful()) {
            session()->flash('success', '🚀 ¡Viaje iniciado con éxito! El GPS está transmitiendo.');
            $this->cargarDatosViaje();
        } else {
            $errDetail = $response->json()['detail'] ?? 'No se pudo iniciar el viaje.'; $error = is_array($errDetail) ? (isset($errDetail[0]['msg']) ? $errDetail[0]['msg'] : json_encode($errDetail)) : $errDetail;
            session()->flash('error', $error);
        }
    }

    // Cambia el estatus a 'completado'
    public function finalizarViaje()
    {
        $response = Http::withToken($this->token)->put(config('services.fastapi.url') . '/viajes/' . $this->viaje_id, [
            'estatus' => 'completado'
        ]);

        if ($response->successful()) {
            session()->flash('success', '🏁 ¡Viaje finalizado correctamente! Gracias por apoyar la movilidad UPQ.');
            // Próximo paso: Redirigir a la pantalla de calificaciones
            return redirect()->route('viajes.mios');
        } else {
            $errDetail = $response->json()['detail'] ?? 'No se pudo finalizar el viaje.'; $error = is_array($errDetail) ? (isset($errDetail[0]['msg']) ? $errDetail[0]['msg'] : json_encode($errDetail)) : $errDetail;
            session()->flash('error', $error);
        }
    }

    public function render()
    {
        return view('livewire.rastreo-viaje')->layout('layouts.app');
    }
}