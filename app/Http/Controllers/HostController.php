<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Exception;

class HostController extends Controller
{
    // Centralización de endpoints del Backend Node.js
    private $backendUrlGifts = 'http://localhost:3000/api/regalos';
    private $backendUrlMyEvent = 'http://localhost:3000/api/babyshower/mio'; 
    private $backendUrlGuests = 'http://localhost:3000/api/babyshower/mio/invitados';

    /**
     * Muestra el Centro de Control Seguro del Anfitrión (Catálogo de regalos del evento)
     */
    public function index()
    {
        $usuario = Session::get('usuario_logueado');
        if (!isset($usuario['rol']) || $usuario['rol'] !== 'anfitrion') {
            return redirect('/')->with('error', 'Acceso restringido.');
        }

        $evento = null;

        try {
            // 1. Obtener la lista global de regalos sugeridos
            $responseGifts = Http::get($this->backendUrlGifts);
            $gifts = $responseGifts->successful() ? collect($responseGifts->json()) : collect([]);

            // 2. Pedir exclusivamente el evento del anfitrión autenticado mediante JWT
            $token = Session::get('token_jwt');
            $responseEvent = Http::withToken($token)->get($this->backendUrlMyEvent);
            
            
            if ($responseEvent->successful()) {
                $data = $responseEvent->json();
                $evento = !empty($data) ? $data : null;
            }

        } catch (Exception $e) {
            $gifts = collect([]);
            session()->now('error', 'Fallo de comunicación con el servidor backend.');
        }

        return view('anfitrion.index', compact('gifts', 'evento'));
    }

    /**
     * 👥 REFACTORIZADO Y BLINDADO: Muestra la lista de invitados con métricas y bloqueo automático (RF-51, RF-52, RF-53)
     */
    /**
     * 👥 Muestra la lista de invitados con métricas y bypass de revelado manual (RF-51, RF-52, RF-53)
     */
    public function invitadosIndex(Request $request)
    {
        $token = Session::get('token_jwt');
        if (!$token) {
            return redirect('/login')->with('error', 'Sesión expirada. Por favor vuelve a ingresar.');
        }
        
        try {
            // 1. Solicitamos los datos al Backend de Node.js
            $responseGuests = Http::withToken($token)->get($this->backendUrlGuests);
            $responseEvent = Http::withToken($token)->get($this->backendUrlMyEvent);
            
            if (!$responseGuests->successful()) {
                $errorMsg = $responseGuests->json('mensaje') ?? 'Error al conectar con la lista de invitados.';
                return redirect('/anfitrion')->with('error', 'Fallo de API: ' . $errorMsg);
            }

            $guestsData = $responseGuests->json();
            $evento = $responseEvent->successful() ? $responseEvent->json() : null;

            // 2. 📊 MÁTEMATICA DE COLECCIONES (RF-51): Calculamos los estados en tiempo real
            $guestsCollection = collect($guestsData);
            $metricas = [
                'confirmados' => $guestsCollection->where('estadoAsistencia', 'confirmado')->count(),
                'rechazados'  => $guestsCollection->where('estadoAsistencia', 'rechazado')->count(),
                'pendientes'  => $guestsCollection->where('estadoAsistencia', 'pendiente')->count(),
            ];

            // 3. 🔒 REGLA CRONOLÓGICA (RF-52 y RF-53): Evaluamos la fecha
            $listaBloqueada = false;
            $reveladoManualmente = false;
            $diasRestantes = 0;

            if ($evento && isset($evento['fecha'])) {
                $fechaEvento = new \DateTime($evento['fecha']);
                $hoy = new \DateTime();
                $intervalo = $hoy->diff($fechaEvento);
                $diasRestantes = (int)$intervalo->format('%r%a');

                // Si faltan más de 2 días para la celebración, el candado se activa por defecto
                if ($diasRestantes > 2) {
                    $listaBloqueada = true;
                }
            }

            // 👁️ BYPASS DE QA Y LOGÍSTICA: Si viene el parámetro ?revelar=true, abrimos el candado
            if ($request->input('revelar') === 'true') {
                $listaBloqueada = false;
                $reveladoManualmente = true;
            }

            $datosLista = [
                'listaBloqueada'      => $listaBloqueada,
                'reveladoManualmente' => $reveladoManualmente,
                'diasRestantes'       => $diasRestantes,
                'invitados'           => $guestsData
            ];

            return view('anfitrion.invitados', compact('evento', 'metricas', 'datosLista'));

        } catch (Exception $e) {
            return redirect('/anfitrion')->with('error', 'Fallo crítico de conexión física con el backend: ' . $e->getMessage());
        }
    }

    /**
     * Registra un invitado individual de forma manual (RF-16)
     */
public function invitadosStore(Request $request)
{
    $token = Session::get('token_jwt');
    $datos = [
        'nombre' => $request->input('nombre'),
        'correo' => $request->input('correo')
    ];

    $response = Http::withToken($token)->post($this->backendUrlGuests, $datos);

    if ($response->successful()) {
        // 💡 AQUÍ ESTÁ EL CAMBIO:
        // Enviamos el mensaje de éxito Y la bandera 'show_balloons' en verdadero
        return redirect()->route('hosts.guests.index')
                         ->with('success', '¡Hurra! Se agregó un invitado más al club de los futuros tíos y tías 🍼💖')
                         ->with('show_balloons', true); 
    }
    
    return back()->with('error', 'No se pudo registrar al invitado en el servidor.');
}

