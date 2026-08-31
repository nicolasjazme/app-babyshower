<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Exception;

class AdminController extends Controller
{
    // 🌐 Centralización de URLs de conexión hacia el Backend de Node.js
    private $backendUrlGifts = 'http://localhost:3000/api/regalos';
    private $backendUrlEvents = 'http://localhost:3000/api/eventos/todos';
    private $backendUrlIncidencias = 'http://localhost:3000/api/eventos/incidencias';
    private $backendUrlMetricasRegalos = 'http://localhost:3000/api/admin/metricas/regalos';
    private $backendUrlUsuarios = 'http://localhost:3000/api/usuarios'; // Endpoint para gestionar comunidad

    /**
     * Helper privado para validar de forma limpia si el usuario es Administrador
     */
    private function validarAdmin()
    {
        $usuario = Session::get('usuario_logueado');
        return isset($usuario['rol']) && $usuario['rol'] === 'administrador';
    }

    // ========================================================
    // 🎨 1. VISTAS DEL DASHBOARD LÚDICO (SEPARADAS Y OPTIMIZADAS)
    // ========================================================

    /**
     * Pantalla Principal: Menú de Tarjetas Gigantes
     */
    public function index()
    {
        if (!$this->validarAdmin()) return redirect('/')->with('error', 'No tienes permisos para acceder al panel de administración.');

        $token = Session::get('token_jwt');
        $eventos = [];
        $incidencias = [];

        try {
            // Solo consultamos conteos rápidos para las burbujas de notificaciones
            $resEvents = Http::withToken($token)->get($this->backendUrlEvents);
            if ($resEvents->successful()) $eventos = $resEvents->json();

            $resIncidencias = Http::withToken($token)->get($this->backendUrlIncidencias);
            if ($resIncidencias->successful()) $incidencias = $resIncidencias->json();
        } catch (Exception $e) {
            // Silencioso en el index para no romper la navegación si Node tarda
        }

        return view('admin.index', compact('eventos', 'incidencias'));
    }

    /**
     * Tarjeta 1: Directorio de Celebraciones Activas
     */
    public function eventos()
    {
        if (!$this->validarAdmin()) return redirect('/')->with('error', 'Zona exclusiva de administración.');
        
        $token = Session::get('token_jwt');
        $eventos = [];

        try {
            $response = Http::withToken($token)->get($this->backendUrlEvents);
            if ($response->successful()) {
                $eventos = $response->json();
            }
        } catch (Exception $e) {
            Session::now('error', 'Fallo de comunicación física con el backend.');
        }

        return view('admin.eventos', compact('eventos'));
    }

    /**
     * Tarjeta 2: Caja de Soporte e Incidencias
     */
    public function soporte()
    {
        if (!$this->validarAdmin()) return redirect('/')->with('error', 'Zona exclusiva de administración.');

        $token = Session::get('token_jwt');
        $incidencias = [];

        try {
            $response = Http::withToken($token)->get($this->backendUrlIncidencias);
            if ($response->successful()) {
                $incidencias = $response->json();
            }
        } catch (Exception $e) {
            Session::now('error', 'Fallo de comunicación física con el backend.');
        }

        return view('admin.soporte', compact('incidencias'));
    }

    /**
     * Tarjeta 3: Métricas Globales y Salud del Sistema
     */
    public function metricas()
    {
        if (!$this->validarAdmin()) return redirect('/')->with('error', 'Zona exclusiva de administración.');
        
        $token = Session::get('token_jwt');
        
        $gifts = collect([]);
        $metricasEvents = ['publicados' => 0, 'ocultos' => 0, 'cerrados' => 0];
        $metricas = ['confirmados' => 0, 'rechazados' => 0, 'pendientes' => 0];

        try {
            // Catálogo Global
            $resGifts = Http::get($this->backendUrlGifts);
            if ($resGifts->successful()) $gifts = collect($resGifts->json());

            // Conteo de Eventos
            $resEvents = Http::withToken($token)->get($this->backendUrlEvents);
            if ($resEvents->successful()) {
                $events = collect($resEvents->json());
                $metricasEvents = [
                    'publicados' => $events->where('estado', 'publicado')->count(),
                    'ocultos'    => $events->where('estado', 'oculto')->count(),
                    'cerrados'   => $events->where('estado', 'cerrado')->count(),
                ];
            }

            // Integración Endpoint Métricas Avanzadas
            $resMetricas = Http::withToken($token)->get($this->backendUrlMetricasRegalos);
            if ($resMetricas->successful()) {
                $dataNode = $resMetricas->json();
                if(isset($dataNode['asistencias'])) {
                    $metricas = $dataNode['asistencias'];
                }
            }
        } catch (Exception $e) {
            Session::now('error', 'Fallo de conexión con el servidor Node.js.');
        }

        return view('admin.metricas', compact('gifts', 'metricasEvents', 'metricas'));
    }

