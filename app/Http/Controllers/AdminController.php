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
    private $backendUrlEvents = 'http://localhost:3000/api/babyshower/todos';
    private $backendUrlIncidencias = 'http://localhost:3000/api/babyshower/incidencias';
    private $backendUrlMetricasRegalos = 'http://localhost:3000/api/admin/metricas/regalos'; // 💡 Nuevo endpoint de agregación

    /**
     * Muestra el Panel de Administración con las métricas del catálogo, 
     * las estadísticas de celebraciones, incidencias y supervisión de regalos por slug (RF-13)
     * EXCLUSIVO: Solo Administrador
     */
    public function index()
    {
        // 1. Verificación de Seguridad Estricta
        $usuario = Session::get('usuario_logueado');
        if (!isset($usuario['rol']) || $usuario['rol'] !== 'administrador') {
            return redirect('/baby-shower')->with('error', 'No tienes permisos para acceder al panel de administración.');
        }

        $token = Session::get('token_jwt');
        
        // Inicializamos variables defensivas por si falla la conexión física con NoSQL
        $gifts = collect([]);
        $incidencias = [];
        $metricasRegalos = []; // 📊 Guardará el lote mapeado de regalos por cada Baby Shower
        $metricasEvents = [
            'publicados' => 0,
            'ocultos'    => 0,
            'cerrados'   => 0
        ];

        try {
            // 2. Obtener el catálogo global de regalos sugeridos
            $responseGifts = Http::get($this->backendUrlGifts);
            if ($responseGifts->successful()) {
                $gifts = collect($responseGifts->json());
            } else {
                session()->now('error', 'No se pudo conectar con el servidor de regalos sugeridos.');
            }

            if ($token) {
                // 3. Obtener todas las celebraciones para procesar métricas globales
                $responseEvents = Http::withToken($token)->get($this->backendUrlEvents);
                if ($responseEvents->successful()) {
                    $events = collect($responseEvents->json());
                    
                    // Contamos los estados usando los ayudantes nativos de colecciones de Laravel
                    $metricasEvents = [
                        'publicados' => $events->where('estado', 'publicado')->count(),
                        'ocultos'    => $events->where('estado', 'oculto')->count(),
                        'cerrados'   => $events->where('estado', 'cerrado')->count(),
                    ];
                }

                // 4. Obtener tickets de soporte e incidencias abiertas en tiempo real
                $responseIncidencias = Http::withToken($token)->get($this->backendUrlIncidencias);
                if ($responseIncidencias->successful()) {
                    $incidencias = $responseIncidencias->json();
                }

                // 5. 📊 NUEVO REQUERIMIENTO: Consumir las métricas de regalos asociados a cada celebración (slug)
                $responseMetricasRegalos = Http::withToken($token)->get($this->backendUrlMetricasRegalos);
                if ($responseMetricasRegalos->successful()) {
                    $metricasRegalos = $responseMetricasRegalos->json();
                } else {
                    session()->now('error', 'No se pudo recuperar el inventario de regalos por celebración.');
                }
            }

            
        } catch (Exception $e) {
            session()->now('error', 'Fallo crítico de red: El servidor backend de Node.js está fuera de línea.');
        }

        // Enviamos todas las estructuras unificadas de forma compacta a la vista del Admin
        return view('admin.index', compact('gifts', 'metricasEvents', 'incidencias', 'metricasRegalos'));
    }

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

        // Empaquetamos todos los campos exigidos sanitizando el tipo de dato numérico
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
     * Liberar un regalo por completo (Cambiar de reservado a disponible de forma total)
     * COMPARTIDO: Administrador y Anfitrión
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
     * COMPARTIDO: Administrador y Anfitrión
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
     * PEDIR LISTA DE TODOS LOS BABY SHOWERS (Con auditoría de invitados mapeada en tiempo real)
     * EXCLUSIVO: Solo Administrador
     */
   public function listBabyShowers()
{
    $usuario = Session::get('usuario_logueado');
    if (!isset($usuario['rol']) || $usuario['rol'] !== 'administrador') {
        return redirect('/baby-shower')->with('error', 'Zona exclusiva de administración global.');
    }

    $token = Session::get('token_jwt');
    if (!$token) return redirect('/login')->with('error', 'Sesión expirada.');

    $events = [];

    try {
        // 1. Solicitamos los eventos con el token
        $response = Http::withToken($token)->get($this->backendUrlEvents);
        
        if ($response->successful()) {
            $eventosRaw = $response->json();
            
            foreach ($eventosRaw as $ev) {
                $idEvento = $ev['_id'] ?? '';
                // 🔥 CORRECCIÓN: Llamamos a la nueva ruta que creamos arriba
                $urlInvitados = "http://localhost:3000/api/babyshower/{$idEvento}/invitados";
                
                $responseInvitados = Http::withToken($token)->get($urlInvitados);
                
                // Mapeamos los invitados al campo que tu vista Blade está esperando
                $ev['invitados'] = $responseInvitados->successful() ? $responseInvitados->json() : [];
                $events[] = $ev;
            }
        } else {
            // Si falla, imprimimos el error para debuggear
            \Log::error("Error al obtener eventos desde Node: " . $response->body());
        }
    } catch (Exception $e) {
        \Log::error("Fallo de comunicación: " . $e->getMessage());
        Session::now('error', 'Fallo de comunicación física con el backend.');
    }

    return view('admin.babyshowers', compact('events'));
}

    /**
     * RF-13: Cambiar el estado de visibilidad de un Baby Shower desde la tabla de control
     * EXCLUSIVO: Solo Administrador
     */
    public function updateStatus(Request $request, $id)
    {
        $usuario = Session::get('usuario_logueado');
        if (!isset($usuario['rol']) || $usuario['rol'] !== 'administrador') {
            return redirect('/baby-shower')->with('error', 'No tienes autorización para alterar el estado de eventos ajenos.');
        }

        $request->validate([
            'estado' => 'required|in:publicado,oculto,cerrado'
        ]);

        $token = Session::get('token_jwt');
        if (!$token) {
            return redirect('/login')->with('error', 'Sesión expirada. Por favor vuelve a ingresar.');
        }

        // Enviamos el estado modificado al backend de Node.js
        $response = Http::withToken($token)->put("http://localhost:3000/api/babyshower/{$id}", [
            'estado' => $request->input('estado')
        ]);

        if ($response->successful()) {
            return back()->with('success', '¡Estado de la celebración actualizado correctamente! 🔄');
        }

        return back()->with('error', 'No se pudo cambiar el estado en el servidor.');
    }

    /**
     * Liberar una reserva específica de un invitado desde el catálogo (RF-31)
     * EXCLUSIVO: Administrador de la plataforma
     */
    public function liberarRegalo(Request $request, $id)
    {
        $usuario = Session::get('usuario_logueado');
        if (!isset($usuario['rol']) || $usuario['rol'] !== 'administrador') {
            return redirect('/')->with('error', 'Acceso denegado. Se requieren permisos de Admin.');
        }

        $token = Session::get('token_jwt');

        try {
            // Conectamos con el backend de Node.js enviando el reservaId
            $response = Http::withToken($token)->put("http://localhost:3000/api/regalos/{$id}/reservar", [
                'estado'    => 'disponible',
                'reservaId' => $request->input('reserva_id')
            ]);

            if ($response->successful()) {
                $msgExito = $response->json('mensaje') ?? 'Reserva anulada con éxito.';
                return back()->with('success', $msgExito);
            }

            $mensajeError = $response->json('mensaje') ?? 'Error al interactuar con el inventario.';
            return back()->with('error', 'Error del Servidor: ' . $mensajeError);

        } catch (Exception $e) {
            return back()->with('error', 'Fallo de comunicación física con el backend: ' . $e->getMessage());
        }
    }

    /**
     * Resolver y cerrar un ticket de soporte/incidencia desde la bandeja (RF-31)
     * EXCLUSIVO: Solo Administrador
     */
    public function completarIncidencia($id)
    {
        $usuario = Session::get('usuario_logueado');
        if (!isset($usuario['rol']) || $usuario['rol'] !== 'administrador') {
            return redirect('/')->with('error', 'Acceso denegado. Se requieren privilegios de Administrador.');
        }

        $token = Session::get('token_jwt');

        try {
            // Enviamos la petición DELETE al backend de Node.js para remover el ticket resuelto
            $response = Http::withToken($token)->delete("{$this->backendUrlIncidencias}/{$id}");

            if ($response->successful()) {
                $msgExito = $response->json('mensaje') ?? 'El ticket de soporte fue resuelto y removido con éxito.';
                return back()->with('success', $msgExito);
            }

            $mensajeError = $response->json('mensaje') ?? 'No se pudo completar la solicitud de soporte.';
            return back()->with('error', 'Error del Servidor Backend: ' . $mensajeError);

        } catch (Exception $e) {
            return back()->with('error', 'Fallo de comunicación física con el backend: ' . $e->getMessage());
        }
    }

    /**
     * Elimina de forma definitiva un Baby Shower de la plataforma
     * EXCLUSIVO: Solo Administrador
     */
    public function destruirBabyShower($id)
    {
        $usuario = Session::get('usuario_logueado');
        if (!isset($usuario['rol']) || $usuario['rol'] !== 'administrador') {
            return redirect('/')->with('error', 'Acceso restringido. Se requieren permisos globales.');
        }

        $token = Session::get('token_jwt');
        if (!$token) {
            return redirect('/login')->with('error', 'Sesión expirada.');
        }

        try {
            // Golpeamos el nuevo endpoint DELETE de Express
            $response = Http::withToken($token)->delete("http://localhost:3000/api/admin/babyshower/{$id}");

            if ($response->successful()) {
                return back()->with('success', $response->json('mensaje') ?? 'Celebración eliminada correctamente del servidor.');
            }

            $msgError = $response->json('mensaje') ?? 'No se pudo procesar la eliminación.';
            return back()->with('error', 'Fallo del Servidor NoSQL: ' . $msgError);

        } catch (\Exception $e) {
            return back()->with('error', 'Fallo crítico de red al intentar conectar con Node.js.');
        }
    }
}