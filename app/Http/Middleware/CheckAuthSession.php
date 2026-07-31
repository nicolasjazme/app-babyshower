<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthSession
{
    /*Maneja la petición entrante.*/
   public function handle(Request $request, Closure $next): Response
    {
        // Verificar si la persona NO tiene el ticket de usuario logueado
        if (!Session::has('usuario_logueado')) {
            // Si no lo tiene, lo expulsamos al login con un mensaje
            return redirect('/login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        // Si tiene el ticket, lo dejamos pasar a la vista que solicitó
        return $next($request);
    }
}
