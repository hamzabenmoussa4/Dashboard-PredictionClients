<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur n'est pas connecté, on redirige vers login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Si l'utilisateur n'est pas admin, on bloque l'accès
        if (!$request->user()->is_admin) {
            abort(403, 'Accès refusé (admin seulement).');
        }

        // Sinon, accès autorisé
        return $next($request);
    }
}

