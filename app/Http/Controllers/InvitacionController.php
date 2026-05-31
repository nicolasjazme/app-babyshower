<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InvitacionController extends Controller
{
    // Asegúrate de que esta URL apunte a tu Node.js local o en producción
    private $nodeApiUrl = 'http://localhost:3000/api';

    public function mostrarInvitacion($token)
    {
        // 1. Le preguntamos a Node.js si este token existe y es válido
        $response = Http::get("{$this->nodeApiUrl}/invitacion/{$token}");

        if ($response->failed() || $response->status() === 404) {
            // Si el token no existe, mostramos un error amigable
            abort(404, 'Enlace de invitación inválido o expirado.');
        }

        $invitado = $response->json();

        // 2. Traemos la lista de regalos disponibles para mostrársela
        $regalosResponse = Http::get("{$this->nodeApiUrl}/regalos");
        $regalos = $regalosResponse->successful() ? $regalosResponse->json() : [];

        // 3. Mandamos toda esta información a la nueva vista de Blade
        return view('invitacion.magica', [
            'invitado' => $invitado,
            'regalos' => $regalos,
            'token' => $token
        ]);
    }
}
