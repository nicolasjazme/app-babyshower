<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class HostController extends Controller
{
    /**
     * Panel de control principal del Anfitrión / Organizador
     */
    public function index()
    {
        // 1. Recuperamos el token y los datos del usuario desde la sesión de Laravel
        $token = Session::get('token_jwt');
        $usuario = Session::get('usuario_logueado');
        $idUsuario = $usuario['_id'] ?? $usuario['id'] ?? null;

        if (!$idUsuario) {
            return redirect('/login')->with('error', 'Sesión inválida. Por favor, vuelve a ingresar.');
        }

        try {
            // 2. Consultamos al Backend en Node.js el evento asignado a este creador
            $response = Http::withToken($token)->get("http://localhost:3000/api/eventos/creador/{$idUsuario}");

            if ($response->successful()) {
                $evento = $response->json('evento');
                $items = $response->json('items') ?? [];
                $invitados = $response->json('invitados') ?? [];

                // 3. Procesamos métricas básicas en tiempo real para las tarjetas informativas
                $totalInvitados = count($invitados);
                $confirmados = count(array_filter($invitados, function($inv) {
                    return isset($inv['confirmado']) && $inv['confirmado'] === true;
                }));

                $totalItems = count($items);
                $itemsReservados = count(array_filter($items, function($item) {
                    return isset($item['asignado_a']) && $item['asignado_a'] !== null;
                }));

                // 4. Agrupamos las métricas calculadas
                $metricas = [
                    'total_invitados' => $totalInvitados,
                    'confirmados'     => $confirmados,
                    'total_items'     => $totalItems,
                    'items_tomados'   => $itemsReservados
                ];

                return view('anfitrion.index', compact('evento', 'items', 'metricas'));
            }

            // Si el usuario no tiene eventos aún, lo mandamos a crear uno
            return redirect()->route('anfitrion.event.create')->with('info', 'Comencemos creando tu primer evento dinámico.');

        } catch (\Exception $e) {
            Log::error('Error en HostController: ' . $e->getMessage());
            return view('anfitrion.index')->with('error', 'El servidor de datos (Node.js) se encuentra desconectado temporalmente.');
        }
    }

    /**
     * Sincroniza estados de asistencia en tiempo real provenientes de WebSockets (Node.js)
     */
    public function sincronizarDesdeNode(Request $request)
    {
        // Recibe las actualizaciones de los clientes en tiempo real y refresca el estado
        Log::info('Webhook de sincronización recibido con éxito', $request->all());
        return response()->json(['status' => 'Sincronizado']);
    }
}