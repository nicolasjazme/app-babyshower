
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full">
                    Panel del Organizador
                </span>
                <h1 class="text-3xl font-black text-slate-900 mt-2">
                    {{ $evento['titulo'] ?? 'Mi Celebración Global' }}
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    Gestiona tu evento, revisa la asistencia y coordina los requerimientos desde un solo lugar.
                </p>
            </div>
            
            <div class="bg-slate-100 p-3 rounded-2xl flex items-center space-x-3 w-full sm:w-auto">
                <span class="text-xs font-mono text-slate-600 select-all truncate max-w-xs">
                    {{ route('event.public', $evento['slug'] ?? '') }}
                </span>
                <button onclick="copiarLink('{{ route('event.public', $evento['slug'] ?? '') }}')" class="bg-indigo-600 text-white px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-indigo-700 transition-colors shrink-0">
                    📋 Copiar Link
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl">👥</div>
                <div>
                    <p class="text-slate-400 text-xs font-semibold uppercase">Total Invitados</p>
                    <h3 class="text-2xl font-black text-slate-800">{{ $metricas['total_invitados'] ?? 0 }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl">✅</div>
                <div>
                    <p class="text-slate-400 text-xs font-semibold uppercase">Confirmados</p>
                    <h3 class="text-2xl font-black text-slate-800">{{ $metricas['confirmados'] ?? 0 }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-2xl">📦</div>
                <div>
                    <p class="text-slate-400 text-xs font-semibold uppercase">
                        {{ ($evento['tipo_evento'] === 'asado' || $evento['tipo_evento'] === 'fiesta') ? 'Insumos / Lista' : 'Regalos en Lista' }}
                    </p>
                    <h3 class="text-2xl font-black text-slate-800">{{ $metricas['total_items'] ?? 0 }}</h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center space-x-4">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl">🔒</div>
                <div>
                    <p class="text-slate-400 text-xs font-semibold uppercase">
                        {{ ($evento['tipo_evento'] === 'asado' || $evento['tipo_evento'] === 'fiesta') ? 'Cubiertos' : 'Reservados' }}
                    </p>
                    <h3 class="text-2xl font-black text-slate-800">{{ $metricas['items_tomados'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">
                        {{ ($evento['tipo_evento'] === 'asado' || $evento['tipo_evento'] === 'fiesta') ? '📊 Listado de Cooperación Colectiva' : '📊 Estado de la Mesa de Regalos' }}
                    </h2>
                    <p class="text-slate-500 text-sm">Monitorea quién se comprometió con cada elemento de la celebración.</p>
                </div>
                
                <div class="flex space-x-2 w-full sm:w-auto">
                    <a href="{{ route('anfitrion.guests.index') }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl text-sm hover:bg-slate-200 transition-colors text-center flex-1 sm:flex-initial">
                        👥 Ver Invitados
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase text-xs font-bold tracking-wider border-b border-slate-100">
                            <th class="py-4 px-6">Ítem / Artículo</th>
                            <th class="py-4 px-6">Cantidad Req.</th>
                            <th class="py-4 px-6">Estado</th>
                            <th class="py-4 px-6">Responsable</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700 divide-y divide-slate-50 font-medium">
                        @forelse($items as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $item['nombre_articulo'] ?? $item['nombre_item'] }}
                                </td>
                                <td class="py-4 px-6 text-slate-500">
                                    {{ $item['cantidad_requerida'] ?? 1 }}
                                </td>
                                <td class="py-4 px-6">
                                    @if(isset($item['asignado_a']) && $item['asignado_a'] !== null)
                                        <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-lg text-xs font-bold">
                                            Asignado
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @if(isset($item['asignado_a']) && $item['asignado_a'] !== null)
                                        <div class="text-slate-900 font-semibold">{{ $item['asignado_a'] }}</div>
                                    @else
                                        <div class="text-slate-300 italic text-sm">Nadie se ha ofrecido aún</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-400 italic">
                                    No hay requerimientos cargados para este evento en MongoDB.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function copiarLink(url) {
        navigator.clipboard.writeText(url).then(() => {
            Swal.fire({
                icon: 'success',
                title: '¡Link Copiado!',
                text: 'Ya puedes enviar la invitación digital a tus amigos por WhatsApp.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        });
    }
</script>
@endsection