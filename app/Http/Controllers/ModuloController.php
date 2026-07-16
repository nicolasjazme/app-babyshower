<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ModuloController extends Controller
{
    // Asegúrate de que esta URL apunte al backend de tu compañero
    private $apiUrl = 'http://localhost:3000/api'; 

    /**
     * RENDERIZADOR MAESTRO DE VISTAS
     * Lee la URL (ej: /eventos/modulos/regalos) y carga la vista correspondiente
     * pidiendo los datos al backend de Node.js.
     */
    public function show($modulo)
    {
        $token = Session::get('token_jwt');
        $eventoId = Session::get('evento_activo')['_id'] ?? Session::get('evento_activo')['id'] ?? null;

        if (!$eventoId) {
            return redirect('/admin')->with('error', 'No tienes un evento activo seleccionado.');
        }

        // Le pedimos al backend los datos específicos del módulo que queremos ver
        $response = Http::withToken($token)->get("{$this->apiUrl}/eventos/{$eventoId}/modulos/{$modulo}");

        // Extraemos la información (fallback vacío si falla)
        $datos = $response->successful() ? $response->json() : [];

        // Verifica si la vista blade existe (ej: modulos.regalos, modulos.cuotas)
        if (view()->exists("modulos.{$modulo}")) {
            return view("modulos.{$modulo}", $datos);
        }

        return redirect('/admin')->with('error', 'El módulo solicitado no existe o está en construcción.');
    }

    // ==========================================
    // LÓGICA MÓDULO 1: REGALOS
    // ==========================================
    
    public function storeRegalo(Request $request)
    {
        $response = Http::withToken(Session::get('token_jwt'))
            ->post("{$this->apiUrl}/eventos/{$request->evento_id}/regalos", [
                'nombre'       => $request->nombre,
                'descripcion'  => $request->descripcion,
                'link_externo' => $request->link_externo
            ]);

        if ($response->successful()) return back()->with('success', '¡Regalo añadido a la lista!');
        return back()->with('error', 'Error al guardar el regalo.');
    }

    public function destroyRegalo($id)
    {
        $eventoId = Session::get('evento_activo')['_id'] ?? null;
        $response = Http::withToken(Session::get('token_jwt'))
            ->delete("{$this->apiUrl}/eventos/{$eventoId}/regalos/{$id}");

        if ($response->successful()) return back()->with('success', 'Regalo eliminado correctamente.');
        return back()->with('error', 'No se pudo eliminar el regalo.');
    }

    // ==========================================
    // LÓGICA MÓDULO 2: CUOTAS (LA VACA)
    // ==========================================

    public function configurarCuotas(Request $request)
    {
        $response = Http::withToken(Session::get('token_jwt'))
            ->post("{$this->apiUrl}/eventos/{$request->evento_id}/cuotas/config", $request->all());

        if ($response->successful()) return back()->with('success', '¡Datos bancarios actualizados!');
        return back()->with('error', 'Error al guardar la configuración de La Vaca.');
    }

    public function aprobarPago($id)
    {
        $eventoId = Session::get('evento_activo')['_id'] ?? null;
        $response = Http::withToken(Session::get('token_jwt'))
            ->put("{$this->apiUrl}/eventos/{$eventoId}/cuotas/pagos/{$id}/aprobar");

        if ($response->successful()) return back()->with('success', '¡Pago aprobado y sumado al pozo!');
        return back()->with('error', 'Error al aprobar el pago.');
    }

    public function rechazarPago($id)
    {
        $eventoId = Session::get('evento_activo')['_id'] ?? null;
        $response = Http::withToken(Session::get('token_jwt'))
            ->delete("{$this->apiUrl}/eventos/{$eventoId}/cuotas/pagos/{$id}");

        if ($response->successful()) return back()->with('success', 'Pago rechazado y eliminado.');
        return back()->with('error', 'Error al rechazar el pago.');
    }

    // ==========================================
    // LÓGICA MÓDULO 3: MESAS
    // ==========================================

    public function storeMesa(Request $request)
    {
        $response = Http::withToken(Session::get('token_jwt'))
            ->post("{$this->apiUrl}/eventos/{$request->evento_id}/mesas", [
                'nombre'        => $request->nombre,
                'limite_sillas' => (int) $request->limite_sillas
            ]);

        if ($response->successful()) return back()->with('success', '¡Nueva mesa creada en el salón!');
        return back()->with('error', 'Error al crear la mesa.');
    }

    public function destroyMesa($id)
    {
        $eventoId = Session::get('evento_activo')['_id'] ?? null;
        $response = Http::withToken(Session::get('token_jwt'))
            ->delete("{$this->apiUrl}/eventos/{$eventoId}/mesas/{$id}");

        if ($response->successful()) return back()->with('success', 'Mesa eliminada (los invitados quedaron libres).');
        return back()->with('error', 'No se pudo eliminar la mesa.');
    }

    public function asignarMesa(Request $request)
    {
        $eventoId = Session::get('evento_activo')['_id'] ?? null;
        $response = Http::withToken(Session::get('token_jwt'))
            ->put("{$this->apiUrl}/eventos/{$eventoId}/mesas/asignar", [
                'mesa_id'     => $request->mesa_id,
                'invitado_id' => $request->invitado_id
            ]);

        if ($response->successful()) return back()->with('success', 'Invitado ubicado en la mesa con éxito.');
        return back()->with('error', $response->json('mensaje') ?? 'Error al asignar mesa. (¿Mesa llena?)');
    }

    public function removerMesa(Request $request)
    {
        $eventoId = Session::get('evento_activo')['_id'] ?? null;
        $response = Http::withToken(Session::get('token_jwt'))
            ->put("{$this->apiUrl}/eventos/{$eventoId}/mesas/remover", [
                'mesa_id'     => $request->mesa_id,
                'invitado_id' => $request->invitado_id
            ]);

        if ($response->successful()) return back()->with('success', 'Invitado retirado de la mesa.');
        return back()->with('error', 'Error al remover al invitado.');
    }
}