<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // ==========================================
    // FORMULARIO DE REGISTRO (RF-01)
    // ==========================================
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $nombre     = $request->input('nombre');
        $correo     = $request->input('correo') ?? $request->input('email');
        $contrasena = $request->input('contrasena') ?? $request->input('password');

        if (empty($nombre) || empty($correo) || empty($contrasena)) {
            return back()->with('error', 'Todos los campos son obligatorios.');
        }

        $datos = [
            'nombre'        => $nombre,
            'correo'        => $correo,
            'email'         => $correo,
            'contrasena'    => $contrasena,
            'password'      => $contrasena,
            'rol'           => $request->input('rol') ?? 'anfitrion',
            'tipo_evento'   => $request->input('tipo_evento', 'general'), 
            'titulo_evento' => 'Mi ' . ucfirst(str_replace('_', ' ', $request->input('tipo_evento', 'celebracion')))
        ];

        try {
            $response = Http::timeout(5)->post('http://localhost:3000/api/usuarios/registro', $datos);
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo conectar con el servidor Backend (Node.js en puerto 3000).');
        }

        if ($response->successful()) {
            return redirect('/login')->with('success', '¡Cuenta creada con éxito! Inicia sesión para personalizar tu evento.');
        } else {
            $json = $response->json();
            $mensajeError = is_array($json) 
                ? ($json['mensaje'] ?? $json['error'] ?? $json['message'] ?? 'Error al registrar la cuenta.')
                : 'Error al procesar el registro en el servidor.';
                
            return back()->with('error', $mensajeError);
        }
    }

    // ==========================================
    // FORMULARIO DE LOGIN (RF-02)
    // ==========================================
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $correo     = trim($request->input('correo') ?? $request->input('email') ?? '');
        $contrasena = trim($request->input('contrasena') ?? $request->input('password') ?? '');

        if (empty($correo) || empty($contrasena)) {
            return back()->with('error', 'Por favor ingresa correo y contraseña.');
        }

        $credenciales = [
            'correo'     => $correo,
            'email'      => $correo,
            'contrasena' => $contrasena,
            'password'   => $contrasena
        ];

        try {
            $response = Http::timeout(5)->post('http://localhost:3000/api/usuarios/login', $credenciales);
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo conectar con el servidor Backend (Node.js en puerto 3000).');
        }

        if ($response->successful()) {
            $usuario = $response->json('usuario');
            $token   = $response->json('token'); 

            Session::put('usuario_logueado', $usuario);
            Session::put('token_jwt', $token); 

            $rol = strtolower($usuario['rol'] ?? 'anfitrion');

            if ($rol === 'administrador' || $rol === 'admin') {
                return redirect('/admin')->with('success', '¡Bienvenido, Administrador!');
            } 
            elseif ($rol === 'organizador' || $rol === 'anfitrion') {
                try {
                    $resEvento = Http::withToken($token)->timeout(5)->get('http://localhost:3000/api/eventos/mi-evento');
                    if ($resEvento->successful() && !empty($resEvento->json())) {
                        Session::put('evento_activo', $resEvento->json());
                        return redirect('/eventos/dashboard')->with('success', '¡Bienvenido de vuelta!');
                    }
                } catch (\Exception $e) {}

                Session::forget('evento_activo');
                return redirect('/eventos/crear')->with('info', '¡Bienvenido! Crea tu primer evento para comenzar.');
            } 
            else {
                return redirect('/')->with('success', '¡Bienvenido!');
            }

        } else {
            $json = $response->json();
            $mensajeError = is_array($json) 
                ? ($json['mensaje'] ?? $json['error'] ?? $json['message'] ?? 'Correo o contraseña incorrectos.')
                : 'Correo o contraseña incorrectos.';

            return back()->with('error', $mensajeError);
        }
    }

    // ==========================================
    // CERRAR SESIÓN
    // ==========================================
    public function logout()
    {
        Session::forget('usuario_logueado');
        Session::forget('token_jwt');
        Session::forget('evento_activo');

        return redirect('/login')->with('success', 'Has cerrado sesión de forma segura. ¡Hasta pronto!');
    }

    // ==========================================
    // GESTIÓN DE PERFIL (RF-04)
    // ==========================================
    public function editProfile()
    {
        return view('profile.edit');
    }

    public function updateProfile(Request $request)
    {
        $datos = [
            'nombre' => $request->input('nombre'),
            'correo' => $request->input('correo') ?? $request->input('email'),
        ];

        if ($request->filled('contrasena') || $request->filled('password')) {
            $datos['contrasena'] = $request->input('contrasena') ?? $request->input('password');
        }

        $usuarioLogueado = Session::get('usuario_logueado');
        $idUsuario = is_array($usuarioLogueado) ? ($usuarioLogueado['_id'] ?? $usuarioLogueado['id'] ?? null) : null;

        if (!$idUsuario) {
            return back()->with('error', 'Sesión expirada. Por favor vuelve a ingresar.');
        }

        $token = Session::get('token_jwt');

        try {
            $response = Http::withToken($token)->put("http://localhost:3000/api/usuarios/{$idUsuario}", $datos);
        } catch (\Exception $e) {
            return back()->with('error', 'Error de conexión al actualizar el perfil.');
        }

        if ($response->successful()) {
            if (is_array($usuarioLogueado)) {
                $usuarioLogueado['nombre'] = $datos['nombre'];
                $usuarioLogueado['correo'] = $datos['correo'];
                Session::put('usuario_logueado', $usuarioLogueado);
            }

            return back()->with('success', '¡Perfil actualizado con éxito!');
        } else {
            $status = $response->status();
            return back()->with('error', "No se pudo actualizar el perfil (Código de error: $status).");
        }
    }

    // ==========================================
    // RECUPERAR CONTRASEÑA (RF-03)
    // ==========================================
    public function showRecuperarForm()
    {
        return view('auth.recuperar'); 
    }

    public function recuperarPassword(Request $request)
    {
        $correo = $request->input('correo') ?? $request->input('email');

        if (empty($correo)) {
            return back()->with('error', 'Por favor ingresa tu correo electrónico.');
        }

        try {
            $response = Http::post('http://localhost:3000/api/usuarios/recuperar', [
                'correo' => $correo,
                'email'  => $correo
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al conectar con el servidor de correos.');
        }

        if ($response->successful()) {
            return back()->with('success', "¡Solicitud procesada! Si el correo existe en el sistema, recibirás una clave provisoria.");
        } else {
            return back()->with('error', "No se pudo procesar la solicitud de recuperación.");
        }
    }

    public function actualizarPassword(Request $request)
    {
        $correo = $request->input('correo') ?? $request->input('email');
        $actual = $request->input('contrasenaActual');
        $nueva  = $request->input('nuevaContrasena');

        if (empty($correo) || empty($actual) || empty($nueva)) {
            return back()->with('error', 'Todos los campos son obligatorios.');
        }

        try {
            $response = Http::post('http://localhost:3000/api/usuarios/cambiar-contrasena', [
                'correo'           => $correo,
                'email'            => $correo,
                'contrasenaActual' => $actual,
                'nuevaContrasena'  => $nueva
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error de conexión con el servidor.');
        }

        if ($response->successful()) {
            return back()->with('success', '¡Tu contraseña ha sido actualizada correctamente!');
        } else {
            $json = $response->json();
            $msg = is_array($json) ? ($json['mensaje'] ?? 'Error al actualizar la contraseña.') : 'Error al actualizar la contraseña.';
            return back()->with('error', $msg);
        }
    }
}