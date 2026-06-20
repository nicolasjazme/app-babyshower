<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session; // Agregamos Session para guardar el estado del usuario

class AuthController extends Controller
{
    // ==========================================
    // REGISTRO (RF-01) - ¡ACTUALIZADO PARA INVITA APP!
    // ==========================================
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. Capturamos los datos tradicionales sumando el parámetro dinámico 'tipo_evento'
        $datos = [
            'nombre'     => $request->input('nombre'),
            'correo'     => $request->input('correo'),
            'contrasena' => $request->input('contrasena'),
            'rol'        => $request->input('rol') ?? 'organizador', // Rol por defecto si no viene marcado
            
            // 🚀 NUEVO: Captura el tipo de evento elegido en la página de inicio general
            'tipo_evento'   => $request->input('tipo_evento', 'baby_shower'), 
            'titulo_evento' => 'Mi ' . ucfirst(str_replace('_', ' ', $request->input('tipo_evento', 'celebracion')))
        ];

        // 2. Enviamos al endpoint del Backend en Node.js (actualizado a la ruta unificada)
        $response = Http::post('http://localhost:3000/api/usuarios/registro', $datos);

        if ($response->successful()) {
            return redirect('/login')->with('success', '¡Cuenta creada con éxito! Ahora inicia sesión para personalizar tu evento.');
        } else {
            // Obtenemos el error del backend
            $mensajeError = $response->json('error') ?? $response->json('mensaje') ?? 'Error al registrar';
            return back()->with('error', $mensajeError);
        }
    }

    // ==========================================
    // INICIO DE SESIÓN (RF-02) - ¡ACTUALIZADO PARA PROTOCOLO GENERAL!
    // ==========================================
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credenciales = [
            'correo' => $request->input('correo'),
            'contrasena' => $request->input('contrasena'),
        ];

        $response = Http::post('http://localhost:3000/api/usuarios/login', $credenciales);

        if ($response->successful()) {
            $usuario = $response->json('usuario');
            $token = $response->json('token'); 

            // Guardamos todo en la sesión
            $usuarioLogueado = Session::put('usuario_logueado', $usuario);
            Session::put('token_jwt', $token); 

            // 🚪 ENRUTAMIENTO POR ROLES EN INVITA APP
            if (isset($usuario['rol'])) {
                
                if ($usuario['rol'] === 'administrador') {
                    // El admin va a su panel global
                    return redirect('/admin')->with('success', '¡Bienvenido, Administrador de Invita App!');
                } 
                elseif ($usuario['rol'] === 'organizador') {
                    // El organizador va a su panel general de eventos (Rutas modificadas en el Paso 3 anterior)
                    return redirect('/eventos/dashboard')->with('success', '¡Bienvenido de vuelta a tu panel de control!');
                } 
                else {
                    // CUALQUIER OTRO (Invitados) va a la Landing/Invitación del evento correspondiente
                    return redirect('/')->with('success', '¡Bienvenido a la celebración!');
                }
            }

            return redirect('/')->with('success', '¡Bienvenido!');

        } else {
            $mensajeError = $response->json('mensaje') ?? 'Correo o contraseña incorrectos.';
            return back()->with('error', $mensajeError);
        }
    }
    

    public function logout()
    {
        // Eliminamos el "ticket" de usuario y el Token de seguridad
        Session::forget('usuario_logueado');
        Session::forget('token_jwt');

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
        // 1. Recoger SOLO los datos básicos del formulario
        $datos = [
            'nombre' => $request->input('nombre'),
            'correo' => $request->input('correo'),
        ];

        // 2. Solo sumamos la contraseña al paquete si el usuario escribió una nueva
        if ($request->filled('contrasena')) {
            $datos['contrasena'] = $request->input('contrasena');
        }

        // Recuperamos el usuario actual de la sesión para saber a quién estamos editando
        $usuarioLogueado = Session::get('usuario_logueado');
        
        // BD de MongoDB  '_id' o 'id'
        $idUsuario = $usuarioLogueado['_id'] ?? $usuarioLogueado['id'];

        // 3. Enviar la petición PUT usando el Token guardado en sesión
        $token = Session::get('token_jwt');
        $response = Http::withToken($token)->put("http://localhost:3000/api/usuarios/{$idUsuario}", $datos);

        // 4. Validar la respuesta del servidor Node.js
        if ($response->successful()) {
            
            // Actualizamos la sesión para que refleje los nuevos datos inmediatamente
            $usuarioLogueado['nombre'] = $datos['nombre'];
            $usuarioLogueado['correo'] = $datos['correo'];
            Session::put('usuario_logueado', $usuarioLogueado);

            return back()->with('success', '¡Perfil actualizado con éxito!');
        } else {
            $status = $response->status();
            $errorReal = $response->body();
            
            return back()->with('error', "Error del Backend (Código $status): " . $errorReal);
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
        $request->validate([
            'correo' => 'required|email'
        ]);

        $response = Http::post('http://localhost:3000/api/usuarios/recuperar', [
            'correo' => $request->input('correo')
        ]);

        if ($response->successful()) {
            return back()->with('success', "¡Solicitud procesada! Si el correo es correcto, se ha generado una nueva contraseña provisoria de acceso de forma privada.");
        } else {
            $errorReal = $response->body(); 
            $status = $response->status();
            
            return back()->with('error', "Error (Código $status): " . $errorReal);
        }
    }

    public function actualizarPassword(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contrasenaActual' => 'required',
            'nuevaContrasena' => 'required|min:6'
        ]);

        $urlBackend = env('BACKEND_URL', 'http://localhost:3000') . '/api/usuarios/cambiar-contrasena';
        
        $response = Http::post($urlBackend, [
            'correo' => $request->correo,
            'contrasenaActual' => $request->contrasenaActual,
            'nuevaContrasena' => $request->nuevaContrasena
        ]);

        if ($response->successful()) {
            return back()->with('success', '¡Tu contraseña ha sido actualizada correctamente!');
        } else {
            $mensajeError = $response->json('mensaje') ?? 'Error al actualizar la contraseña.';
            return back()->with('error', $mensajeError);
        }
    }
}