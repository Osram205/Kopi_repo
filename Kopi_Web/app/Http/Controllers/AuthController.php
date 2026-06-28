<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Muestra la vista de Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Procesa las credenciales contra FastAPI
    public function procesarLogin(Request $request)
    {
        $request->validate([
            'correo_institucional' => 'required|email',
            'contrasena' => 'required'
        ]);

        try {
            // Hacemos la petición a FastAPI simulando el formulario de Swagger
            $response = Http::asForm()->post(env('FASTAPI_URL') . '/auth/login', [
                'username' => $request->correo_institucional,
                'password' => $request->contrasena
            ]);

            if ($response->successful()) {
                // Si FastAPI nos da luz verde, guardamos el JWT en la sesión de Laravel
                $token = $response->json()['access_token'];
                Session::put('jwt_token', $token);
                
                return redirect()->route('viajes.index')->with('success', '¡Bienvenido a Kopi!');
            }

            // Si FastAPI devuelve 401
            return back()->with('error', 'Credenciales inválidas. Revisa tu correo y contraseña.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error de conexión con el servidor principal.');
        }
    }

    public function showRegistro()
    {
        return view('auth.registro');
    }

    // Procesa el formulario de registro contra FastAPI
    public function procesarRecuperacion(Request $request)
    {
        $request->validate([
            'correo_institucional' => 'required|email',
            'matricula' => 'required|string',
            'nueva_contrasena' => 'required|string|min:6'
        ]);

        try {
            $response = Http::post(env('FASTAPI_URL') . '/auth/restablecer-password', [
                'correo_institucional' => $request->correo_institucional,
                'matricula' => $request->matricula,
                'nueva_contrasena' => $request->nueva_contrasena
            ]);

            if ($response->successful()) {
                return redirect()->route('login')->with('success', '¡Tu contraseña ha sido restablecida! Ya puedes iniciar sesión.');
            }

            $mensajeError = $response->json()['detail'] ?? 'Error al restablecer la contraseña. Verifica tus datos.';
            return back()->with('error', $mensajeError)->withInput();

        } catch (\Exception $e) {
            return back()->with('error', 'Error de conexión con el servidor central.')->withInput();
        }
    }

    // Muestra la vista de Recuperar Contraseña (Plantilla)
    // Cierra la sesión borrando el Token
    public function logout()
    {
        Session::forget('jwt_token');
        return redirect()->route('login');
    }

    
}