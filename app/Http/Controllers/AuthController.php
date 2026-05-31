<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session; // Agregamos Session para guardar el estado del usuario

class AuthController extends Controller
{
    // ==========================================
    // REGISTRO (RF-01)
    // ==========================================
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
{
    // 1. Capturamos los datos, incluyendo el nuevo campo 'rol'
    $datos = [
        'nombre'     => $request->input('nombre'),
        'correo'     => $request->input('correo'),
        'contrasena' => $request->input('contrasena'),
        'rol'        => $request->input('rol'), // <--- ESTO CAPTURA EL VALOR DEL RADIO BUTTON
    ];

    // 2. Enviamos al Backend (API de Node.js)
    // Asegúrate de que el backend esté listo para recibir este nuevo campo 'rol'
    $response = Http::post('http://localhost:3000/api/usuarios/registro', $datos);

    if ($response->successful()) {
        return redirect('/login')->with('success', '¡Cuenta creada con éxito! Ahora inicia sesión.');
    } else {
        // Obtenemos el error del backend
        $mensajeError = $response->json('error') ?? $response->json('mensaje') ?? 'Error al registrar';
        return back()->with('error', $mensajeError);
    }
}

    // ==========================================
    // INICIO DE SESIÓN (RF-02)
    // ==========================================
    
    // Función para mostrar la vista HTML de login
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
            $token = $response->json('token'); // <--- CAPTURAMOS EL TOKEN

            // Guardamos todo en la sesión
            Session::put('usuario_logueado', $usuario);
            Session::put('token_jwt', $token); // <--- GUARDAMOS EL TOKEN PARA DESPUÉS

            if (isset($usuario['rol']) && $usuario['rol'] === 'administrador') {
                return redirect('/admin')->with('success', '¡Bienvenido, Administrador!');
            }
            return redirect('/anfitrion')->with('success', '¡Bienvenido de vuelta!');
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
    } // <- Aquí quitamos la llave extra

    // ==========================================
    // GESTIÓN DE PERFIL (RF-04)
    // ==========================================

    // Mostrar formulario de perfil
    public function editProfile()
    {
        return view('profile.edit');
    }

    // Actualizar datos del perfil
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
    
    // ¡NUEVO!: Rescatamos el rol para enviarlo al middleware del Backend
    $rolUsuario = $usuarioLogueado['rol'] ?? 'invitado';

    // 3. Enviar la petición PUT usando el Token guardado en sesión
    $token = Session::get('token_jwt');
    $response = Http::withToken($token)->put("http://localhost:3000/api/usuarios/{$idUsuario}", $datos);

    // 4. Validar la respuesta del servidor Node.js
    if ($response->successful()) {
        
        // Actualizamos la sesión para que refleje los nuevos datos inmediatamente
        $usuarioLogueado['nombre'] = $datos['nombre'];
        $usuarioLogueado['correo'] = $datos['correo'];
        $usuarioLogueado['telefono'] = $request->input('telefono') ?? ''; // Tomado del request directamente
        Session::put('usuario_logueado', $usuarioLogueado);

        // Devolvemos al usuario a la página con un mensaje de éxito
        return back()->with('success', '¡Perfil actualizado con éxito!');
    } else {
        // 🚀 MODO DEPURACIÓN: Vamos a imprimir la respuesta cruda de Node.js
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
        // 🚀 MODO DEPURACIÓN: Vamos a imprimir la respuesta cruda de Node.js
        $errorReal = $response->body(); 
        $status = $response->status();
        
        return back()->with('error', "Error (Código $status): " . $errorReal);
    }
}

public function actualizarPassword(Request $request)
{
    // 1. Validamos que llene los campos
    $request->validate([
        'correo' => 'required|email',
        'contrasenaActual' => 'required',
        'nuevaContrasena' => 'required|min:6'
    ]);

    // 2. Enviamos la petición al Backend de Node.js
    $urlBackend = env('BACKEND_URL', 'http://localhost:3000') . '/api/usuarios/cambiar-contrasena';
    
    $response = Http::post($urlBackend, [
        'correo' => $request->correo,
        'contrasenaActual' => $request->contrasenaActual,
        'nuevaContrasena' => $request->nuevaContrasena
    ]);

    // 3. Manejamos la respuesta
    if ($response->successful()) {
        return back()->with('success', '¡Tu contraseña ha sido actualizada correctamente!');
    } else {
        $mensajeError = $response->json('mensaje') ?? 'Error al actualizar la contraseña.';
        return back()->with('error', $mensajeError);
    }
}
} 