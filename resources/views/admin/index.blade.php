@extends('layouts.app')

@section('contenido')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- Encabezado Principal --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold uppercase tracking-wider mb-2">
                🛡️ Control Central
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Administración Global</h1>
            <p class="text-slate-500 text-sm mt-1">Supervisa todas las celebraciones, atiende incidencias y audita eventos en tiempo real.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-2xl text-sm border border-slate-200">
                Total Eventos: <span class="text-emerald-600">{{ count($eventos ?? []) }}</span>
            </span>
        </div>
    </div>

    {{-- Métricas Rápidas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                🎉
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Eventos Activos</p>
                <h3 class="text-2xl font-black text-slate-800">{{ count($eventos ?? []) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                ⚠️
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Incidencias</p>
                <h3 class="text-2xl font-black text-slate-800">{{ count($incidencias ?? []) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                👥
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Anfitriones</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalUsuarios ?? count($eventos ?? []) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
                ⚡
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Estado API</p>
                <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Node Online
                </span>
            </div>
        </div>
    </div>

    {{-- Seccion 1: Bandeja de Incidencias / Soporte --}}
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    🚨 Tickets de Incidencias / Soporte
                </h2>
                <p class="text-slate-500 text-xs mt-0.5">Reportes enviados por anfitriones pendientes de atención.</p>
            </div>
            <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full">
                {{ count($incidencias ?? []) }} Abiertas
            </span>
        </div>

        @if(empty($incidencias))
            <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <p class="text-2xl mb-1">🎉</p>
                <p class="text-slate-600 font-bold text-sm">¡Sin incidencias pendientes!</p>
                <p class="text-slate-400 text-xs">Todos los tickets de soporte han sido resueltos correctamente.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($incidencias as $ticket)
                    <div id="ticket-{{ $ticket['_id'] ?? $ticket['id'] }}" class="p-4 rounded-2xl border border-amber-200 bg-amber-50/50 flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex justify-between items-start gap-2">
                                <span class="px-2.5 py-1 bg-amber-200 text-amber-900 text-xs font-black rounded-lg uppercase">
                                    {{ $ticket['tipo'] ?? 'Soporte' }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">
                                    {{ \Carbon\Carbon::parse($ticket['created_at'] ?? now())->diffForHumans() }}
                                </span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-base mt-2">{{ $ticket['titulo'] ?? 'Reporte de Anfitrión' }}</h4>
                            <p class="text-slate-600 text-xs mt-1 leading-relaxed">{{ $ticket['descripcion'] ?? 'Sin detalle adicional' }}</p>
                        </div>
                        
                        <div class="flex items-center justify-between pt-2 border-t border-amber-200/60">
                            <span class="text-xs text-slate-500">Evento ID: <strong class="text-slate-700">{{ substr($ticket['evento_id'] ?? 'N/A', 0, 8) }}...</strong></span>
                            <button onclick="resolverTicket('{{ $ticket['_id'] ?? $ticket['id'] }}')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                ✅ Resolver Ticket
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Seccion 2: Tabla de Control Global de Celebraciones --}}
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    🎉 Directores de Eventos Registrados
                </h2>
                <p class="text-slate-500 text-xs mt-0.5">Listado centralizado de todas las celebraciones de la plataforma.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase bg-slate-50/50">
                        <th class="py-3 px-4 rounded-l-xl">Evento / Anfitrión</th>
                        <th class="py-3 px-4">Tipo</th>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4">Módulos Activos</th>
                        <th class="py-3 px-4 text-center rounded-r-xl">Acciones Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($eventos ?? [] as $evento)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $evento['nombre_evento'] ?? 'Celebración sin nombre' }}</div>
                                <div class="text-xs text-slate-400">Creador: {{ $evento['creador_email'] ?? 'Anfitrión' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-xs font-bold capitalize">
                                    {{ str_replace('_', ' ', $evento['tipo_evento'] ?? 'general') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                {{ $evento['fecha_evento'] ?? 'Por definir' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @foreach(array_slice($evento['modulos_activos'] ?? [], 0, 4) as $mod)
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-semibold rounded-md">
                                            {{ $mod }}
                                        </span>
                                    @endforeach
                                    @if(count($evento['modulos_activos'] ?? []) > 4)
                                        <span class="text-[10px] text-slate-400 font-bold align-center">+{{ count($evento['modulos_activos']) - 4 }} más</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <button onclick="auditarEvento('{{ $evento['_id'] ?? $evento['id'] }}', '{{ addslashes($evento['nombre_evento'] ?? '') }}')" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                    🔍 Auditar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-sm">
                                No se encontraron eventos registrados en el servidor Node.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Scripts JS para API y SweetAlert2 --}}
<script>
function resolverTicket(id) {
    Swal.fire({
        title: '¿Cerrar esta incidencia?',
        text: "El ticket quedará marcado como resuelto en la base de datos.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, resolver',
        cancelButtonText: 'Cancelar',
        customClass: { popup: 'rounded-3xl' }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/eventos/incidencias/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Ticket Resuelto!',
                    text: data.mensaje || 'La incidencia ha sido solucionada.',
                    confirmButtonColor: '#0B6658',
                    customClass: { popup: 'rounded-3xl' }
                });
                const card = document.getElementById(`ticket-${id}`);
                if(card) card.remove();
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cerrar la incidencia.',
                    confirmButtonColor: '#e11d48'
                });
            });
        }
    });
}

function auditarEvento(id, nombre) {
    Swal.fire({
        title: `Auditoría: ${nombre}`,
        html: `
            <div class="text-left text-sm space-y-3 p-2">
                <p><strong>ID Evento:</strong> <code class="bg-slate-100 px-2 py-0.5 rounded text-xs">${id}</code></p>
                <p class="text-slate-500">Conexión con MongoDB establecida. Los registros de asistentes, regalos e insumos se gestionan vía API Node.</p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#0F172A',
        customClass: { popup: 'rounded-3xl' }
    });
}
</script>
@endsection