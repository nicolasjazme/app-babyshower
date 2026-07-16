@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;

    // Fallback de datos simulando la respuesta de la API modular del backend
    $cancionesSugeridas = $playlistSugerencias ?? [];
    
    // Agrupamos por estado para organizar la vista del anfitrión
    $pendientes = collect($cancionesSugeridas)->where('estado', 'pendiente')->all();
    $aprobadas = collect($cancionesSugeridas)->where('estado', 'aprobado')->all();
@endphp

<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl">🎵</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Playlist Colaborativa</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Modera las canciones sugeridas por tus invitados. Aprueba las que vayan con la temática o rechaza las que no correspondan.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                ⏳ Canciones en Espera 
                <span class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-md font-bold">{{ count($pendientes) }}</span>
            </h2>
            
            @if(empty($pendientes))
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-lg mx-auto mb-3">
                        <i class="fa-solid fa-compact-disc"></i>
                    </div>
                    <p class="text-xs text-slate-400 italic">No hay sugerencias pendientes por el momento.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($pendientes as $cancion)
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between gap-4">
                            <div class="space-y-1 truncate">
                                <strong class="block text-sm text-slate-800 truncate">📻 {{ $cancion['titulo_cancion'] ?? 'Sugerencia Externa' }}</strong>
                                <span class="block text-[11px] text-slate-400 font-medium">Propuesta por: {{ $cancion['nombre_invitado'] ?? 'Invitado anónimo' }}</span>
                                @if(!empty($cancion['link_url']))
                                    <a href="{{ $cancion['link_url'] }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-violet-500 hover:text-violet-600 font-bold mt-1">
                                        <i class="fa-solid fa-circle-play text-sm"></i> Escuchar enlace
                                    </a>
                                @endif
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                <form action="/eventos/modulos/musica/{{ $cancion['_id'] ?? $cancion['id'] }}/aprobar" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="w-9 h-10 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-xl transition-colors flex items-center justify-center" title="Aprobar canción">
                                        <i class="fa-solid fa-check text-sm"></i>
                                    </button>
                                </form>
                                <form action="/eventos/modulos/musica/{{ $cancion['_id'] ?? $cancion['id'] }}/rechazar" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="w-9 h-10 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl transition-colors flex items-center justify-center" title="Rechazar">
                                        <i class="fa-solid fa-xmark text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                🎉 Setlist de la Fiesta 
                <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md font-bold">{{ count($aprobadas) }}</span>
            </h2>

            @if(empty($aprobadas))
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-lg mx-auto mb-3">
                        <i class="fa-solid fa-music"></i>
                    </div>
                    <p class="text-xs text-slate-400 italic">La playlist está vacía. Aprueba canciones para armar el setlist.</p>
                </div>
            @else
                <div class="space-y-3 max-h-[450px] overflow-y-auto pr-1">
                    @foreach($aprobadas as $cancionAprobada)
                        <div class="p-4 bg-emerald-50/40 border border-emerald-100 rounded-2xl flex items-center justify-between gap-4">
                            <div class="truncate">
                                <h3 class="text-sm font-bold text-slate-800 truncate">🎵 {{ $cancionAprobada['titulo_cancion'] }}</h3>
                                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Añadida al setlist oficial</p>
                            </div>
                            
                            @if(!empty($cancionAprobada['link_url']))
                                <a href="{{ $cancionAprobada['link_url'] }}" target="_blank" class="w-9 h-9 bg-white text-slate-700 hover:text-violet-600 rounded-xl shadow-sm border border-slate-100 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-up-right-from-square text-xs"></i>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection