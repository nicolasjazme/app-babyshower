@extends('layouts.app')

@section('contenido')
@php
    $id = $evento['_id'] ?? $evento['id'] ?? '';
    $tipo = $evento['tipo_evento'] ?? 'personalizado';
@endphp

<div class="max-w-4xl mx-auto py-4 space-y-8 pb-24">
    
    <header class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">🛠️ Configuración del Evento</h1>
            <p class="text-slate-500 text-sm">Actualiza la información general, visibilidad o tema estético de tu invitación.</p>
        </div>
        <span class="inline-block text-center text-xs font-extrabold px-4 py-2 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-wider">
            {{ str_replace('_', ' ', $tipo) }}
        </span>
    </header>

    <form action="{{ route('anfitrion.event.update', $id) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- ========================================== --}}
        {{-- VISIBILIDAD (RADIO BUTTONS VISUALES)       --}}
        {{-- ========================================== --}}
        <div class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-200/60 shadow-sm space-y-6">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-2">
                <span class="text-2xl">👁️</span> Visibilidad de la Invitación
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php $estadoActual = $evento['estado'] ?? 'publicado'; @endphp
                
                <label class="cursor-pointer">
                    <input type="radio" name="estado" value="publicado" class="peer sr-only" {{ $estadoActual == 'publicado' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center justify-center p-5 bg-slate-50 border-2 border-slate-200 rounded-2xl hover:bg-emerald-50 hover:border-emerald-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:shadow-md transition-all text-center h-full">
                        <span class="text-3xl mb-2">🟢</span>
                        <strong class="font-bold text-slate-800 text-sm peer-checked:text-emerald-800">Publicado</strong>
                        <span class="text-xs text-slate-500 mt-1">Visible para tus invitados</span>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="estado" value="oculto" class="peer sr-only" {{ $estadoActual == 'oculto' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center justify-center p-5 bg-slate-50 border-2 border-slate-200 rounded-2xl hover:bg-amber-50 hover:border-amber-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:shadow-md transition-all text-center h-full">
                        <span class="text-3xl mb-2">🟡</span>
                        <strong class="font-bold text-slate-800 text-sm peer-checked:text-amber-800">Oculto</strong>
                        <span class="text-xs text-slate-500 mt-1">Vista preliminar privada</span>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="estado" value="cerrado" class="peer sr-only" {{ $estadoActual == 'cerrado' ? 'checked' : '' }}>
                    <div class="flex flex-col items-center justify-center p-5 bg-slate-50 border-2 border-slate-200 rounded-2xl hover:bg-red-50 hover:border-red-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:shadow-md transition-all text-center h-full">
                        <span class="text-3xl mb-2">🔴</span>
                        <strong class="font-bold text-slate-800 text-sm peer-checked:text-red-800">Cerrado</strong>
                        <span class="text-xs text-slate-500 mt-1">El evento ha finalizado</span>
                    </div>
                </label>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- COLOR DEL TEMA                             --}}
        {{-- ========================================== --}}
        <div class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col md:flex-row items-center gap-6">
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-lg font-bold text-slate-800 flex items-center justify-center md:justify-start gap-2 mb-1">
                    <span class="text-2xl">🎨</span> Color de Acento
                </h2>
                <p class="text-sm text-slate-500">Establece el tono principal para los botones y acentos de tu invitación pública.</p>
            </div>
            <div class="shrink-0 relative group">
                <input type="color" name="color_tema" value="{{ $evento['configVisual']['colorTema'] ?? '#4f46e5' }}" 
                       class="w-20 h-20 rounded-full cursor-pointer bg-white shadow-md border-4 border-white transform transition-transform group-hover:scale-105 p-0 overflow-hidden appearance-none" style="clip-path: circle(50%);">
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- DATOS BÁSICOS                              --}}
        {{-- ========================================== --}}
        <div class="bg-white p-6 md:p-8 rounded-[2rem] border border-slate-200/60 shadow-sm space-y-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Información Principal</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Título del Evento</label>
                    <input type="text" name="titulo" required value="{{ $evento['titulo'] ?? '' }}" 
                           class="w-full px-5 py-4 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all bg-slate-50 text-base font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Fecha</label>
                    <input type="date" name="fecha" required value="{{ isset($evento['fecha']) ? date('Y-m-d', strtotime($evento['fecha'])) : '' }}" 
                           class="w-full px-5 py-4 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all bg-slate-50 text-base font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hora</label>
                    <input type="time" name="hora" required value="{{ $evento['hora'] ?? '' }}" 
                           class="w-full px-5 py-4 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all bg-slate-50 text-base font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Lugar / Salón</label>
                    <input type="text" name="lugar" required value="{{ $evento['lugar'] ?? '' }}" 
                           class="w-full px-5 py-4 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all bg-slate-50 text-base font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Dirección Exacta</label>
                    <input type="text" name="direccion" required value="{{ $evento['direccion'] ?? '' }}" 
                           class="w-full px-5 py-4 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all bg-slate-50 text-base font-medium">
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- SECCIÓN CONDICIONAL DEL BEBÉ               --}}
        {{-- ========================================== --}}
        @if($tipo === 'baby_shower')
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 md:p-8 rounded-[2rem] border border-blue-100 shadow-sm space-y-6">
                <h2 class="text-lg font-bold text-blue-900 flex items-center gap-2 mb-2">
                    🍼 Detalles del Bebé
                </h2>
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-blue-700 uppercase tracking-wider mb-2">Nombre del Bebé</label>
                        <input type="text" name="bebe_nombre" value="{{ $evento['bebeNombre'] ?? '' }}" 
                               class="w-full px-5 py-4 border border-white rounded-2xl outline-none focus:ring-4 focus:ring-blue-200 transition-all bg-white text-base font-medium shadow-sm">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-blue-700 uppercase tracking-wider mb-3">Sexo del Bebé</label>
                        <div class="grid grid-cols-3 gap-3">
                            @php $sexoActual = $evento['bebeSexo'] ?? 'Por revelar'; @endphp
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="bebe_sexo" value="Por revelar" class="peer sr-only" {{ $sexoActual == 'Por revelar' ? 'checked' : '' }}>
                                <div class="flex flex-col items-center justify-center p-3 bg-white border-2 border-transparent rounded-2xl peer-checked:border-blue-500 peer-checked:bg-blue-100 peer-checked:shadow-sm transition-all text-center">
                                    <span class="text-2xl mb-1">❓</span>
                                    <span class="text-xs font-bold text-slate-700 peer-checked:text-blue-800">Sorpresa</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="bebe_sexo" value="Niño" class="peer sr-only" {{ $sexoActual == 'Niño' ? 'checked' : '' }}>
                                <div class="flex flex-col items-center justify-center p-3 bg-white border-2 border-transparent rounded-2xl peer-checked:border-blue-500 peer-checked:bg-blue-100 peer-checked:shadow-sm transition-all text-center">
                                    <span class="text-2xl mb-1">🧑</span>
                                    <span class="text-xs font-bold text-slate-700 peer-checked:text-blue-800">Niño</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="bebe_sexo" value="Niña" class="peer sr-only" {{ $sexoActual == 'Niña' ? 'checked' : '' }}>
                                <div class="flex flex-col items-center justify-center p-3 bg-white border-2 border-transparent rounded-2xl peer-checked:border-blue-500 peer-checked:bg-blue-100 peer-checked:shadow-sm transition-all text-center">
                                    <span class="text-2xl mb-1">👧</span>
                                    <span class="text-xs font-bold text-slate-700 peer-checked:text-blue-800">Niña</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ========================================== --}}
        {{-- BOTONES DE ACCIÓN FLOTANTES                --}}
        {{-- ========================================== --}}
        <div class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-md border-t border-slate-200 flex justify-center sm:justify-end gap-3 z-40">
            <div class="max-w-4xl w-full mx-auto flex justify-end gap-3 px-4 md:px-0">
                <a href="{{ route('anfitrion.index') }}" class="px-6 py-4 rounded-full border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-100 transition-all cursor-pointer flex items-center">
                    Cancelar
                </a>
                <button type="submit" class="px-8 py-4 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold shadow-xl hover:shadow-indigo-200 hover:-translate-y-1 transition-all text-sm tracking-wide cursor-pointer flex items-center gap-2">
                    <span>💾</span> Guardar Cambios
                </button>
            </div>
        </div>

    </form>
</div>
@endsection