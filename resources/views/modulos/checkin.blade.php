@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;

    // Fallback de datos simulando la respuesta de la API del backend
    $invitados = $invitadosLista ?? [];
    
    // Cálculos para las métricas de la puerta
    $totalConfirmados = collect($invitados)->where('estado_rsvp', 'confirmado')->count() ?: 120; // 120 de prueba
    $ingresados = collect($invitados)->where('check_in', true)->all();
    $totalIngresados = count($ingresados) ?: 45; // 45 de prueba
    $faltan = $totalConfirmados - $totalIngresados;
@endphp

<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm relative overflow-hidden">
        
        <div class="absolute top-0 right-0 bg-rose-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg flex items-center gap-1 animate-pulse">
            <i class="fa-solid fa-circle text-[6px]"></i> Modo Puerta
        </div>

        <div>
            <div class="flex items-center gap-3 mt-2 sm:mt-0">
                <span class="text-2xl">🎟️</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Control de Check-In</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Escanea el código QR de tus invitados al ingresar para validar su acceso y evitar duplicados.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Confirmados</span>
                <span class="text-2xl font-black text-slate-900">{{ $totalConfirmados }}</span>
            </div>
        </div>

        <div class="bg-cyan-50 p-6 rounded-3xl border border-cyan-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500 text-white flex items-center justify-center text-xl shadow-md">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-cyan-600 uppercase tracking-wider block">Ya Ingresaron</span>
                <span class="text-2xl font-black text-cyan-900">{{ $totalIngresados }}</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-person-walking-arrow-right"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Faltan por llegar</span>
                <span class="text-2xl font-black text-slate-900">{{ $faltan }}</span>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-slate-900 p-6 rounded-3xl border border-slate-800 shadow-lg flex flex-col items-center justify-center text-center min-h-[400px] relative overflow-hidden">
            
            <h2 class="text-lg font-bold text-white mb-2 flex items-center gap-2 z-10">
                <i class="fa-solid fa-camera"></i> Escáner de Acceso
            </h2>
            <p class="text-xs text-slate-400 mb-8 z-10">Apunta la cámara al código QR de la invitación.</p>

            <div class="w-64 h-64 border-2 border-dashed border-cyan-500 rounded-3xl relative flex items-center justify-center bg-slate-800 z-10 group">
                <div class="absolute w-full h-1 bg-cyan-400/50 shadow-[0_0_15px_rgba(34,211,238,0.8)] top-0 left-0 animate-[scan_2s_ease-in-out_infinite]"></div>
                
                <button class="bg-cyan-500 hover:bg-cyan-400 text-white font-bold py-3 px-6 rounded-2xl transition-all shadow-lg text-sm z-20 group-hover:scale-105">
                    Activar Cámara
                </button>
            </div>
            
            <p class="text-[10px] text-slate-500 mt-6 z-10">
                *Requiere permisos de navegador para acceder a la cámara.
            </p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col">
            
            <div class="flex border-b border-slate-100 mb-4">
                <button class="px-4 py-2 text-sm font-bold text-cyan-600 border-b-2 border-cyan-600">Historial Reciente</button>
                <button class="px-4 py-2 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Búsqueda Manual</button>
            </div>

            <div class="flex-1 max-h-[350px] overflow-y-auto pr-2 space-y-3">
                
                <div class="relative mb-4 hidden">
                    <input type="text" placeholder="Buscar invitado por nombre..." class="w-full bg-slate-50 border border-slate-200 text-sm rounded-xl px-4 py-2.5 pl-10 focus:outline-none focus:border-cyan-400">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400"></i>
                </div>

                <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <strong class="block text-sm text-slate-800">María Ignacia Torres</strong>
                            <span class="block text-[10px] text-slate-500 font-medium">Ingresó a las 22:15 hrs</span>
                        </div>
                    </div>
                    <span class="text-[9px] font-black px-2 py-1 bg-emerald-200 text-emerald-800 rounded uppercase">Acceso Autorizado</span>
                </div>

                <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <strong class="block text-sm text-slate-800">Familia González (3)</strong>
                            <span class="block text-[10px] text-slate-500 font-medium">Ingresó a las 22:10 hrs</span>
                        </div>
                    </div>
                    <span class="text-[9px] font-black px-2 py-1 bg-emerald-200 text-emerald-800 rounded uppercase">Acceso Autorizado</span>
                </div>

                <div class="p-3 bg-rose-50 border border-rose-100 rounded-2xl flex items-center justify-between opacity-80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                        <div>
                            <strong class="block text-sm text-slate-800">Roberto Carlos Vera</strong>
                            <span class="block text-[10px] text-rose-500 font-bold">Intento duplicado (Ya ingresó 21:05)</span>
                        </div>
                    </div>
                    <span class="text-[9px] font-black px-2 py-1 bg-rose-200 text-rose-800 rounded uppercase">Denegado</span>
                </div>

            </div>
            
            <div class="mt-4 border-t border-slate-100 pt-3 text-center">
                <span class="text-[10px] text-slate-400 font-medium">
                    <i class="fa-solid fa-shield-halved"></i> Sistema protegido contra duplicidad de entradas.
                </span>
            </div>
        </div>

    </div>
</div>

<style>
    @keyframes scan {
        0%, 100% { top: 0%; }
        50% { top: 100%; }
    }
</style>
@endsection