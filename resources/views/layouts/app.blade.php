<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>{{ isset($evento) ? $evento['titulo'] : 'Invita App - Gestión Multi-Evento' }}</title>
</head>
@php
    // 🎨 MOTOR DE ESTILOS DINÁMICOS PARA EL LAYOUT DE INVITA APP
    // Si hay un evento en la vista, adaptamos la paleta; si no (como en login/registro), usamos el tema por defecto.
    $tipoEvento = $evento['tipo_evento'] ?? 'defecto';

    $configuracionTemas = [
        'baby_shower' => [
            'bg_aside'   => 'bg-[#F8E1C6]/90 border-stone-200',
            'bg_header'  => 'bg-[#F8E1C6]/80',
            'bg_activo'  => 'bg-[#EAD8C1]',
            'logo_emoji' => '🍼',
            'texto_item' => '🎁 Mesa de Regalos',
            'fondo_app'  => "background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); font-family: sans-serif;"
        ],
        'asado' => [
            'bg_aside'   => 'bg-amber-900/95 text-orange-100 border-amber-800',
            'bg_header'  => 'bg-amber-950/40',
            'bg_activo'  => 'bg-orange-700 text-white',
            'logo_emoji' => '🥩',
            'texto_item' => '🍖 Gestión de Insumos',
            'fondo_app'  => "background-image: linear-gradient(to bottom right, #fff7ed, #ffedd5); font-family: monospace;"
        ],
        'matrimonio' => [
            'bg_aside'   => 'bg-rose-50/90 text-stone-800 border-rose-200',
            'bg_header'  => 'bg-rose-100/50',
            'bg_activo'  => 'bg-rose-200 text-rose-900 font-bold',
            'logo_emoji' => '💍',
            'texto_item' => '🎁 Buzón de Regalos',
            'fondo_app'  => "background-image: linear-gradient(to bottom right, #fff1f2, #fffbeb); font-family: serif;"
        ],
        'cumpleanos' => [
            'bg_aside'   => 'bg-purple-900/90 text-purple-100 border-purple-800',
            'bg_header'  => 'bg-purple-950/40',
            'bg_activo'  => 'bg-fuchsia-600 text-white',
            'logo_emoji' => '🎂',
            'texto_item' => '🎁 Lista de Deseos',
            'fondo_app'  => "background-image: linear-gradient(to bottom right, #faf5ff, #f3e8ff); font-family: sans-serif;"
        ],
        'fiesta' => [
            'bg_aside'   => 'bg-slate-900/95 text-slate-100 border-slate-800',
            'bg_header'  => 'bg-slate-950/50',
            'bg_activo'  => 'bg-indigo-600 text-white',
            'logo_emoji' => '🎉',
            'texto_item' => '🍹 Barra / Cooperación',
            'fondo_app'  => "background-image: linear-gradient(to bottom right, #0f172a, #1e1b4b); color: #f8fafc; font-family: sans-serif;"
        ],
        'defecto' => [
            'bg_aside'   => 'bg-slate-100/90 text-slate-800 border-slate-200',
            'bg_header'  => 'bg-slate-200/50',
            'bg_activo'  => 'bg-indigo-600 text-white',
            'logo_emoji' => '🫵🏻',
            'texto_item' => '📦 Elementos de Lista',
            'fondo_app'  => "background-color: #f8fafc; font-family: sans-serif;"
        ]
    ];

    $temaActual = $configuracionTemas[$tipoEvento] ?? $configuracionTemas['defecto'];
@endphp

<body class="min-h-screen flex flex-col md:flex-row text-stone-800 transition-all duration-500" style="{{ $temaActual['fondo_app'] }}">

    <aside class="w-full md:w-64 {{ $temaActual['bg_aside'] }} backdrop-blur-md flex flex-col shadow-lg shrink-0 border-b md:border-b-0 md:border-r sticky top-0 z-50 md:h-screen transition-colors duration-500">
        
        <div class="flex items-center gap-3 p-4 border-b border-stone-200/30 {{ $temaActual['bg_header'] }}">
            <img src="/images/invita_app.png" alt="Logo Invita App" class="h-10 md:h-12 w-auto shrink-0">
            <div class="flex flex-col justify-center">
                <span class="text-[9px] md:text-[10px] font-bold tracking-widest text-rose-500 uppercase mb-0.5">
                    Un evento por y para ti
                </span>
                <span class="text-base md:text-lg font-black leading-none tracking-tight">
                    Invita App {{ $temaActual['logo_emoji'] }}
                </span>
            </div>
        </div> 

        <nav class="flex md:flex-col overflow-x-auto md:overflow-visible px-4 py-3 md:py-6 space-x-2 md:space-x-0 md:space-y-2 flex-1">
            
            <a href="{{ route('home') }}" class="whitespace-nowrap px-4 py-2 hover:bg-stone-500/20 rounded-xl transition-colors font-semibold flex items-center gap-2">
                🏠 <span class="hidden md:inline">Inicio General</span>
            </a>
            
            <div class="hidden md:block border-t border-stone-200/20 my-2"></div>
            
            <a href="{{ route('anfitrion.index') }}" class="whitespace-nowrap px-4 py-2 {{ Request::is('anfitrion') ? $temaActual['bg_activo'] : 'hover:bg-stone-500/20' }} rounded-xl transition-all font-semibold flex items-center gap-2">
                📊 <span class="hidden md:inline">Mi Dashboard</span>
            </a>

            <a href="#" class="whitespace-nowrap px-4 py-2 hover:bg-stone-500/20 rounded-xl transition-colors font-semibold flex items-center gap-2">
                <span>{!! $temaActual['texto_item'] !!}</span>
            </a>
            
            <a href="{{ route('anfitrion.guests.index') }}" class="whitespace-nowrap px-4 py-2 {{ Request::is('anfitrion/invitados*') ? $temaActual['bg_activo'] : 'hover:bg-stone-500/20' }} rounded-xl transition-all font-semibold flex items-center gap-2">
                👥 <span class="hidden md:inline">Lista Invitados</span>
            </a>
            
            <a href="{{ route('anfitrion.event.create') }}" class="whitespace-nowrap px-4 py-2 {{ Request::is('anfitrion/eventos/nuevo') ? $temaActual['bg_activo'] : 'hover:bg-stone-500/20' }} rounded-xl transition-all font-semibold flex items-center gap-2">
                🛠️ <span class="hidden md:inline">Configurar Evento</span>
            </a>
            
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 hover:bg-indigo-600 hover:text-white font-bold text-sm transition-all bg-white/40 px-4 py-2 rounded-xl border border-stone-200/50 shadow-sm">
                ⚙️ Mi Perfil
            </a>

            <form action="{{ route('logout') }}" method="POST" class="m-0 md:hidden flex items-center">
                @csrf
                <button type="submit" class="whitespace-nowrap px-4 py-2 hover:bg-rose-100 rounded-xl transition-colors font-bold flex items-center gap-2 text-rose-600">
                    🚪 Salir
                </button>
            </form>
        </nav>
        
        <div class="hidden md:block p-4 border-t border-stone-200/20">
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-stone-500 hover:text-rose-600 font-bold transition-colors">
                    🚪 Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-4 md:p-8 h-full md:h-screen overflow-y-auto">
        @yield('content') {{-- Cambiado de 'contenido' a 'content' para estandarizar con las vistas --}}
    </main>

</body>
</html>