<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class BabyShowerController extends Controller
{
    private $backendUrl = 'http://localhost:3000/api/regalos';

    /**
     * Vista pública de la lista de regalos con buscador sincronizado
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $response = Http::get($this->backendUrl);
        $gifts = $response->json();

        if ($search) {
            $gifts = collect($gifts)->filter(function ($gift) use ($search) {
                return str_contains(strtolower($gift['nombre'] ?? ''), strtolower($search)) || 
                       str_contains(strtolower($gift['descripcion'] ?? ''), strtolower($search));
            })->all();
        }   

        return view('welcome', compact('gifts', 'search'));
    }

    /**
     * RF-03: Procesar la reserva del regalo incluyendo token, vinculación de ID y datos de correo
     */
    public function reserve(Request $request)
    {
        $giftId = $request->input('gift_id');
        $guestName = $request->input('guest_name');
        $guestEmail = $request->input('guest_email'); // Capturamos el correo electrónico

        // 1. Recuperamos el Token JWT y los datos del usuario de la sesión de Laravel
        $token = Session::get('token_jwt');
        $usuarioLogueado = Session::get('usuario_logueado');
        
        // Obtenemos el ID de MongoDB del usuario logueado para que se guarde la relación real
        $invitadoId = $usuarioLogueado['_id'] ?? $usuarioLogueado['id'] ?? null;

        // Redirección de seguridad si el usuario no está logueado
        if (!$token) {
            return redirect('/login')->with('error', 'Debes iniciar sesión para poder reservar un regalo.');
        }

        // 2. CORRECCIÓN CRÍTICA: Añadimos '/reservar' al final de la URL para sincronizar con la nueva ruta de Express
        $response = Http::withToken($token)->put("{$this->backendUrl}/{$giftId}/reservar", [
            'invitadoId'  => $invitadoId,
            'guest_name'  => $guestName,
            'guest_email' => $guestEmail,
            'estado'      => 'reservado'
        ]);

        if ($response->successful()) {
            return redirect('/baby-shower')->with('success', "¡Gracias, {$guestName}! El regalo ha sido reservado y te enviamos un comprobante a tu correo. ✨");
        }

        $mensajeError = $response->json('mensaje') ?? 'Error al procesar la reserva en el servidor.';
        return back()->with('error', 'Error del Servidor: ' . $mensajeError);
    }
}