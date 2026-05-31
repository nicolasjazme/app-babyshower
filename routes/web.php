<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\BabyShowerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\InvitacionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================================================
// 1. RUTAS PÚBLICAS (Acceso Libre Global - No requieren estar logueado)
// ==========================================================================
Route::get('/', [BabyShowerController::class, 'index'])->name('home');
Route::get('/baby-shower', [BabyShowerController::class, 'index']);
Route::get('/recuperar-contrasena', [AuthController::class, 'showRecuperarForm']);
Route::post('/recuperar-contrasena', [AuthController::class, 'recuperarPassword']);

// Autenticación de Usuarios (Anfitriones y Administradores)
Route::get('/registro', [AuthController::class, 'showRegisterForm']);
Route::post('/registro', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);

// RF-11: Enlace único público para invitados
Route::get('/e/{slug}', [EventController::class, 'showPublic'])->name('event.public');
Route::post('/asistencia/registrar', [HostController::class, 'registrarAsistencia'])->name('asistencia.store');
Route::get('/regalos', [BabyShowerController::class, 'index'])->name('regalos.index');

// Ruta mágica para invitados (SIN LOGIN)
Route::get('/invitacion/{token}', [InvitacionController::class, 'mostrarInvitacion'])->name('invitacion.magica');

// 🔥 Endpoint de Sincronización PÚBLICO (Para que Node pueda avisar libremente)
Route::post('/api/sincronizar-estado', [HostController::class, 'sincronizarDesdeNode']);

// ==========================================================================
// 2. RUTAS PROTEGIDAS (Requieren Sesión Iniciada y pasar por el Middleware)
// ==========================================================================
Route::middleware(['auth.custom'])->group(function () {
    
    // --- Cuenta y Perfil ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/perfil', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::put('/perfil', [AuthController::class, 'updateProfile'])->name('profile.update');
    // Ruta para procesar el cambio de contraseña
    Route::post('/perfil/cambiar-contrasena', [AuthController::class, 'actualizarPassword'])->name('perfil.password.update');
    Route::get('/perfil', function () {
    return view('profile.edit'); // O la ruta donde tengas guardado tu edit.blade.php
});

    // 🔥 Interacción de Invitados protegida (Pide Login para reservar)
    Route::post('/reservar-regalo', [BabyShowerController::class, 'reserve'])->name('gifts.reserve');


    // --- 🍼 PANEL EXCLUSIVO DEL ANFITRIÓN (RF-13 y RF-14) ---
    Route::get('/anfitrion', [HostController::class, 'index'])->name('anfitrion.index');
    Route::get('/baby-shower/nuevo', [EventController::class, 'create'])->name('event.create');
    Route::post('/baby-shower/nuevo', [EventController::class, 'store'])->name('event.store');
    Route::get('/baby-shower/{id}/editar', [EventController::class, 'edit'])->name('event.edit');
    Route::put('/baby-shower/{id}', [EventController::class, 'update'])->name('event.update');

    // --- 🖥️ PANEL EXCLUSIVO DEL ADMINISTRADOR ---
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/baby-showers', [AdminController::class, 'listBabyShowers'])->name('admin.babyshowers.list');
    Route::post('/admin/baby-showers/{id}/estado', [AdminController::class, 'updateStatus'])->name('admin.babyshowers.status');

    // --- ACCIONES COMPARTIDAS DE REGALOS (Admin y Anfitrión) ---
    Route::post('/admin/regalos', [AdminController::class, 'store'])->name('gifts.store');
    Route::post('/admin/regalos/liberar/{id}', [AdminController::class, 'restore'])->name('gifts.restore');
    Route::delete('/admin/regalos/{id}', [AdminController::class, 'destroy'])->name('gifts.destroy');
    Route::put('/admin/regalos/{id}', [AdminController::class, 'update'])->name('admin.regalos.update');

    // ========================================================
    // 👥 GESTIÓN DE INVITADOS DEL ANFITRIÓN (Módulo Interno Privado)
    // ========================================================
    Route::get('/anfitrion/invitados', [HostController::class, 'invitadosIndex'])->name('hosts.guests.index');
    Route::post('/anfitrion/invitados', [HostController::class, 'invitadosStore'])->name('hosts.guests.store');
    Route::put('/anfitrion/invitados/{id}', [HostController::class, 'invitadosUpdate'])->name('hosts.guests.update');
    Route::delete('/anfitrion/invitados/{id}', [HostController::class, 'invitadosDestroy'])->name('hosts.guests.destroy');
    Route::post('/anfitrion/invitados/importar', [HostController::class, 'invitadosImport'])->name('hosts.guests.import');
    Route::post('/anfitrion/invitados/recordatorio', [HostController::class, 'enviarRecordatorioMasivo'])->name('hosts.invitados.remind');

    // --- LOGÍSTICA EXCLUSIVA Y SOPORTE ---
    Route::post('/anfitrion/regalos/{id}/liberar', [HostController::class, 'liberarRegalo'])->name('hosts.gifts.restore');
    Route::post('/admin/regalos/{id}/liberar-reserva', [AdminController::class, 'liberarRegalo'])->name('admin.regalos.liberar_reserva');
    Route::post('/anfitrion/incidencias', [HostController::class, 'enviarIncidencia'])->name('incidencias.store');
    Route::post('/admin/incidencias/{id}/completar', [AdminController::class, 'completarIncidencia'])->name('incidencias.complete');
});

