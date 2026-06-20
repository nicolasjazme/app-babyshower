@extends('layouts.app')

@section('content')
{{-- El fondo cambia automáticamente gracias al gradiente calculado en el controlador --}}
<div class="min-h-screen bg-gradient-to-br {{ $estiloTema['bg_gradient'] }} py-12 px-4 sm:px-6 lg:px-8 transition-all duration-500">
    
    <div class="max-w-4xl mx-auto space-y-8">
        
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100 transform transition duration-300">
            <div class="h-40 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center relative">
                <div class="absolute -bottom-10 bg-white p-4 rounded-full shadow-md text-5xl">
                    {{ $evento['configVisual']['iconoEmoji'] ?? '🎉' }}
                </div>
            </div>

            <div class="pt-14 pb-8 px-6 sm:px-12 text-center space-y-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider {{ $estiloTema['badge_color'] }}">
                    {{ str_replace('_', ' ', $evento['tipo_evento']) }}
                </span>
                
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">
                    {{ $evento['titulo'] }}
                </h1>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 text-slate-600 font-medium border-t border-slate-100">
                    <div class="flex items-center justify-center space-x-2">
                        <span>📅</span>
                        <span>{{ \Carbon\Carbon::parse($evento['fecha'])->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center justify-center space-x-2">
                        <span>⏰</span>
                        <span>{{ $evento['hora'] }} Hrs</span>
                    </div>
                    <div class="flex items-center justify-center space-x-2">
                        <span>📍</span>
                        <span class="truncate" title="{{ $evento['ubicacion'] }}">{{ $evento['ubicacion'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-lg border border-slate-100">
            <h2 class="text-2xl font-bold text-slate-800 mb-4 text-center sm:text-left">👋 Confirma tu Asistencia</h2>
            <form action="{{ route('asistencia.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                @csrf
                <input type="hidden" name="evento_id" value="{{ $evento['_id'] }}">
                
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tu Nombre Completo</label>
                    <input type="text" name="nombre" required placeholder="Ej: Juan Pérez" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
                </div>
                
                <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold transition-all shadow-sm {{ $estiloTema['color_boton'] }}">
                    Confirmar Asistencia
                </button>
            </form>
        </div>

        <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-lg border border-slate-100">
            <div class="mb-6 text-center sm:text-left">
                <h2 class="text-2xl font-bold text-slate-900">
                    @if($evento['tipo_evento'] === 'asado' || $evento['tipo_evento'] === 'fiesta')
                        🍖 ¿Con qué vas a cooperar?
                    @else
                        🎁 Lista de Regalos Ideales
                    @endif
                </h2>
                <p class="text-slate-500 text-sm mt-1">
                    @if($evento['tipo_evento'] === 'asado' || $evento['tipo_evento'] === 'fiesta')
                        Selecciona un insumo de la lista para comprometerte a traerlo. ¡Hagamos el mejor evento!
                    @else
                        Elige un obsequio que desees regalar para evitar que se repita.
                    @endif
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($items as $item)
                    <div class="border border-slate-100 rounded-2xl p-4 flex justify-between items-center bg-slate-50 hover:bg-white hover:shadow-md transition-all">
                        <div>
                            <h4 class="font-bold text-slate-800 text-lg">{{ $item['nombre_articulo'] ?? $item['nombre_item'] }}</h4>
                            <p class="text-xs text-slate-400">Cantidad: {{ $item['cantidad_requerida'] ?? 1 }}</p>
                            
                            @if(isset($item['asignado_a']) && $item['asignado_a'] !== null)
                                <span class="inline-block mt-2 text-xs font-semibold px-2 py-1 bg-red-50 text-red-600 rounded-md">
                                    🔒 Tomado por: {{ $item['asignado_a'] }}
                                </span>
                            @else
                                <span class="inline-block mt-2 text-xs font-semibold px-2 py-1 bg-emerald-50 text-emerald-600 rounded-md">
                                    ✅ Disponible
                                </span>
                            @endif
                        </div>

                        @if(!isset($item['asignado_a']) || $item['asignado_a'] === null)
                            <button onclick="abrirModalReserva('{{ $item['_id'] }}', '{{ $item['nombre_articulo'] ?? $item['nombre_item'] }}')" class="py-2 px-4 rounded-xl font-bold text-sm transition-colors {{ $estiloTema['color_boton'] }}">
                                Elegir
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-slate-400 font-medium">
                        Aún no se han añadido requerimientos para esta celebración.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<div id="modalReserva" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white w-full max-w-md p-6 rounded-3xl shadow-2xl space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold text-slate-950">Confirmar Elección</h3>
            <button onclick="cerrarModalReserva()" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold">&times;</button>
        </div>
        
        <p class="text-sm text-slate-500">
            Te estás comprometiendo con: <span id="nombreItemModal" class="font-bold text-slate-800"></span>. Por favor ingresa tu nombre para registrarlo.
        </p>

        <form action="{{ route('items.reserve') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="item_id" id="itemIdModal">
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tu Nombre</label>
                <input type="text" name="nombre_invitado" required placeholder="Escribe tu nombre para la lista" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50">
            </div>

            <div class="flex space-x-3 pt-2">
                <button type="button" onclick="cerrarModalReserva()" class="w-1/2 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="w-1/2 py-3 font-bold rounded-xl transition-all shadow-sm {{ $estiloTema['color_boton'] }}">
                    Confirmar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalReserva(id, nombreItem) {
        document.getElementById('itemIdModal').value = id;
        document.getElementById('nombreItemModal').innerText = nombreItem;
        document.getElementById('modalReserva').classList.remove('hidden');
    }

    function cerrarModalReserva() {
        document.getElementById('modalReserva').classList.add('hidden');
    }
</script>
@endsection