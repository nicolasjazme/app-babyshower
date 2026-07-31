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
                <span class="text-2xl">🪑</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Organizador de Mesas</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Crea mesas para tu evento, define el límite de sillas y organiza a tus invitados cómodamente.
            </p>
        </div>
        
        <button onclick="toggleModal('modal-nueva-mesa', true)" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-plus"></i> Crear Nueva Mesa
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Columna Izquierda: Salón y Mesas --}}
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">🍽️ Distribución del Salón</h2>

            <div id="contenedor-mesas" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-full bg-white text-center py-16 rounded-3xl border border-slate-200/80 shadow-sm">
                    <i class="fa-solid fa-spinner fa-spin text-indigo-500 text-2xl mb-2"></i>
                    <p class="text-xs text-slate-400">Cargando distribución de mesas...</p>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Invitados por Ubicar --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">👥 Invitados por Ubicar</h2>
                <p class="text-xs text-slate-400 mb-4">Lista de asistentes confirmados que aún no tienen una silla asignada.</p>

                <div id="contenedor-sin-mesa" class="space-y-3 max-h-[400px] overflow-y-auto pr-1">
                    <div class="text-center py-8 text-slate-400 text-xs">Cargando invitados...</div>
                </div>
            </div>

            <div class="mt-6 border-t border-slate-100 pt-4 text-xs text-slate-400 flex items-start gap-1.5 leading-relaxed">
                <i class="fa-solid fa-circle-info mt-0.5 text-slate-400 text-sm shrink-0"></i>
                Solo los invitados confirmados en el RSVP de la plataforma aparecerán elegibles en este listado.
            </div>
        </div>
    </div>
</div>

