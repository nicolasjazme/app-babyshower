@extends('layouts.app')

@section('contenido')
@php
    $id = $evento['_id'] ?? $evento['id'] ?? '';
    $tipo = $evento['tipo_evento'] ?? 'personalizado';
@endphp

<div class="max-w-3xl mx-auto py-4 space-y-8">
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-8">
        
        <header class="border-b border-slate-100 pb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">🛠️ Panel de Configuración del Evento</h1>
                <p class="text-slate-500 text-xs mt-1">Actualiza la información general, visibilidad o tema estético de tu invitación.</p>
            </div>
            <span class="text-xs font-extrabold px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase">
                {{ str_replace('_', ' ', $tipo) }}
            </span>
        </header>

        <form action="{{ route('anfitrion.event.update', $id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- ESTADO DE VISIBILIDAD --}}
            <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/80">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">👁️ Visibilidad de la Página Pública</label>
                <select name="estado" class="w-full px-4 py-3 border border-slate-200 rounded-2xl bg-white outline-none focus:ring-2 focus:ring-indigo-200 text-sm font-bold text-slate-700 cursor-pointer">
                    <option value="publicado" {{ (isset($evento['estado']) && $evento['estado'] == 'publicado') ? 'selected' : '' }}>🟢 Publicado (Visible para invitados)</option>
                    <option value="oculto" {{ (isset($evento['estado']) && $evento['estado'] == 'oculto') ? 'selected' : '' }}>🟡 Oculto (Vista preliminar privada)</option>
                    <option value="cerrado" {{ (isset($evento['estado']) && $evento['estado'] == 'cerrado') ? 'selected' : '' }}>🔴 Cerrado (Evento finalizado)</option>
                </select>
            </div>

            {{-- COLOR DE TEMA CUSTOME --}}
            <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/80">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">🎨 Color de Acento Personalizado</label>
                <p class="text-xs text-slate-400 mb-3">Establece el tono principal para botones y acentos de tu invitación.</p>
                <div class="flex items-center gap-4">
                    <input type="color" name="color_tema" value="{{ $evento['configVisual']['colorTema'] ?? '#4f46e5' }}" 
                           class="w-14 h-12 p-1 border border-slate-200 rounded-2xl cursor-pointer bg-white shadow-xs">
                    <span class="text-xs text-slate-600 font-medium">Haz clic para seleccionar el color oficial de la fiesta.</span>
                </div>
            </div>

            {{-- DATOS BÁSICOS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Título del Evento</label>
                    <input type="text" name="titulo" required value="{{ $evento['titulo'] ?? '' }}" class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 text-sm bg-slate-50/50 font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Fecha</label>
                    <input type="date" name="fecha" required value="{{ isset($evento['fecha']) ? date('Y-m-d', strtotime($evento['fecha'])) : '' }}" class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 text-sm bg-slate-50/50 font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Hora</label>
                    <input type="time" name="hora" required value="{{ $evento['hora'] ?? '' }}" class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 text-sm bg-slate-50/50 font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Lugar / Salón</label>
                    <input type="text" name="lugar" required value="{{ $evento['lugar'] ?? '' }}" class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 text-sm bg-slate-50/50 font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Dirección Exacta</label>
                    <input type="text" name="direccion" required value="{{ $evento['direccion'] ?? '' }}" class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 text-sm bg-slate-50/50 font-medium">
                </div>
            </div>

            {{-- SECCIÓN CONDICIONAL DEL BEBÉ --}}
            @if($tipo === 'baby_shower')
                <div class="bg-blue-50/40 p-5 rounded-2xl border border-blue-100 space-y-4">
                    <h3 class="text-xs font-bold text-blue-900 uppercase tracking-wider">👶 Detalles del Bebé</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-blue-600 uppercase mb-1">Nombre del Bebé</label>
                            <input type="text" name="bebe_nombre" value="{{ $evento['bebeNombre'] ?? '' }}" class="w-full px-4 py-2.5 border border-blue-200 rounded-xl outline-none text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-blue-600 uppercase mb-1">Sexo del Bebé</label>
                            <select name="bebe_sexo" class="w-full px-4 py-2.5 border border-blue-200 rounded-xl outline-none text-xs bg-white font-bold text-slate-700">
                                <option value="Por revelar" {{ (isset($evento['bebeSexo']) && $evento['bebeSexo'] == 'Por revelar') ? 'selected' : '' }}>Por revelar ❓</option>
                                <option value="Niño" {{ (isset($evento['bebeSexo']) && $evento['bebeSexo'] == 'Niño') ? 'selected' : '' }}>Niño 🧑</option>
                                <option value="Niña" {{ (isset($evento['bebeSexo']) && $evento['bebeSexo'] == 'Niña') ? 'selected' : '' }}>Niña 👧</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            {{-- BOTONES DE GUARDADO --}}
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('anfitrion.index') }}" class="px-6 py-3 rounded-2xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition-all cursor-pointer">
                    Cancelar
                </a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-2xl shadow-md transition-all text-xs uppercase tracking-wider cursor-pointer">
                    💾 Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection