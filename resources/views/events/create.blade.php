@extends('layouts.app')

@section('contenido')

<div class="max-w-4xl mx-auto py-4 space-y-8">
    
    <header class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">🎉 Configurar Nueva Celebración</h1>
            <p class="text-slate-500 text-sm mt-1">Completa los detalles generales de tu evento y activa las funciones interactivas para tus invitados.</p>
        </div>
    </header>

    <form action="{{ route('eventos.guardar') }}" method="POST" class="space-y-8">
        @csrf

        {{-- SECCIÓN 1: INFORMACIÓN BÁSICA DEL EVENTO --}}
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-indigo-500"></i> 1. Datos Generales de la Celebración
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nombre / Título del Evento *</label>
                    <input type="text" name="titulo" required placeholder="Ej. Cumpleaños de Nico, Matrimonio Ana y Pedro, Asado Fin de Año" value="{{ old('titulo') }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all bg-slate-50/50 text-sm font-medium">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tipo de Celebración *</label>
                    <select name="tipo_evento" id="tipo_evento" onchange="adaptarFormulario(this.value)"
                            class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all bg-white text-sm font-bold text-slate-700 cursor-pointer">
                        <option value="babyshower" {{ old('tipo_evento') == 'babyshower' ? 'selected' : '' }}>🍼 Baby Shower</option>
                        <option value="matrimonio" {{ old('tipo_evento') == 'matrimonio' ? 'selected' : '' }}>💍 Matrimonio / Boda</option>
                        <option value="cumpleanos" {{ old('tipo_evento') == 'cumpleanos' ? 'selected' : '' }}>🎂 Cumpleaños</option>
                        <option value="asado" {{ old('tipo_evento') == 'asado' ? 'selected' : '' }}>🥩 Asado o Encuentro</option>
                        <option value="fiesta" {{ old('tipo_evento') == 'fiesta' ? 'selected' : '' }}>🎉 Fiesta / Carrete</option>
                        <option value="personalizado" {{ old('tipo_evento') == 'personalizado' ? 'selected' : '' }}>⚙️ Personalizado (Elegir Módulos a la Carta)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Fecha del Evento *</label>
                    <input type="date" name="fecha" required value="{{ old('fecha') }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all bg-slate-50/50 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Hora *</label>
                    <input type="time" name="hora" required value="{{ old('hora', '16:00') }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all bg-slate-50/50 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Lugar / Local *</label>
                    <input type="text" name="lugar" required placeholder="Ej. Casa de los abuelos, Centro de eventos" value="{{ old('lugar') }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all bg-slate-50/50 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Dirección Exacta *</label>
                    <input type="text" name="direccion" required placeholder="Calle, Número, Comuna" value="{{ old('direccion') }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all bg-slate-50/50 text-sm font-medium">
                </div>
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Mensaje de Bienvenida (Opcional)</label>
                <textarea name="mensaje_bienvenida" rows="2" placeholder="¡Bienvenidos a nuestra celebración! Gracias por ser parte de este momento..."
                          class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all bg-slate-50/50 text-sm font-medium resize-none">{{ old('mensaje_bienvenida') }}</textarea>
            </div>
        </div>

        {{-- SECCIÓN 2: INFORMACIÓN ESPECÍFICA DE BABY SHOWER (CONDICIONAL) --}}
        <div id="seccion_baby_shower" class="bg-blue-50/50 p-6 md:p-8 rounded-3xl border border-blue-100 shadow-sm space-y-5 transition-all duration-300">
            <h2 class="text-base font-bold text-blue-900 flex items-center gap-2">
                🍼 Información del Bebé (Exclusivo Baby Shower)
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1.5">Nombre del Bebé</label>
                    <input type="text" name="bebe_nombre" placeholder="Dejar vacío si aún es sorpresa" value="{{ old('bebe_nombre') }}"
                           class="w-full px-4 py-3 border border-blue-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-300 transition-all bg-white text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1.5">Sexo del Bebé</label>
                    <select name="bebe_sexo" class="w-full px-4 py-3 border border-blue-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-300 transition-all bg-white text-sm font-bold text-slate-700 cursor-pointer">
                        <option value="Por revelar" {{ old('bebe_sexo') == 'Por revelar' ? 'selected' : '' }}>Por revelar / Sorpresa ❓</option>
                        <option value="Niño" {{ old('bebe_sexo') == 'Niño' ? 'selected' : '' }}>Niño 🧑</option>
                        <option value="Niña" {{ old('bebe_sexo') == 'Niña' ? 'selected' : '' }}>Niña 👧</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1.5">Fecha Estimada de Nacimiento</label>
                    <input type="date" name="fecha_estimada" value="{{ old('fecha_estimada') }}"
                           class="w-full px-4 py-3 border border-blue-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-300 transition-all bg-white text-sm font-medium">
                </div>
            </div>
        </div>

        {{-- SECCIÓN 3: SELECCIÓN DE LOS 11 MÓDULOS DEL ECOSISTEMA --}}
        <div id="seccion_modulos_personalizados" class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
            <div>
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-cubes text-indigo-500"></i> 2. Módulos Interactivos Activados
                </h2>
                <p class="text-slate-500 text-xs mt-1">Marca las funciones que estarán disponibles en la invitación para tus invitados.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <label class="flex items-start p-4 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-indigo-300 cursor-pointer transition-all">
                    <input type="checkbox" id="mod_regalos" name="modulos_activos[]" value="regalos" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-slate-800 text-sm">🎁 Lista de Deseos (Regalos)</strong>
                        <span class="block text-xs text-slate-400">Reserva de obsequios con catálogo y stock.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-indigo-300 cursor-pointer transition-all">
                    <input type="checkbox" id="mod_cuota" name="modulos_activos[]" value="cuota" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-slate-800 text-sm">🐮 Cuota (La Vaca)</strong>
                        <span class="block text-xs text-slate-400">Pozo común con datos bancarios.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-indigo-300 cursor-pointer transition-all">
                    <input type="checkbox" id="mod_insumos" name="modulos_activos[]" value="insumos" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-slate-800 text-sm">🛒 Lista de Insumos (Cooperación)</strong>
                        <span class="block text-xs text-slate-400">Checklist "Yo lo llevo" para asados o reuniones.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-indigo-300 cursor-pointer transition-all">
                    <input type="checkbox" id="mod_mesas" name="modulos_activos[]" value="mesas" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-slate-800 text-sm">🪑 Asignación de Mesas</strong>
                        <span class="block text-xs text-slate-400">Límite de sillas y distribución de invitados.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-indigo-300 cursor-pointer transition-all">
                    <input type="checkbox" id="mod_itinerario" name="modulos_activos[]" value="itinerario" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-slate-800 text-sm">⏳ Itinerario Cronológico</strong>
                        <span class="block text-xs text-slate-400">Agenda con hitos y horarios de la fiesta.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-indigo-300 cursor-pointer transition-all">
                    <input type="checkbox" id="mod_menu" name="modulos_activos[]" value="menu" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-slate-800 text-sm">🍽️ Menú & Alergias</strong>
                        <span class="block text-xs text-slate-400">Opciones gastronómicas y reporte médico.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-indigo-300 cursor-pointer transition-all">
                    <input type="checkbox" id="mod_avisos" name="modulos_activos[]" value="avisos" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-slate-800 text-sm">📢 Tablón de Anuncios</strong>
                        <span class="block text-xs text-slate-400">Comunicados urgentes o informativos.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-indigo-300 cursor-pointer transition-all">
                    <input type="checkbox" id="mod_musica" name="modulos_activos[]" value="musica" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-slate-800 text-sm">🎵 Playlist Colaborativa</strong>
                        <span class="block text-xs text-slate-400">Sugerencias musicales de los invitados.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-indigo-300 cursor-pointer transition-all">
                    <input type="checkbox" id="mod_galeria" name="modulos_activos[]" value="galeria" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-slate-800 text-sm">📸 Muro de Recuerdos (Galería)</strong>
                        <span class="block text-xs text-slate-400">Feed de fotos con interacción por likes.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-indigo-300 cursor-pointer transition-all">
                    <input type="checkbox" id="mod_presupuesto" name="modulos_activos[]" value="presupuesto" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-slate-800 text-sm">💰 Presupuesto Tracker</strong>
                        <span class="block text-xs text-slate-400">Control privado de gastos estimado vs real.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-slate-50 rounded-2xl border border-slate-200/80 hover:border-indigo-300 cursor-pointer transition-all md:col-span-2">
                    <input type="checkbox" id="mod_checkin" name="modulos_activos[]" value="checkin" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-slate-800 text-sm">🎟️ Check-In en Puerta</strong>
                        <span class="block text-xs text-slate-400">Validación de entradas con escáner de código QR.</span>
                    </span>
                </label>

            </div>
        </div>

        {{-- BOTONES DE ACCIÓN --}}
        <div class="pt-4 flex justify-end gap-3">
            <a href="{{ route('anfitrion.index') }}" class="px-6 py-3.5 rounded-2xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition-all cursor-pointer">
                Cancelar
            </a>
            <button type="submit" class="px-8 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold shadow-md transition-all text-xs uppercase tracking-wider cursor-pointer">
                🚀 Guardar y Publicar Evento
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('error'))
    <script>
        Swal.fire({ icon: 'error', title: 'Hubo un problema', text: "{{ session('error') }}", confirmButtonColor: '#e11d48' });
    </script>
