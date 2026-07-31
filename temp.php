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
            'contrasena' => 'required|string',
        ]);

        try {
            // Mandamos las credenciales en formato x-www-form-urlencoded como pide OAuth2PasswordRequestForm
            $response = Http::asForm()->post(config('services.fastapi.url') . '/auth/login', [
                'username' => $request->correo_institucional,
                'password' => $request->contrasena,
            ]);

            if ($response->successful()) {
                $token = $response->json()['access_token'];
                Session::regenerate();
                Session::put('jwt_token', $token);
                $perfilResponse = Http::withToken($token)->get(config('services.fastapi.url') . '/usuarios/perfil');
                
                if ($perfilResponse->successful()) {
                    $datosPerfil = $perfilResponse->json();
                    // Almacenamos los estados de conductor
                    Session::put('es_conductor', $datosPerfil['es_conductor'] ?? false);
                    Session::put('estatus_verificacion', $datosPerfil['estatus_verificacion'] ?? 'pendiente');
                }

                return redirect()->route('viajes.index');
            }

            $mensajeError = $response->json()['detail'] ?? 'Credenciales incorrectas.';
            return back()->with('error', $mensajeError)->withInput();

        } catch (\Exception $e) {
            return back()->with('error', 'Error de conexión con el servidor central: ' . ->getMessage())->withInput();
        }
    }

// Procesa el formulario de registro y lo envía a FastAPI
    public function procesarRegistro(Request $request)
    {
        // 1. Validamos que el usuario llene todo y use correo de la universidad
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'carrera' => 'required|string|max:150',
            'matricula' => 'required|string|max:20',
            'telefono' => 'required|string|max:15',
            'correo_institucional' => 'required|email|ends_with:@upq.edu.mx',
            'contrasena' => 'required|string|min:6',
            'foto_credencial' => 'required|image|max:5120'
        ], [
            'correo_institucional.ends_with' => 'Debes usar tu correo institucional (@upq.edu.mx).'
        ]);

        try {
            // 2. Enviamos los datos con Attach para el archivo (Multipart form data)
            $response = Http::attach(
                'foto_credencial',
                file_get_contents($request->file('foto_credencial')->getRealPath()),
                $request->file('foto_credencial')->getClientOriginalName()
            )->post(config('services.fastapi.url') . '/auth/registro', [
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'carrera' => $request->carrera,
                'matricula' => $request->matricula,
                'telefono' => $request->telefono,
                'correo_institucional' => $request->correo_institucional,
                'contrasena' => $request->contrasena
            ]);

            // 3. Si FastAPI responde exitosamente
            if ($response->successful()) {
                return redirect()->route('login')->with('success', '¡Cuenta creada con éxito! Ya puedes iniciar sesión.');
            }

            // Si hay error desde FastAPI (ej. el correo o matrícula ya existe)
            $mensajeError = $response->json()['detail'] ?? 'Error al crear la cuenta. Verifica tus datos.';
            return back()->with('error', $mensajeError)->withInput();

        } catch (\Exception $e) {
            return back()->with('error', 'Error de conexión con el servidor central: ' . ->getMessage())->withInput();
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
            $response = Http::post(config('services.fastapi.url') . '/auth/restablecer-password', [
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
            return back()->with('error', 'Error de conexión con el servidor central: ' . ->getMessage())->withInput();
        }
    }

    // Muestra la vista de Recuperar Contraseña (Plantilla)
    // Cierra la sesión borrando el Token
    public function logout()
    {
        Session::flush();
        Session::regenerateToken();
        return redirect()->route('login');
    }

    
}
