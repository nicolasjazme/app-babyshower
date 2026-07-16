@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;

    // Fallback de datos simulando la respuesta de la colección del backend modular
    $avisos = $avisosLista ?? [];
    
    // Ordenamos para que los avisos más recientes o urgentes tengan prioridad visual
    $avisosOrdenados = collect($avisos)->sortByDesc('created_at')->all();
@endphp

<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl">📢</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tablón de Avisos</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Publica anuncios importantes, actualizaciones del recinto o recordatorios para que tus invitados los vean en su invitación.
            </p>
        </div>
        
        <button onclick="toggleModal('modal-nuevo-aviso', true)" class="inline-flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-bullhorn"></i> Publicar Anuncio
        </button>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">📋 Historial de Comunicados</h2>

        @if(empty($avisosOrdenados))
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-sky-50 text-sky-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                    <i class="fa-solid fa-message"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tu tablón está limpio</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">¿Hay algún cambio de clima o recordatorio de vestimenta? Ponlo aquí para mantener a todos informados.</p>
            </div>
        @else
            <div class="space-y-4 max-w-3xl">
                @foreach($avisosOrdenados as $aviso)
                    @php
                        $esUrgente = ($aviso['prioridad'] ?? 'normal') === 'urgente';
                    @endphp
                    <div class="p-5 rounded-2xl border transition-all flex flex-col sm:flex-row sm:items-start justify-between gap-4 {{ $esUrgente ? 'bg-amber-50/60 border-amber-200' : 'bg-slate-50/60 border-slate-100' }}">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                @if($esUrgente)
                                    <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 bg-amber-500 text-white rounded-md uppercase tracking-wider animate-pulse">
                                        🚨 Urgente
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 bg-slate-200 text-slate-700 rounded-md uppercase tracking-wider">
                                        📌 Informativo
                                    </span>
                                @endif
                                <span class="text-[10px] text-slate-400 font-semibold">
                                    {{ isset($aviso['created_at']) ? \Carbon\Carbon::parse($aviso['created_at'])->diffForHumans() : 'Reciente' }}
                                </span>
                            </div>
                            
                            <h3 class="font-bold text-slate-800 text-base">{{ $aviso['titulo'] }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed font-medium">{{ $aviso['contenido'] }}</p>
                        </div>

                        <div class="shrink-0 self-end sm:self-start">
                            <form action="/eventos/modulos/avisos/{{ $aviso['_id'] ?? $aviso['id'] }}" method="POST" onsubmit="return confirm('¿Quitar este comunicado del tablón? Desaparecerá de la vista de los invitados.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-colors" title="Eliminar anuncio">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div id="modal-nuevo-aviso" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-nuevo-aviso', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">📢 Redactar Anuncio</h3>
        <p class="text-xs text-slate-500 mb-6">Envía un comunicado oficial a la cartelera interactiva de tu evento.</p>

        <form action="/eventos/modulos/avisos" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="evento_id" value="{{ $eventoId }}">

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Título del Aviso *</label>
                <input type="text" name="titulo" required placeholder="Ej. ¡Cambio de Salón!, Traer traje de baño, Estacionamiento"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-sky-200 transition-all text-sm font-medium">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nivel de Prioridad (Flag) *</label>
                <select name="prioridad" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-sky-200 transition-all text-sm font-semibold bg-white cursor-pointer text-slate-700">
                    <option value="normal">📌 Normal / Informativo general</option>
                    <option value="urgente">🚨 Urgente / Alerta inmediata</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mensaje o Detalle del Aviso *</label>
                <textarea name="contenido" required rows="4" placeholder="Escribe de forma clara y concisa las instrucciones para tus invitados..."
                          class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-sky-200 transition-all text-sm font-medium"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-nuevo-aviso', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-sky-500 hover:bg-sky-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Publicar Ahora
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