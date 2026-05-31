<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - App Baby Shower</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-sans text-stone-800" style="background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); background-size: cover; background-attachment: fixed; background-position: center;">

    <div class="max-w-md w-full bg-white/80 backdrop-blur-md p-10 rounded-3xl shadow-xl border border-stone-200">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🍼</div>
            <h1 class="text-3xl font-extrabold text-stone-800 mb-2">Crear Cuenta</h1>
            <p class="text-stone-600 text-sm">Regístrate para organizar tu Baby Shower</p>
        </div>

        @if(session('error'))
            <div class="bg-[#FADBD8] border-l-4 border-[#7B241C] text-[#7B241C] px-4 py-3 rounded-2xl mb-6 text-sm text-center font-bold">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <form action="/registro" method="POST" class="space-y-5">
            @csrf 
            
            <div>
                <label for="nombre" class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ej: María Pérez"
                    class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
            </div>

            <div>
                <label for="correo" class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" required placeholder="correo@ejemplo.com"
                    class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
            </div>

            <div>
                <label for="contrasena" class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" required placeholder="••••••••"
                    class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-2">¿Cómo deseas registrarte? *</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="border border-stone-200 rounded-2xl p-4 flex flex-col items-center cursor-pointer bg-white/50 transition-all hover:bg-[#F8E1C6]/30 has-[:checked]:border-[#F8E1C6] has-[:checked]:bg-[#F8E1C6]/30 has-[:checked]:ring-2 has-[:checked]:ring-[#F8E1C6] group select-none">
                        <input type="radio" name="rol" value="anfitrion" required class="sr-only">
                        <span class="text-xl">🍼</span>
                        <span class="text-[10px] font-bold text-stone-700 uppercase mt-1">Anfitrión</span>
                    </label>    
                
                    <label class="border border-stone-200 rounded-2xl p-4 flex flex-col items-center cursor-pointer bg-white/50 transition-all hover:bg-[#F8E1C6]/30 has-[:checked]:border-[#F8E1C6] has-[:checked]:bg-[#F8E1C6]/30 has-[:checked]:ring-2 has-[:checked]:ring-[#F8E1C6] group select-none">
                        <input type="radio" name="rol" value="invitado" required class="sr-only">
                        <span class="text-xl">👥</span>
                        <span class="text-[10px] font-bold text-stone-700 uppercase mt-1">Invitado</span>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 font-extrabold py-4 rounded-2xl transition-all duration-300 uppercase tracking-wider text-sm shadow-md cursor-pointer mt-2">
                Registrarme
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-stone-500 font-medium">
            ¿Ya tienes una cuenta? 
            <a href="/login" class="text-[#3949AB] font-bold hover:underline ml-1">Inicia sesión aquí</a>
        </div>
    </div>

</body>
</html>
