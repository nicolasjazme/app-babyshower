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
                <span class="text-2xl">🛒</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Lista de Insumos (Cooperación)</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Define qué elementos faltan para la celebración y monitorea cuáles invitados se han asignado la tarea de llevarlos.
            </p>
        </div>
        
        <button onclick="toggleModal('modal-nuevo-insumo', true)" class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-plus"></i> Añadir Insumo
        </button>
    </div>

    {{-- Contenedor de Dos Columnas --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        {{-- Columna 1: Elementos por Conseguir --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2">⏳ Elementos por Conseguir</span>
                <span id="badge-pendientes" class="text-xs bg-orange-100 text-orange-800 px-2 py-0.5 rounded-md font-bold">0</span>
            </h2>
            
            <div id="contenedor-pendientes" class="space-y-3">
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <i class="fa-solid fa-spinner fa-spin text-slate-300 text-xl mb-2"></i>
                    <p class="text-xs text-slate-400">Cargando pendientes...</p>
                </div>
            </div>
        </div>

        {{-- Columna 2: Coordinados y Asignados --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2">✅ Coordinados y Asignados</span>
                <span id="badge-cubiertos" class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md font-bold">0</span>
            </h2>

            <div id="contenedor-cubiertos" class="space-y-3 max-h-[450px] overflow-y-auto pr-1">
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <i class="fa-solid fa-spinner fa-spin text-slate-300 text-xl mb-2"></i>
                    <p class="text-xs text-slate-400">Cargando asignados...</p>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal para Crear Insumo --}}
<div id="modal-nuevo-insumo" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-nuevo-insumo', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">🛒 Añadir Requerimiento</h3>
        <p class="text-xs text-slate-500 mb-6">Indica qué artículo se necesita coordinar para la fiesta.</p>

        <form id="form-insumo" onsubmit="guardarInsumo(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nombre del Artículo / Insumo *</label>
                <input type="text" id="input-nombre" required placeholder="Ej. Carbón, Bolsas de Hielo, Papas Fritas"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-orange-200 transition-all text-sm font-medium">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Cantidad o Detalle *</label>
                <input type="text" id="input-cantidad" required placeholder="Ej. 2 bolsas grandes, 3 botellas de 3L"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-orange-200 transition-all text-sm font-medium">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-nuevo-insumo', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-orange-500 hover:bg-orange-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Guardar Insumo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const EVENTO_ID = '{{ $eventoId }}';
    const API_URL = `/api/eventos/${EVENTO_ID}/insumos`;

    document.addEventListener('DOMContentLoaded', cargarInsumos);

    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) modal.classList.remove('hidden');
        else {
            modal.classList.add('hidden');
            document.getElementById('form-insumo').reset();
        }
    }

    // --- LEER INSUMOS (GET) ---
    function cargarInsumos() {
        if(!EVENTO_ID) return;

        fetch(API_URL)
            .then(res => res.json())
            .then(data => renderizarInsumos(data))
            .catch(err => {
                console.error("Error cargando insumos:", err);
                const errorHtml = `<div class="text-center py-8 text-rose-500 text-xs font-bold">No se pudieron cargar los datos.</div>`;
                document.getElementById('contenedor-pendientes').innerHTML = errorHtml;
                document.getElementById('contenedor-cubiertos').innerHTML = errorHtml;
            });
    }

    // --- RENDERIZAR EN LAS DOS COLUMNAS ---
    function renderizarInsumos(insumosData) {
        const listaInsumos = insumosData.insumos || insumosData || [];

        // Filtramos pendientes vs cubiertos
        const pendientes = listaInsumos.filter(item => !item.nombre_invitado && !item.invitado && !item.asignado_a);
        const cubiertos = listaInsumos.filter(item => item.nombre_invitado || item.invitado || item.asignado_a);

        // Actualizamos los badges contadores
        document.getElementById('badge-pendientes').innerText = pendientes.length;
        document.getElementById('badge-cubiertos').innerText = cubiertos.length;

        // --- Render Columna Pendientes ---
        const contPendientes = document.getElementById('contenedor-pendientes');
        if (pendientes.length === 0) {
            contPendientes.innerHTML = `
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-lg mx-auto mb-3">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>
                    <p class="text-xs text-slate-400 italic">No hay insumos pendientes.</p>
                </div>
            `;
        } else {
            contPendientes.innerHTML = pendientes.map(item => `
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between gap-4">
                    <div>
                        <strong class="block text-sm text-slate-800">📦 ${item.nombre || item.articulo}</strong>
                        ${item.cantidad ? `<span class="block text-xs text-slate-400 font-semibold mt-0.5">Cantidad requerida: ${item.cantidad}</span>` : ''}
                    </div>

                    <button onclick="eliminarInsumo('${item._id || item.id}')" class="p-2 text-slate-400 hover:text-rose-500 transition-colors" title="Eliminar insumo">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>
            `).join('');
        }

        // --- Render Columna Cubiertos ---
        const contCubiertos = document.getElementById('contenedor-cubiertos');
        if (cubiertos.length === 0) {
            contCubiertos.innerHTML = `
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center text-lg mx-auto mb-3">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <p class="text-xs text-slate-400 italic">Ningún invitado se ha anotado todavía.</p>
                </div>
            `;
        } else {
            contCubiertos.innerHTML = cubiertos.map(item => {
                const nombreQuienLleva = item.nombre_invitado || item.invitado || item.asignado_a || 'Invitado';
                return `
                    <div class="p-4 bg-emerald-50/40 border border-emerald-100 rounded-2xl flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">🛒 ${item.nombre || item.articulo}</h3>
                            <span class="inline-block mt-1 text-[10px] font-black px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-md uppercase">
                                Lo trae: ${nombreQuienLleva}
                            </span>
                        </div>
                        
                        <button onclick="liberarInsumo('${item._id || item.id}')" class="p-2 text-slate-400 hover:text-orange-500 transition-colors" title="Liberar y volver a poner disponible">
                            <i class="fa-solid fa-arrow-rotate-left text-sm"></i>
                        </button>
                    </div>
                `;
            }).join('');
        }
    }

    // --- GUARDAR NUEVO INSUMO (POST) ---
    function guardarInsumo(e) {
        e.preventDefault();

        const payload = {
            nombre: document.getElementById('input-nombre').value,
            articulo: document.getElementById('input-nombre').value,
            cantidad: document.getElementById('input-cantidad').value
        };

        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            toggleModal('modal-nuevo-insumo', false);
            cargarInsumos();
            Swal.fire({
                icon: 'success',
                title: '¡Insumo Agregado!',
                text: 'Aparecerá en la lista de compras del evento.',
                confirmButtonColor: '#f97316',
                customClass: { popup: 'rounded-3xl' }
            });
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'No se pudo guardar el insumo.', 'error');
        });
    }

    // --- LIBERAR INSUMO (PUT) ---
    function liberarInsumo(id) {
        fetch(`${API_URL}/${id}/liberar`, {
            method: 'PUT',
            headers: { 'Accept': 'application/json' }
        })
        .then(() => {
            cargarInsumos();
            Swal.fire({
                icon: 'info',
                title: 'Insumo Liberado',
                text: 'El elemento volvió a la lista de disponibles.',
                showConfirmButton: false,
                timer: 1500,
                customClass: { popup: 'rounded-3xl' }
            });
        })
        .catch(err => console.error("Error al liberar insumo:", err));
    }

    // --- ELIMINAR INSUMO (DELETE) ---
    function eliminarInsumo(id) {
        Swal.fire({
            title: '¿Quitar insumo?',
            text: "Se eliminará de la lista de compras.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-3xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                })
                .then(() => {
                    cargarInsumos();
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: { popup: 'rounded-3xl' }
                    });
                })
                .catch(err => console.error("Error al eliminar insumo:", err));
            }
        });
    }
</script>
@endsection