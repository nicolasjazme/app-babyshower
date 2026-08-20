<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invita App - Organiza tus Eventos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-indigo-500 selection:text-white">

    <nav class="w-full bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">✨</span>
                    <span class="font-black text-xl tracking-tight text-slate-900">Invita App</span>
                </div>

                <div>
                    @if(Session::has('usuario_logueado'))
                        <a href="/admin" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors bg-indigo-50 px-4 py-2 rounded-full border border-indigo-100">
                            Ir a mi Panel 🚀
                        </a>
                    @else
                        <a href="/login" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors px-4 py-2">
                            Iniciar Sesión
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <div class="min-h-[calc(100vh-4rem)] relative overflow-hidden flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-10">
            <h1 class="text-5xl font-extrabold text-slate-900 tracking-tight mb-6">
                Bienvenido a <span class="text-indigo-600">Invita App</span>
            </h1>
            <p class="text-xl text-slate-600 mb-12">
                Crea invitaciones digitales e interactivas, gestiona tus listas en tiempo real y organiza cualquier evento en minutos.
            </p>
            
            <button onclick="abrirMenuEventos()" class="inline-flex items-center gap-3 bg-indigo-600 text-white px-10 py-5 rounded-full font-bold shadow-xl hover:bg-indigo-700 hover:shadow-2xl transition-all transform hover:-translate-y-1 active:scale-95 text-lg">
                <span class="text-2xl">✨</span> 
                <span>Crear Nuevo Evento</span>
            </button>
        </div>

        <div id="overlay-eventos" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] hidden transition-opacity duration-300 opacity-0" 
             onclick="cerrarMenuEventos()">
        </div>

        <div id="bottom-sheet-eventos" 
             class="fixed bottom-0 left-0 right-0 bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.15)] z-[70] transform translate-y-full transition-transform duration-300 ease-out p-6 sm:p-8 max-h-[85vh] overflow-y-auto w-full max-w-2xl mx-auto">
            
            <div class="w-16 h-1.5 bg-slate-200 rounded-full mx-auto mb-8"></div>
            
            <h3 class="text-2xl font-extrabold text-slate-800 text-center mb-8">¿Qué vas a celebrar hoy? 🎉</h3>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                
                <a href="{{ route('register', ['tipo' => 'baby_shower']) }}" class="flex flex-col items-center justify-center p-5 bg-blue-50 rounded-3xl border border-blue-100 hover:bg-blue-100 hover:border-blue-300 hover:scale-105 transition-all text-center group">
                    <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">🍼</span>
                    <span class="font-bold text-blue-700 text-sm">Baby Shower</span>
                </a>

                <a href="{{ route('register', ['tipo' => 'matrimonio']) }}" class="flex flex-col items-center justify-center p-5 bg-rose-50 rounded-3xl border border-rose-100 hover:bg-rose-100 hover:border-rose-300 hover:scale-105 transition-all text-center group">
                    <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">💍</span>
                    <span class="font-bold text-rose-700 text-sm">Matrimonio</span>
                </a>

                <a href="{{ route('register', ['tipo' => 'cumpleanos']) }}" class="flex flex-col items-center justify-center p-5 bg-amber-50 rounded-3xl border border-amber-100 hover:bg-amber-100 hover:border-amber-300 hover:scale-105 transition-all text-center group">
                    <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">🎂</span>
                    <span class="font-bold text-amber-700 text-sm">Cumpleaños</span>
                </a>

                <a href="{{ route('register', ['tipo' => 'asado']) }}" class="flex flex-col items-center justify-center p-5 bg-orange-50 rounded-3xl border border-orange-100 hover:bg-orange-100 hover:border-orange-300 hover:scale-105 transition-all text-center group">
                    <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">🥩</span>
                    <span class="font-bold text-orange-700 text-sm">Asado</span>
                </a>

                <a href="{{ route('register', ['tipo' => 'fiesta']) }}" class="flex flex-col items-center justify-center p-5 bg-purple-50 rounded-3xl border border-purple-100 hover:bg-purple-100 hover:border-purple-300 hover:scale-105 transition-all text-center group">
                    <span class="text-4xl mb-3 group-hover:scale-110 transition-transform">🎉</span>
                    <span class="font-bold text-purple-700 text-sm">Fiesta</span>
                </a>

            </div>
        </div>
    </div>

    <script>
        function abrirMenuEventos() {
            const overlay = document.getElementById('overlay-eventos');
            const sheet = document.getElementById('bottom-sheet-eventos');
            
            overlay.classList.remove('hidden');
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                sheet.classList.remove('translate-y-full');
            }, 10);
        }

        function cerrarMenuEventos() {
            const overlay = document.getElementById('overlay-eventos');
            const sheet = document.getElementById('bottom-sheet-eventos');
            
            sheet.classList.add('translate-y-full');
            overlay.classList.add('opacity-0');
            
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }
    </script>
</body>
</html>