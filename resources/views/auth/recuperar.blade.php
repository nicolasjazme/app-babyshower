<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - App Baby Shower</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-sans text-stone-800" style="background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); background-size: cover; background-attachment: fixed; background-position: center;">

    <div class="max-w-md w-full bg-white/80 backdrop-blur-md p-10 rounded-3xl shadow-xl border border-stone-200">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🔐</div>
            <h1 class="text-3xl font-extrabold text-stone-800 mb-2">Recuperar Acceso</h1>
            <p class="text-stone-600 text-sm">Ingresa tu correo y te enviaremos las instrucciones para restablecer tu contraseña.</p>
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

        <form action="/recuperar-contrasena" method="POST" class="space-y-6">
            @csrf 
            <div>
                <label for="correo" class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Correo Electrónico Registrado</label>
                <input type="email" id="correo" name="correo" required placeholder="correo@ejemplo.com"
                    class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
            </div>

            <button type="submit"
                class="w-full bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 font-extrabold py-4 rounded-2xl transition-all shadow-md uppercase tracking-wider text-sm cursor-pointer mt-2">
                ✉️ Enviar Instrucciones
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-stone-500 font-medium">
            <a href="/login" class="text-[#3949AB] font-bold hover:underline flex items-center justify-center gap-1">
                ← Volver al inicio de sesión
            </a>
        </div>
    </div>

</body>
</html>