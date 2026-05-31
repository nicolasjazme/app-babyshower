
@extends('layouts.app')

@section('contenido')

    <main class="flex-1 p-8 h-screen overflow-y-auto">
        <div class="max-w-5xl mx-auto">
            
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 bg-white/80 backdrop-blur-md p-6 rounded-3xl shadow-sm border border-stone-200">
    <div>
        <h1 class="text-3xl font-extrabold text-stone-800 tracking-tight">Mi Centro de Control</h1>
        <p class="text-stone-600 mt-1 text-sm">Gestiona la logística, regalos e invitados de tu Baby Shower.</p>
    </div>
    
    <div class="bg-[#F8E1C6] px-4 py-2.5 rounded-2xl font-bold text-xs text-stone-800 uppercase tracking-wide">
        Anfitrión: {{ Session::get('usuario_logueado')['nombre'] ?? 'Organizador' }}
    </div>
</header>

            @if(!isset($evento) || empty($evento))
                
                <div class="bg-white/80 backdrop-blur-md p-12 rounded-3xl shadow-sm border border-stone-100 text-center mt-10">
                    <div class="text-6xl mb-6">🍼✨</div>
                    <h2 class="text-2xl font-extrabold text-stone-800 mb-3">¡Bienvenido a tu panel de Anfitrión!</h2>
                    <p class="text-stone-500 mb-8 max-w-lg mx-auto leading-relaxed">
                        Parece que aún no has creado tu evento en nuestra nueva base de datos. Para empezar a recibir confirmaciones y armar tu lista de regalos, crea tu Baby Shower ahora.
                    </p>
                    <a href="{{ route('event.create') }}" class="bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 px-8 py-4 rounded-2xl font-bold transition-all text-sm shadow-md inline-block uppercase tracking-wider">
                        ➕ Crear mi Baby Shower
                    </a>
                </div>

            @else

                <div class="bg-[#D1F2EB] border border-[#A3E4D7] p-6 rounded-3xl mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm">
                    <div>
                        <h2 class="text-lg font-extrabold text-[#0B6658]">¡Tu Baby Shower está Activo! 🎉</h2>
                        <p class="text-[#117A65] text-xs mt-1 font-medium">Comparte este enlace único con tus invitados para que confirmen asistencia:</p>
                        <a href="/e/{{ $evento['slug'] ?? '' }}" target="_blank" class="text-[#0B6658] font-mono font-bold mt-2 block hover:underline bg-white/50 inline-block px-3 py-1 rounded-xl border border-[#A3E4D7]">
                            {{ url('/e/' . ($evento['slug'] ?? '')) }}
                        </a>
                    </div>
                    <a href="{{ route('event.edit', $evento['_id'] ?? '') }}" class="bg-white text-stone-700 border border-stone-200 px-5 py-2.5 rounded-2xl font-bold hover:bg-stone-50 transition-colors text-xs whitespace-nowrap shadow-sm">
                        ✏️ Configurar Evento
                    </a>
                </div>

                <div class="bg-[#E8EAF6] p-6 rounded-3xl shadow-sm mb-8 border border-[#C5CAE9]">
                    <h2 class="text-base font-bold text-[#3949AB] mb-2 flex items-center gap-2">✉️ ¿Necesitas ayuda con una reserva?</h2>
                    <p class="text-xs text-stone-600 mb-4 max-w-2xl">Para mantener el factor sorpresa, no puedes ver quién reservó tus regalos. Si un invitado canceló o cometió un error, escríbele al Administrador indicando el nombre del regalo y el cambio requerido.</p>
                    
                    <form action="{{ route('incidencias.store') }}" method="POST" class="flex flex-col md:flex-row gap-3">
                        @csrf
                        <input type="text" name="mensaje" placeholder="Ej: Por favor liberar 1 unidad del regalo 'Pañales Pampers G'..." required 
                               class="flex-grow border border-stone-200 p-3 rounded-2xl outline-none focus:ring-2 focus:ring-[#5C6BC0] text-xs bg-white font-medium shadow-sm">
                        <button type="submit" class="bg-[#5C6BC0] hover:bg-[#3949AB] text-white px-6 py-3 rounded-2xl font-bold transition-all text-xs shadow-sm cursor-pointer whitespace-nowrap">
                            🚀 Enviar a Soporte
                        </button>
                    </form>
                </div>

                <div class="bg-white/80 backdrop-blur-md p-8 rounded-3xl shadow-sm mb-6 border border-stone-200 mt-10">
                <h2 class="text-base font-extrabold text-stone-800 mb-5 flex items-center gap-2">✨ Añadir Nuevo Regalo al Catálogo Base Global</h2>
                <form action="{{ route('gifts.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Nombre del Artículo *</label>
                        <input type="text" name="nombre" placeholder="Ej: Cuna Mecedora de Madera" required class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 font-medium">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Descripción Detallada</label>
                        <input type="text" name="descripcion" placeholder="Ej: Color blanco con colchón antiahogo" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 font-medium">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">URL Imagen Referencial</label>
                        <input type="text" name="imagen" placeholder="http://tienda.com/foto.jpg" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 font-mono">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Categoría</label>
                        <select name="categoria" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 cursor-pointer font-bold text-stone-700">
                            <option value="Higiene">🧻 Higiene y Aseo</option>
                            <option value="Dormitorio">🛏️ Dormitorio y Cunas</option>
                            <option value="Alimentación">🍼 Alimentación y Lactancia</option>
                            <option value="Ropa">👕 Vestuario y Ropa</option>
                            <option value="Transporte">🚗 Transporte y Paseo</option>
                            <option value="General" selected>🧸 Entretención / Otros</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Tipo de Artículo</label>
                        <select name="tipo" id="select-tipo" onchange="conmutarCantidad(this.value)" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 cursor-pointer font-bold text-stone-700">
                            <option value="unico" selected>🔒 Único (Solo 1 unidad)</option>
                            <option value="repetible">🔄 Repetible (Múltiples unidades)</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Cantidad Requerida</label>
                        <input type="number" id="input-cantidad" name="cantidad_solicitada" value="1" min="1" required class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-stone-200 text-stone-400 font-bold" readonly>
                    </div>

                    <div class="flex flex-col gap-1.5 md:col-span-2 lg:col-span-3">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Enlace Externo de Compra Opcional</label>
                        <input type="text" name="link_referencia" placeholder="https://www.tienda.com/producto" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 font-mono">
                    </div>
                    
                    <div class="md:col-span-2 lg:col-span-3 flex justify-end pt-2">
                        <button type="submit" class="bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 px-10 py-4 rounded-3xl font-extrabold transition-all text-xs whitespace-nowrap shadow-md cursor-pointer tracking-wider uppercase">
                            ➕ Guardar Regalo Nuevo
                        </button>
                    </div>
                </form>
            </div>

                <h3 class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-3">📈 Estadísticas de mis Regalos</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-[#FADBD8] p-6 rounded-3xl shadow-sm border border-[#F5B7B1] flex items-center">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-2xl mr-4">🎁</div>
                        <div>
                            <p class="text-xs text-[#7B241C] font-bold uppercase tracking-wider">Total de Ideas</p>
                            <p class="text-2xl font-black text-stone-800 mt-0.5">{{ $gifts->count() }}</p>
                        </div>
                    </div>
                    <div class="bg-[#FCF3CF] p-6 rounded-3xl shadow-sm border border-[#F9E79F] flex items-center">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-2xl mr-4">🔒</div>
                        <div>
                            <p class="text-xs text-[#7D6608] font-bold uppercase tracking-wider">Total Reservados</p>
                            <p class="text-2xl font-black text-stone-800 mt-0.5">{{ $gifts->where('estado', 'reservado')->count() }}</p>
                        </div>
                    </div>
                    <div class="bg-[#D4EFDF] p-6 rounded-3xl shadow-sm border border-[#A9DFBF] flex items-center">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-2xl mr-4">⏳</div>
                        <div>
                            <p class="text-xs text-[#186A3B] font-bold uppercase tracking-wider">Aún Disponibles</p>
                            <p class="text-2xl font-black text-stone-800 mt-0.5">{{ $gifts->where('estado', '!=', 'reservado')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-sm overflow-hidden border border-stone-200">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-stone-100 border-b border-stone-200">
                            <tr>
                                <th class="p-4 font-bold text-stone-600 uppercase text-xs tracking-wider">Regalo / Clasificación</th>
                                <th class="p-4 font-bold text-stone-600 uppercase text-xs tracking-wider">Configuración</th>
                                <th class="p-4 font-bold text-stone-600 uppercase text-xs tracking-wider">Inventario</th>
                                <th class="p-4 font-bold text-stone-600 uppercase text-xs tracking-wider">Estado</th>
                                <th class="p-4 font-bold text-stone-600 uppercase text-xs tracking-wider text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-xs">
                            @forelse($gifts as $gift)
                            <tr class="hover:bg-stone-50 transition-colors">
                                <td class="p-4">
                                    <p class="font-bold text-stone-800 text-sm">{{ $gift['nombre'] ?? 'Sin nombre' }}</p>
                                    <p class="text-[11px] text-stone-400 mt-0.5">{{ $gift['descripcion'] ?? 'Sin descripción.' }}</p>
                                    <span class="inline-block bg-stone-200 text-stone-700 font-bold px-2 py-0.5 rounded-lg text-[9px] mt-1.5 uppercase">
                                        📦 {{ $gift['categoria'] ?? 'General' }}
                                    </span>
                                </td>
                                <td class="p-4 align-middle">
                                    <span class="bg-stone-100 text-stone-700 px-2 py-0.5 rounded-lg font-bold text-[10px]">
                                        {{ ($gift['tipo'] ?? 'unico') === 'unico' ? '🔒 Único' : '🔄 Repetible' }}
                                    </span>
                                </td>
                                <td class="p-4 align-middle font-mono text-sm">
                                    <span class="font-black text-stone-800">{{ $gift['cantidad_disponible'] ?? 0 }}</span>
                                    <span class="text-stone-300">/</span>
                                    <span class="text-stone-500 font-medium">{{ $gift['cantidad_solicitada'] ?? 1 }}</span>
                                </td>
                                <td class="p-4 align-middle">
                                    @if(($gift['cantidad_disponible'] ?? 0) <= 0 || ($gift['estado'] ?? '') === 'reservado')
                                        <span class="bg-[#FADBD8] text-[#7B241C] px-3 py-1 rounded-full font-bold text-[10px]">🚫 Reservado</span>
                                    @else
                                        <span class="bg-[#D4EFDF] text-[#186A3B] px-3 py-1 rounded-full font-bold text-[10px]">✅ Disponible</span>
                                    @endif
                                </td>
                                <td class="p-4 align-middle text-right">
                                    <form action="{{ route('gifts.destroy', $gift['_id'] ?? '') }}" method="POST" onsubmit="return confirm('¿Eliminar?')" class="inline m-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 px-3 py-1.5 rounded-lg transition-all font-bold">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="p-8 text-center text-stone-400">No hay regalos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @endif
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function conmutarCantidad(tipo) {
            const input = document.getElementById('input-cantidad');
            if (tipo === 'unico') {
                input.value = 1;
                input.readOnly = true;
                input.classList.add('bg-stone-200');
                input.classList.remove('bg-white');
            } else {
                input.readOnly = false;
                input.value = 3;
                input.classList.remove('bg-stone-200');
                input.classList.add('bg-white');
            }
        }
    </script>

@endsection