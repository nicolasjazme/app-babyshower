<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - App Baby Shower</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen p-4 font-sans text-stone-800" style="background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); background-size: cover; background-attachment: fixed; background-position: center;">

    <nav class="max-w-3xl mx-auto mb-8 flex justify-between items-center py-4 px-6 bg-white/50 backdrop-blur-sm rounded-2xl border border-stone-200 shadow-sm">
        <a href="/baby-shower" class="text-[#3949AB] font-bold text-xs hover:underline transition-all flex items-center gap-1">
            ← Volver a los regalos
        </a>
        <span class="text-stone-700 font-extrabold text-sm">App Baby Shower 🍼</span>
    </nav>

    <div class="max-w-3xl mx-auto bg-white/80 backdrop-blur-md p-10 rounded-3xl shadow-xl border border-stone-200">
        <div class="mb-8 border-b border-stone-200 pb-6">
            <h1 class="text-3xl font-extrabold text-stone-800">Gestión de Perfil</h1>
            <p class="text-stone-500 text-sm mt-1">Actualiza tus datos básicos y contraseña.</p>
        </div>

        @if(session('success'))
            <div class="bg-[#D4EFDF] border-l-4 border-[#186A3B] text-[#186A3B] px-4 py-3 rounded-2xl mb-6 text-sm font-bold">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-[#FADBD8] border-l-4 border-[#7B241C] text-[#7B241C] px-4 py-3 rounded-2xl mb-6 text-sm font-bold">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <form id="formPerfil" action="/perfil" method="POST" class="space-y-6">
            @csrf
            @method('PUT') 
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre" class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" required 
                           value="{{ Session::get('usuario_logueado')['nombre'] ?? '' }}"
                           class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                </div>

                <div>
                    <label for="correo" class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" required 
                           value="{{ Session::get('usuario_logueado')['correo'] ?? '' }}"
                           class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                </div>
            </div>

            <div>
                <label for="telefono" class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Teléfono (Opcional)</label>
                <input type="text" id="telefono" name="telefono" placeholder="+56 9 1234 5678"
                       value="{{ Session::get('usuario_logueado')['telefono'] ?? '' }}"
                       class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
            </div>

            <div class="border-t border-stone-200 pt-6">
                <h2 class="text-lg font-extrabold text-stone-800 mb-4">Seguridad</h2>
                
                <div class="mb-4">
                    <label for="contrasenaActual" class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Contraseña Actual</label>
                    <input type="password" id="contrasenaActual" name="contrasenaActual" placeholder="Ingresa tu clave actual"
                           class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm">
                </div>

                <div>
                    <label for="contrasena" class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" id="contrasena" name="contrasena" placeholder="********"
                           class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" id="btnGuardar"
                        class="bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 font-extrabold py-4 px-8 rounded-2xl transition-all duration-300 uppercase tracking-wider text-xs shadow-md cursor-pointer">
                    💾 Guardar Cambios
                </button>
            </div>
        </form>
    </div>

</body>
</html>