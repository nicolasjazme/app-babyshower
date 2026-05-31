@extends('layouts.app')

@section('contenido')

<body class="min-h-screen pb-16 font-sans text-stone-800" style="--color-tema: {{ $evento['configVisual']['colorTema'] ?? '#4f46e5' }}; background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); background-size: cover; background-attachment: fixed; background-position: center;">

    <header class="text-white py-20 px-4 text-center shadow-md color-tema-bg">
        <span class="text-xs font-bold tracking-widest uppercase bg-white/20 px-4 py-1.5 rounded-full">¡Estás Invitado!</span>
        <h1 class="text-5xl font-extrabold mt-4">Baby Shower de {{ $evento['bebeNombre'] ?? 'Nuestro Bebé' }} 🍼</h1>
        <p class="text-xl opacity-90 mt-4 max-w-xl mx-auto italic">"{{ $evento['mensajeBienvenida'] ?? 'Acompáñanos en este momento tan especial.' }}"</p>
    </header>

    <main class="max-w-5xl mx-auto mt-10 px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-white/80 backdrop-blur-md p-7 rounded-3xl border border-stone-200 shadow-sm space-y-5">
                <h2 class="font-extrabold text-lg text-stone-800 border-b border-stone-200 pb-3">📍 Datos Clave</h2>
                <div>
                    <p class="text-[10px] text-stone-500 font-bold uppercase tracking-wider">Fecha y Hora</p>
                    <p class="text-stone-800 font-medium text-sm">{{ isset($evento['fecha']) ? date('d/m/Y', strtotime($evento['fecha'])) : 'Por definir' }} a las {{ $evento['hora'] ?? '--:--' }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-stone-500 font-bold uppercase tracking-wider">Lugar</p>
                    <p class="text-stone-800 font-medium text-sm">{{ $evento['lugar'] ?? 'No definido' }}</p>
                    <p class="text-xs text-stone-500">{{ $evento['direccion'] ?? '' }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-stone-500 font-bold uppercase tracking-wider">Sexo del Bebé</p>
                    <span class="inline-block text-[10px] font-bold uppercase tracking-wide px-3 py-1 rounded-lg mt-1 bg-stone-100 text-stone-700">
                        {{ $evento['bebeSexo'] ?? 'Sorpresa' }}
                    </span>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-md p-7 rounded-3xl border border-stone-200 shadow-sm space-y-4">
                <div>
                    <h3 class="font-extrabold text-base text-stone-800 flex items-center gap-1.5">💌 Confirmar Asistencia</h3>
                    <p class="text-[11px] text-stone-500 mt-1 leading-relaxed">Indícanos si nos acompañaras para organizar el evento de la mejor manera.</p>
                </div>

                <form action="{{ route('asistencia.store') }}" method="POST" class="space-y-4 m-0">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1">Nombre Completo *</label>
                        <input type="text" name="nombre_invitado" required placeholder="Ej: María José" 
                               class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none text-xs bg-white/60 font-medium transition-all focus:ring-2 focus:ring-[var(--color-tema)]">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1">Correo Electrónico *</label>
                        <input type="email" name="correo_invitado" required placeholder="maria@correo.com" 
                               class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none text-xs bg-white/60 font-mono transition-all focus:ring-2 focus:ring-[var(--color-tema)]">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-2">¿Asistirás? *</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="border border-stone-200 rounded-2xl p-4 flex flex-col items-center justify-center gap-1 cursor-pointer bg-white/50 transition-all text-center has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20 group select-none">
                                <input type="radio" name="estado_asistencia" value="confirmado" required class="sr-only">
                                <span class="text-xl">🎉</span>
                                <span class="text-[10px] font-bold text-stone-700">Sí, iré</span>
                            </label>
                            <label class="border border-stone-200 rounded-2xl p-4 flex flex-col items-center justify-center gap-1 cursor-pointer bg-white/50 transition-all text-center has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50 has-[:checked]:ring-2 has-[:checked]:ring-rose-500/20 group select-none">
                                <input type="radio" name="estado_asistencia" value="rechazado" required class="sr-only">
                                <span class="text-xl">😢</span>
                                <span class="text-[10px] font-bold text-stone-700">No podré</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full text-white py-4 rounded-2xl font-extrabold text-xs uppercase tracking-wider shadow-md transition-all cursor-pointer text-center hover:opacity-90" style="background-color: var(--color-tema);">
                        ✨ Responder Invitación
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <h2 class="font-extrabold text-2xl text-stone-800">🎁 Tabla de Regalos Sugeridos</h2>
            <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-sm overflow-hidden border border-stone-200">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-stone-100/50 text-stone-500 text-[10px] font-bold uppercase tracking-wider border-b border-stone-200">
                        <tr>
                            <th class="p-5">Regalo</th>
                            <th class="p-5">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm">
                        @foreach($gifts as $gift)
                            <tr class="hover:bg-stone-50/50 transition-colors">
                                <td class="p-5">
                                    <p class="font-bold text-stone-800 text-sm">{{ $gift['nombre'] ?? 'Sin nombre' }}</p>
                                    <p class="text-xs text-stone-500 mt-0.5">{{ $gift['descripcion'] ?? '' }}</p>
                                </td>
                                <td class="p-5">
                                    @if(($gift['estado'] ?? 'disponible') === 'reservado')
                                        <span class="bg-[#FADBD8] text-[#7B241C] px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider border border-[#F5B7B1]">Reservado</span>
                                    @else
                                        <span class="bg-[#D4EFDF] text-[#186A3B] px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider border border-[#A9DFBF]">Disponible</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('error'))
        <script>
            Swal.fire({ icon: 'error', title: 'Hubo un problema', text: "{{ session('error') }}", confirmButtonColor: '#ef4444', customClass: { popup: 'rounded-3xl' } });
        </script>
    @endif
@endsection