@extends('layouts.app')

@section('contenido')
@php
    // Recuperamos el evento activo
    $eventoActivo = $evento ?? Session::get('evento_activo') ?? [];
    $tipoEvento = $eventoActivo['tipo_evento'] ?? 'personalizado';
    $modulosActivos = $eventoActivo['modulos_activos'] ?? [];

    // Fallback de plantillas sincronizado con MongoDB
    if (empty($modulosActivos) && $tipoEvento !== 'personalizado') {
        $plantillas = [
            'babyshower' => ['regalos', 'itinerario', 'menu'],
            'matrimonio' => ['regalos', 'mesas', 'itinerario', 'menu', 'galeria'],
            'cumpleanos' => ['itinerario', 'avisos', 'musica', 'galeria'],
            'asado'      => ['cuota', 'itinerario', 'insumos', 'musica'],
            'fiesta'     => ['itinerario', 'avisos', 'musica', 'checkin'],
        ];
        $modulosActivos = $plantillas[$tipoEvento] ?? [];
    }

    $totalInv = $metricas['total_invitados'] ?? count($invitados ?? []);
    $confirmadosInv = $metricas['confirmados'] ?? 0;
@endphp

<div class="space-y-8 animate-fade-in-up">
    
    {{-- CABECERA BIENVENIDA Y ACCIONES RÁPIDAS --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden">
        
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-indigo-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>

        <div class="relative z-10">
            <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase tracking-widest mb-3">
                Panel de Control VIP
            </span>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
                ¡Hola, {{ Session::get('usuario_logueado')['nombre'] ?? 'Anfitrión' }}! 👋
            </h1>
            <p class="text-slate-500 text-sm mt-2 max-w-lg">
                @if(!empty($eventoActivo))
                    Estás gestionando la celebración: <strong class="text-indigo-600">{{ $eventoActivo['titulo'] ?? 'Mi Evento' }}</strong>. 
                    Revisa las métricas y configura tus módulos interactivos.
                @else
                    Aún no tienes ninguna celebración activa. ¡Comienza creando una para sorprender a tus invitados!
                @endif
            </p>
        </div>
        
        <div class="relative z-10 shrink-0">
            @if(empty($eventoActivo))
                <a href="/eventos/crear" class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-extrabold px-8 py-4 rounded-2xl transition-all shadow-lg shadow-indigo-200 transform hover:-translate-y-1">
                    <i class="fa-solid fa-wand-magic-sparkles text-lg"></i> 
                    <span>Crear Mi Primer Evento</span>
                </a>
            @else
                <div class="flex flex-col sm:flex-row gap-3">
                    {{-- Botón para ver la invitación actual --}}
                    <a href="/e/{{ $eventoActivo['slug'] ?? '' }}" target="_blank" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-bold px-6 py-3.5 rounded-2xl transition-all border border-slate-200 shadow-sm hover:shadow">
                        <i class="fa-solid fa-eye text-indigo-500"></i> Ver Invitación
                    </a>
                    
                    {{-- Botón para editar el evento actual --}}
                    <a href="/eventos/{{ $eventoActivo['_id'] ?? $eventoActivo['id'] ?? '' }}/editar" class="inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold px-6 py-3.5 rounded-2xl transition-all shadow-md">
                        <i class="fa-solid fa-gear"></i> Ajustes
                    </a>

                    {{-- 🌟 EL BOTÓN ESTRELLA: Crear un evento nuevo siempre visible --}}
                    <a href="/eventos/crear" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold px-6 py-3.5 rounded-2xl transition-all shadow-md transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-plus"></i> Nuevo Evento
                    </a>
                </div>
            @endif
        </div>

    @if(empty($eventoActivo))
        {{-- ESTADO VACÍO (EMPTY STATE) --}}
        <a href="/eventos/crear" class="group flex flex-col items-center justify-center py-16 px-4 bg-slate-50/50 border-2 border-dashed border-slate-300 rounded-[2rem] hover:bg-indigo-50/50 hover:border-indigo-400 transition-all cursor-pointer">
            <div class="w-20 h-20 rounded-full bg-white shadow-sm flex items-center justify-center text-indigo-400 text-3xl mb-4 group-hover:scale-110 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-500">
                <i class="fa-solid fa-plus"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-700 group-hover:text-indigo-900 transition-colors">Configurar Nueva Celebración</h3>
            <p class="text-sm text-slate-500 mt-2 text-center max-w-md">Diseña invitaciones hermosas para Baby Showers, Matrimonios, Cumpleaños o Asados en un par de clics.</p>
        </a>
    @else
        {{-- TARJETAS KPI DE MÉTRICAS POLIMÓRFICAS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Métrica 1: Confirmaciones --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-0.5">Confirmados</span>
                    <span class="text-3xl font-black text-slate-900">{{ $confirmadosInv }} <span class="text-sm text-slate-400 font-bold">/ {{ $totalInv }}</span></span>
                </div>
            </div>

            {{-- Métrica 2 Dinámica: Regalos / Insumos / Cuotas --}}
            @if(in_array('regalos', $modulosActivos))
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-2xl shrink-0">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-0.5">Lista de Deseos</span>
                        <span class="text-xl font-black text-slate-900">Activa</span>
                    </div>
                </div>
            @elseif(in_array('insumos', $modulosActivos))
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-2xl shrink-0">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-0.5">Insumos</span>
                        <span class="text-xl font-black text-slate-900">En uso</span>
                    </div>
                </div>
            @elseif(in_array('cuota', $modulosActivos))
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl shrink-0">
                        <i class="fa-solid fa-piggy-bank"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-0.5">Pozo Colectivo</span>
                        <span class="text-xl font-black text-slate-900">La Vaca</span>
                    </div>
                </div>
            @else
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-2xl shrink-0">
                        <i class="fa-solid fa-cubes"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-0.5">Módulos</span>
                        <span class="text-2xl font-black text-slate-900">{{ count($modulosActivos) }} <span class="text-sm text-slate-400 font-bold">Activos</span></span>
                    </div>
                </div>
            @endif

            {{-- Métrica 3: Tablón de Comunicados --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-500 flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-0.5">Notificaciones</span>
                    <span class="text-xl font-black text-slate-900">Anuncios</span>
                </div>
            </div>

            {{-- Métrica 4: Modo Check-In --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 rounded-2xl bg-cyan-50 text-cyan-500 flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-0.5">Control Acceso</span>
                    <span class="text-xl font-black text-slate-900">Check-In</span>
                </div>
            </div>
        </div>

        {{-- GRID DE ACCESOS RÁPIDOS A MÓDULOS ACTIVOS --}}
        <div class="mt-12">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-lg shadow-md">
                    <i class="fa-solid fa-shapes"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-900">Gestionar Módulos Activos</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                
                @if(in_array('regalos', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-100 to-pink-100 text-rose-600 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-gift"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg mb-2">Lista de Deseos</h3>
                            <p class="text-sm text-slate-500 mb-6">Administra tu catálogo de regalos y revisa quién ha reservado.</p>
                        </div>
                        <a href="/eventos/modulos/regalos" class="w-full text-center py-3 px-4 bg-slate-50 hover:bg-rose-50 text-slate-700 hover:text-rose-700 font-bold text-sm rounded-xl transition-colors block border border-slate-200 hover:border-rose-200">
                            Gestionar Regalos
                        </a>
                    </div>
                @endif

                @if(in_array('cuota', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-100 text-emerald-600 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-piggy-bank"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg mb-2">La Vaca (Cuotas)</h3>
                            <p class="text-sm text-slate-500 mb-6">Revisa los aportes bancarios y el pozo acumulado para la fiesta.</p>
                        </div>
                        <a href="/eventos/modulos/cuotas" class="w-full text-center py-3 px-4 bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 font-bold text-sm rounded-xl transition-colors block border border-slate-200 hover:border-emerald-200">
                            Ver Aportes
                        </a>
                    </div>
                @endif

                @if(in_array('insumos', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-100 to-amber-100 text-orange-600 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-basket-shopping"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg mb-2">Lista de Insumos</h3>
                            <p class="text-sm text-slate-500 mb-6">Organiza el asado. Revisa quién llevará el hielo, la carne y los bebestibles.</p>
                        </div>
                        <a href="/eventos/modulos/insumos" class="w-full text-center py-3 px-4 bg-slate-50 hover:bg-orange-50 text-slate-700 hover:text-orange-700 font-bold text-sm rounded-xl transition-colors block border border-slate-200 hover:border-orange-200">
                            Revisar Insumos
                        </a>
                    </div>
                @endif

                @if(in_array('mesas', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-100 to-blue-100 text-indigo-600 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-chair"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg mb-2">Mesas</h3>
                            <p class="text-sm text-slate-500 mb-6">Distribuye a los invitados confirmados en sus asientos.</p>
                        </div>
                        <a href="/eventos/modulos/mesas" class="w-full text-center py-3 px-4 bg-slate-50 hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 font-bold text-sm rounded-xl transition-colors block border border-slate-200 hover:border-indigo-200">
                            Asignar Mesas
                        </a>
                    </div>
                @endif

                @if(in_array('itinerario', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-100 text-amber-600 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg mb-2">Itinerario</h3>
                            <p class="text-sm text-slate-500 mb-6">Establece los horarios clave del evento para los invitados.</p>
                        </div>
                        <a href="/eventos/modulos/itinerario" class="w-full text-center py-3 px-4 bg-slate-50 hover:bg-amber-50 text-slate-700 hover:text-amber-700 font-bold text-sm rounded-xl transition-colors block border border-slate-200 hover:border-amber-200">
                            Editar Cronograma
                        </a>
                    </div>
                @endif

                @if(in_array('menu', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-100 to-cyan-100 text-teal-600 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg mb-2">Menú & Alergias</h3>
                            <p class="text-sm text-slate-500 mb-6">Revisa las restricciones alimentarias enviadas por los invitados.</p>
                        </div>
                        <a href="/eventos/modulos/menu" class="w-full text-center py-3 px-4 bg-slate-50 hover:bg-teal-50 text-slate-700 hover:text-teal-700 font-bold text-sm rounded-xl transition-colors block border border-slate-200 hover:border-teal-200">
                            Ver Opciones
                        </a>
                    </div>
                @endif

                @if(in_array('avisos', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-100 to-blue-100 text-sky-600 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg mb-2">Avisos</h3>
                            <p class="text-sm text-slate-500 mb-6">Publica anuncios urgentes en la cartelera digital.</p>
                        </div>
                        <a href="/eventos/modulos/avisos" class="w-full text-center py-3 px-4 bg-slate-50 hover:bg-sky-50 text-slate-700 hover:text-sky-700 font-bold text-sm rounded-xl transition-colors block border border-slate-200 hover:border-sky-200">
                            Publicar Aviso
                        </a>
                    </div>
                @endif

                @if(in_array('musica', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-100 to-purple-100 text-violet-600 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-music"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg mb-2">Playlist</h3>
                            <p class="text-sm text-slate-500 mb-6">Descubre las canciones que los asistentes quieren escuchar.</p>
                        </div>
                        <a href="/eventos/modulos/musica" class="w-full text-center py-3 px-4 bg-slate-50 hover:bg-violet-50 text-slate-700 hover:text-violet-700 font-bold text-sm rounded-xl transition-colors block border border-slate-200 hover:border-violet-200">
                            Ver Sugerencias
                        </a>
                    </div>
                @endif

                @if(in_array('galeria', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-pink-100 to-rose-100 text-pink-600 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-images"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg mb-2">Galería</h3>
                            <p class="text-sm text-slate-500 mb-6">Modera y disfruta las fotos capturadas durante la celebración.</p>
                        </div>
                        <a href="/eventos/modulos/galeria" class="w-full text-center py-3 px-4 bg-slate-50 hover:bg-pink-50 text-slate-700 hover:text-pink-700 font-bold text-sm rounded-xl transition-colors block border border-slate-200 hover:border-pink-200">
                            Moderar Fotos
                        </a>
                    </div>
                @endif

                @if(in_array('presupuesto', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slate-200 to-slate-300 text-slate-700 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg mb-2">Presupuesto</h3>
                            <p class="text-sm text-slate-500 mb-6">Maneja internamente los gastos de tu evento.</p>
                        </div>
                        <a href="/eventos/modulos/presupuesto" class="w-full text-center py-3 px-4 bg-slate-50 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-colors block border border-slate-200 hover:border-slate-300">
                            Ver Gastos
                        </a>
                    </div>
                @endif

                @if(in_array('checkin', $modulosActivos) || in_array('check_in', $modulosActivos))
                    <div class="group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between hover:-translate-y-1 duration-300">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-100 to-blue-100 text-cyan-600 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg mb-2">Check-In</h3>
                            <p class="text-sm text-slate-500 mb-6">Escanea códigos QR en puerta y lleva registro de quién ingresa.</p>
                        </div>
                        <a href="/eventos/modulos/checkin" class="w-full text-center py-3 px-4 bg-slate-50 hover:bg-cyan-50 text-slate-700 hover:text-cyan-700 font-bold text-sm rounded-xl transition-colors block border border-slate-200 hover:border-cyan-200">
                            Iniciar Escáner
                        </a>
                    </div>
                @endif

            </div>
        </div>
    @endif
</div>

<style>
    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }
</style>
@endsection