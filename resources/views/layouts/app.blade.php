@php
    // 1. Recuperamos de la sesión el evento activo y el usuario
    $eventoActivo = Session::get('evento_activo') ?? [];
    $tipoEvento = $eventoActivo['tipo_evento'] ?? 'personalizado';
    $modulosActivos = $eventoActivo['modulos_activos'] ?? []; 
    
    // Si no es personalizado y no tiene módulos explícitos, le inyectamos los predefinidos por plantilla
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

    // 2. Paleta de colores dinámica de la barra de navegación según el tipo de evento
    $configuracionTemas = [
        'baby_shower' => [
            'primario'    => 'bg-blue-500',
            'texto_hover' => 'hover:bg-blue-50 hover:text-blue-600',
            'badge'       => 'bg-blue-100 text-blue-800',
            'emoji'       => '🍼'
        ],
        'matrimonio' => [
            'primario'    => 'bg-rose-500',
            'texto_hover' => 'hover:bg-rose-50 hover:text-rose-600',
            'badge'       => 'bg-rose-100 text-rose-800',
            'emoji'       => '💍'
        ],
        'cumpleanos' => [
            'primario'    => 'bg-purple-600',
            'texto_hover' => 'hover:bg-purple-50 hover:text-purple-700',
            'badge'       => 'bg-purple-100 text-purple-800',
            'emoji'       => '🎂'
        ],
        'asado' => [
            'primario'    => 'bg-orange-600',
            'texto_hover' => 'hover:bg-orange-50 hover:text-orange-700',
            'badge'       => 'bg-orange-100 text-orange-800',
            'emoji'       => '🥩'
        ],
        'fiesta' => [
            'primario'    => 'bg-fuchsia-600',
            'texto_hover' => 'hover:bg-fuchsia-50 hover:text-fuchsia-700',
            'badge'       => 'bg-fuchsia-100 text-fuchsia-800',
            'emoji'       => '🎉'
        ],
        'personalizado' => [
            'primario'    => 'bg-indigo-600',
            'texto_hover' => 'hover:bg-indigo-50 hover:text-indigo-600',
            'badge'       => 'bg-indigo-100 text-indigo-800',
            'emoji'       => '⚙️'
        ]
    ];

    $temaActual = $configuracionTemas[$tipoEvento] ?? $configuracionTemas['personalizado'];
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invita App - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <div class="flex min-h-screen">
        
        <aside class="w-80 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 {{ $temaActual['primario'] }} text-white rounded-xl flex items-center justify-center text-xl shadow-md transition-all">
                        {{ $temaActual['emoji'] }}
                    </div>
                    <div>
                        <h2 class="font-black text-xl tracking-tight text-slate-900">Invita App</h2>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Panel del Evento</span>
                    </div>
                </div>

                @if(!empty($eventoActivo))
                    <div class="bg-slate-50 p-4 rounded-2xl mb-6 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Celebración Activa</span>
                        <h3 class="font-bold text-slate-800 text-sm truncate mt-0.5">{{ $eventoActivo['titulo'] ?? 'Sin título' }}</h3>
                        <span class="inline-block mt-2 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $temaActual['badge'] }} uppercase">
                            {{ str_replace('_', ' ', $tipoEvento) }}
                        </span>
                    </div>
                @endif

                <nav class="space-y-1.5">
                    
                    <a href="{{ route('anfitrion.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-lg"></i>
                        <span>Resumen Dashboard</span>
                    </a>

                    <hr class="border-slate-100 my-4">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-4 block mb-2">Módulos de la Invitación</span>

                    @if(in_array('regalos', $modulosActivos))
                        <a href="/eventos/modulos/regalos" class="flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-gift w-5 text-center text-lg text-rose-500"></i>
                                <span>Lista de Deseos</span>
                            </div>
                            <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-md font-bold">Premium</span>
                        </a>
                    @endif

                    @if(in_array('cuotas', $modulosActivos))
                        <a href="/eventos/modulos/cuotas" class="flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-piggy-bank w-5 text-center text-lg text-emerald-500"></i>
                                <span>Cuotas (La Vaca)</span>
                            </div>
                        </a>
                    @endif

                    @if(in_array('mesas', $modulosActivos))
                        <a href="/eventos/modulos/mesas" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                            <i class="fa-solid fa-chair w-5 text-center text-lg text-indigo-500"></i>
                            <span>Asignación de Mesas</span>
                        </a>
                    @endif

                    @if(in_array('itinerario', $modulosActivos))
                        <a href="/eventos/modulos/itinerario" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                            <i class="fa-solid fa-clock-rotate-left w-5 text-center text-lg text-amber-500"></i>
                            <span>Itinerario Cronológico</span>
                        </a>
                    @endif

                    @if(in_array('menu', $modulosActivos))
                        <a href="/eventos/modulos/menu" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                            <i class="fa-solid fa-utensils w-5 text-center text-lg text-teal-500"></i>
                            <span>Menú & Alergias</span>
                        </a>
                    @endif

                    @if(in_array('avisos', $modulosActivos))
                        <a href="/eventos/modulos/avisos" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                            <i class="fa-solid fa-bullhorn w-5 text-center text-lg text-sky-500"></i>
                            <span>Tablón de Anuncios</span>
                        </a>
                    @endif

                    @if(in_array('musica', $modulosActivos))
                        <a href="/eventos/modulos/musica" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                            <i class="fa-solid fa-music w-5 text-center text-lg text-violet-500"></i>
                            <span>Playlist Colaborativa</span>
                        </a>
                    @endif

                    @if(in_array('insumos', $modulosActivos))
                        <a href="/eventos/modulos/insumos" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                            <i class="fa-solid fa-basket-shopping w-5 text-center text-lg text-orange-500"></i>
                            <span>Lista de Insumos</span>
                        </a>
                    @endif

                    @if(in_array('galeria', $modulosActivos))
                        <a href="/eventos/modulos/galeria" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                            <i class="fa-solid fa-images w-5 text-center text-lg text-pink-500"></i>
                            <span>Muro de Galería</span>
                        </a>
                    @endif

                    @if(in_array('presupuesto', $modulosActivos))
                        <a href="/eventos/modulos/presupuesto" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                            <i class="fa-solid fa-wallet w-5 text-center text-lg text-slate-500"></i>
                            <span>Presupuesto Tracker</span>
                        </a>
                    @endif

                    @if(in_array('check_in', $modulosActivos))
                        <a href="/eventos/modulos/checkin" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-700 transition-all {{ $temaActual['texto_hover'] }}">
                            <i class="fa-solid fa-qrcode w-5 text-center text-lg text-cyan-500"></i>
                            <span>Check-In de Invitados</span>
                        </a>
                    @endif

                </nav>
            </div>

            <div class="p-6 border-t border-slate-100 bg-slate-50/50 rounded-b-3xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ Session::get('usuario_logueado')['nombre'] ?? 'Anfitrión' }}</p>
                        <p class="text-[10px] text-slate-500 font-medium">Anfitrión del Evento</p>
                    </div>
                    <form action="/logout" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Cerrar Sesión">
                            <i class="fa-solid fa-right-from-bracket text-lg"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-1 p-10 overflow-y-auto">
            @yield('contenido')
        </main>

    </div>

</body>
</html>