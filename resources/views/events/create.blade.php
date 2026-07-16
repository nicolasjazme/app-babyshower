@extends('layouts.app')

@section('contenido')

<div class="max-w-4xl mx-auto py-6">
    <header class="mb-10 border-b border-stone-200 pb-6">
        <h1 class="text-3xl font-extrabold text-stone-800 tracking-tight">🎉 Configurar Nueva Celebración</h1>
        <p class="text-stone-600 mt-1 text-sm">Completa la información básica y define qué módulos premium deseas habilitar en tu invitación interactiva.</p>
    </header>

    <form action="{{ route('anfitrion.event.store') }}" method="POST" class="space-y-8">
        @csrf

        <div>
            <h2 class="text-base font-bold text-stone-800 mb-5 flex items-center gap-2">🗓️ Información del Evento</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nombre / Título del Evento *</label>
                    <input type="text" name="titulo" required placeholder="Ej. Cumpleaños de Nico o Baby Shower de Emma" value="{{ old('titulo') }}"
                           class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Tipo de Celebración *</label>
                    <select name="tipo_evento" id="tipo_evento" onchange="adaptarFormulario(this.value)"
                            class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-bold text-stone-700 cursor-pointer">
                        <option value="baby_shower" {{ old('tipo_evento') == 'baby_shower' ? 'selected' : '' }}>🍼 Baby Shower (Plantilla por defecto)</option>
                        <option value="matrimonio" {{ old('tipo_evento') == 'matrimonio' ? 'selected' : '' }}>💍 Matrimonio / Boda</option>
                        <option value="cumpleanos" {{ old('tipo_evento') == 'cumpleanos' ? 'selected' : '' }}>🎂 Cumpleaños</option>
                        <option value="asado" {{ old('tipo_evento') == 'asado' ? 'selected' : '' }}>🥩 Asado Familiar o Amigos</option>
                        <option value="fiesta" {{ old('tipo_evento') == 'fiesta' ? 'selected' : '' }}>🎉 Fiesta / Carrete</option>
                        <option value="personalizado" {{ old('tipo_evento') == 'personalizado' ? 'selected' : '' }}>⚙️ Evento Personalizado (Elige tus módulos)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Fecha del Evento *</label>
                    <input type="date" name="fecha" required value="{{ old('fecha') }}"
                           class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Hora *</label>
                    <input type="time" name="hora" required value="{{ old('hora') }}"
                           class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Lugar / Establecimiento *</label>
                    <input type="text" name="lugar" required placeholder="Ej. Casa de los abuelos, Centro de eventos" value="{{ old('lugar') }}"
                           class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Dirección Exacta *</label>
                    <input type="text" name="direccion" required placeholder="Calle, Número, Comuna" value="{{ old('direccion') }}"
                           class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                </div>
            </div>
            
            <div class="mt-5">
                <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Mensaje de Bienvenida (Opcional)</label>
                <textarea name="mensaje_bienvenida" rows="2" placeholder="¡Bienvenidos a nuestra celebración!..."
                          class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">{{ old('mensaje_bienvenida') }}</textarea>
            </div>
        </div>

        <div id="seccion_baby_shower" class="transition-all duration-300">
            <hr class="border-stone-200 mb-8">
            <h2 class="text-base font-bold text-stone-800 mb-5 flex items-center gap-2">🍼 Información del Bebé (Opcional)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nombre del Bebé</label>
                    <input type="text" name="bebe_nombre" placeholder="Dejar vacío si es sorpresa" value="{{ old('bebe_nombre') }}"
                           class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Sexo del Bebé</label>
                    <select name="bebe_sexo" class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-bold text-stone-700 cursor-pointer">
                        <option value="Por revelar" {{ old('bebe_sexo') == 'Por revelar' ? 'selected' : '' }}>Por revelar / Sorpresa ❓</option>
                        <option value="Niño" {{ old('bebe_sexo') == 'Niño' ? 'selected' : '' }}>Niño 🧑</option>
                        <option value="Niña" {{ old('bebe_sexo') == 'Niña' ? 'selected' : '' }}>Niña 👧</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Fecha Estimada de Nacimiento</label>
                    <input type="date" name="fecha_estimada" value="{{ old('fecha_estimada') }}"
                           class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                </div>
            </div>
            
            <div class="mt-5">
                <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Observaciones / Detalles</label>
                <textarea name="observaciones" rows="2" placeholder="Ej: Información sobre estacionamiento..."
                          class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">{{ old('observaciones') }}</textarea>
            </div>
        </div>

        <div id="seccion_modulos_personalizados" class="hidden transition-all duration-300">
            <hr class="border-stone-200 mb-8">
            <h2 class="text-base font-bold text-stone-800 mb-2 flex items-center gap-2">⚙️ Configura tu Ecosistema de Módulos</h2>
            <p class="text-stone-600 mb-6 text-sm">Selecciona las funciones que estarán activas y visibles para tus invitados.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <label class="flex items-start p-4 bg-white rounded-2xl border border-stone-200 hover:border-indigo-300 cursor-pointer transition-all shadow-sm">
                    <input type="checkbox" name="modulos_activos[]" value="regalos" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-stone-800 text-sm">🎁 Lista de Deseos (Regalos)</strong>
                        <span class="block text-xs text-stone-500">Sincroniza links externos para regalos.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-white rounded-2xl border border-stone-200 hover:border-indigo-300 cursor-pointer transition-all shadow-sm">
                    <input type="checkbox" name="modulos_activos[]" value="cuotas" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-stone-800 text-sm">🐮 La Vaca (Cuotas)</strong>
                        <span class="block text-xs text-stone-500">Establece cuotas y datos para transferencias.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-white rounded-2xl border border-stone-200 hover:border-indigo-300 cursor-pointer transition-all shadow-sm">
                    <input type="checkbox" name="modulos_activos[]" value="mesas" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-stone-800 text-sm">🪑 Organizador de Mesas</strong>
                        <span class="block text-xs text-stone-500">Límite de sillas y distribución de invitados.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-white rounded-2xl border border-stone-200 hover:border-indigo-300 cursor-pointer transition-all shadow-sm">
                    <input type="checkbox" name="modulos_activos[]" value="itinerario" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-stone-800 text-sm">⏳ Itinerario de Actividades</strong>
                        <span class="block text-xs text-stone-500">Línea de tiempo cronológica con hitos.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-white rounded-2xl border border-stone-200 hover:border-indigo-300 cursor-pointer transition-all shadow-sm">
                    <input type="checkbox" name="modulos_activos[]" value="menu" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-stone-800 text-sm">🍽️ Menú & Alergias (RSVP Pro)</strong>
                        <span class="block text-xs text-stone-500">Controla opciones de comida y captura alergias.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-white rounded-2xl border border-stone-200 hover:border-indigo-300 cursor-pointer transition-all shadow-sm">
                    <input type="checkbox" name="modulos_activos[]" value="avisos" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-stone-800 text-sm">📢 Tablón de Avisos</strong>
                        <span class="block text-xs text-stone-500">Muro con comunicados urgentes o importantes.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-white rounded-2xl border border-stone-200 hover:border-indigo-300 cursor-pointer transition-all shadow-sm">
                    <input type="checkbox" name="modulos_activos[]" value="musica" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-stone-800 text-sm">🎵 Playlist Colaborativa</strong>
                        <span class="block text-xs text-stone-500">Los invitados proponen enlaces de Spotify/YT.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-white rounded-2xl border border-stone-200 hover:border-indigo-300 cursor-pointer transition-all shadow-sm">
                    <input type="checkbox" name="modulos_activos[]" value="insumos" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-stone-800 text-sm">🛒 Lista de Cooperación (Insumos)</strong>
                        <span class="block text-xs text-stone-500">"Yo llevo esto": coordina quién aporta qué.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-white rounded-2xl border border-stone-200 hover:border-indigo-300 cursor-pointer transition-all shadow-sm">
                    <input type="checkbox" name="modulos_activos[]" value="galeria" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-stone-800 text-sm">📸 Muro de Recuerdos (Galería)</strong>
                        <span class="block text-xs text-stone-500">Muro interactivo de fotos con sistema de likes.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-white rounded-2xl border border-stone-200 hover:border-indigo-300 cursor-pointer transition-all shadow-sm">
                    <input type="checkbox" name="modulos_activos[]" value="presupuesto" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-stone-800 text-sm">💰 Tracker de Gastos</strong>
                        <span class="block text-xs text-stone-500">Presupuesto privado para el anfitrión.</span>
                    </span>
                </label>

                <label class="flex items-start p-4 bg-white rounded-2xl border border-stone-200 hover:border-indigo-300 cursor-pointer transition-all shadow-sm">
                    <input type="checkbox" name="modulos_activos[]" value="check_in" class="mt-1 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="ml-3">
                        <strong class="block text-stone-800 text-sm">🎟️ Check-In en Puerta</strong>
                        <span class="block text-xs text-stone-500">Escáner QR para gestionar accesos sin duplicados.</span>
                    </span>
                </label>

            </div>
        </div>

        <div class="pt-4 flex justify-end gap-3">
            <a href="/admin" class="px-6 py-3 rounded-2xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100 transition-all cursor-pointer">
                Cancelar
            </a>
            <button type="submit" class="px-8 py-3 rounded-2xl bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 font-extrabold shadow-md transition-all text-xs uppercase tracking-wider cursor-pointer">
                🚀 Guardar y Publicar
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
     * Controlador dinámico de secciones del formulario
     */
    function adaptarFormulario(tipo) {
        const seccionBabyShower = document.getElementById('seccion_baby_shower');
        const seccionModulos = document.getElementById('seccion_modulos_personalizados');

        // Manejo de visibilidad con transiciones suaves
        if (tipo === 'baby_shower') {
            seccionBabyShower.classList.remove('hidden');
        } else {
            seccionBabyShower.classList.add('hidden');
        }

        if (tipo === 'personalizado') {
            seccionModulos.classList.remove('hidden');
        } else {
            seccionModulos.classList.add('hidden');
        }
    }

    // Inicializar el estado al cargar la página
    document.addEventListener("DOMContentLoaded", function() {
        adaptarFormulario(document.getElementById('tipo_evento').value);
    });
</script>

@endsection