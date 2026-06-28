<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads; // <- IMPORTANTE: Para habilitar subida de archivos
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PanelConductor extends Component
{
    use WithFileUploads; // Trait requerido por Livewire

    public $estatusVerificacion = ''; 
    
    // Propiedades del Formulario de Postulación (Documentos)
    public $foto_credencial;
    public $foto_licencia;
    public $tarjeta_circulacion;

    // Propiedades del Formulario del Vehículo (Se mantienen igual)
    public $marca, $modelo, $placas, $color, $asientos_totales;

    public function mount()
    {
        $this->obtenerEstatusUsuario();
    }

    public function obtenerEstatusUsuario()
    {
        $token = Session::get('jwt_token');
        $response = Http::withToken($token)->get(env('FASTAPI_URL') . '/usuarios/perfil');
        
        if ($response->successful()) {
            $this->estatusVerificacion = $response->json()['estatus_verificacion'] ?? '';
        }
    }

    // ACCIÓN 1: ENVIAR SOLICITUD DE CONDUCCIÓN CON ARCHIVOS ADJUNTOS
    public function enviarSolicitudConduccion()
    {
        // 1. LA VALIDACIÓN SE HACE AQUÍ, UNA SOLA VEZ AL INICIO.
        // Si una imagen pesa más de 2MB, Laravel detiene todo aquí y muestra el error.
        $this->validate([
            'foto_credencial' => 'required|image|max:10230',
            'foto_licencia' => 'required|image|max:10230',
            'tarjeta_circulacion' => 'required|image|max:10230',
        ]);

        $token = Session::get('jwt_token');

        // Construimos la petición usando el método ->get() de Livewire para extraer los binarios de forma segura
        $response = Http::withToken($token)
            ->attach(
                'foto_credencial', 
                $this->foto_credencial->get(), 
                $this->foto_credencial->getClientOriginalName()
            )
            ->attach(
                'foto_licencia', 
                $this->foto_licencia->get(), 
                $this->foto_licencia->getClientOriginalName()
            )
            ->attach(
                'tarjeta_circulacion', 
                $this->tarjeta_circulacion->get(), 
                $this->tarjeta_circulacion->getClientOriginalName()
            )
            ->put(env('FASTAPI_URL') . '/usuarios/solicitar-conductor'); // Enviamos por PUT a FastAPI

        if ($response->successful()) {
            session()->flash('success', 'Tu postulación y documentos han sido enviados al comité administrador.');
            $this->obtenerEstatusUsuario();
        } else {
            $error = $response->json()['detail'] ?? 'Error al subir los documentos de verificación.';
            session()->flash('error', $error);
        }
    }

    // ACCIÓN 2: REGISTRAR VEHÍCULO (Se queda exactamente igual)
    public function registrarVehiculo()
    {
        // 1. Refuerzo local: Si en la sesión actual el estatus no es aprobado, bloqueamos el envío
        if ($this->estatusVerificacion !== 'aprobado') {
            session()->flash('error', 'Acceso denegado. No tienes permisos de conductor autorizado.');
            return;
        }

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

        // Enviamos la petición a FastAPI
        $response = Http::withToken($token)->post(env('FASTAPI_URL') . '/vehiculos', $payload);

        if ($response->successful()) {
            session()->flash('success', '¡Vehículo registrado con éxito! Tu perfil de conductor está activo.');
            return redirect()->route('viajes.index'); 
        } else {
            // Atrapamos el error 403 de FastAPI o cualquier otra falla de validación
            $error = $response->json()['detail'] ?? 'Error al dar de alta el coche.';
            session()->flash('error', $error);
        }
    }

    public function render()
    {
        return view('livewire.panel-conductor')->layout('layouts.app');
    }
}