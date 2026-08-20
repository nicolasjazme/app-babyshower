@extends('layouts.app')

@section('content')
{{-- El fondo cambia automáticamente gracias al gradiente calculado en el controlador --}}
<div class="min-h-screen bg-gradient-to-br {{ $estiloTema['bg_gradient'] }} py-8 px-4 sm:px-6 lg:px-8 transition-all duration-500 pb-20">
    
    <div class="max-w-3xl mx-auto space-y-6">
        
        {{-- ========================================== --}}
        {{-- TARJETA PRINCIPAL (HERO)                   --}}
        {{-- ========================================== --}}
        <div class="bg-white/90 backdrop-blur-sm rounded-[2rem] shadow-xl overflow-hidden border border-white/50 transform transition duration-300">
            <div class="h-40 bg-gradient-to-r {{ $estiloTema['bg_gradient'] }} flex items-center justify-center relative opacity-90">
                <div class="absolute -bottom-12 bg-white p-5 rounded-full shadow-lg text-6xl border-4 border-white/80">
                    {{ $evento['configVisual']['iconoEmoji'] ?? '🎉' }}
                </div>
            </div>

            <div class="pt-16 pb-10 px-6 sm:px-12 text-center space-y-4">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $estiloTema['badge_color'] }}">
                    {{ str_replace('_', ' ', $evento['tipo_evento']) }}
                </span>
                
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                    {{ $evento['titulo'] }}
                </h1>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-8 text-slate-600 font-medium">
                    <div class="flex flex-col items-center justify-center bg-slate-50 rounded-2xl p-4">
                        <span class="text-2xl mb-1">📅</span>
                        <span class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($evento['fecha'])->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex flex-col items-center justify-center bg-slate-50 rounded-2xl p-4">
                        <span class="text-2xl mb-1">⏰</span>
                        <span class="text-sm font-bold text-slate-800">{{ $evento['hora'] }} Hrs</span>
                    </div>
                    <div class="flex flex-col items-center justify-center bg-slate-50 rounded-2xl p-4">
                        <span class="text-2xl mb-1">📍</span>
                        <span class="text-sm font-bold text-slate-800 truncate w-full text-center" title="{{ $evento['ubicacion'] }}">{{ $evento['ubicacion'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- CONFIRMACIÓN DE ASISTENCIA                 --}}
        {{-- ========================================== --}}
        <div class="bg-white rounded-[2rem] shadow-lg border border-slate-100 p-6 md:p-10">
            <div class="flex flex-col items-center text-center mb-6">
                <span class="text-4xl mb-2">👋</span>
                <h2 class="text-2xl font-bold text-slate-800">Confirma tu Asistencia</h2>
                <p class="text-slate-500 text-sm mt-1">¡Nos encantaría que nos acompañes!</p>
            </div>

            <form action="{{ route('asistencia.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="evento_id" value="{{ $evento['_id'] }}">
                
                <div>
                    <input type="text" name="nombre" required placeholder="Tu Nombre Completo (Ej: Juan Pérez)" 
                           class="w-full px-6 py-4 rounded-2xl border-2 border-slate-100 focus:outline-none focus:border-indigo-500 bg-slate-50 text-center font-medium text-lg transition-colors placeholder:text-slate-400">
                </div>
                
                <button type="submit" class="w-full py-4 px-6 rounded-2xl font-black text-lg text-white transition-all shadow-xl hover:-translate-y-1 active:scale-95 {{ $estiloTema['color_boton'] ?? 'bg-indigo-600 hover:bg-indigo-700' }}">
                    Confirmar Asistencia
                </button>
            </form>
        </div>

        {{-- ========================================== --}}
        {{-- LISTA DE ÍTEMS / REGALOS                   --}}
        {{-- ========================================== --}}
        <div class="bg-white rounded-[2rem] shadow-lg border border-slate-100 p-6 md:p-10">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-slate-900">
                    @if($evento['tipo_evento'] === 'asado' || $evento['tipo_evento'] === 'fiesta')
                        🍖 ¿Con qué vas a cooperar?
                    @else
                        🎁 Lista de Regalos Ideales
                    @endif
                </h2>
                <p class="text-slate-500 text-sm mt-2 max-w-lg mx-auto">
                    @if($evento['tipo_evento'] === 'asado' || $evento['tipo_evento'] === 'fiesta')
                        Selecciona un insumo de la lista para comprometerte a traerlo. ¡Hagamos el mejor evento!
                    @else
                        Elige un obsequio que desees regalar para evitar que se repita.
                    @endif
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($items as $item)
                    <div class="relative overflow-hidden border-2 border-slate-100 rounded-2xl p-5 flex flex-col justify-between bg-white hover:border-indigo-200 hover:shadow-md transition-all group">
                        
                        <div class="mb-4">
                            <h4 class="font-black text-slate-800 text-lg leading-tight">{{ $item['nombre_articulo'] ?? $item['nombre_item'] }}</h4>
                            <p class="text-sm text-slate-500 font-medium mt-1">Cantidad: {{ $item['cantidad_requerida'] ?? 1 }}</p>
                        </div>

                        @if(isset($item['asignado_a']) && $item['asignado_a'] !== null)
                            <div class="bg-slate-100 rounded-xl p-3 flex items-center justify-center gap-2 border border-slate-200">
                                <span class="text-slate-400">🔒</span>
                                <span class="text-sm font-bold text-slate-500 truncate">Tomado por {{ explode(' ', trim($item['asignado_a']))[0] }}</span>
                            </div>
                        @else
                            <button onclick="abrirModalReserva('{{ $item['_id'] }}', '{{ $item['nombre_articulo'] ?? $item['nombre_item'] }}')" 
                                    class="w-full py-3 px-4 rounded-xl font-bold text-sm text-white transition-all shadow-sm active:scale-95 {{ $estiloTema['color_boton'] ?? 'bg-indigo-600 hover:bg-indigo-700' }}">
                                Elegir esto
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center p-10 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 text-center">
                        <span class="text-4xl mb-3 text-slate-300">🤷‍♂️</span>
                        <p class="text-slate-500 font-medium">Aún no se han añadido requerimientos para esta celebración.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL DE RESERVA                           --}}
{{-- ========================================== --}}
<div id="modalReserva" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4 transition-opacity">
    <div class="bg-white w-full max-w-md p-8 rounded-[2rem] shadow-2xl space-y-6 transform transition-transform scale-100">
        
        <div class="text-center relative">
            <button onclick="cerrarModalReserva()" class="absolute -top-2 -right-2 w-8 h-8 flex items-center justify-center bg-slate-100 text-slate-500 rounded-full hover:bg-slate-200 transition-colors">&times;</button>
            <span class="text-5xl block mb-4">🤝</span>
            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Confirmar Elección</h3>
        </div>
        
        <p class="text-base text-slate-600 text-center">
            Te estás comprometiendo con:<br>
            <span id="nombreItemModal" class="font-bold text-indigo-600 text-lg block mt-2"></span>
        </p>

        <form action="{{ route('items.reserve') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="item_id" id="itemIdModal">
            
            <div>
                <input type="text" name="nombre_invitado" required placeholder="Ingresa tu nombre para la lista" 
                       class="w-full px-5 py-4 rounded-2xl border-2 border-slate-200 focus:outline-none focus:border-indigo-500 bg-slate-50 text-center font-medium transition-colors">
            </div>

            <button type="submit" class="w-full py-4 font-black text-white text-lg rounded-2xl transition-all shadow-lg active:scale-95 {{ $estiloTema['color_boton'] ?? 'bg-indigo-600 hover:bg-indigo-700' }}">
                ¡Confirmar!
            </button>
            <button type="button" onclick="cerrarModalReserva()" class="w-full py-3 text-slate-500 font-bold hover:text-slate-800 transition-colors">
                Cancelar
            </button>
        </form>
    </div>
</div>

<script>
    function abrirModalReserva(id, nombreItem) {
        document.getElementById('itemIdModal').value = id;
        document.getElementById('nombreItemModal').innerText = nombreItem;
        const modal = document.getElementById('modalReserva');
        modal.classList.remove('hidden');
    }

    function cerrarModalReserva() {
        const modal = document.getElementById('modalReserva');
        modal.classList.add('hidden');
    }
</script>
@endsection