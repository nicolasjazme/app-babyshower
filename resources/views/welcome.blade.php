<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baby Shower {{ isset($evento) ? 'de ' . ($evento['bebeNombre'] ?? 'Nuestro Bebé') : '- Fenrir SPA' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        :root {
            --color-tema: {{ isset($evento['configVisual']['colorTema']) ? $evento['configVisual']['colorTema'] : '#4f46e5' }};
        }
        .bg-tema { background-color: var(--color-tema); }
        .text-tema { color: var(--color-tema); }
        .border-tema { border-color: var(--color-tema); }
        .hover-bg-tema:hover { background-color: var(--color-tema); opacity: 0.9; }
    </style>
</head>
<body class="bg-slate-50 font-sans relative pb-20 min-h-screen">

<div class="fixed inset-0 -z-10 opacity-[0.2] pointer-events-none" 
     style="background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); background-repeat: repeat; background-size: auto;"></div>

    <nav class="bg-rose-50/40 backdrop-blur-sm border-b border-rose-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            
            <div class="flex items-center">
                <img src="/images/logo-fenrir.png" alt="Logo" class="h-12 w-auto">
                <div class="flex flex-col justify-center">
                    <span class="text-[10px] font-bold tracking-widest text-rose-500 uppercase mb-0.5">
                        Fenrir SPA | Empresa TI
                    </span>
                    <span class="text-lg font-bold text-stone-700 leading-none">
                        App Baby Shower 🍼
                    </span>
                </div>
            </div>
            
            <div class="flex space-x-6 items-center">
                @if(Session::has('usuario_logueado'))
                    <span class="text-stone-600 font-medium italic text-xs md:text-sm">
                        ¡Hola, {{ Session::get('usuario_logueado')['nombre'] }}!
                    </span>
                    
                    @if(Session::get('usuario_logueado')['rol'] === 'administrador')
                        <a href="/admin" class="bg-rose-100 text-rose-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-rose-200 transition-all flex items-center gap-1">
                            🖥️ Panel Admin
                        </a>
                    @elseif(Session::get('usuario_logueado')['rol'] === 'anfitrion')
                        <a href="/anfitrion" class="bg-sky-100 text-sky-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-sky-200 transition-all flex items-center gap-1">
                            🍼 Mi Panel Anfitrión
                        </a>
                    @else
                        <a href="/perfil" class="text-rose-600 hover:text-rose-800 text-xs font-semibold transition-colors flex items-center gap-1">
                            👤 Mi Perfil
                        </a>
                    @endif 
                    
                    <form action="/logout" method="POST" class="inline m-0">
                        @csrf
                        <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-semibold flex items-center gap-1">
                            🚪 Salir
                        </button>
                    </form>
                @else
                    <a href="/login" class="text-stone-600 hover:text-rose-600 text-xs font-medium transition-colors">
                        Iniciar Sesión
                    </a>
                    <a href="/registro" class="bg-rose-400 hover:bg-rose-500 text-white px-3 py-1.5 rounded-lg font-medium text-xs transition-colors">
                        Crear Cuenta
                    </a>
                @endif 
            </div>

        </div>
    </div>
</nav>

    @if(isset($evento['configVisual']['imagenPrincipal']) && $evento['configVisual']['imagenPrincipal'] !== '')
        <div class="w-full h-64 md:h-80 bg-cover bg-center shadow-inner border-b border-slate-200" 
             style="background-image: url('{{ $evento['configVisual']['imagenPrincipal'] }}')">
        </div>
    @endif

    <main class="max-w-6xl mx-auto py-8 px-4 sm:px-6">
        
        <header class="text-center mb-10 {{ isset($evento['configVisual']['imagenPrincipal']) ? 'mt-6' : '' }}">
            @if(isset($evento))
                <h1 class="text-3xl font-black text-slate-900 mb-3 tracking-tight md:text-4xl">
                    Baby Shower de: <span class="text-tema italic capitalize">{{ $evento['bebeNombre'] ?? 'Nuestro Bebé' }}</span> 🧸
                </h1>
                <p class="text-slate-600 max-w-2xl mx-auto text-sm md:text-base leading-relaxed">
                    {{ $evento['mensajeBienvenida'] ?? '¡Estamos muy felices de compartir este hermoso momento contigo! Si deseas hacernos un presente, puedes elegir y reservar uno de los artículos sugeridos en la lista a continuación.' }}
                </p>
            @else
                <h1 class="text-3xl font-black text-slate-900 mb-3 tracking-tight md:text-4xl">
                    Lista General de <span class="text-indigo-600 italic">Regalos</span> 🍼
                </h1>
                <p class="text-slate-600 max-w-2xl mx-auto text-sm md:text-base leading-relaxed">
                    Bienvenidos a la plataforma. Si fuiste invitado a una celebración, utiliza el enlace único que te compartió el organizador para ver su información personalizada y confirmar asistencia.
                </p>
            @endif
        </header>

        @if(isset($evento))
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-8 max-w-3xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start gap-3">
                    <span class="text-2xl mt-0.5">📅</span>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cuándo</p>
                        <p class="text-slate-800 font-bold text-sm mt-0.5">Fecha: {{ isset($evento['fecha']) ? date('d/m/Y', strtotime($evento['fecha'])) : 'Por definir' }}</p>
                        <p class="text-slate-500 text-xs mt-0.5">Hora: {{ $evento['hora'] ?? '--:--' }} Hrs</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-2xl mt-0.5">📍</span>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dónde</p>
                        <p class="text-slate-800 font-bold text-sm mt-0.5">{{ $evento['lugar'] ?? 'No definido' }}</p>
                        <p class="text-slate-500 text-xs mt-0.5">{{ $evento['direccion'] ?? '' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm max-w-md mx-auto mb-10">
                <div class="text-center mb-4">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center justify-center gap-1.5">💌 ¿Podrás Acompañarnos?</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Registra tu respuesta para confirmar tu asistencia a la celebración de forma oficial.</p>
                </div>

                <form action="{{ route('asistencia.store') }}" method="POST" class="space-y-3 m-0">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Nombre Completo *</label>
                            <input type="text" name="nombre_invitado" required placeholder="Ej: Carlos Silva" class="w-full border border-slate-200 p-2.5 rounded-xl outline-none text-xs bg-slate-50 focus:ring-2 focus:ring-[var(--color-tema)] focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Tu Correo Electrónico *</label>
                            <input type="email" name="correo_invitado" required placeholder="carlos@correo.com" class="w-full border border-slate-200 p-2.5 rounded-xl outline-none text-xs bg-slate-50 font-mono focus:ring-2 focus:ring-[var(--color-tema)] focus:border-transparent transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <label class="border border-slate-200 rounded-xl p-2.5 flex flex-col items-center justify-center gap-0.5 cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all text-center has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20 group select-none">
                            <input type="radio" name="estado_asistencia" value="confirmado" required class="sr-only">
                            <span class="text-lg group-hover:scale-110 transition-transform">🎉</span>
                            <span class="text-[10px] font-bold text-slate-700">Sí, asistiré</span>
                        </label>
                        <label class="border border-slate-200 rounded-xl p-2.5 flex flex-col items-center justify-center gap-0.5 cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all text-center has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50 has-[:checked]:ring-2 has-[:checked]:ring-rose-500/20 group select-none">
                            <input type="radio" name="estado_asistencia" value="rechazado" required class="sr-only">
                            <span class="text-lg group-hover:scale-110 transition-transform">😢</span>
                            <span class="text-[10px] font-bold text-slate-700">No podré ir</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-tema text-white py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-sm hover:opacity-90 active:scale-95 transition-all cursor-pointer">
                        ✉️ Confirmar Estado de Asistencia
                    </button>
                </form>
            </div>
        @endif

        <div class="mb-10 text-center">
            <p class="text-slate-400 font-bold uppercase tracking-widest text-[9px] mb-3">🔍 Filtrar Catálogo por Categoría</p>
            <div class="flex flex-wrap justify-center gap-2">
                <button onclick="filtrarCategoria('Todos')" class="btn-filtro bg-indigo-600 text-white text-xs font-bold px-4 py-2 rounded-full shadow-sm transition-all cursor-pointer">✨ Todos</button>
                <button onclick="filtrarCategoria('Higiene')" class="btn-filtro bg-white text-slate-600 hover:bg-slate-100 text-xs font-bold px-4 py-2 rounded-full shadow-sm border border-slate-200 transition-all cursor-pointer">🧻 Higiene</button>
                <button onclick="filtrarCategoria('Dormitorio')" class="btn-filtro bg-white text-slate-600 hover:bg-slate-100 text-xs font-bold px-4 py-2 rounded-full shadow-sm border border-slate-200 transition-all cursor-pointer">🛏️ Dormitorio</button>
                <button onclick="filtrarCategoria('Alimentación')" class="btn-filtro bg-white text-slate-600 hover:bg-slate-100 text-xs font-bold px-4 py-2 rounded-full shadow-sm border border-slate-200 transition-all cursor-pointer">🍼 Alimentación</button>
                <button onclick="filtrarCategoria('Ropa')" class="btn-filtro bg-white text-slate-600 hover:bg-slate-100 text-xs font-bold px-4 py-2 rounded-full shadow-sm border border-slate-200 transition-all cursor-pointer">👕 Ropa</button>
                <button onclick="filtrarCategoria('Transporte')" class="btn-filtro bg-white text-slate-600 hover:bg-slate-100 text-xs font-bold px-4 py-2 rounded-full shadow-sm border border-slate-200 transition-all cursor-pointer">🚗 Transporte</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($gifts as $gift)
                <div class="card-regalo bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full transition-all hover:shadow-lg hover:-translate-y-1 relative" data-categoria="{{ $gift['categoria'] ?? 'General' }}">
                    
                    <div class="w-full h-56 bg-slate-100 overflow-hidden relative">
                        <img src="{{ $gift['url_imagen'] ?? 'https://via.placeholder.com/400x300?text=Regalo' }}" 
                             class="w-full h-full object-cover"
                             onerror="this.src='https://via.placeholder.com/400x300?text=Imagen+No+Disponible';">

                        <span class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-slate-700 px-2 py-0.5 rounded-md font-bold text-[10px] shadow-sm border border-slate-200/50">
                            {{ ($gift['tipo'] ?? 'unico') === 'unico' ? '🔒 Único' : '🔄 Repetible' }}
                        </span>

                        <span class="absolute top-3 left-3 bg-slate-900/70 backdrop-blur-sm text-white px-2 py-0.5 rounded-md font-bold text-[9px] uppercase tracking-wider">
                            {{ $gift['categoria'] ?? 'General' }}
                        </span>
                    </div>

                    <div class="p-6 flex flex-col flex-grow justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2 gap-2">
                                <h3 class="text-base font-extrabold text-slate-800 leading-tight line-clamp-1">
                                    {{ $gift['nombre'] ?? 'Regalo sugerido' }}
                                </h3>
                                <span class="{{ (($gift['cantidad_disponible'] ?? 0) <= 0 || ($gift['estado'] ?? 'disponible') === 'reservado') ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }} text-[9px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider shrink-0 border">
                                    {{ (($gift['cantidad_disponible'] ?? 0) <= 0 || ($gift['estado'] ?? 'disponible') === 'reservado') ? 'Agotado' : 'Disponible' }}
                                </span>
                            </div>

                            <p class="text-slate-500 text-xs mb-3 line-clamp-2 font-medium">
                                {{ $gift['descripcion'] ?? 'Completa tu reserva para apoyarnos en esta hermosa etapa.' }}
                            </p>

                            @if(!empty($gift['link_referencia']))
                                <a href="{{ $gift['link_referencia'] }}" target="_blank" class="inline-flex items-center text-[11px] font-bold text-indigo-600 hover:text-indigo-800 mb-4 hover:underline gap-0.5 cursor-pointer">
                                    🛍️ Ver artículo de referencia ↗️
                                </a>
                            @endif
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 mb-3 uppercase tracking-wider">
                                <span>Unidades Disponibles:</span>
                                <span class="font-mono text-xs text-slate-800">
                                    {{ $gift['cantidad_disponible'] ?? 0 }} de {{ $gift['cantidad_solicitada'] ?? 1 }}
                                </span>
                            </div>

                            @if(($gift['cantidad_disponible'] ?? 0) <= 0 || ($gift['estado'] ?? 'disponible') === 'reservado')
                                <div class="bg-rose-50 border border-rose-100 text-rose-700 font-bold p-3 rounded-xl text-center text-xs select-none">
                                    🚫 ¡Lo siento! Mis papis ya no necesitan más de este regalo. 👶🍼
                                </div>
                            @else
                                @if(isset($evento['estado']) && $evento['estado'] === 'cerrado')
                                    <div class="bg-red-50 border border-red-100 text-red-600 font-bold p-3 rounded-xl text-center text-xs">
                                        🔒 Reservas bloqueadas temporalmente por el anfitrión
                                    </div>
                                @else
                                    <form action="/reservar-regalo" method="POST" onsubmit="desactivarBoton(this)" class="m-0 space-y-2">
                                        @csrf
                                        <input type="hidden" name="gift_id" value="{{ $gift['_id'] ?? '' }}">
                                        <input type="text" name="guest_name" required placeholder="Tu Nombre Completo..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl outline-none bg-slate-50 text-xs font-medium focus:ring-2 focus:border-transparent" style="--tw-ring-color: var(--color-tema);">
                                        <input type="email" name="guest_email" required placeholder="Tu Correo (Para enviarte comprobante)..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl outline-none bg-slate-50 text-xs font-medium focus:ring-2 focus:border-transparent" style="--tw-ring-color: var(--color-tema);">
                                        <button type="submit" class="w-full bg-tema text-white py-2.5 rounded-xl font-bold text-xs transition-all shadow-md active:scale-95 hover:opacity-90 cursor-pointer">
                                            🎁 Reservar este Regalo
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </main>

    @if(Session::has('usuario_logueado'))
        <div class="fixed bottom-8 right-8 z-50 flex flex-col gap-2">
            @if(Session::get('usuario_logueado')['rol'] === 'administrador')
                <a href="/admin" class="bg-slate-900 text-white px-5 py-3 rounded-full font-bold shadow-2xl hover:bg-black hover:scale-105 transition-all flex items-center gap-2 border border-slate-700 text-xs tracking-wide">
                    🛠️ Panel General Admin
                </a>
            @elseif(Session::get('usuario_logueado')['rol'] === 'anfitrion')
                <a href="/anfitrion" class="bg-emerald-800 text-white px-5 py-3 rounded-full font-bold shadow-2xl hover:bg-emerald-950 hover:scale-105 transition-all flex items-center gap-2 border border-emerald-600 text-xs tracking-wide">
                    🍼 Volver a Mi Panel Anfitrión
                </a>
            @endif
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function desactivarBoton(formulario) {
            const boton = formulario.querySelector('button[type="submit"]');
            boton.disabled = true;
            boton.innerHTML = '⏳ Procesando tu obsequio...';
            boton.classList.add('bg-slate-400', 'cursor-not-allowed');
        }

        function filtrarCategoria(categoriaSeleccionada) {
            const tarjetas = document.querySelectorAll('.card-regalo');
            const botones = document.querySelectorAll('.btn-filtro');

            tarjetas.forEach(tarjeta => {
                const catTarjeta = tarjeta.getAttribute('data-categoria');
                if (categoriaSeleccionada === 'Todos' || catTarjeta === categoriaSeleccionada) {
                    tarjeta.classList.remove('hidden');
                } else {
                    tarjeta.classList.add('hidden');
                }
            });

            botones.forEach(btn => {
                if (btn.innerText.includes(categoriaSeleccionada) || (categoriaSeleccionada === 'Todos' && btn.innerText.includes('Todos'))) {
                    btn.className = "btn-filtro bg-indigo-600 text-white text-xs font-bold px-4 py-2 rounded-full shadow-sm transition-all cursor-pointer";
                } else {
                    btn.className = "btn-filtro bg-white text-slate-600 hover:bg-slate-100 text-xs font-bold px-4 py-2 rounded-full shadow-sm border border-slate-200 transition-all cursor-pointer";
                }
            });
        }
    </script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '{{ Session::has("usuario_logueado") ? "¡Bienvenido, " . Session::get("usuario_logueado")["nombre"] . "!" : "¡Todo listo!" }}',
                text: "{{ session('success') }}",
                confirmButtonColor: '{{ isset($evento['configVisual']['colorTema']) ? $evento['configVisual']['colorTema'] : "#4f46e5" }}',
                timer: 4500,
                timerProgressBar: true
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Hubo un problema',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ef4444'
            });
        </script>
    @endif
</body>
</html>