    /**
     * Modifica los datos o el estado de confirmación de un asistente (RF-18 y RF-20)
     */
    public function invitadosUpdate(Request $request, $id)
    {
        $token = Session::get('token_jwt');
        
        // Soportamos tanto 'estadoAsistencia' (nuevo) como 'estadoConfirmacion' (por compatibilidad)
        $datos = $request->only(['nombre', 'correo']);
        if ($request->has('estadoAsistencia')) {
            $datos['estadoAsistencia'] = $request->input('estadoAsistencia');
        } elseif ($request->has('estadoConfirmacion')) {
            $datos['estadoAsistencia'] = $request->input('estadoConfirmacion');
        }

        $response = Http::withToken($token)->put("{$this->backendUrlGuests}/{$id}", $datos);

        if ($response->successful()) {
            return back()->with('success', 'Invitado actualizado correctamente.');
        }
        return back()->with('error', 'No se pudieron guardar las modificaciones del registro.');
    }

    /**
     * Remueve físicamente a un invitado de la lista embebida (RF-19)
     */
    public function invitadosDestroy($id)
    {
        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->delete("{$this->backendUrlGuests}/{$id}");

        if ($response->successful()) {
            return back()->with('success', 'Invitado eliminado de la lista.');
        }
        return back()->with('error', 'No se pudo eliminar el registro del servidor.');
    }

    /**
     * Parsea un archivo CSV para realizar una carga e importación masiva (RF-17)
     */
    public function invitadosImport(Request $request)
    {
        $request->validate([
            'archivo_csv' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('archivo_csv');
        $invitados = [];

        if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
            $firstLine = fgets($handle);
            $separator = (strpos($firstLine, ';') !== false) ? ';' : ',';
            rewind($handle);

            fgetcsv($handle, 1000, $separator);

            while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE) {
                if (isset($data[0]) && !empty(trim($data[0]))) {
                    $invitados[] = [
                        'nombre' => trim($data[0]),
                        'correo' => isset($data[1]) ? trim($data[1]) : ''
                    ];
                }
            }
            fclose($handle);
        }

        if (empty($invitados)) {
            return back()->with('error', 'El archivo CSV está vacío o no tiene el formato correcto.');
        }

        $token = Session::get('token_jwt');
        
        try {
            $response = Http::withToken($token)->post("{$this->backendUrlGuests}/importar", [
                'invitados' => $invitados
            ]);

            if ($response->successful()) {
                return back()->with('success', $response->json('mensaje'));
            }
            return back()->with('error', 'Error: ' . ($response->json('mensaje') ?? 'No se pudo procesar la carga masiva.'));
        } catch (Exception $e) {
            return back()->with('error', 'Fallo de conexión física con el servidor de base de datos.');
        }
    }

    /**
     * Liberar una reserva específica de un invitado del listado (RF-31)
     */
    public function liberarRegalo(Request $request, $id)
    {
        $usuario = Session::get('usuario_logueado');
        if (!isset($usuario['rol']) || $usuario['rol'] !== 'anfitrion') {
            return redirect('/')->with('error', 'Acceso denegado.');
        }

        $token = Session::get('token_jwt');

        try {
            $response = Http::withToken($token)->put("http://localhost:3000/api/regalos/{$id}/reservar", [
                'estado'    => 'disponible',
                'reservaId' => $request->input('reserva_id')
            ]);

            if ($response->successful()) {
                return back()->with('success', $response->json('mensaje') ?? 'Inventario actualizado con éxito.');
            }
            return back()->with('error', 'Error Backend: ' . ($response->json('mensaje') ?? 'Error de inventario.'));
        } catch (Exception $e) {
            return back()->with('error', 'Fallo de comunicación física con el servidor de regalos.');
        }
    }

    /**
     * Enviar una solicitud de soporte o incidencia al Administrador
     */
    public function enviarIncidencia(Request $request)
    {
        $usuario = Session::get('usuario_logueado');
        $token = Session::get('token_jwt');
        
        $datos = [
            'anfitrion' => $usuario['nombre'],
            'mensaje'   => $request->input('mensaje'),
            'fecha'     => now()->toDateTimeString()
        ];

        $response = Http::withToken($token)->post("http://localhost:3000/api/babyshower/incidencias", $datos);

        if ($response->successful()) {
            return back()->with('success', '¡Incidencia reportada con éxito! El Administrador la revisará a la brevedad.');
        }
        return back()->with('error', 'No se pudo enviar el reporte de soporte.');
    }

    /**
     * El invitado registra de forma pública su estado de asistencia (RF-47, RF-48, RF-49, RF-50)
     */
    public function registrarAsistencia(Request $request)
    {
        try {
            $response = Http::post("http://localhost:3000/api/asistencia", [
                'nombre' => $request->input('nombre_invitado'),
                'correo' => $request->input('correo_invitado'),
                'estado' => $request->input('estado_asistencia')
            ]);

            if ($response->successful()) {
                return back()->with('success', '¡Tu respuesta de asistencia fue registrada y procesada correctamente! 💌');
            }
            return back()->with('error', 'Hubo un error al registrar tu asistencia.');
        } catch (Exception $e) {
            return back()->with('error', 'Fallo crítico de red con el backend.');
        }
    }

    /**
     * Gatillar recordatorios a los que sigan en espera (RF-54)
     */
    public function enviarRecordatorioMasivo()
    {
        $token = Session::get('token_jwt');

        try {
            $response = Http::withToken($token)->post("http://localhost:3000/api/asistencia/recordatorio");
            
            if ($response->successful()) {
                return back()->with('success', $response->json('mensaje'));
            }
            return back()->with('error', 'No se pudieron despachar los recordatorios.');
        } catch (Exception $e) {
            return back()->with('error', 'Fallo de comunicación de red masiva.');
        }
    }
}