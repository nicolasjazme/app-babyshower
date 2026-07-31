<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class HostController extends Controller
{
    // 🌐 URL Base de la API de Node.js
    private $backendUrl = 'http://localhost:3000/api';

    /**
     * Panel de control principal del Anfitrión / Organizador
     */
    public function index()
    {
        // 1. Recuperamos el token JWT desde la sesión de Laravel
        $token = Session::get('token_jwt');

        if (!$token) {
            return redirect('/login')->with('error', 'Sesión inválida. Por favor, vuelve a ingresar.');
        }

        try {
            // 2. Consultamos a Node.js el evento del anfitrión logueado mediante su JWT
            $response = Http::withToken($token)->get("{$this->backendUrl}/eventos/mio");

            if ($response->successful() && !empty($response->json())) {
                $evento = $response->json();

                // Guardamos el evento activo en la sesión para uso de los módulos
                Session::put('evento_activo', $evento);

                // 3. Consultamos la lista nominal de invitados del evento
                $resInvitados = Http::withToken($token)->get("{$this->backendUrl}/eventos/mio/invitados");
                $invitados = $resInvitados->successful() ? $resInvitados->json() : [];

                // 4. Calculamos métricas en tiempo real
                $totalInvitados = count($invitados);
                $confirmados = count(array_filter($invitados, function ($inv) {
                    return isset($inv['estadoConfirmacion']) && $inv['estadoConfirmacion'] === 'confirmado';
                }));
                $pendientes = count(array_filter($invitados, function ($inv) {
                    return !isset($inv['estadoConfirmacion']) || $inv['estadoConfirmacion'] === 'pendiente';
                }));
                $rechazados = count(array_filter($invitados, function ($inv) {
                    return isset($inv['estadoConfirmacion']) && $inv['estadoConfirmacion'] === 'rechazado';
                }));

                $metricas = [
                    'total_invitados' => $totalInvitados,
                    'confirmados'     => $confirmados,
                    'pendientes'      => $pendientes,
                    'rechazados'      => $rechazados,
                ];

                return view('anfitrion.index', compact('evento', 'invitados', 'metricas'));
            }

            // Si el usuario aún no tiene eventos creados, redirigir al formulario
            return redirect()->route('anfitrion.event.create')->with('info', 'Comencemos creando tu primer evento dinámico.');

        } catch (\Exception $e) {
            Log::error('Error en HostController@index: ' . $e->getMessage());
            return view('anfitrion.index')->with('error', 'El servidor de datos (Node.js) se encuentra desconectado temporalmente.');
        }
    }

    /**
     * Muestra la vista de gestión de invitados del anfitrión (RF-15)
     */
    public function invitadosIndex()
    {
        $token = Session::get('token_jwt');
        if (!$token) {
            return redirect('/login')->with('error', 'Sesión expirada.');
        }

        try {
            $response = Http::withToken($token)->get("{$this->backendUrl}/eventos/mio/invitados");
            $invitados = $response->successful() ? $response->json() : [];
            $eventoActivo = Session::get('evento_activo') ?? [];

            $metricas = [
                'confirmados' => count(array_filter($invitados, fn($i) => ($i['estadoConfirmacion'] ?? '') === 'confirmado')),
                'rechazados'  => count(array_filter($invitados, fn($i) => ($i['estadoConfirmacion'] ?? '') === 'rechazado')),
                'pendientes'  => count(array_filter($invitados, fn($i) => ($i['estadoConfirmacion'] ?? 'pendiente') === 'pendiente'))
            ];

            $datosLista = [
                'invitados' => $invitados,
                'listaBloqueada' => false
            ];

            return view('anfitrion.invitados', compact('datosLista', 'metricas', 'eventoActivo'));

        } catch (\Exception $e) {
            Log::error('Error en HostController@invitadosIndex: ' . $e->getMessage());
            return back()->with('error', 'Error al obtener la lista de invitados desde el servidor.');
        }
    }

    /**
     * Agrega un invitado manualmente (RF-16)
     */
    public function invitadosStore(Request $request)
    {
        $token = Session::get('token_jwt');
        $datos = [
            'nombre' => $request->input('nombre'),
            'correo' => $request->input('correo')
        ];

        $response = Http::withToken($token)->post("{$this->backendUrl}/eventos/mio/invitados", $datos);

        if ($response->successful()) {
            return back()->with('success', '¡Invitado registrado con éxito!')->with('show_balloons', true);
        }

        $mensajeError = $response->json('mensaje') ?? 'Error al guardar invitado.';
        return back()->with('error', $mensajeError);
    }

    /**
     * Edita datos o estado de confirmación del invitado (RF-18 y RF-20)
     */
    public function invitadosUpdate(Request $request, $id)
    {
        $token = Session::get('token_jwt');
        $datos = array_filter([
            'nombre'             => $request->input('nombre'),
            'correo'             => $request->input('correo'),
            'estadoConfirmacion' => $request->input('estadoAsistencia')
        ], fn($val) => !is_null($val) && $val !== '');

        $response = Http::withToken($token)->put("{$this->backendUrl}/eventos/mio/invitados/{$id}", $datos);

        if ($response->successful()) {
            return back()->with('success', 'Datos del invitado actualizados correctamente.');
        }

        return back()->with('error', $response->json('mensaje') ?? 'Error al actualizar invitado.');
    }

    /**
     * Elimina un invitado de la lista (RF-19)
     */
    public function invitadosDestroy($id)
    {
        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->delete("{$this->backendUrl}/eventos/mio/invitados/{$id}");

        if ($response->successful()) {
            return back()->with('success', 'Invitado removido de la lista.');
        }

        return back()->with('error', 'No se pudo eliminar al invitado.');
    }

    /**
     * Importación masiva de invitados desde un archivo CSV (RF-17)
     */
    public function invitadosImport(Request $request)
    {
        $request->validate([
            'archivo_csv' => 'required|file'
        ]);

        $file = $request->file('archivo_csv');
        $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $invitados = [];
        foreach ($lines as $index => $line) {
            // Omitir cabecera si existe
            if ($index === 0 && (str_contains(strtolower($line), 'nombre') || str_contains(strtolower($line), 'correo'))) {
                continue;
            }

            $data = str_getcsv($line);
            if (!empty($data[0])) {
                $invitados[] = [
                    'nombre' => trim($data[0]),
                    'correo' => isset($data[1]) ? trim($data[1]) : ''
                ];
            }
        }

        if (empty($invitados)) {
            return back()->with('error', 'El archivo no contiene registros válidos.');
        }

        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->post("{$this->backendUrl}/eventos/mio/invitados/importar", [
            'invitados' => $invitados
        ]);

        if ($response->successful()) {
            return back()->with('success', $response->json('mensaje') ?? '¡Carga masiva completada exitosamente!');
        }

        return back()->with('error', $response->json('mensaje') ?? 'Error al procesar la importación masiva.');
    }

    /**
     * Envía recordatorios masivos por correo (RF-54)
     */
    public function enviarRecordatorioMasivo()
    {
        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->post("{$this->backendUrl}/asistencia/recordatorio");

        if ($response->successful()) {
            return back()->with('success', $response->json('mensaje') ?? 'Recordatorios despachados por correo.');
        }

        return back()->with('error', $response->json('mensaje') ?? 'Error al enviar recordatorios.');
    }

    /**
     * Registrar respuesta de asistencia pública desde la Landing
     */
    public function registrarAsistencia(Request $request)
    {
        $datos = [
            'nombre' => $request->input('nombre'),
            'correo' => $request->input('correo'),
            'estado' => $request->input('estado', 'confirmado')
        ];

        $response = Http::post("{$this->backendUrl}/asistencia", $datos);

        if ($response->successful()) {
            return back()->with('success', '¡Asistencia registrada correctamente!');
        }

        return back()->with('error', 'Error al registrar la asistencia.');
    }

    /**
     * Webhook de sincronización de estados emitidos por Node.js
     */
    public function sincronizarDesdeNode(Request $request)
    {
        Log::info('Webhook de sincronización recibido', $request->all());
        return response()->json(['status' => 'Sincronizado']);
    }

    /**
     * Libera la reserva de un artículo o regalo (Anfitrión)
     */
    public function liberarItem(Request $request, $id)
    {
        $token = Session::get('token_jwt');

        $response = Http::withToken($token)->put("{$this->backendUrl}/regalos/{$id}/reservar", [
            'estado'    => 'disponible',
            'reservaId' => $request->input('reserva_id')
        ]);

        if ($response->successful()) {
            return back()->with('success', 'Artículo liberado con éxito.');
        }

        return back()->with('error', $response->json('mensaje') ?? 'Error al liberar el artículo.');
    }

    /**
     * Envía un ticket de soporte o incidencia al administrador
     */
    public function enviarIncidencia(Request $request)
    {
        $usuario = Session::get('usuario_logueado');
        $datos = [
            'anfitrion' => $usuario['nombre'] ?? 'Anfitrión',
            'mensaje'   => $request->input('mensaje')
        ];

        $response = Http::post("{$this->backendUrl}/eventos/incidencias", $datos);

        if ($response->successful()) {
            return back()->with('success', 'Ticket de soporte enviado con éxito.');
        }

        return back()->with('error', 'Error al enviar reporte.');
    }
}