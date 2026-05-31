<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Exception;

class EventController extends Controller
{
    private $backendUrl = 'http://localhost:3000/api/babyshower';

    /**
     * Muestra el formulario para crear un Baby Shower nuevo
     */
    public function create()
    {
        $usuario = Session::get('usuario_logueado');
        if (!$usuario || $usuario['rol'] !== 'anfitrion') {
            return redirect('/')->with('error', 'Acceso denegado.');
        }

        $evento = null; 
        return view('anfitrion.evento', compact('evento'));
    }

    /**
     * Guarda la configuración inicial del evento (POST)
     */
    public function store(Request $request)
    {
        $token = Session::get('token_jwt');
        if (!$token) {
            return redirect('/login')->with('error', 'Sesión expirada.');
        }

        $payload = $request->except('_token');
        $payload['configVisual'] = [
            'imagenPrincipal' => $request->input('imagenPrincipal', ''),
            'colorTema'       => $request->input('colorTema', '#4f46e5')
        ];

        try {
            $response = Http::withToken($token)->post($this->backendUrl, $payload);

            if ($response->successful()) {
                return redirect('/anfitrion')->with('success', '¡Celebración creada con éxito! 🎉');
            }
            return back()->withInput()->withErrors(['error' => 'Error: ' . $response->body()]);
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Backend desconectado.']);
        }
    }

    /**
     * Muestra el formulario de edición con los datos actuales cargados (RF-12)
     */
    public function edit($id)
    {
        $token = Session::get('token_jwt');
        try {
            // 🛠️ CORRECCIÓN CRÍTICA: Ya no llamamos a /todos (bloqueado para proteger datos).
            // Llamamos al endpoint seguro /mio que creamos exclusivamente para el anfitrión.
            $response = Http::withToken($token)->get("{$this->backendUrl}/mio");
            
            if ($response->successful()) {
                $evento = $response->json();
                
                // Verificación defensiva de seguridad para asegurar que el ID coincida
                if (!$evento || $evento['_id'] !== $id) {
                    return redirect('/anfitrion')->with('error', 'No tienes permisos para editar esta celebración.');
                }
                
                return view('anfitrion.evento', compact('evento'));
            }
            return redirect('/anfitrion')->with('error', 'Error al conectar con el servidor.');
        } catch (Exception $e) {
            return redirect('/anfitrion')->with('error', 'Backend fuera de línea.');
        }
    }

    /**
     * Procesa la actualización de datos o estados en la base de datos (PUT)
     */
    public function update(Request $request, $id)
    {
        $token = Session::get('token_jwt');
        $payload = $request->except(['_token', '_method']);
        
        if ($request->has('colorTema') || $request->has('imagenPrincipal')) {
            $payload['configVisual'] = [
                'imagenPrincipal' => $request->input('imagenPrincipal', ''),
                'colorTema'       => $request->input('colorTema', '#4f46e5')
            ];
        }

        try {
            $response = Http::withToken($token)->put("{$this->backendUrl}/{$id}", $payload);

            if ($response->successful()) {
                return redirect('/anfitrion')->with('success', '¡Cambios guardados correctamente en el servidor! 🔄');
            }
            return back()->with('error', 'No se pudo actualizar el evento.');
        } catch (Exception $e) {
            return back()->with('error', 'Fallo de red con el backend.');
        }
    }

    /**
     * 🛠️ SE AGREGA MÉTODO RF-11: Muestra la landing page pública del Baby Shower para los invitados
     */
    public function showPublic($slug)
    {
        try {
            // 1. Consultar el evento por su slug al endpoint público de Node.js
            $responseEvent = Http::get("{$this->backendUrl}/publico/{$slug}");
            
            if (!$responseEvent->successful() || empty($responseEvent->json())) {
                return redirect('/')->with('error', 'La celebración solicitada no existe o no está disponible.');
            }
            
            $evento = $responseEvent->json();

            // RF-13: Protección perimetral. Si está oculto, un invitado común no puede verlo
            $usuario = Session::get('usuario_logueado');
            $userId = $usuario['_id'] ?? ($usuario['id'] ?? null);
            $organizadorId = $evento['organizadorId']['_id'] ?? ($evento['organizadorId'] ?? null);
            $esDueno = $userId && $organizadorId === $userId;

            if ($evento['estado'] === 'oculto' && !$esDueno) {
                return redirect('/')->with('error', 'Esta página se encuentra en modo de borrador privado.');
            }

            // 2. Traer la lista de regalos para que los invitados puedan interactuar
            $responseGifts = Http::get('http://localhost:3000/api/regalos');
            $gifts = $responseGifts->successful() ? collect($responseGifts->json()) : collect([]);

            // 3. Renderizar la vista pública pasando la información cruzada
            return view('welcome', compact('evento', 'gifts'));
        } catch (Exception $e) {
            return redirect('/')->with('error', 'Hubo un problema al cargar la página de la celebración.');
        }
    }
}