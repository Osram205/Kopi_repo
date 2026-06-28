<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class RestablecerPassword extends Component
{
    // Propiedades del Paso 1
    public $correo_institucional;
    public $matricula;

    // Propiedades del Paso 2
    public $nueva_contrasena;
    public $token_recuperacion;

    // Control del Wizard (Paso actual)
    public $paso = 1;

    // Ejecuta el Paso 1 contra FastAPI
    public function verificarIdentidad()
    {
        $this->validate([
            'correo_institucional' => 'required|email',
            'matricula' => 'required|string',
        ]);

        try {
            $response = Http::post(env('FASTAPI_URL') . '/auth/verificar-identidad', [
                'correo_institucional' => $this->correo_institucional,
                'matricula' => $this->matricula
            ]);

            if ($response->successful()) {
                // Almacenamos el token secreto devuelto en memoria del componente
                $this->token_recuperacion = $response->json()['token'];
                // Avanzamos el formulario reactivamente
                $this->paso = 2;
                session()->forget('error');
            } else {
                session()->flash('error', $response->json()['detail'] ?? 'Identidad no encontrada.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error de comunicación con el servidor central.');
        }
    }

    // Ejecuta el Paso 2 utilizando el token validado
    public function cambiarContrasena()
    {
        $this->validate([
            'nueva_contrasena' => 'required|string|min:6',
        ]);

        try {
            $response = Http::post(env('FASTAPI_URL') . '/auth/restablecer-con-token', [
                'token' => $this->token_recuperacion,
                'nueva_contrasena' => $this->nueva_contrasena
            ]);

            if ($response->successful()) {
                // Redirigimos al Login tradicional de Laravel con mensaje de éxito
                return redirect()->route('login')->with('success', '¡Tu contraseña ha sido actualizada con éxito! Ya puedes ingresar.');
            } else {
                session()->flash('error', $response->json()['detail'] ?? 'El proceso falló.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error de conexión al procesar el cambio.');
        }
    }

    public function render()
    {
        return view('livewire.restablecer-password')->layout('layouts.app');
    }
}