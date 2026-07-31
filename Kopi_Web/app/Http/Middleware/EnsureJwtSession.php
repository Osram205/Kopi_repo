<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJwtSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('jwt_token')) {
            return redirect()->route('login')->with('error', 'Inicia sesión para continuar.');
        }

        return $next($request);
    }
}
