<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Invita App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-sans text-slate-800 bg-gradient-to-br from-slate-50 via-indigo-50/30 to-slate-100" style="background-attachment: fixed;">

    <div class="max-w-md w-full bg-white/90 backdrop-blur-md p-10 rounded-3xl shadow-xl border border-slate-100">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 text-2xl font-bold mb-3 shadow-inner">
                ✨
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Crear Cuenta</h1>
            <p class="text-slate-500 text-sm">Regístrate para organizar tus celebraciones en Invita App</p>
        </div>

        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 px-4 py-3 rounded-2xl mb-6 text-sm text-center font-bold">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <form action="/registro" method="POST" class="space-y-5">
            @csrf 
            {{-- Captura automáticamente el parámetro 'tipo' de la URL o asigna general por defecto --}}
            <input type="hidden" name="tipo_evento" value="{{ request()->get('tipo', 'general') }}">
            
            <div>
                <label for="nombre" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ej: María Pérez"
                    class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all bg-white/60 text-sm font-medium">
            </div>

            <div>
                <label for="correo" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" required placeholder="correo@ejemplo.com"
                    class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all bg-white/60 text-sm font-medium">
            </div>

            <div>
                <label for="contrasena" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" required placeholder="••••••••"
                    class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all bg-white/60 text-sm font-medium">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">¿Cómo deseas registrarte? *</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="border border-slate-200 rounded-2xl p-4 flex flex-col items-center cursor-pointer bg-white/50 transition-all hover:bg-indigo-50/50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 has-[:checked]:ring-2 has-[:checked]:ring-indigo-200 group select-none">
                        <input type="radio" name="rol" value="anfitrion" required class="sr-only">
                        <span class="text-xl">📋</span>
                        <span class="text-[10px] font-bold text-slate-700 uppercase mt-1">Anfitrión</span>
                    </label>    
                
                    <label class="border border-slate-200 rounded-2xl p-4 flex flex-col items-center cursor-pointer bg-white/50 transition-all hover:bg-indigo-50/50 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 has-[:checked]:ring-2 has-[:checked]:ring-indigo-200 group select-none">
                        <input type="radio" name="rol" value="invitado" required class="sr-only">
                        <span class="text-xl">👥</span>
                        <span class="text-[10px] font-bold text-slate-700 uppercase mt-1">Invitado</span>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold py-4 rounded-2xl transition-all duration-300 uppercase tracking-wider text-sm shadow-md cursor-pointer mt-2">
                Registrarme
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-slate-500 font-medium">
            ¿Ya tienes una cuenta? 
            <a href="/login" class="text-indigo-600 font-bold hover:underline ml-1">Inicia sesión aquí</a>
        </div>
    </div>

</body>
</html>