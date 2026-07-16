@extends('layouts.app')

@section('contenido')
@php
    // Recuperamos de la sesión el evento activo y el usuario
    $eventoActivo = Session::get('evento_activo') ?? [];
    $tipoEvento = $eventoActivo['tipo_evento'] ?? 'personalizado';
    $modulosActivos = $eventoActivo['modulos_activos'] ?? [];

    // Fallback de módulos si no es personalizado
    if (empty($modulosActivos) && $tipoEvento !== 'personalizado') {
        $plantillas = [
            'baby_shower' => ['regalos', 'itinerario', 'menu'],
            'matrimonio'  => ['regalos', 'mesas', 'itinerario', 'menu', 'galeria'],
            'cumpleanos'  => ['itinerario', 'avisos', 'musica', 'galeria'],
            'asado'       => ['cuotas', 'itinerario', 'insumos', 'musica'],
            'fiesta'      => ['itinerario', 'avisos', 'musica', 'check_in'],
        ];
        $modulosActivos = $plantillas[$tipoEvento] ?? [];
    }

    // Configuración estética rápida para las tarjetas principales
    $coloresEstilo = [
        'baby_shower' => ['borde' => 'border-blue-100', 'texto' => 'text-blue-600', 'bg' => 'bg-blue-50'],
        'matrimonio'  => ['borde' => 'border-rose-100', 'texto' => 'text-rose-600', 'bg' => 'bg-rose-50'],
        'cumpleanos'  => ['borde' => 'border-purple-100', 'texto' => 'text-purple-600', 'bg' => 'bg-purple-50'],
        'asado'       => ['borde' => 'border-orange-100', 'texto' => 'text-orange-600', 'bg' => 'bg-orange-50'],
        'fiesta'      => ['borde' => 'border-fuchsia-100', 'texto' => 'text-fuchsia-600', 'bg' => 'bg-fuchsia-50'],
        'personalizado' => ['borde' => 'border-indigo-100', 'texto' => 'text-indigo-600', 'bg' => 'bg-indigo-50'],
    ];

    $estilo = $coloresEstilo[$tipoEvento] ?? $coloresEstilo['personalizado'];
@endphp

<div class="space-y-8">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Panel de Control</span>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mt-1">
                ¡Hola, {{ Session::get('usuario_logueado')['nombre'] ?? 'Anfitrión' }}! 👋
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                @if(!empty($eventoActivo))
                    Gestiona los módulos y mantén bajo control la organización de: <strong class="text-slate-700">{{ $eventoActivo['titulo'] }}</strong>
                @else
                    Aún no tienes ninguna celebración activa. ¡Comienza creando una!
                @endif
            </p>
        </div>
        
        @if(empty($eventoActivo))
            <a href="{{ route('anfitrion.event.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-2xl transition-all shadow-md hover:shadow-lg text-sm">
                <i class="fa-solid fa-plus"></i> Crear Mi Primer Evento
            </a>
        @else
            <div class="flex gap-3">
                <a href="/e/{{ $eventoActivo['slug'] ?? '' }}" target="_blank" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-3 rounded-2xl transition-all text-sm border border-slate-200">
                    <i class="fa-solid fa-up-right-from-square"></i> Ver Invitación Pública
                </a>
                <a href="{{ route('anfitrion.event.edit', $eventoActivo['_id'] ?? $eventoActivo['id'] ?? '') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white font-bold px-5 py-3 rounded-2xl transition-all text-sm">
                    <i class="fa-solid fa-gear"></i> Editar Datos
                </a>
            </div>
        @endif
    </div>

    @if(!empty($eventoActivo))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Confirmados</span>
                    <span class="text-2xl font-black text-slate-900">12 / 45</span> </div>
            </div>

            @if(in_array('regalos', $modulosActivos))
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Regalos Reservados</span>
                        <span class="text-2xl font-black text-slate-900">8 Obsequios</span>
                    </div>
                </div>
            @elseif(in_array('insumos', $modulosActivos))
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Insumos Cubiertos</span>
                        <span class="text-2xl font-black text-slate-900">65% Listo</span>
                    </div>
                </div>
            @elseif(in_array('cuotas', $modulosActivos))
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-piggy-bank"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pozo Recaudado</span>
                        <span class="text-2xl font-black text-slate-900">$240.000</span>
                    </div>
                </div>
            @else
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Módulos Activos</span>
                        <span class="text-2xl font-black text-slate-900">{{ count($modulosActivos) }} Características</span>
                    </div>
                </div>
            @endif

            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Comunicados</span>
                    <span class="text-2xl font-black text-slate-900">2 Publicados</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Ingresos en Puerta</span>
                    <span class="text-2xl font-black text-slate-900">5 Escaneos</span>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2">
                🚀 Características Premium Habilitadas
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                
                @if(in_array('regalos', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-gift"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Lista de Deseos (Regalos)</h3>
                            <p class="text-xs text-slate-400 mb-6">Administra los regalos deseados y verifica cuáles han sido reservados.</p>
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
                            <p class="text-xs text-slate-400 mb-6">Configura datos bancarios, montos requeridos y lleva el tracker de pagos.</p>
                        </div>
                        <a href="/eventos/modulos/cuotas" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
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
                            <p class="text-xs text-slate-400 mb-6">Crea esquemas de mesas, define sillas máximas y ubica a tus invitados.</p>
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
                            <p class="text-xs text-slate-400 mb-6">Establece hitos clave de la fiesta con su respectiva hora e iconos descriptivos.</p>
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
                            <p class="text-xs text-slate-400 mb-6">Publica opciones gastronómicas y revisa restricciones o alergias de la lista.</p>
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
                            <h3 class="font-bold text-slate-800 text-base mb-1">Tablón de Avisos</h3>
                            <p class="text-xs text-slate-400 mb-6">Envía notificaciones rápidas a tus invitados con avisos normales o urgentes.</p>
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
                            <p class="text-xs text-slate-400 mb-6">Modera las sugerencias musicales (Spotify/YT) hechas por tus invitados.</p>
                        </div>
                        <a href="/eventos/modulos/musica" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
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
                            <p class="text-xs text-slate-400 mb-6">Agrega los elementos necesarios y coordina qué invitado aportará cada uno.</p>
                        </div>
                        <a href="/eventos/modulos/insumos" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
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
                            <h3 class="font-bold text-slate-800 text-base mb-1">Galería de Recuerdos</h3>
                            <p class="text-xs text-slate-400 mb-6">Muro social estilo Instagram con fotos de tus invitados y sistema de likes.</p>
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
                            <p class="text-xs text-slate-400 mb-6">Lleva de forma 100% privada tus gastos estimados y el total invertido en tiempo real.</p>
                        </div>
                        <a href="/eventos/modulos/presupuesto" class="w-full text-center py-2.5 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition-colors block border border-slate-200">
                            Entrar al Módulo
                        </a>
                    </div>
                @endif

                @if(in_array('check_in', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base mb-1">Check-In en Puerta</h3>
                            <p class="text-xs text-slate-400 mb-6">Utiliza la cámara para escanear los QR de los invitados al ingresar al recinto.</p>
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