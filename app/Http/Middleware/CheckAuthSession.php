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
        // 1. OBTENER LA RUTA ACTUAL
        $path = $request->path();

        // 2. LISTA DE RUTAS PÚBLICAS (No necesitan sesión)
        // Agregamos aquí todo lo que un invitado debe poder ver sin loguearse
        $rutasPublicas = [
            '/', 
            'login', 
            'registro',
            'recuperar-contrasena'
        ];

        // 3. LA EXCEPCIÓN: Invitaciones públicas (slugs)
        // Si la ruta empieza con 'e/' (tu ruta de eventos), dejamos pasar al invitado
        if (str_starts_with($path, 'e/')) {
            return $next($request);
        }

        // 4. VERIFICAR RUTAS PÚBLICAS
        if (in_array($path, $rutasPublicas)) {
            return $next($request);
        }

        // 5. VALIDACIÓN NORMAL PARA EL RESTO (Admin, Dashboard, etc.)
        if (!Session::has('usuario_logueado')) {
            return redirect('/login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        return $next($request);
    }
}
