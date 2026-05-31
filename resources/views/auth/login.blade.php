<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - App Baby Shower</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-sans text-stone-800" style="background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); background-size: cover; background-attachment: fixed; background-position: center;">

    <div class="w-full max-w-md">
        <div class="bg-white/80 backdrop-blur-md p-10 rounded-3xl shadow-xl border border-stone-200">
            <div class="text-center mb-8">
                <div class="text-6xl mb-4">🍼</div>
                <h1 class="text-3xl font-extrabold text-stone-800">¡Bienvenido!</h1>
                <p class="text-stone-600 text-sm mt-2">Accede a tu panel para gestionar tu evento.</p>
            </div>

            @if(session('success'))
                <div class="bg-[#D4EFDF] border-l-4 border-[#186A3B] text-[#186A3B] p-4 rounded-2xl mb-6 font-bold text-xs shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-[#FADBD8] border-l-4 border-[#7B241C] text-[#7B241C] p-4 rounded-2xl mb-6 font-bold text-xs shadow-sm">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-6">
                @csrf 
                <div>
                    <label for="correo" class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" required placeholder="correo@ejemplo.com"
                        class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="contrasena" class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider">Contraseña</label>
                        <a href="/recuperar-contrasena" class="text-[10px] text-[#3949AB] font-bold hover:underline">¿Olvidaste tu contraseña?</a>
                    </div>
                    <input type="password" id="contrasena" name="contrasena" required placeholder="••••••••"
                        class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                </div>

                <button type="submit"
                    class="w-full bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 font-extrabold py-4 rounded-2xl transition-all shadow-md uppercase tracking-wider text-sm cursor-pointer mt-2">
                    🚀 Iniciar Sesión
                </button>
            </form>

            <div class="mt-8 text-center text-xs text-stone-500 font-medium">
                ¿No tienes una cuenta? 
                <a href="/registro" class="text-[#3949AB] font-bold hover:underline ml-1">Regístrate aquí</a>
            </div>
        </div>
        
        <p class="text-center text-[10px] text-stone-400 mt-8 uppercase tracking-widest font-bold">
            Desarrollado por Nicolás Jazme - Patricio Larenas - Hector Humeres 2026
        </p>
    </div>

</body>
</html>

