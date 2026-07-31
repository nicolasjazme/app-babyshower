@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;
@endphp

<div class="space-y-8">
    
    {{-- Cabecera --}}
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
        
        {{-- Columna Izquierda: Canciones en Espera (Pendientes) --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2">⏳ Canciones en Espera</span>
                <span id="badge-pendientes" class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-md font-bold">0</span>
            </h2>
            
            <div id="contenedor-pendientes" class="space-y-3">
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <i class="fa-solid fa-spinner fa-spin text-slate-300 text-xl mb-2"></i>
                    <p class="text-xs text-slate-400">Cargando sugerencias...</p>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Setlist de la Fiesta (Aprobadas) --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2">🎉 Setlist de la Fiesta</span>
                <span id="badge-aprobadas" class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md font-bold">0</span>
            </h2>

            <div id="contenedor-aprobadas" class="space-y-3 max-h-[450px] overflow-y-auto pr-1">
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <i class="fa-solid fa-spinner fa-spin text-slate-300 text-xl mb-2"></i>
                    <p class="text-xs text-slate-400">Cargando setlist...</p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const EVENTO_ID = '{{ $eventoId }}';
    const API_URL = `/api/eventos/${EVENTO_ID}/musica`;

    document.addEventListener('DOMContentLoaded', cargarPlaylist);

    // --- LEER PLAYLIST (GET) ---
    function cargarPlaylist() {
        if(!EVENTO_ID) return;

        fetch(API_URL)
            .then(res => res.json())
            .then(data => renderizarPlaylist(data))
            .catch(err => {
                console.error("Error al cargar música:", err);
                const errorHtml = `<div class="text-center py-8 text-rose-500 text-xs font-bold">Error al sincronizar sugerencias.</div>`;
                document.getElementById('contenedor-pendientes').innerHTML = errorHtml;
                document.getElementById('contenedor-aprobadas').innerHTML = errorHtml;
            });
    }

    // --- RENDERIZAR EN AMBAS COLUMNAS ---
    function renderizarPlaylist(response) {
        const canciones = response.musica || response.playlist || response || [];

        const pendientes = canciones.filter(c => (c.estado || 'pendiente') === 'pendiente');
        const aprobadas = canciones.filter(c => c.estado === 'aprobado');

        // Actualizar contadores
        document.getElementById('badge-pendientes').innerText = pendientes.length;
        document.getElementById('badge-aprobadas').innerText = aprobadas.length;

        // --- Render Pendientes ---
        const contPendientes = document.getElementById('contenedor-pendientes');
        if (pendientes.length === 0) {
            contPendientes.innerHTML = `
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-lg mx-auto mb-3">
                        <i class="fa-solid fa-compact-disc"></i>
                    </div>
                    <p class="text-xs text-slate-400 italic">No hay sugerencias pendientes por el momento.</p>
                </div>
            `;
        } else {
            contPendientes.innerHTML = pendientes.map(cancion => {
                const id = cancion._id || cancion.id;
                const titulo = cancion.titulo_cancion || cancion.cancion || 'Sugerencia Externa';
                const invitado = cancion.nombre_invitado || cancion.sugerido_por || 'Invitado anónimo';
                const link = cancion.link_url || cancion.url || '';

                return `
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between gap-4">
                        <div class="space-y-1 truncate">
                            <strong class="block text-sm text-slate-800 truncate">📻 ${titulo}</strong>
                            <span class="block text-[11px] text-slate-400 font-medium">Propuesta por: ${invitado}</span>
                            ${link ? `
                                <a href="${link}" target="_blank" class="inline-flex items-center gap-1 text-xs text-violet-500 hover:text-violet-600 font-bold mt-1">
                                    <i class="fa-solid fa-circle-play text-sm"></i> Escuchar enlace
                                </a>
                            ` : ''}
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <button onclick="cambiarEstado('${id}', 'aprobado')" class="w-9 h-10 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-xl transition-colors flex items-center justify-center cursor-pointer" title="Aprobar canción">
                                <i class="fa-solid fa-check text-sm"></i>
                            </button>
                            <button onclick="cambiarEstado('${id}', 'rechazado')" class="w-9 h-10 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl transition-colors flex items-center justify-center cursor-pointer" title="Rechazar/Borrar">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // --- Render Aprobadas ---
        const contAprobadas = document.getElementById('contenedor-aprobadas');
        if (aprobadas.length === 0) {
            contAprobadas.innerHTML = `
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-lg mx-auto mb-3">
                        <i class="fa-solid fa-music"></i>
                    </div>
                    <p class="text-xs text-slate-400 italic">La playlist está vacía. Aprueba canciones para armar el setlist.</p>
                </div>
            `;
        } else {
            contAprobadas.innerHTML = aprobadas.map(cancion => {
                const titulo = cancion.titulo_cancion || cancion.cancion || 'Canción oficial';
                const link = cancion.link_url || cancion.url || '';

                return `
                    <div class="p-4 bg-emerald-50/40 border border-emerald-100 rounded-2xl flex items-center justify-between gap-4">
                        <div class="truncate">
                            <h3 class="text-sm font-bold text-slate-800 truncate">🎵 ${titulo}</h3>
                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Añadida al setlist oficial</p>
                        </div>
                        
                        ${link ? `
                            <a href="${link}" target="_blank" class="w-9 h-9 bg-white text-slate-700 hover:text-violet-600 rounded-xl shadow-sm border border-slate-100 flex items-center justify-center transition-colors shrink-0">
                                <i class="fa-solid fa-up-right-from-square text-xs"></i>
                            </a>
                        ` : ''}
                    </div>
                `;
            }).join('');
        }
    }

    // --- ACCIÓN: APROBAR O RECHAZAR CANCIÓN (PUT / DELETE) ---
    function cambiarEstado(id, nuevoEstado) {
        if (nuevoEstado === 'aprobado') {
            fetch(`${API_URL}/${id}/aprobar`, {
                method: 'PUT',
                headers: { 'Accept': 'application/json' }
            })
            .then(() => {
                cargarPlaylist();
                Swal.fire({ icon: 'success', title: '¡Canción en el Setlist!', showConfirmButton: false, timer: 1200, customClass: { popup: 'rounded-3xl' } });
            })
            .catch(err => console.error("Error al aprobar:", err));
        } else {
            // Si rechaza, eliminamos o marcamos rechazada según prefiera tu backend (aquí usamos DELETE de ejemplo)
            fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' }
            })
            .then(() => {
                cargarPlaylist();
                Swal.fire({ icon: 'info', title: 'Sugerencia descartada', showConfirmButton: false, timer: 1200, customClass: { popup: 'rounded-3xl' } });
            })
            .catch(err => console.error("Error al rechazar:", err));
        }
    }
</script>
@endsection