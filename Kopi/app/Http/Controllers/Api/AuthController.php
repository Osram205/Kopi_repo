<?php

namespace App\Http\Controllers\Api;

use App\Models\ApiToken;
use App\Models\Usuario;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function registro(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'matricula' => ['required', 'string', 'max:20', 'unique:usuarios,matricula'],
            'correo_institucional' => ['required', 'email', 'max:100', 'ends_with:@upq.edu.mx', 'unique:usuarios,correo_institucional'],
            'contrasena' => ['required', 'string', 'min:8'],
            'telefono' => ['required', 'string', 'max:15'],
            'foto_credencial' => ['nullable', 'string', 'max:255'],
            'es_conductor' => ['sometimes', 'boolean'],
        ]);

        $data['contrasena'] = Hash::make($data['contrasena']);
        $usuario = Usuario::create($data);

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'usuario' => $usuario,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo_institucional' => ['required', 'email'],
            'contrasena' => ['required', 'string'],
        ]);

        $usuario = Usuario::where('correo_institucional', $credentials['correo_institucional'])->first();

        if (! $usuario || ! Hash::check($credentials['contrasena'], $usuario->contrasena)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        $plainToken = bin2hex(random_bytes(40));
        $token = ApiToken::create([
            'usuario_id' => $usuario->id,
            'name' => $request->input('device_name', 'api'),
            'token_hash' => hash('sha256', $plainToken),
        ]);

        return response()->json([
            'access_token' => $plainToken,
            'token_type' => 'Bearer',
            'token_id' => $token->id,
            'usuario' => $usuario,
        ]);
    }

    public function perfil(Request $request)
    {
        return response()->json($request->user()->load(['vehiculos']));
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if ($token) {
            ApiToken::where('token_hash', hash('sha256', $token))->delete();
        }

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }
}
