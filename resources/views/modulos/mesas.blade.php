@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;

    // Fallback de datos simulando la estructura del backend modular
    $mesas = $mesasLista ?? [];
    $invitadosSinMesa = $invitadosDisponibles ?? [];
@endphp

<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl">🪑</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Organizador de Mesas</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Crea mesas para tu evento, define el límite de sillas y organiza a tus invitados cómodamente.
            </p>
        </div>
        
        <button onclick="toggleModal('modal-nueva-mesa', true)" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-plus"></i> Crear Nueva Mesa
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">🍽️ Distribución del Salón</h2>

            @if(empty($mesas))
                <div class="bg-white text-center py-16 rounded-3xl border border-slate-200/80 shadow-sm">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-chair"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700">No has creado mesas aún</h3>
                    <p class="text-xs text-slate-400 mt-1">Crea tu primera mesa (ej: "Mesa Familiar", "Mesa Amigos") para empezar a asignar lugares.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($mesas as $mesa)
                        @php
                            $invitadosAsignados = $mesa['invitados'] ?? [];
                            $sillasOcupadas = count($invitadosAsignados);
                            $sillasTotales = $mesa['limite_sillas'] ?? 8;
                            $estaLlena = $sillasOcupadas >= $sillasTotales;
                        @endphp
                        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-indigo-200 transition-all">
                            <div>
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-black text-slate-800 text-base flex items-center gap-1.5">
                                            🏢 {{ $mesa['nombre'] }}
                                        </h3>
                                        <span class="text-[10px] text-slate-400 font-bold block uppercase mt-0.5">
                                            Capacidad: {{ $sillasTotales }} Sillas
                                        </span>
                                    </div>
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $estaLlena ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $sillasOcupadas }} / {{ $sillasTotales }}
                                    </span>
                                </div>

                                <div class="bg-slate-50 rounded-2xl p-3 min-h-[100px] border border-slate-100 space-y-2">
                                    @if(empty($invitadosAsignados))
                                        <p class="text-xs text-slate-400 italic text-center py-6">Mesa vacía</p>
                                    @else
                                        @foreach($invitadosAsignados as $invitadoMesa)
                                            <div class="bg-white px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 shadow-sm border border-slate-100 flex justify-between items-center">
                                                <span>👤 {{ $invitadoMesa['nombre'] }}</span>
                                                <form action="/eventos/modulos/mesas/remover" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="mesa_id" value="{{ $mesa['_id'] ?? $mesa['id'] }}">
                                                    <input type="hidden" name="invitado_id" value="{{ $invitadoMesa['_id'] ?? $invitadoMesa['id'] }}">
                                                    <button type="submit" class="text-slate-400 hover:text-rose-500 transition-colors">
                                                        <i class="fa-solid fa-circle-minus"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex justify-end">
                                <form action="/eventos/modulos/mesas/{{ $mesa['_id'] ?? $mesa['id'] }}" method="POST" onsubmit="return confirm('¿Quitar esta mesa? Todos los invitados asignados quedarán libres.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-slate-400 hover:text-red-500 font-bold transition-colors flex items-center gap-1">
                                        <i class="fa-solid fa-trash-can"></i> Desarmar Mesa
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">👥 Invitados por Ubicar</h2>
                <p class="text-xs text-slate-400 mb-4">Lista de asistentes confirmados que aún no tienen una silla asignada.</p>

                @if(empty($invitadosSinMesa))
                    <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-xs text-slate-400 italic font-medium">¡Todos ubicados u organizados! 🎉</p>
                    </div>
                @else
                    <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1">
                        @foreach($invitadosSinMesa as $invitadoLibre)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex flex-col gap-2">
                                <span class="text-xs font-bold text-slate-700">👤 {{ $invitadoLibre['nombre'] }}</span>
                                
                                <form action="/eventos/modulos/mesas/asignar" method="POST" class="flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="invitado_id" value="{{ $invitadoLibre['_id'] ?? $invitadoLibre['id'] }}">
                                    <select name="mesa_id" required class="flex-1 px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium outline-none">
                                        <option value="">Elegir Mesa...</option>
                                        @foreach($mesas as $mOption)
                                            @if(count($mOption['invitados'] ?? []) < ($mOption['limite_sillas'] ?? 8))
                                                <option value="{{ $mOption['_id'] ?? $mOption['id'] }}">{{ $mOption['nombre'] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-3 py-1.5 rounded-lg transition-colors">
                                        Ubicar
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-6 border-t border-slate-100 pt-4 text-xs text-slate-400 flex items-start gap-1.5 leading-relaxed">
                <i class="fa-solid fa-circle-info mt-0.5 text-slate-400 text-sm shrink-0"></i>
                Solo los invitados confirmados en el RSVP de la plataforma aparecerán elegibles en este listado.
            </div>
        </div>
    </div>
</div>

<div id="modal-nueva-mesa" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-nueva-mesa', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">🪑 Configurar Nueva Mesa</h3>
        <p class="text-xs text-slate-500 mb-6">Añade una mesa al comedor definiendo un límite de sillas físicas.</p>

        <form action="/eventos/modulos/mesas" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="evento_id" value="{{ $eventoId }}">

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nombre de la Mesa *</label>
                <input type="text" name="nombre" required placeholder="Ej. Mesa Principal, Familia Anfitrión, etc."
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all text-sm font-medium">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Límite de Sillas / Capacidad *</label>
                <input type="number" name="limite_sillas" required min="1" max="20" value="8"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all text-sm font-semibold">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-nueva-mesa', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Guardar Mesa
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