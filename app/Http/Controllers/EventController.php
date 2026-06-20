<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * 1. INDEX: Muestra la Landing Page general de Invita App (Tu catálogo de temas)
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * 2. CREATE: Redirige al formulario de creación manual de eventos si se requiere
     */
    public function create()
    {
        return view('eventos.create');
    }

    /**
     * 3. STORE: Envía a Node.js la orden de crear un nuevo evento personalizado
     */
    public function store(Request $request)
    {
        $token = Session::get('token_jwt');
        $usuario = Session::get('usuario_logueado');
        $idUsuario = $usuario['_id'] ?? $usuario['id'] ?? null;

        $datosEvento = [
            'creador_id'   => $idUsuario,
            'titulo'       => $request->input('titulo'),
            'tipo_evento'  => $request->input('tipo_evento', 'baby_shower'),
            'fecha'        => $request->input('fecha'),
            'hora'         => $request->input('hora'),
            'ubicacion'    => $request->input('ubicacion')
        ];

        $response = Http::withToken($token)->post('http://localhost:3000/api/eventos', $datosEvento);

        if ($response->successful()) {
            return redirect()->route('anfitrion.index')->with('success', '¡Tu evento ha sido creado de forma espectacular!');
        }

        return back()->with('error', 'No se pudo sincronizar la creación del evento en el servidor.');
    }

    /**
     * 4. EDIT: Solicita a Node.js los datos de un evento específico para listarlo en el formulario de edición
     */
    public function edit($id)
    {
        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->get("http://localhost:3000/api/eventos/{$id}");

        if ($response->successful()) {
            $evento = $response->json('evento');
            return view('eventos.edit', compact('evento'));
        }

        return redirect()->route('anfitrion.index')->with('error', 'No se pudo encontrar la información del evento.');
    }

    /**
     * 5. UPDATE: Actualiza la información estructural o visual del evento
     */
    public function update(Request $request, $id)
    {
        $token = Session::get('token_jwt');

        $datosActualizar = [
            'titulo'    => $request->input('titulo'),
            'fecha'     => $request->input('fecha'),
            'hora'      => $request->input('hora'),
            'ubicacion' => $request->input('ubicacion'),
            // Permite actualizar temas visuales sobre la marcha
            'configVisual' => [
                'colorTema'  => $request->input('color_tema'),
                'iconoEmoji' => $request->input('icono_emoji')
            ]
        ];

        $response = Http::withToken($token)->put("http://localhost:3000/api/eventos/{$id}", $datosActualizar);

        if ($response->successful()) {
            return back()->with('success', '¡Parámetros del evento actualizados con éxito!');
        }

        return back()->with('error', 'Error al intentar guardar los cambios del evento.');
    }

    /**
     * 6. SHOW PUBLIC (RF-11 / RF-12): La joya de la corona.
     * Renderiza la invitación dinámica que verán los invitados mediante la URL con Slug.
     */
    public function showPublic($slug)
    {
        // Consultamos a Node.js los detalles completos del evento mediante el slug de la URL
        $response = Http::get("http://localhost:3000/api/eventos/publico/{$slug}");

        if (!$response->successful()) {
            return redirect('/')->with('error', 'La invitación que buscas no existe o el evento ha expirado.');
        }

        $evento = $response->json('evento');
        $items  = $response->json('items') ?? []; // Pueden ser regalos o ítems de cooperación (asado)

        // Definimos un helper de estilos dinámicos nativos en base al tipo de evento
        // Esto le ahorrará toneladas de código al Frontend y aplicará los colores automáticamente
        $estiloTema = $this->obtenerPaletaColor($evento['tipo_evento'] ?? 'baby_shower');

        return view('eventos.invitacion_publica', compact('evento', 'items', 'estiloTema'));
    }

    /**
     * 7. RESERVE ITEM: Procesa de forma abstracta la reserva de un regalo o de un insumo de cooperación
     */
    public function reserveItem(Request $request)
    {
        $itemId = $request->input('item_id');
        $nombreInvitado = $request->input('nombre_invitado');

        $response = Http::post("http://localhost:3000/api/eventos/items/reservar/{$itemId}", [
            'nombre_invitado' => $nombreInvitado
        ]);

        if ($response->successful()) {
            return back()->with('success', '¡Muchas gracias por tu valiosa colaboración en el evento!');
        }

        $error = $response->json('mensaje') ?? 'Este artículo ya fue tomado o reservado por otra persona.';
        return back()->with('error', $error);
    }

    /**
     * FUNCIÓN AUXILIAR PRIVADA: Mapea configuraciones visuales según el tipo de celebración
     */
    private function obtenerPaletaColor($tipo)
    {
        $temas = [
            'baby_shower' => [
                'bg_gradient'  => 'from-blue-50 to-pink-50',
                'color_card'   => 'bg-white text-blue-600',
                'color_boton'  => 'bg-blue-500 hover:bg-blue-600 text-white',
                'badge_color'  => 'bg-sky-100 text-sky-800'
            ],
            'matrimonio' => [
                'bg_gradient'  => 'from-rose-50 to-amber-50',
                'color_card'   => 'bg-white text-rose-600',
                'color_boton'  => 'bg-rose-500 hover:bg-rose-600 text-white',
                'badge_color'  => 'bg-rose-100 text-rose-800'
            ],
            'cumpleanos' => [
                'bg_gradient'  => 'from-indigo-50 to-purple-50',
                'color_card'   => 'bg-white text-purple-600',
                'color_boton'  => 'bg-purple-600 hover:bg-purple-700 text-white',
                'badge_color'  => 'bg-purple-100 text-purple-800'
            ],
            'asado' => [
                'bg_gradient'  => 'from-orange-50 to-amber-100',
                'color_card'   => 'bg-amber-900 text-white',
                'color_boton'  => 'bg-orange-600 hover:bg-orange-700 text-white',
                'badge_color'  => 'bg-orange-100 text-orange-800'
            ],
            'fiesta' => [
                'bg_gradient'  => 'from-slate-900 to-purple-950 text-white',
                'color_card'   => 'bg-slate-800 text-fuchsia-400',
                'color_boton'  => 'bg-fuchsia-600 hover:bg-fuchsia-700 text-white',
                'badge_color'  => 'bg-fuchsia-100 text-fuchsia-800'
            ],
        ];

        return $temas[$tipo] ?? $temas['baby_shower'];
    }
}