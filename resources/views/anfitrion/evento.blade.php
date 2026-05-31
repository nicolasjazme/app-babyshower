<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Configurar Mi Baby Shower</title>
</head>
<body class="min-h-screen flex flex-col md:flex-row font-sans text-stone-800" style="background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); background-size: cover; background-attachment: fixed; background-position: center;">

    <aside class="w-full md:w-64 bg-[#F8E1C6]/90 backdrop-blur-md text-stone-800 flex flex-col shadow-lg shrink-0 border-b md:border-b-0 md:border-r border-stone-200 sticky top-0 z-50">
        <div class="h-16 flex items-center justify-center border-b border-stone-200/50 bg-[#F8E1C6]/80">
            <span class="text-xl font-bold tracking-wider text-stone-800">Panel Anfitrión 🍼</span>
        </div>
        
        <nav class="flex md:flex-col overflow-x-auto md:overflow-visible px-4 py-3 md:py-6 space-x-2 md:space-x-0 md:space-y-2 flex-1">
            <a href="/baby-shower" class="whitespace-nowrap px-4 py-2 hover:bg-stone-200/50 rounded-xl transition-colors font-medium flex items-center gap-2 text-stone-700">
                🏠 <span class="hidden md:inline">Pantalla Principal</span>
            </a>
            <div class="hidden md:block border-t border-stone-200/50 my-2"></div>
            <a href="/anfitrion" class="whitespace-nowrap px-4 py-2 hover:bg-stone-200/50 rounded-xl transition-colors font-medium flex items-center gap-2 text-stone-700">
                🎁 <span class="hidden md:inline">Mis Regalos</span>
            </a>
            <a href="/anfitrion/invitados" class="whitespace-nowrap px-4 py-2 hover:bg-stone-200/50 rounded-xl transition-colors font-medium flex items-center gap-2 text-stone-700">
                👥 <span class="hidden md:inline">Invitados</span>
            </a>
            <a href="/baby-shower/nuevo" class="whitespace-nowrap px-4 py-2 bg-[#EAD8C1] rounded-xl font-bold text-stone-900 transition-colors flex items-center gap-2">
                🛠️ <span class="hidden md:inline">Configurar Evento</span>
            </a>
        </nav>
        
        <div class="hidden md:block p-4 border-t border-stone-200/50">
            <form action="/logout" method="POST" class="m-0">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-stone-600 hover:text-stone-900 font-bold transition-colors">
                    🚪 Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-4 md:p-8 h-full md:h-screen overflow-y-auto">
        <div class="max-w-4xl mx-auto">

            <header class="flex flex-col justify-between items-start gap-4 mb-6 md:mb-8 bg-[#FAD7B9]/90 backdrop-blur-sm p-5 md:p-6 rounded-3xl shadow-sm border border-[#F8E1C6]">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-stone-800 tracking-tight">🛠️ Configurar Mi Baby Shower</h1>
                    <p class="text-stone-600 mt-1 text-xs md:text-sm">Define la información de la fiesta, el estado de la web y el estilo visual.</p>
                </div>
            </header>

            @if($errors->any())
                <div class="bg-[#FADBD8] border-l-4 border-[#7B241C] text-[#7B241C] p-4 mb-6 rounded-2xl shadow-sm text-sm">
                    <strong>⚠️ Hubo un problema:</strong> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ isset($evento) ? '/baby-shower/'.$evento['_id'] : '/baby-shower/nuevo' }}" method="POST" class="space-y-6 md:space-y-8">
                @csrf
                @if(isset($evento))
                    @method('PUT')
                @endif

                <div class="bg-white/80 backdrop-blur-md p-6 md:p-8 rounded-3xl shadow-sm border border-stone-200">
                    <h2 class="text-base md:text-lg font-extrabold text-stone-800 mb-4 md:mb-5 flex items-center gap-2">📅 1. Datos de la Celebración</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Fecha del Evento</label>
                            <input type="date" name="fecha" value="{{ isset($evento['fecha']) ? date('Y-m-d', strtotime($evento['fecha'])) : '' }}" required class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Hora</label>
                            <input type="text" name="hora" value="{{ $evento['hora'] ?? '' }}" placeholder="Ej: 16:30 Hrs" required class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Lugar / Salón</label>
                            <input type="text" name="lugar" value="{{ $evento['lugar'] ?? '' }}" placeholder="Ej: Casa de los abuelos" required class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Dirección Exacta</label>
                            <input type="text" name="direccion" value="{{ $evento['direccion'] ?? '' }}" placeholder="Ej: Av. Vitacura 1234" required class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-medium">
                        </div>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-md p-6 md:p-8 rounded-3xl shadow-sm border border-stone-200">
                    <h2 class="text-base md:text-lg font-extrabold text-stone-800 mb-4 md:mb-5 flex items-center gap-2">👶 2. Información del Bebé</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nombre del Bebé</label>
                            <input type="text" name="bebeNombre" value="{{ $evento['bebeNombre'] ?? '' }}" placeholder="Ej: Mateo" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Sexo del Bebé</label>
                            <select name="bebeSexo" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-bold text-stone-700 cursor-pointer">
                                <option value="Por revelar" {{ (isset($evento) && $evento['bebeSexo'] === 'Por revelar') ? 'selected' : '' }}>Por revelar ❓</option>
                                <option value="Niño" {{ (isset($evento) && $evento['bebeSexo'] === 'Niño') ? 'selected' : '' }}>Niño 💙</option>
                                <option value="Niña" {{ (isset($evento) && $evento['bebeSexo'] === 'Niña') ? 'selected' : '' }}>Niña 💗</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-md p-6 md:p-8 rounded-3xl shadow-sm border border-stone-200">
                    <h2 class="text-base md:text-lg font-extrabold text-stone-800 mb-4 md:mb-5">👁️ 3. Estado de Publicación</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-5">
                        <label class="border border-stone-200 p-4 rounded-2xl flex items-center gap-3 cursor-pointer hover:bg-stone-50 bg-white/50 transition-colors">
                            <input type="radio" name="estado" value="oculto" {{ (!isset($evento) || $evento['estado'] === 'oculto') ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-[#F8E1C6] focus:ring-[#F8E1C6]">
                            <div><p class="font-bold text-sm text-stone-700">Oculto</p></div>
                        </label>
                        <label class="border border-stone-200 p-4 rounded-2xl flex items-center gap-3 cursor-pointer hover:bg-stone-50 bg-white/50 transition-colors">
                            <input type="radio" name="estado" value="publicado" {{ (isset($evento) && $evento['estado'] === 'publicado') ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-[#F8E1C6] focus:ring-[#F8E1C6]">
                            <div><p class="font-bold text-sm text-stone-700">Publicado</p></div>
                        </label>
                        <label class="border border-stone-200 p-4 rounded-2xl flex items-center gap-3 cursor-pointer hover:bg-stone-50 bg-white/50 transition-colors">
                            <input type="radio" name="estado" value="cerrado" {{ (isset($evento) && $evento['estado'] === 'cerrado') ? 'checked' : '' }} class="mt-0.5 w-4 h-4 text-[#F8E1C6] focus:ring-[#F8E1C6]">
                            <div><p class="font-bold text-sm text-stone-700">Cerrado</p></div>
                        </label>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-md p-6 md:p-8 rounded-3xl shadow-sm border border-stone-200">
                    <h2 class="text-base md:text-lg font-extrabold text-stone-800 mb-4 md:mb-5">🎨 4. Estilo Estético</h2>
                    <div class="space-y-4 md:space-y-5">
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Imagen de Portada (URL)</label>
                            <input type="url" name="imagenPrincipal" value="{{ $evento['configVisual']['imagenPrincipal'] ?? '' }}" placeholder="https://..." class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Color del Tema</label>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 md:gap-4">
                                <input type="color" name="colorTema" value="{{ $evento['configVisual']['colorTema'] ?? '#4f46e5' }}" class="w-16 h-14 rounded-2xl border-none bg-transparent cursor-pointer shadow-sm shrink-0">
                                <span class="text-xs text-stone-500 font-medium">Haz clic para elegir el color principal de la pantalla de tus invitados.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center md:justify-end pt-2 pb-10">
                    <button type="submit" class="w-full md:w-auto bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 px-12 py-4 rounded-3xl font-extrabold text-sm shadow-md transition-all uppercase tracking-wider">
                        🚀 {{ isset($evento) ? 'Guardar Cambios' : 'Crear Celebración' }}
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>