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
    
    public $foto_credencial_frente;
    public $poliza_seguro;
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
        $response = Http::withToken($token)->get(config('services.fastapi.url') . '/usuarios/perfil');
        
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
            'foto_credencial_frente' => 'required|image|max:10230',
            'poliza_seguro' => 'required|image|max:10230',
            'foto_licencia' => 'required|image|max:10230',
            'tarjeta_circulacion' => 'required|image|max:10230',
        ]);

        $token = Session::get('jwt_token');

        // Construimos la petición de forma segura, forzando explícitamente multipart/form-data
        $response = Http::withToken($token)
            ->asMultipart()
            ->attach(
                'foto_credencial_frente', 
                $this->foto_credencial_frente->get(), 
                $this->foto_credencial_frente->getClientOriginalName()
            )
            ->attach(
                'poliza_seguro', 
                $this->poliza_seguro->get(), 
                $this->poliza_seguro->getClientOriginalName()
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
            ->post(config('services.fastapi.url') . '/usuarios/solicitar-conductor'); // Enviamos por POST a FastAPI

        if ($response->successful()) {
            session()->flash('success', 'Tu postulación y documentos han sido enviados al comité administrador.');
            $this->obtenerEstatusUsuario();
        } else {
            $errDetail = $response->json()['detail'] ?? 'Error al subir los documentos de verificación.'; $error = is_array($errDetail) ? (isset($errDetail[0]['msg']) ? $errDetail[0]['msg'] : json_encode($errDetail)) : $errDetail;
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
        $response = Http::withToken($token)->post(config('services.fastapi.url') . '/vehiculos', $payload);

        if ($response->successful()) {
            session()->flash('success', '¡Vehículo registrado con éxito! Tu perfil de conductor está activo.');
            return redirect()->route('viajes.index'); 
        } else {
            // Atrapamos el error 403 de FastAPI o cualquier otra falla de validación
            $errDetail = $response->json()['detail'] ?? 'Error al dar de alta el coche.'; $error = is_array($errDetail) ? (isset($errDetail[0]['msg']) ? $errDetail[0]['msg'] : json_encode($errDetail)) : $errDetail;
            session()->flash('error', $error);
        }
    }

    public function render()
    {
        return view('livewire.panel-conductor')->layout('layouts.app');
    }
}