{{-- Modal Crear Mesa --}}
<div id="modal-nueva-mesa" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-nueva-mesa', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">🪑 Configurar Nueva Mesa</h3>
        <p class="text-xs text-slate-500 mb-6">Añade una mesa al comedor definiendo un límite de sillas físicas.</p>

        <form id="form-mesa" onsubmit="guardarMesa(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nombre de la Mesa *</label>
                <input type="text" id="input-nombre" required placeholder="Ej. Mesa Principal, Familia Anfitrión, etc."
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all text-sm font-medium">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Límite de Sillas / Capacidad *</label>
                <input type="number" id="input-sillas" required min="1" max="20" value="8"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 transition-all text-sm font-semibold">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-nueva-mesa', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Guardar Mesa
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const EVENTO_ID = '{{ $eventoId }}';
    const API_MESAS = `/api/eventos/${EVENTO_ID}/mesas`;
    const API_INVITADOS = `/api/eventos/${EVENTO_ID}/invitados`;

    let globalMesas = [];
    let globalInvitados = [];

    document.addEventListener('DOMContentLoaded', () => {
        cargarDatosCompletos();
    });

    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) modal.classList.remove('hidden');
        else {
            modal.classList.add('hidden');
            document.getElementById('form-mesa').reset();
        }
    }

    // --- CARGAR DATOS EN PARALELO ---
    function cargarDatosCompletos() {
        if(!EVENTO_ID) return;

        Promise.all([
            fetch(API_MESAS).then(res => res.json()),
            fetch(API_INVITADOS).then(res => res.json())
        ])
        .then(([mesasData, invitadosData]) => {
            globalMesas = mesasData.mesas || mesasData || [];
            
            // Extraemos los invitados (manejando si vienen en objeto o array directo)
            const invList = invitadosData.invitados || invitadosData || [];
            // Filtramos solo los confirmados o disponibles para ubicar
            globalInvitados = invList.filter(i => i.confirmado === true || i.estado_rsvp === 'confirmado' || true);

            renderizarSalon();
        })
        .catch(err => console.error("Error sincronizando mesas e invitados:", err));
    }

    // --- RENDERIZAR MESAS E INVITADOS SIN UBICAR ---
    function renderizarSalon() {
        const contMesas = document.getElementById('contenedor-mesas');
        const contSinMesa = document.getElementById('contenedor-sin-mesa');

        // 1. Renderizar Mesas
        if (globalMesas.length === 0) {
            contMesas.innerHTML = `
                <div class="col-span-full bg-white text-center py-16 rounded-3xl border border-slate-200/80 shadow-sm">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-chair"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700">No has creado mesas aún</h3>
                    <p class="text-xs text-slate-400 mt-1">Crea tu primera mesa para empezar a asignar lugares.</p>
                </div>
            `;
        } else {
            contMesas.innerHTML = globalMesas.map(mesa => {
                const mesaId = mesa._id || mesa.id;
                const invitadosMesa = mesa.invitados || [];
                const ocupadas = invitadosMesa.length;
                const totales = mesa.limite_sillas || 8;
                const estaLlena = ocupadas >= totales;

                return `
                    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between hover:border-indigo-200 transition-all">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-black text-slate-800 text-base flex items-center gap-1.5">
                                        🏢 ${mesa.nombre}
                                    </h3>
                                    <span class="text-[10px] text-slate-400 font-bold block uppercase mt-0.5">
                                        Capacidad: ${totales} Sillas
                                    </span>
                                </div>
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full ${estaLlena ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-600'}">
                                    ${ocupadas} / ${totales}
                                </span>
                            </div>

                            <div class="bg-slate-50 rounded-2xl p-3 min-h-[100px] border border-slate-100 space-y-2">
                                ${invitadosMesa.length === 0 ? `
                                    <p class="text-xs text-slate-400 italic text-center py-6">Mesa vacía</p>
                                ` : invitadosMesa.map(inv => `
                                    <div class="bg-white px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 shadow-sm border border-slate-100 flex justify-between items-center">
                                        <span>👤 ${inv.nombre}</span>
                                        <button onclick="removerDeMesa('${mesaId}', '${inv._id || inv.id}')" class="text-slate-400 hover:text-rose-500 transition-colors" title="Remover de mesa">
                                            <i class="fa-solid fa-circle-minus"></i>
                                        </button>
                                    </div>
                               `).join('')}
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-end">
                            <button onclick="desarmarMesa('${mesaId}')" class="text-xs text-slate-400 hover:text-red-500 font-bold transition-colors flex items-center gap-1">
                                <i class="fa-solid fa-trash-can"></i> Desarmar Mesa
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // 2. Filtrar invitados que NO están en ninguna mesa
        // Recopilamos todos los IDs de invitados ya ubicados
        const idsUbicados = new Set();
        globalMesas.forEach(m => {
            (m.invitados || []).forEach(inv => idsUbicados.add(String(inv._id || inv.id)));
        });

        const invitadosSinUbicacion = globalInvitados.filter(i => !idsUbicados.has(String(i._id || i.id)));

        if (invitadosSinUbicacion.length === 0) {
            contSinMesa.innerHTML = `
                <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <p class="text-xs text-slate-400 italic font-medium">¡Todos ubicados u organizados! 🎉</p>
                </div>
            `;
        } else {
            contSinMesa.innerHTML = invitadosSinUbicacion.map(inv => {
                const invId = inv._id || inv.id;
                // Opciones de mesas que aún tienen espacio
                const opcionesMesasDisponibles = globalMesas.filter(m => (m.invitados || []).length < (m.limite_sillas || 8));

                return `
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex flex-col gap-2">
                        <span class="text-xs font-bold text-slate-700">👤 ${inv.nombre}</span>
                        
                        <div class="flex gap-2">
                            <select id="select-mesa-${invId}" class="flex-1 px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-medium outline-none">
                                <option value="">Elegir Mesa...</option>
                                ${opcionesMesasDisponibles.map(m => `
                                    <option value="${m._id || m.id}">${m.nombre}</option>
                                `).join('')}
                            </select>
                            <button onclick="ubicarInvitado('${invId}')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-3 py-1.5 rounded-lg transition-colors">
                                Ubicar
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }
    }

    // --- ACCIÓN: GUARDAR NUEVA MESA (POST) ---
    function guardarMesa(e) {
        e.preventDefault();
        const payload = {
            nombre: document.getElementById('input-nombre').value,
            limite_sillas: document.getElementById('input-sillas').value
        };

        fetch(API_MESAS, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(() => {
            toggleModal('modal-nueva-mesa', false);
            cargarDatosCompletos();
            Swal.fire({ icon: 'success', title: '¡Mesa Creada!', showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-3xl' } });
        })
        .catch(err => console.error(err));
    }

    // --- ACCIÓN: ASIGNAR INVITADO A MESA (PUT) ---
    function ubicarInvitado(invitadoId) {
        const select = document.getElementById(`select-mesa-${invitadoId}`);
        const mesaId = select.value;

        if(!mesaId) {
            Swal.fire('Atención', 'Selecciona una mesa primero.', 'warning');
            return;
        }

        fetch(`${API_MESAS}/asignar`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ mesa_id: mesaId, invitado_id: invitadoId })
        })
        .then(res => res.json())
        .then(() => {
            cargarDatosCompletos();
            Swal.fire({ icon: 'success', title: '¡Ubicado!', showConfirmButton: false, timer: 1200, customClass: { popup: 'rounded-3xl' } });
        })
        .catch(err => console.error(err));
    }

    // --- ACCIÓN: REMOVER INVITADO DE MESA (PUT) ---
    function removerDeMesa(mesaId, invitadoId) {
        fetch(`${API_MESAS}/remover`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ mesa_id: mesaId, invitado_id: invitadoId })
        })
        .then(() => {
            cargarDatosCompletos();
        })
        .catch(err => console.error(err));
    }

    // --- ACCIÓN: DESARMAR / ELIMINAR MESA (DELETE) ---
    function desarmarMesa(mesaId) {
        Swal.fire({
            title: '¿Desarmar esta mesa?',
            text: "Los invitados asignados volverán a quedar libres.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, desarmar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-3xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${API_MESAS}/${mesaId}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                })
                .then(() => {
                    cargarDatosCompletos();
                    Swal.fire({ icon: 'success', title: 'Mesa eliminada', showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-3xl' } });
                })
                .catch(err => console.error(err));
            }
        });
    }
</script>
@endsection