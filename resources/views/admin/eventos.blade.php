@extends('layouts.app')

@section('contenido')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Encabezado de la Vista --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-2">
                🎉 Módulo de Eventos
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Gestión General de Eventos</h1>
            <p class="text-slate-500 text-sm mt-1">Inspecciona, audita y supervisa todas las celebraciones registradas en la plataforma.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition-all">
                ⬅️ Volver al Control Central
            </a>
        </div>
    </div>

    {{-- Tabla Principal de Eventos --}}
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                📋 Listado de Celebraciones
            </h2>
            <span class="text-xs font-semibold text-slate-400">
                Sincronizado con MongoDB & Node.js
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase bg-slate-50/50">
                        <th class="py-3 px-4 rounded-l-xl">Nombre del Evento</th>
                        <th class="py-3 px-4">Anfitrión</th>
                        <th class="py-3 px-4">Tipo</th>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4 text-center rounded-r-xl">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($eventos ?? [] as $evento)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $evento['nombre_evento'] ?? 'Celebración sin Nombre' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $evento['creador_email'] ?? 'Anfitrión' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full text-xs font-bold capitalize">
                                    {{ str_replace('_', ' ', $evento['tipo_evento'] ?? 'general') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                {{ $evento['fecha_evento'] ?? 'Por definir' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <button onclick="verDetallesEvento('{{ $evento['_id'] ?? $evento['id'] }}', '{{ addslashes($evento['nombre_evento'] ?? '') }}')" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                    👁️ Ver Detalles
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-sm">
                                No hay eventos registrados en la base de datos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function verDetallesEvento(id, nombre) {
    Swal.fire({
        title: `Evento: ${nombre}`,
        html: `
            <div class="text-left text-sm space-y-2 p-2">
                <p><strong>ID Evento:</strong> <code class="bg-slate-100 px-2 py-0.5 rounded text-xs">${id}</code></p>
                <p class="text-slate-500">Puedes gestionar los asistentes, regalos o modulos activos desde la API unificada.</p>
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