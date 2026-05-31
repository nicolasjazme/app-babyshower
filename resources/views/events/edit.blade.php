@extends('layouts.app')

@section('contenido')

<div class="max-w-3xl w-full">
        <div class="bg-white/80 backdrop-blur-md p-10 rounded-3xl shadow-xl border border-stone-200">
            
            <header class="mb-8 border-b border-stone-200 pb-6">
                <h1 class="text-3xl font-extrabold text-stone-800 tracking-tight">🛠️ Panel de Configuración</h1>
                <p class="text-stone-600 mt-1 text-sm">Modifica los detalles del evento, cambia su visibilidad o personaliza su estilo visual.</p>
            </header>

            <form action="/baby-shower/{{ $id }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="bg-white/50 p-6 rounded-2xl border border-stone-200">
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-3">👁️ Visibilidad de la Página (RF-13)</label>
                    <select name="estado" class="w-full px-4 py-3 border border-stone-200 rounded-2xl bg-white outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm font-bold text-stone-700 cursor-pointer">
                        <option value="publicado" {{ (isset($evento['estado']) && $evento['estado'] == 'publicado') ? 'selected' : '' }}>🟢 Publicado (Visible)</option>
                        <option value="oculto" {{ (isset($evento['estado']) && $evento['estado'] == 'oculto') ? 'selected' : '' }}>🟡 Oculto (Privado)</option>
                        <option value="cerrado" {{ (isset($evento['estado']) && $evento['estado'] == 'cerrado') ? 'selected' : '' }}>🔴 Cerrado (Finalizado)</option>
                    </select>
                </div>

                <div class="bg-white/50 p-6 rounded-2xl border border-stone-200">
                    <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-2">🎨 Color de Acento (RF-14)</label>
                    <p class="text-xs text-stone-400 mb-4">Define el color principal de los botones y detalles para tus invitados.</p>
                    <div class="flex items-center gap-4">
                        <input type="color" name="color_tema" value="{{ $evento['configVisual']['colorTema'] ?? '#4f46e5' }}" 
                               class="w-16 h-14 p-1 border border-stone-200 rounded-2xl cursor-pointer bg-white shadow-sm">
                        <span class="text-xs text-stone-600 font-medium">Haz clic en el cuadro para elegir tu color.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Fecha</label>
                        <input type="date" name="fecha" required value="{{ isset($evento['fecha']) ? date('Y-m-d', strtotime($evento['fecha'])) : '' }}" class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-medium">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Hora</label>
                        <input type="text" name="hora" required value="{{ $evento['hora'] ?? '' }}" class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-medium">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Lugar</label>
                        <input type="text" name="lugar" required value="{{ $evento['lugar'] ?? '' }}" class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-medium">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Dirección</label>
                        <input type="text" name="direccion" required value="{{ $evento['direccion'] ?? '' }}" class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nombre del Bebé</label>
                        <input type="text" name="bebe_nombre" value="{{ $evento['bebeNombre'] ?? '' }}" class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-medium">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Sexo del Bebé</label>
                        <select name="bebe_sexo" class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-sm bg-white/60 font-bold text-stone-700 cursor-pointer">
                            <option value="Por revelar" {{ (isset($evento['bebeSexo']) && $evento['bebeSexo'] == 'Por revelar') ? 'selected' : '' }}>Por revelar ❓</option>
                            <option value="Niño" {{ (isset($evento['bebeSexo']) && $evento['bebeSexo'] == 'Niño') ? 'selected' : '' }}>Niño 🧑</option>
                            <option value="Niña" {{ (isset($evento['bebeSexo']) && $evento['bebeSexo'] == 'Niña') ? 'selected' : '' }}>Niña 👧</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-stone-200">
                    <a href="/admin" class="px-6 py-3 rounded-2xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100 transition-all cursor-pointer">
                        Cancelar
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-2xl bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 font-extrabold shadow-md transition-all text-xs uppercase tracking-wider cursor-pointer">
                        💾 Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection