@extends('layouts.app')

@section('contenido')
@php
    // Recuperamos el evento activo (pasado por el controlador o leído de la sesión)
    $eventoActivo = $evento ?? Session::get('evento_activo') ?? [];
    $tipoEvento = $eventoActivo['tipo_evento'] ?? 'personalizado';
    $modulosActivos = $eventoActivo['modulos_activos'] ?? [];

    // Fallback de plantillas si no es personalizado
    if (empty($modulosActivos) && $tipoEvento !== 'personalizado') {
        $plantillas = [
            'baby_shower' => ['regalos', 'itinerario', 'menu'],
            'matrimonio'  => ['regalos', 'mesas', 'itinerario', 'menu', 'galeria'],
            'cumpleanos'  => ['itinerario', 'avisos', 'musica', 'galeria'],
            'asado'       => ['cuotas', 'itinerario', 'insumos', 'musica'],
            'fiesta'      => ['itinerario', 'avisos', 'musica', 'checkin'],
        ];
        $modulosActivos = $plantillas[$tipoEvento] ?? [];
    }

    $totalInv = $metricas['total_invitados'] ?? count($invitados ?? []);
    $confirmadosInv = $metricas['confirmados'] ?? 0;
@endphp

<div class="space-y-8">
    
    {{-- CABECERA BIENVENIDA Y ACCIONES RÁPIDAS --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 md:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Panel de Control</span>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mt-1">
                ¡Hola, {{ Session::get('usuario_logueado')['nombre'] ?? 'Anfitrión' }}! 👋
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                @if(!empty($eventoActivo))
                    Gestiona los módulos y mantén bajo control la organización de: <strong class="text-slate-800">{{ $eventoActivo['titulo'] ?? 'Mi Celebración' }}</strong>
                @else
                    Aún no tienes ninguna celebración activa. ¡Comienza creando una!
                @endif
            </p>
        </div>
        
        @if(empty($eventoActivo))
            <a href="{{ route('anfitrion.event.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold px-6 py-3.5 rounded-2xl transition-all shadow-md text-xs uppercase tracking-wider">
                <i class="fa-solid fa-plus"></i> Crear Mi Primer Evento
            </a>
        @else
            <div class="flex flex-wrap gap-3">
                <a href="/e/{{ $eventoActivo['slug'] ?? '' }}" target="_blank" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-3 rounded-2xl transition-all text-xs border border-slate-200">
                    <i class="fa-solid fa-up-right-from-square"></i> Ver Invitación Pública
                </a>
                <a href="{{ route('anfitrion.event.edit', $eventoActivo['_id'] ?? $eventoActivo['id'] ?? '') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white font-bold px-5 py-3 rounded-2xl transition-all text-xs">
                    <i class="fa-solid fa-gear"></i> Editar Configuración
                </a>
            </div>
        @endif
    </div>

    @if(!empty($eventoActivo))
        {{-- TARJETAS KPI DE MÉTRICAS POLIMÓRFICAS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Métrica 1: Confirmaciones --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Confirmados</span>
                    <span class="text-2xl font-black text-slate-900">{{ $confirmadosInv }} / {{ $totalInv }}</span>
                </div>
            </div>

            {{-- Métrica 2 Dinámica: Regalos / Insumos / Cuotas --}}
            @if(in_array('regalos', $modulosActivos))
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lista de Deseos</span>
                        <span class="text-2xl font-black text-slate-900">Módulo Activo</span>
                    </div>
                </div>
            @elseif(in_array('insumos', $modulosActivos))
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Insumos (Asado)</span>
                        <span class="text-2xl font-black text-slate-900">Checklist Activa</span>
                    </div>
                </div>
            @elseif(in_array('cuotas', $modulosActivos))
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-piggy-bank"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pozo de Cuotas</span>
                        <span class="text-2xl font-black text-slate-900">La Vaca Activa</span>
                    </div>
                </div>
            @else
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Módulos Habilitados</span>
                        <span class="text-2xl font-black text-slate-900">{{ count($modulosActivos) }} Activos</span>
                    </div>
                </div>
            @endif

            {{-- Métrica 3: Tablón de Comunicados --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Comunicados</span>
                    <span class="text-2xl font-black text-slate-900">Canal Directo</span>
                </div>
            </div>

            {{-- Métrica 4: Modo Check-In --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Check-In QR</span>
                    <span class="text-2xl font-black text-slate-900">Acceso Puerta</span>
                </div>
            </div>
        </div>

        {{-- GRID DE ACCESOS RÁPIDOS A MÓDULOS ACTIVOS --}}
        <div>
            <h2 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2">
                🚀 Módulos Habilitados para tu Invitación
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                
                @if(in_array('regalos', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-gift"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Lista de Deseos (Regalos)</h3>
                            <p class="text-xs text-slate-400 mb-6">Administra los productos sugeridos y revisa las reservas de los invitados.</p>
                        </div>
                        <a href="/eventos/modulos/regalos" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

                @if(in_array('cuotas', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-piggy-bank"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Cuotas (La Vaca)</h3>
                            <p class="text-xs text-slate-400 mb-6">Configura tus datos bancarios y lleva el control de transferencias aprobadas.</p>
                        </div>
                        <a href="/eventos/modulos/cuotas" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

                @if(in_array('insumos', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-basket-shopping"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Lista de Insumos</h3>
                            <p class="text-xs text-slate-400 mb-6">Agrega los elementos necesarios (carne, hielo, bebidas) y coordina qué aportará cada uno.</p>
                        </div>
                        <a href="/eventos/modulos/insumos" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

                @if(in_array('mesas', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-chair"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Distribución de Mesas</h3>
                            <p class="text-xs text-slate-400 mb-6">Crea esquemas de mesas, limita sillas y ubica a tus invitados fácilmente.</p>
                        </div>
                        <a href="/eventos/modulos/mesas" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

                @if(in_array('itinerario', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Itinerario / Cronograma</h3>
                            <p class="text-xs text-slate-400 mb-6">Establece hitos clave con su respectiva hora e iconos representativos.</p>
                        </div>
                        <a href="/eventos/modulos/itinerario" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

                @if(in_array('menu', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Menú & Alergias</h3>
                            <p class="text-xs text-slate-400 mb-6">Publica opciones de platos y revisa las restricciones médicas declaradas.</p>
                        </div>
                        <a href="/eventos/modulos/menu" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

                @if(in_array('avisos', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Tablón de Anuncios</h3>
                            <p class="text-xs text-slate-400 mb-6">Envía noticias o avisos urgentes a la cartelera pública del evento.</p>
                        </div>
                        <a href="/eventos/modulos/avisos" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

                @if(in_array('musica', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-music"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Playlist Colaborativa</h3>
                            <p class="text-xs text-slate-400 mb-6">Modera las canciones de Spotify o YouTube sugeridas por los asistentes.</p>
                        </div>
                        <a href="/eventos/modulos/musica" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

                @if(in_array('galeria', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-pink-50 text-pink-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-images"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Muro de Recuerdos</h3>
                            <p class="text-xs text-slate-400 mb-6">Muro social estilo Instagram con fotos capturadas en vivo y sistema de likes.</p>
                        </div>
                        <a href="/eventos/modulos/galeria" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

                @if(in_array('presupuesto', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Tracker de Presupuesto</h3>
                            <p class="text-xs text-slate-400 mb-6">Control 100% privado de gastos estimados vs. pagados en tiempo real.</p>
                        </div>
                        <a href="/eventos/modulos/presupuesto" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

                @if(in_array('checkin', $modulosActivos) || in_array('check_in', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Check-In en Puerta</h3>
                            <p class="text-xs text-slate-400 mb-6">Escanea códigos QR con la cámara para registrar accesos sin duplicados.</p>
                        </div>
                        <a href="/eventos/modulos/checkin" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

            </div>
        </div>
    @endif

</div>
@endsection