@endif

<script>
    /**
     * Script de conmutación de secciones y autocompletado de módulos por plantilla
     */
    function adaptarFormulario(tipo) {
        const seccionBabyShower = document.getElementById('seccion_baby_shower');
        const checkboxes = document.querySelectorAll('input[name="modulos_activos[]"]');

        // Visualización del bloque específico del bebé
        if (tipo === 'babyshower') {
            seccionBabyShower.classList.remove('hidden');
        } else {
            seccionBabyShower.classList.add('hidden');
        }

        // Mapeo automático de checkboxes según el tipo de plantilla elegida
        const plantillasModulos = {
            'babyshower': ['regalos', 'itinerario', 'menu', 'galeria', 'avisos'],
            'matrimonio':  ['regalos', 'mesas', 'itinerario', 'menu', 'galeria', 'musica', 'presupuesto'],
            'cumpleanos':  ['itinerario', 'avisos', 'musica', 'galeria', 'regalos'],
            'asado':       ['cuota', 'itinerario', 'insumos', 'musica', 'galeria'],
            'fiesta':      ['itinerario', 'avisos', 'musica', 'checkin', 'galeria'],
            'personalizado': []
        };

        const seleccionados = plantillasModulos[tipo] || [];

        checkboxes.forEach(cb => {
            if (tipo !== 'personalizado') {
                cb.checked = seleccionados.includes(cb.value);
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        adaptarFormulario(document.getElementById('tipo_evento').value);
    });
</script>

@endsection
