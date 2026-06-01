<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return response()->json(['message' => 'Token no proporcionado.'], 401);
        }

        $token = ApiToken::with('usuario')
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();

        if (! $token || ! $token->usuario) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();
        $request->setUserResolver(fn () => $token->usuario);

        return $next($request);
    }
}