    /**
     * Tarjeta 4: Comunidad (Directorio de Usuarios)
     */
    public function usuarios()
    {
        if (!$this->validarAdmin()) return redirect('/')->with('error', 'Zona exclusiva de administración.');
        
        $token = Session::get('token_jwt');
        $usuarios = [];

        try {
            $response = Http::withToken($token)->get($this->backendUrlUsuarios);
            if ($response->successful()) {
                $usuarios = $response->json();
            }
        } catch (Exception $e) {
            Session::now('error', 'No se pudo cargar el directorio de usuarios.');
        }

        return view('admin.usuarios', compact('usuarios'));
    }


    // ========================================================
    // ⚙️ 2. MÉTODOS DE ACCIÓN (CRUD Y LÓGICA DE NEGOCIO INTACTA)
    // ========================================================

    /**
     * RF-01: Añadir un nuevo regalo al catálogo general
     * COMPARTIDO: Administrador y Anfitrión
     */
    public function store(Request $request)
    {
        $usuario = Session::get('usuario_logueado');
        if (!$usuario || !in_array($usuario['rol'], ['administrador', 'anfitrion'])) {
            return redirect('/')->with('error', 'Acceso denegado. No tienes permisos para añadir regalos.');
        }

        $datos = [
            'nombre'              => $request->input('nombre'),
            'descripcion'         => $request->input('descripcion'), 
            'url_imagen'          => $request->input('imagen'),
            'link_referencia'     => $request->input('link_referencia') ?? '',
            'tipo'                => $request->input('tipo') ?? 'unico',
            'cantidad_solicitada' => intval($request->input('cantidad_solicitada') ?? 1),
            'categoria'           => $request->input('categoria') ?? 'General'
        ];

        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->post($this->backendUrlGifts, $datos);

        if ($response->successful()) {
            return back()->with('success', 'Regalo añadido correctamente con su respectivo stock inicial.');
        }

        $mensajeError = $response->json('mensaje') ?? 'Error de autorización o servidor.';
        return back()->with('error', 'Error del Servidor: ' . $mensajeError);
    }

    /**
     * Actualizar los datos de un regalo existente
     * COMPARTIDO: Administrador y Anfitrión
     */
    public function update(Request $request, $id)
    {
        $usuario = Session::get('usuario_logueado');
        if (!$usuario || !in_array($usuario['rol'], ['administrador', 'anfitrion'])) {
            return redirect('/')->with('error', 'No tienes permisos para modificar este artículo.');
        }

        $datos = [
            'nombre'      => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'url_imagen'  => $request->input('imagen')
        ];

        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->put("{$this->backendUrlGifts}/{$id}", $datos);

        if ($response->successful()) {
            return back()->with('success', 'El regalo se actualizó correctamente.');
        }

        $mensajeError = $response->json('mensaje') ?? 'No se pudo actualizar el regalo.';
        return back()->with('error', 'Error: ' . $mensajeError);
    }

    /**
     * Liberar un regalo por completo
     */
    public function restore($id)
    {
        $usuario = Session::get('usuario_logueado');
        if (!$usuario || !in_array($usuario['rol'], ['administrador', 'anfitrion'])) {
            return redirect('/')->with('error', 'No tienes permisos para liberar cupos.');
        }

        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->put("{$this->backendUrlGifts}/{$id}", ['estado' => 'disponible']);

        if ($response->successful()) {
            return back()->with('success', 'El regalo ahora está disponible nuevamente para los invitados.');
        }
        return back()->with('error', 'No se pudo liberar el regalo.');
    }

