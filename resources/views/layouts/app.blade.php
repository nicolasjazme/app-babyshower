<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Configurar Mi Baby Shower</title>
</head>
<body class="min-h-screen flex flex-col md:flex-row font-sans text-stone-800" style="background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); background-size: cover; background-attachment: fixed; background-position: center;">

    <aside class="w-full md:w-64 bg-[#F8E1C6]/90 backdrop-blur-md text-stone-800 flex flex-col shadow-lg shrink-0 border-b md:border-b-0 md:border-r border-stone-200 sticky top-0 z-50 md:h-screen">
        
        <div class="flex items-center gap-3 p-4 border-b border-stone-200/50 bg-[#F8E1C6]/80">
            <img src="/images/logo-fenrir.png" alt="Logo Fenrir" class="h-10 md:h-12 w-auto shrink-0">
            <div class="flex flex-col justify-center">
                <span class="text-[9px] md:text-[10px] font-bold tracking-widest text-rose-500 uppercase mb-0.5">
                    Fenrir SPA | Empresa TI
                </span>
                <span class="text-base md:text-lg font-bold text-stone-700 leading-none">
                    App Baby Shower 🍼
                </span>
            </div>
        </div> <nav class="flex md:flex-col overflow-x-auto md:overflow-visible px-4 py-3 md:py-6 space-x-2 md:space-x-0 md:space-y-2 flex-1">
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
            <a href="/perfil" class="flex items-center gap-2 text-stone-600 hover:text-[#3949AB] font-bold text-sm transition-all bg-white/50 px-4 py-2 rounded-xl border border-stone-200 shadow-sm hover:shadow-md">
                ⚙️ Mi Perfil
            </a>

            <form action="/logout" method="POST" class="m-0 md:hidden flex items-center">
                @csrf
                <button type="submit" class="whitespace-nowrap px-4 py-2 hover:bg-rose-100 rounded-xl transition-colors font-bold flex items-center gap-2 text-rose-600">
                    🚪 Cerrar Sesión
                </button>
            </form>
        </nav>
        
        <div class="hidden md:block p-4 border-t border-stone-200/50">
            <form action="/logout" method="POST" class="m-0">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-stone-600 hover:text-rose-600 font-bold transition-colors">
                    🚪 Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-4 md:p-8 h-full md:h-screen overflow-y-auto">
        @yield('contenido')
    </main>

</body>
</html>