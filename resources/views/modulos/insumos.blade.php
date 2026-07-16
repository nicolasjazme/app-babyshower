@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;

    // Fallback de datos simulando la estructura del backend modular
    $insumos = $insumosLista ?? [];
    
    // Separamos los insumos en cubiertos (ya tomados por invitados) y pendientes
    $pendientes = collect($insumos)->filter(function($item) { return empty($item['nombre_invitado']); })->all();
    $cubiertos = collect($insumos)->filter(function($item) { return !empty($item['nombre_invitado']); })->all();
@endphp

<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl">🛒</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Lista de Insumos (Cooperación)</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Define qué elementos faltan para la celebración y monitorea cuáles invitados se han asignado la tarea de llevarlos.
            </p>
        </div>
        
        <button onclick="toggleModal('modal-nuevo-insumo', true)" class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-plus"></i> Añadir Insumo
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                ⏳ Elementos por Conseguir
                <span class="text-xs bg-orange-100 text-orange-800 px-2 py-0.5 rounded-md font-bold">{{ count($pendientes) }}</span>
            </h2>
            
            @if(empty($pendientes))
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-lg mx-auto mb-3">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>
                    <p class="text-xs text-slate-400 italic">No hay insumos pendientes.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($pendientes as $item)
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between gap-4">
                            <div>
                                <strong class="block text-sm text-slate-800">📦 {{ $item['nombre'] }}</strong>
                                @if(!empty($item['cantidad']))
                                    <span class="block text-xs text-slate-400 font-semibold mt-0.5">Cantidad requerida: {{ $item['cantidad'] }}</span>
                                @endif
                            </div>

                            <form action="/eventos/modulos/insumos/{{ $item['_id'] ?? $item['id'] }}" method="POST" onsubmit="return confirm('¿Quitar este insumo de la lista de compras?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                ✅ Coordinados y Asignados
                <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md font-bold">{{ count($cubiertos) }}</span>
            </h2>

            @if(empty($cubiertos))
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-lg mx-auto mb-3">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <p class="text-xs text-slate-400 italic">Ningún invitado se ha anotado todavía.</p>
                </div>
            @else
                <div class="space-y-3 max-h-[450px] overflow-y-auto pr-1">
                    @foreach($cubiertos as $itemCubierto)
                        <div class="p-4 bg-emerald-50/40 border border-emerald-100 rounded-2xl flex items-center justify-between gap-4">
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">🛒 {{ $itemCubierto['nombre'] }}</h3>
                                <span class="inline-block mt-1 text-[10px] font-black px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-md uppercase">
                                    Lo trae: {{ $itemCubierto['nombre_invitado'] }}
                                </span>
                            </div>
                            
                            <form action="/eventos/modulos/insumos/{{ $itemCubierto['_id'] ?? $itemCubierto['id'] }}/liberar" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="p-2 text-slate-400 hover:text-orange-500 transition-colors" title="Liberar y volver a poner disponible">
                                    <i class="fa-solid fa-arrow-rotate-left text-sm"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

<div id="modal-nuevo-insumo" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-nuevo-insumo', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">🛒 Añadir Requerimiento</h3>
        <p class="text-xs text-slate-500 mb-6">Indica qué artículo se necesita coordinar para la fiesta.</p>

        <form action="/eventos/modulos/insumos" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="evento_id" value="{{ $eventoId }}">

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nombre del Artículo / Insumo *</label>
                <input type="text" name="nombre" required placeholder="Ej. Carbón, Bolsas de Hielo, Papas Fritas"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-orange-200 transition-all text-sm font-medium">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Cantidad o Detalle *</label>
                <input type="text" name="cantidad" required placeholder="Ej. 2 bolsas grandes, 3 botellas de 3L"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-orange-200 transition-all text-sm font-medium">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-nuevo-insumo', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Guardar Insumo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection