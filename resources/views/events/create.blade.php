@extends('layouts.app')

@section('contenido')

<div class="max-w-4xl mx-auto py-4 space-y-8 pb-20">
    
    <header class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-200/60 shadow-sm text-center md:text-left">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">🎉 Configurar Celebración</h1>
        <p class="text-slate-500 text-sm">Completa los detalles y activa las funciones mágicas para tus invitados.</p>
    </header>

    <form action="{{ route('events.store') }}" method="POST" class="space-y-8">
        @csrf

        {{-- ========================================== --}}
        {{-- SECCIÓN 1: SELECCIÓN VISUAL DEL EVENTO     --}}
        {{-- ========================================== --}}
        <div class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-200/60 shadow-sm space-y-6">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-4">
                <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                ¿Qué tipo de evento será? *
            </h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 md:gap-4">
                
                @php $oldTipo = old('tipo_evento', 'personalizado'); @endphp

                <label class="cursor-pointer">
                    <input type="radio" name="tipo_evento" value="babyshower" class="peer sr-only" onchange="adaptarFormulario(this.value)" {{ $oldTipo == 'babyshower' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center justify-center p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl hover:bg-blue-50 hover:border-blue-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-md transition-all text-center">
                        <span class="text-3xl mb-2">🍼</span>
                        <span class="font-bold text-slate-700 text-xs md:text-sm peer-checked:text-blue-700">Baby Shower</span>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="tipo_evento" value="matrimonio" class="peer sr-only" onchange="adaptarFormulario(this.value)" {{ $oldTipo == 'matrimonio' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center justify-center p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl hover:bg-rose-50 hover:border-rose-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:shadow-md transition-all text-center">
                        <span class="text-3xl mb-2">💍</span>
                        <span class="font-bold text-slate-700 text-xs md:text-sm peer-checked:text-rose-700">Matrimonio</span>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="tipo_evento" value="cumpleanos" class="peer sr-only" onchange="adaptarFormulario(this.value)" {{ $oldTipo == 'cumpleanos' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center justify-center p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl hover:bg-amber-50 hover:border-amber-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:shadow-md transition-all text-center">
                        <span class="text-3xl mb-2">🎂</span>
                        <span class="font-bold text-slate-700 text-xs md:text-sm peer-checked:text-amber-700">Cumpleaños</span>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="tipo_evento" value="asado" class="peer sr-only" onchange="adaptarFormulario(this.value)" {{ $oldTipo == 'asado' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center justify-center p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl hover:bg-orange-50 hover:border-orange-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:shadow-md transition-all text-center">
                        <span class="text-3xl mb-2">🥩</span>
                        <span class="font-bold text-slate-700 text-xs md:text-sm peer-checked:text-orange-700">Asado</span>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="tipo_evento" value="fiesta" class="peer sr-only" onchange="adaptarFormulario(this.value)" {{ $oldTipo == 'fiesta' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center justify-center p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl hover:bg-purple-50 hover:border-purple-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:shadow-md transition-all text-center">
                        <span class="text-3xl mb-2">🎉</span>
                        <span class="font-bold text-slate-700 text-xs md:text-sm peer-checked:text-purple-700">Fiesta</span>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="tipo_evento" value="personalizado" class="peer sr-only" onchange="adaptarFormulario(this.value)" {{ $oldTipo == 'personalizado' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center justify-center p-4 bg-slate-50 border-2 border-slate-200 rounded-2xl hover:bg-indigo-50 hover:border-indigo-200 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:shadow-md transition-all text-center">
                        <span class="text-3xl mb-2">⚙️</span>
                        <span class="font-bold text-slate-700 text-xs md:text-sm peer-checked:text-indigo-700">A la Carta</span>
                    </div>
                </label>

            </div>
        </div>

        {{-- ========================================== --}}
        {{-- SECCIÓN 2: INFORMACIÓN BÁSICA              --}}
        {{-- ========================================== --}}
        <div class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-200/60 shadow-sm space-y-6">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-4">
                <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                Datos Generales
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nombre / Título del Evento *</label>
                    <input type="text" name="titulo" required placeholder="Ej. Cumpleaños de Nico, Matrimonio Ana y Pedro..." value="{{ old('titulo') }}"
                           class="w-full px-5 py-4 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all bg-slate-50 text-base font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Fecha *</label>
                    <input type="date" name="fecha" required value="{{ old('fecha') }}"
                           class="w-full px-5 py-4 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all bg-slate-50 text-base font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hora *</label>
                    <input type="time" name="hora" required value="{{ old('hora', '16:00') }}"
                           class="w-full px-5 py-4 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all bg-slate-50 text-base font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Lugar / Local *</label>
                    <input type="text" name="lugar" required placeholder="Ej. Casa de los abuelos, Salón..." value="{{ old('lugar') }}"
                           class="w-full px-5 py-4 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all bg-slate-50 text-base font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Dirección Exacta *</label>
                    <input type="text" name="direccion" required placeholder="Calle, Número, Comuna" value="{{ old('direccion') }}"
                           class="w-full px-5 py-4 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all bg-slate-50 text-base font-medium">
                </div>
            </div>
            
            <div class="pt-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mensaje de Bienvenida (Opcional)</label>
                <textarea name="mensaje_bienvenida" rows="3" placeholder="¡Bienvenidos a nuestra celebración! Gracias por ser parte de este momento..."
                          class="w-full px-5 py-4 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all bg-slate-50 text-base font-medium resize-none">{{ old('mensaje_bienvenida') }}</textarea>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- SECCIÓN 3: BABY SHOWER (CONDICIONAL)       --}}
        {{-- ========================================== --}}
        <div id="seccion_baby_shower" class="hidden bg-gradient-to-br from-blue-50 to-indigo-50 p-6 md:p-8 rounded-[2rem] border border-blue-100 shadow-sm space-y-5 transition-all duration-300 transform origin-top">
            <h2 class="text-lg font-bold text-blue-900 flex items-center gap-2 mb-4">
                🍼 Datos Exclusivos del Baby Shower
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-blue-700 uppercase tracking-wider mb-2">Nombre del Bebé</label>
                    <input type="text" name="bebe_nombre" placeholder="Dejar vacío si es sorpresa" value="{{ old('bebe_nombre') }}"
                           class="w-full px-5 py-4 border border-white rounded-2xl outline-none focus:ring-4 focus:ring-blue-200 transition-all bg-white text-base font-medium shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-blue-700 uppercase tracking-wider mb-2">Sexo del Bebé</label>
                    <select name="bebe_sexo" class="w-full px-5 py-4 border border-white rounded-2xl outline-none focus:ring-4 focus:ring-blue-200 transition-all bg-white text-base font-bold text-slate-700 cursor-pointer shadow-sm">
                        <option value="Por revelar" {{ old('bebe_sexo') == 'Por revelar' ? 'selected' : '' }}>Por revelar / Sorpresa ❓</option>
                        <option value="Niño" {{ old('bebe_sexo') == 'Niño' ? 'selected' : '' }}>Niño 🧑</option>
                        <option value="Niña" {{ old('bebe_sexo') == 'Niña' ? 'selected' : '' }}>Niña 👧</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-blue-700 uppercase tracking-wider mb-2">Fecha Estimada de Nacimiento</label>
                    <input type="date" name="fecha_estimada" value="{{ old('fecha_estimada') }}"
                           class="w-full px-5 py-4 border border-white rounded-2xl outline-none focus:ring-4 focus:ring-blue-200 transition-all bg-white text-base font-medium shadow-sm">
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- SECCIÓN 4: MÓDULOS DEL ECOSISTEMA          --}}
        {{-- ========================================== --}}
        <div id="seccion_modulos_personalizados" class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-200/60 shadow-sm space-y-6">
            <div>
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">3</span>
                    Módulos Interactivos Activados
                </h2>
                <p class="text-slate-500 text-sm mt-2 ml-10">Las funciones predeterminadas se auto-seleccionan según tu evento. Puedes agregar o quitar las que desees.</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                
                @php
                    $modulosDisponibles = [
                        'regalos' => ['icon' => '🎁', 'title' => 'Lista de Deseos', 'desc' => 'Reserva de obsequios con catálogo y stock.'],
                        'cuota' => ['icon' => '🐮', 'title' => 'Cuota (La Vaca)', 'desc' => 'Pozo común con datos bancarios.'],
                        'insumos' => ['icon' => '🛒', 'title' => 'Lista de Insumos', 'desc' => 'Checklist "Yo lo llevo" (Cooperación).'],
                        'mesas' => ['icon' => '🪑', 'title' => 'Asignación de Mesas', 'desc' => 'Límite de sillas y distribución.'],
                        'itinerario' => ['icon' => '⏳', 'title' => 'Itinerario Cronológico', 'desc' => 'Agenda con hitos y horarios.'],
                        'menu' => ['icon' => '🍽️', 'title' => 'Menú & Alergias', 'desc' => 'Opciones gastronómicas y reporte médico.'],
                        'avisos' => ['icon' => '📢', 'title' => 'Tablón de Anuncios', 'desc' => 'Comunicados urgentes o informativos.'],
                        'musica' => ['icon' => '🎵', 'title' => 'Playlist Colaborativa', 'desc' => 'Sugerencias musicales de los invitados.'],
                        'galeria' => ['icon' => '📸', 'title' => 'Muro de Recuerdos', 'desc' => 'Feed de fotos con interacción por likes.'],
                        'presupuesto' => ['icon' => '💰', 'title' => 'Presupuesto Tracker', 'desc' => 'Control privado de gastos.'],
                        'checkin' => ['icon' => '🎟️', 'title' => 'Check-In en Puerta', 'desc' => 'Validación de entradas con código QR.'],
                    ];
                @endphp

                @foreach($modulosDisponibles as $key => $mod)
                    <label class="cursor-pointer relative {{ $key == 'checkin' ? 'sm:col-span-2' : '' }}">
                        <input type="checkbox" id="mod_{{ $key }}" name="modulos_activos[]" value="{{ $key }}" class="peer sr-only">
                        <div class="flex items-start gap-4 p-5 bg-white rounded-2xl border-2 border-slate-200 hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:shadow-md transition-all h-full">
                            <div class="text-3xl mt-1">{{ $mod['icon'] }}</div>
                            <div>
                                <strong class="block text-slate-800 text-sm mb-1 peer-checked:text-indigo-900">{{ $mod['title'] }}</strong>
                                <span class="block text-xs text-slate-500 leading-relaxed">{{ $mod['desc'] }}</span>
                            </div>
                            <div class="absolute top-5 right-5 w-5 h-5 rounded-full border-2 border-slate-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                <i class="fa-solid fa-check text-white text-[10px]"></i>
                            </div>
                        </div>
                    </label>
                @endforeach

            </div>
        </div>

        {{-- ========================================== --}}
        {{-- BOTONES DE ACCIÓN FLOTANTES                --}}
        {{-- ========================================== --}}
        <div class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-md border-t border-slate-200 flex justify-center sm:justify-end gap-3 z-40">
            <div class="max-w-4xl w-full mx-auto flex justify-end gap-3 px-4 md:px-0">
                <a href="{{ route('anfitrion.index') }}" class="px-6 py-4 rounded-full border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition-all cursor-pointer flex items-center">
                    Cancelar
                </a>
                <button type="submit" class="px-8 py-4 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold shadow-xl hover:shadow-indigo-200 hover:-translate-y-1 transition-all text-sm tracking-wide cursor-pointer flex items-center gap-2">
                    <span>🚀</span> Crear Evento
                </button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('error'))
    <script>
        Swal.fire({ icon: 'error', title: 'Hubo un problema', text: "{{ session('error') }}", confirmButtonColor: '#e11d48', borderRadius: '1rem' });
    </script>
@endif

<script>
    /**
     * Script de conmutación de secciones y autocompletado de módulos por plantilla
     */
    function adaptarFormulario(tipo) {
        const seccionBabyShower = document.getElementById('seccion_baby_shower');
        const checkboxes = document.querySelectorAll('input[name="modulos_activos[]"]');

        // Visualización del bloque específico del bebé con pequeña animación
        if (tipo === 'babyshower') {
            seccionBabyShower.classList.remove('hidden');
            setTimeout(() => seccionBabyShower.classList.remove('opacity-0', 'scale-95'), 10);
        } else {
            seccionBabyShower.classList.add('hidden', 'opacity-0', 'scale-95');
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

    // Ejecutar al cargar la página para revisar si hay una opción ya marcada (por la función old() de Laravel)
    document.addEventListener("DOMContentLoaded", function() {
        const radioSeleccionado = document.querySelector('input[name="tipo_evento"]:checked');
        if(radioSeleccionado) {
            adaptarFormulario(radioSeleccionado.value);
        } else {
            adaptarFormulario('personalizado');
        }
    });
</script>

@endsection