    /**
     * Eliminar un regalo físicamente del catálogo global
     */
    public function destroy($id)
    {
        $usuario = Session::get('usuario_logueado');
        if (!$usuario || !in_array($usuario['rol'], ['administrador', 'anfitrion'])) {
            return redirect('/')->with('error', 'No tienes permisos para eliminar artículos.');
        }

        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->delete("{$this->backendUrlGifts}/{$id}");

        if ($response->successful()) {
            return back()->with('success', 'Regalo eliminado de la lista.');
        }

        return back()->with('error', 'No se pudo eliminar el regalo.');
    }

    /**
     * Cambiar el estado de visibilidad de un evento desde la tabla de control
     */
    public function updateStatus(Request $request, $id)
    {
        if (!$this->validarAdmin()) return redirect('/')->with('error', 'No tienes autorización.');

        $request->validate(['estado' => 'required|in:publicado,oculto,cerrado']);

        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->put("http://localhost:3000/api/eventos/{$id}", [
            'estado' => $request->input('estado')
        ]);

        if ($response->successful()) {
            return back()->with('success', '¡Estado de la celebración actualizado correctamente! 🔄');
        }

        return back()->with('error', 'No se pudo cambiar el estado en el servidor.');
    }

    /**
     * Liberar una reserva específica de un invitado desde el catálogo
     */
    public function liberarRegalo(Request $request, $id)
    {
        if (!$this->validarAdmin()) return redirect('/')->with('error', 'Acceso denegado. Se requieren permisos de Admin.');

        $token = Session::get('token_jwt');

        try {
            $response = Http::withToken($token)->put("http://localhost:3000/api/regalos/{$id}/reservar", [
                'estado'    => 'disponible',
                'reservaId' => $request->input('reserva_id')
            ]);

            if ($response->successful()) {
                return back()->with('success', $response->json('mensaje') ?? 'Reserva anulada con éxito.');
            }
            return back()->with('error', 'Error del Servidor: ' . ($response->json('mensaje') ?? 'Error'));

        } catch (Exception $e) {
            return back()->with('error', 'Fallo de comunicación física con el backend.');
        }
    }

    /**
     * Resolver y cerrar un ticket de soporte/incidencia desde la bandeja
     */
    public function completarIncidencia($id)
    {
        if (!$this->validarAdmin()) return redirect('/')->with('error', 'Acceso denegado. Se requieren privilegios de Administrador.');

        $token = Session::get('token_jwt');

        try {
            $response = Http::withToken($token)->delete("{$this->backendUrlIncidencias}/{$id}");

            if ($response->successful()) {
                return back()->with('success', $response->json('mensaje') ?? 'El ticket de soporte fue resuelto y removido con éxito.');
            }
            return back()->with('error', 'Error del Servidor Backend: ' . ($response->json('mensaje') ?? 'No se pudo completar.'));

        } catch (Exception $e) {
            return back()->with('error', 'Fallo de comunicación física con el backend.');
        }
    }

    /**
     * Elimina de forma definitiva una celebración de la plataforma
     */
    public function destruirBabyShower($id)
    {
        if (!$this->validarAdmin()) return redirect('/')->with('error', 'Acceso restringido. Se requieren permisos globales.');

        $token = Session::get('token_jwt');

        try {
            $response = Http::withToken($token)->delete("http://localhost:3000/api/eventos/{$id}");

            if ($response->successful()) {
                return back()->with('success', $response->json('mensaje') ?? 'Celebración eliminada correctamente del servidor.');
            }
            return back()->with('error', 'Fallo del Servidor NoSQL: ' . ($response->json('mensaje') ?? 'No procesado.'));

        } catch (\Exception $e) {
            return back()->with('error', 'Fallo crítico de red al intentar conectar con Node.js.');
        }
    }
}