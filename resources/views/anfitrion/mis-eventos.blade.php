@extends('layouts.app')

@section('contenido')
<div class="max-w-6xl mx-auto py-8 px-4 animate-fade-in-up pb-20">
    
    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <span class="text-4xl">🗂️</span> Mis Celebraciones
            </h1>
            <p class="text-slate-500 text-sm mt-2">Selecciona un evento para administrarlo o crea uno nuevo.</p>
        </div>
        <a href="/eventos/crear" class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3.5 rounded-2xl transition-all shadow-md hover:-translate-y-1 active:scale-95 shrink-0">
            <span class="text-xl">✨</span> Nuevo Evento
        </a>
    </header>

    @if(empty($todosMisEventos))
        <div class="bg-white p-10 rounded-[2.5rem] border-2 border-dashed border-slate-200 text-center flex flex-col items-center justify-center">
            <span class="text-6xl mb-4">🤷‍♂️</span>
            <h3 class="text-xl font-bold text-slate-800 mb-2">Aún no tienes celebraciones</h3>
            <p class="text-slate-500 mb-6">Parece que tu lista está vacía. ¡Anímate a crear tu primer evento!</p>
            <a href="/eventos/crear" class="px-8 py-3 bg-indigo-50 text-indigo-600 font-bold rounded-xl hover:bg-indigo-100 transition-colors">Empezar ahora</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($todosMisEventos as $ev)
                <form action="{{ route('anfitrion.seleccionar_evento') }}" method="POST" class="h-full">
                    @csrf
                    <input type="hidden" name="evento_id" value="{{ $ev['_id'] ?? $ev['id'] }}">
                    
                    <button type="submit" class="w-full h-full text-left p-6 bg-white rounded-[2rem] shadow-sm border-2 border-slate-100 hover:border-indigo-400 hover:shadow-xl transition-all group flex flex-col justify-between hover:-translate-y-1">
                        <div>
                            <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-4xl mb-4 group-hover:scale-110 transition-transform shadow-sm">
                                {{ $ev['configVisual']['iconoEmoji'] ?? '🎉' }}
                            </div>
                            <h3 class="font-black text-xl text-slate-800 mb-1 truncate group-hover:text-indigo-600 transition-colors">{{ $ev['titulo'] ?? 'Evento sin título' }}</h3>
                            
                            <div class="flex items-center gap-2 text-slate-500 text-sm font-medium mb-4">
                                <span>📅</span> {{ isset($ev['fecha']) ? \Carbon\Carbon::parse($ev['fecha'])->format('d/m/Y') : 'Fecha por definir' }}
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="inline-block px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-black uppercase rounded-full tracking-wider group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                {{ str_replace('_', ' ', $ev['tipo_evento'] ?? 'Especial') }}
                            </span>
                            <span class="text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity font-bold text-sm flex items-center gap-1">
                                Administrar <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </div>
                    </button>
                </form>
            @endforeach
        </div>
    @endif
</div>

<style>
    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(15px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection