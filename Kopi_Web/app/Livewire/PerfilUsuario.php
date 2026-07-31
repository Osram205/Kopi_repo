<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class PerfilUsuario extends Component
{
    use WithFileUploads;
    public $nombre;
    public $apellidos;
    public $carrera;
    public $correo_institucional;
    public $telefono;
    public $matricula;
    public $foto_perfil;
    public $nueva_foto;

    public function mount()
    {
        $token = Session::get('jwt_token');
        
        $response = Http::withToken($token)->get(config('services.fastapi.url') . '/usuarios/perfil');
        if ($response->successful()) {
            $user = $response->json();
            $this->nombre = $user['nombre'] ?? 'Sin Nombre';
            $this->apellidos = $user['apellidos'] ?? '';
            $this->carrera = $user['carrera'] ?? 'No especificada';
            $this->correo_institucional = $user['correo_institucional'] ?? '';
            $this->telefono = $user['telefono'] ?? '';
            $this->matricula = $user['matricula'] ?? 'N/A';
            $this->foto_perfil = $user['foto_perfil'] ?? null;
        } else {
            session()->flash('error', 'No se pudo cargar la información del perfil.');
        }
    }

    public function guardar()
    {
        $token = Session::get('jwt_token');
        
        // Deshabilitar "Expect: 100-continue" y forzar HTTP/1.1 para evitar bugs de cURL en Windows XAMPP con FastAPI
        $request = Http::withToken($token)->withOptions([
            'version' => 1.1,
            'headers' => ['Expect' => '']
        ]);

        try {
            if ($this->nueva_foto) {
                // Si hay foto, adjuntarla explicitamente con attach() (que maneja mejor el stream)
                $response = $request
                    ->attach('foto_perfil', file_get_contents($this->nueva_foto->getRealPath()), $this->nueva_foto->getClientOriginalName())
                    ->attach('telefono', (string) $this->telefono)
                    ->post(config('services.fastapi.url') . '/usuarios/perfil');
            } else {
                // Si no hay foto, enviar como Form (x-www-form-urlencoded) para evitar multipart por completo y esquivar el error de cURL
                $response = $request
                    ->asForm()
                    ->post(config('services.fastapi.url') . '/usuarios/perfil', [
                        'telefono' => (string) $this->telefono
                    ]);
            }
            
            Log::info('PerfilUpdate: ', ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                session()->flash('success', 'Perfil actualizado exitosamente.');
                $this->foto_perfil = $response->json()['foto_perfil'] ?? $this->foto_perfil;
                $this->nueva_foto = null;
            } else {
                Log::error('PerfilUpdateError: ', ['body' => $response->json()]);
                $errDetail = $response->json()['detail'] ?? 'Error desconocido.';
                $mensajeError = is_array($errDetail) ? (isset($errDetail[0]['msg']) ? $errDetail[0]['msg'] : json_encode($errDetail)) : $errDetail;
                session()->flash('error', 'Hubo un error al actualizar el perfil: ' . $mensajeError);
            }
        } catch (\Exception $e) {
            Log::error('PerfilUpdateException: ' . $e->getMessage());
            session()->flash('error', 'Error de red (cURL): ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.perfil-usuario')->layout('layouts.app');
    }
}
