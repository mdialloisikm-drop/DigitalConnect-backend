<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!auth('api')->check()) {
            return response()->json([
                'error' => 'Non authentifié'
            ], 401);
        }

        // Vérifier si l'utilisateur est un admin
        if (auth('api')->user()->user_type !== 'admin') {
            return response()->json([
                'error' => 'Accès refusé. Cette action est réservée aux administrateurs.'
            ], 403);
        }
        return $next($request);
    }
}
