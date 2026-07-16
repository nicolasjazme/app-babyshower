@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;

    // Fallback de datos simulando la respuesta del backend modular
    $hitos = $itinerarioLista ?? [];
    
    // Ordenamos cronológicamente los hitos por hora para garantizar una visualización correcta
    $hitosOrdenados = collect($hitos)->sortBy('hora')->all();
@endphp

<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl">⏳</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Itinerario / Cronograma</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Planifica los momentos clave de tu celebración y muéstrales a tus invitados una línea de tiempo elegante con actividades paso a paso.
            </p>
        </div>
        
        <button onclick="toggleModal('modal-nuevo-hito', true)" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-plus"></i> Agregar Actividad
        </button>
    </div>

    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 mb-8 flex items-center gap-2">🕒 Línea de Tiempo del Evento</h2>

        @if(empty($hitosOrdenados))
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">El cronograma está vacío</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Define actividades como el recibimiento de invitados, banquetes o sorpresas para estructurar tu fiesta.</p>
            </div>
        @else
            <div class="relative border-l-2 border-slate-100 ml-4 md:ml-32 space-y-8 pb-4">
                @foreach($hitosOrdenados as $hito)
                    <div class="relative pl-8 group">
                        
                        <div class="hidden md:block absolute -left-36 top-1 w-28 text-right">
                            <span class="font-mono font-black text-slate-800 text-base">
                                {{ \Carbon\Carbon::parse($hito['hora'])->format('H:i') }}
                            </span>
                            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">HRS</span>
                        </div>

                        <div class="absolute -left-[21px] top-0 w-10 h-10 rounded-full bg-white border-2 border-amber-400 text-amber-500 flex items-center justify-center text-sm shadow-sm group-hover:scale-110 transition-transform">
                            <i class="{{ $hito['icono'] ?? 'fa-solid fa-star' }} w-4 text-center"></i>
                        </div>

                        <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:border-amber-200 transition-all">
                            <div>
                                <div class="md:hidden flex items-center gap-1 text-amber-600 font-mono font-bold text-xs mb-1">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>{{ \Carbon\Carbon::parse($hito['hora'])->format('H:i') }} hrs</span>
                                </div>
                                
                                <h3 class="font-bold text-slate-800 text-base">{{ $hito['titulo'] }}</h3>
                                @if(!empty($hito['descripcion']))
                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed max-w-xl">{{ $hito['descripcion'] }}</p>
                                @endif
                            </div>

                            <div class="flex shrink-0 sm:self-center">
                                <form action="/eventos/modulos/itinerario/{{ $hito['_id'] ?? $hito['id'] }}" method="POST" onsubmit="return confirm('¿Estás seguro de quitar este momento del itinerario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-colors" title="Eliminar actividad">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div id="modal-nuevo-hito" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-nuevo-hito', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">⏳ Planificar Actividad</h3>
        <p class="text-xs text-slate-500 mb-6">Agrega un momento programado a la agenda oficial del evento.</p>

        <form action="/eventos/modulos/itinerario" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="evento_id" value="{{ $eventoId }}">

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Título del Bloque *</label>
                    <input type="text" name="titulo" required placeholder="Ej. Recepción, Cóctel, Vals"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-amber-200 transition-all text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Hora *</label>
                    <input type="time" name="hora" required
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-amber-200 transition-all text-sm font-semibold">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Ícono Representativo *</label>
                <select name="icono" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-amber-200 transition-all text-sm font-semibold bg-white cursor-pointer text-slate-700">
                    <option value="fa-solid fa-door-open">🚪 Entrada / Apertura</option>
                    <option value="fa-solid fa-champagne-glasses">🥂 Brindis / Cóctel</option>
                    <option value="fa-solid fa-utensils">🍽️ Almuerzo / Cena</option>
                    <option value="fa-solid fa-cake-candles">🎂 Pastel / Celebración</option>
                    <option value="fa-solid fa-music">🎵 Baile / Fiesta</option>
                    <option value="fa-solid fa-camera">📸 Sesión de Fotos</option>
                    <option value="fa-solid fa-gift">🎁 Apertura de Regalos</option>
                    <option value="fa-solid fa-star">⭐ Hito Especial / Sorpresa</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Breve Descripción (Opcional)</label>
                <textarea name="descripcion" rows="2" placeholder="Ej. Palabras de bienvenida y ubicación en las mesas correspondientes..."
                          class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-amber-200 transition-all text-sm font-medium"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-nuevo-hito', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Guardar Actividad
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