@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;

    // Fallback de datos simulando la respuesta de la API modular del backend
    $fotos = $galeriaFotos ?? [];
    
    // Ordenamos las publicaciones para que aparezcan las más recientes primero en el feed
    $fotosOrdenadas = collect($fotos)->sortByDesc('created_at')->all();
@endphp

<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl">📸</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Muro de Recuerdos (Galería)</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Monitorea las fotos subidas por tus invitados durante la celebración, revisa la interacción por "Likes" y modera el contenido del feed.
            </p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">🖼️ Imágenes Publicadas en la Fiesta</h2>

        @if(empty($fotosOrdenadas))
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-pink-50 text-pink-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                    <i class="fa-solid fa-images"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">El muro de recuerdos está vacío</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Cuando tus invitados comiencen a escanear el enlace de la fiesta y suban sus capturas, verás el catálogo social ordenado en este panel.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($fotosOrdenadas as $foto)
                    <div class="bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden flex flex-col justify-between group hover:shadow-sm transition-all">
                        
                        <div class="w-full h-48 bg-slate-200 relative overflow-hidden flex items-center justify-center text-slate-400">
                            @if(!empty($foto['url_imagen']))
                                <img src="{{ $foto['url_imagen'] }}" alt="Recuerdo de la fiesta" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <i class="fa-solid fa-image text-3xl"></i>
                            @endif
                        </div>

                        <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800 truncate">
                                        👤 {{ $foto['nombre_invitado'] ?? 'Invitado' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-semibold shrink-0">
                                        {{ isset($foto['created_at']) ? \Carbon\Carbon::parse($foto['created_at'])->diffForHumans() : 'Reciente' }}
                                    </span>
                                </div>
                                @if(!empty($foto['pie_foto']))
                                    <p class="text-xs text-slate-600 mt-2 italic line-clamp-2">"{{ $foto['pie_foto'] }}"</p>
                                @endif
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-1 text-pink-600 font-bold text-xs">
                                    <i class="fa-solid fa-heart"></i>
                                    <span>{{ $foto['likes_count'] ?? 0 }} Likes</span>
                                </div>

                                <form action="/eventos/modulos/galeria/{{ $foto['_id'] ?? $foto['id'] }}" method="POST" onsubmit="return confirm('¿Deseas eliminar permanentemente esta foto del feed del evento?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors border border-slate-200" title="Eliminar del feed">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
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
@endsection