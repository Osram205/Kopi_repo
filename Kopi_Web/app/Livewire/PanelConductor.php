<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PanelConductor extends Component
{
    // Estatus del usuario: puede ser nulo, 'solicitado' o 'aprobado'
    public $estatusVerificacion = ''; 
    
    // Propiedades para el formulario del vehículo
    public $marca, $modelo, $placas, $color, $asientos_totales;

    public function mount()
    {
        $this->obtenerEstatusUsuario();
    }

    public function obtenerEstatusUsuario()
    {
        $token = Session::get('jwt_token');
        // Consultamos el perfil del usuario actual en FastAPI para conocer su estatus real
        $response = Http::withToken($token)->get(env('FASTAPI_URL') . '/usuarios/perfil');
        
        if ($response->successful()) {
            // Guardamos el estatus de verificación que viene de MySQL (ej. 'solicitado', 'aprobado' o 'pendiente')
            $this->estatusVerificacion = $response->json()['estatus_verificacion'] ?? '';
        }
    }

    // ACCIÓN 1: LEVANTAR LA MANO PARA SER CONDUCTOR
    public function enviarSolicitudConduccion()
    {
        $token = Session::get('jwt_token');
        $response = Http::withToken($token)->put(env('FASTAPI_URL') . '/usuarios/solicitar-conductor');

        if ($response->successful()) {
            session()->flash('success', 'Tu solicitud ha sido enviada. Espera la validación del administrador.');
            $this->obtenerEstatusUsuario(); // Actualiza la pantalla dinámicamente
        } else {
            session()->flash('error', 'No se pudo procesar la solicitud.');
        }
    }

    // ACCIÓN 2: ALTA DEL VEHÍCULO (SOLO SI YA ESTÁ APROBADO)
    public function registrarVehiculo()
    {
        $this->validate([
            'marca' => 'required|string',
            'modelo' => 'required|string',
            'placas' => 'required|string',
            'color' => 'required|string',
            'asientos_totales' => 'required|integer|min:1|max:7',
        ]);

        $token = Session::get('jwt_token');
        $payload = [
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'placas' => $this->placas,
            'color' => $this->color,
            'asientos_totales' => (int)$this->asientos_totales,
        ];

        $response = Http::withToken($token)->post(env('FASTAPI_URL') . '/vehiculos', $payload);

        if ($response->successful()) {
            session()->flash('success', '¡Vehículo registrado con éxito! Ya puedes publicar viajes.');
            return redirect()->route('viajes.conductor'); // Te mandaría a gestionar tus rutas
        } else {
            session()->flash('error', $response->json()['detail'] ?? 'Error al dar de alta el coche.');
        }
    }

    public function render()
    {
        return view('livewire.panel-conductor')->layout('layouts.app');
    }
}