@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;
@endphp

<div class="space-y-8">
    
    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 bg-slate-800 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg flex items-center gap-1">
            <i class="fa-solid fa-lock text-[8px]"></i> 100% Privado
        </div>

        <div>
            <div class="flex items-center gap-3 mt-2 sm:mt-0">
                <span class="text-2xl">💰</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tracker de Presupuesto</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Lleva el control de tus finanzas. Anota tus gastos estimados vs los reales. (Los invitados nunca verán esta pantalla).
            </p>
        </div>
        
        <button onclick="toggleModal('modal-nuevo-gasto', true)" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-plus"></i> Registrar Gasto
        </button>
    </div>

    {{-- Métricas de Presupuesto --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Presupuesto Estimado</span>
                <span id="stat-estimado" class="text-2xl font-black text-slate-900">$0</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Gasto Real (Pagado)</span>
                <span id="stat-real" class="text-2xl font-black text-slate-900">$0</span>
            </div>
        </div>

        <div id="card-diferencia" class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5 transition-all">
            <div id="icon-diferencia" class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl bg-slate-100 text-slate-600">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <span id="label-diferencia" class="text-xs font-bold uppercase tracking-wider block text-slate-400">Balance</span>
                <span id="stat-diferencia" class="text-2xl font-black text-slate-900">$0</span>
            </div>
        </div>

    </div>

    {{-- Tabla de Desglose de Gastos --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">📊 Desglose de Gastos</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 rounded-tl-xl">Concepto / Detalle</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Costo Estimado</th>
                        <th class="px-4 py-3">Costo Real</th>
                        <th class="px-4 py-3 rounded-tr-xl text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-gastos" class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="5" class="text-center py-12 text-slate-400">
                            <i class="fa-solid fa-spinner fa-spin text-xl mb-2"></i>
                            <p class="text-xs">Cargando presupuesto...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Registrar Gasto --}}
<div id="modal-nuevo-gasto" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-nuevo-gasto', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">💸 Registrar Nuevo Gasto</h3>
        <p class="text-xs text-slate-500 mb-6">Anota un concepto financiero para llevar el tracking correcto de tu evento.</p>

        <form id="form-presupuesto" onsubmit="guardarGasto(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Concepto / Descripción *</label>
                <input type="text" id="input-concepto" required placeholder="Ej. Arriendo de Salón, Fotógrafo, Torta"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-slate-400 transition-all text-sm font-medium">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Costo Estimado *</label>
                    <input type="number" id="input-estimado" required placeholder="Ej. 50000" min="0"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-slate-400 transition-all text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Costo Real Pagado</label>
                    <input type="number" id="input-real" placeholder="Ej. 55000" min="0" value="0"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-slate-400 transition-all text-sm font-semibold">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Estado del Pago *</label>
                <select id="input-estado" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-slate-400 transition-all text-sm font-bold text-slate-700 bg-white cursor-pointer">
                    <option value="pendiente">⏳ Pendiente de Pago</option>
                    <option value="pagado">✅ Pagado Totalmente</option>
                </select>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-nuevo-gasto', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Añadir Gasto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const EVENTO_ID = '{{ $eventoId }}';
    const API_URL = `/api/eventos/${EVENTO_ID}/presupuesto`;

    const formatearDinero = (monto) => new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 0 }).format(monto || 0);

    document.addEventListener('DOMContentLoaded', cargarPresupuesto);

    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) modal.classList.remove('hidden');
        else {
            modal.classList.add('hidden');
            document.getElementById('form-presupuesto').reset();
        }
    }

    // --- CARGAR DATOS (GET) ---
    function cargarPresupuesto() {
        if(!EVENTO_ID) return;

        fetch(API_URL)
            .then(res => res.json())
            .then(data => renderizarPresupuesto(data))
            .catch(err => {
                console.error("Error al cargar presupuesto:", err);
                document.getElementById('tabla-gastos').innerHTML = `<tr><td colspan="5" class="text-center py-8 text-rose-500 font-medium">Error al cargar registros financieros.</td></tr>`;
            });
    }

    // --- RENDERIZAR TABLA Y MÉTRICAS ---
    function renderizarPresupuesto(response) {
        const gastos = response.presupuesto || response.gastos || response || [];
        const tbody = document.getElementById('tabla-gastos');

        // Cálculos
        const totalEstimado = gastos.reduce((acc, curr) => acc + Number(curr.monto_estimado || curr.estimado || 0), 0);
        const totalReal = gastos.reduce((acc, curr) => acc + Number(curr.monto_real || curr.real || 0), 0);
        const diferencia = totalEstimado - totalReal;
        const estaExcedido = diferencia < 0;

        // Actualizar estadísticas superiores
        document.getElementById('stat-estimado').innerText = formatearDinero(totalEstimado);
        document.getElementById('stat-real').innerText = formatearDinero(totalReal);

        const cardDiferencia = document.getElementById('card-diferencia');
        const iconDiferencia = document.getElementById('icon-diferencia');
        const labelDiferencia = document.getElementById('label-diferencia');
        const statDiferencia = document.getElementById('stat-diferencia');

        if (estaExcedido) {
            cardDiferencia.className = "bg-white p-6 rounded-3xl border border-rose-200 bg-rose-50/30 shadow-sm flex items-center gap-5 transition-all";
            iconDiferencia.className = "w-12 h-12 rounded-2xl flex items-center justify-center text-xl bg-rose-100 text-rose-600";
            iconDiferencia.innerHTML = '<i class="fa-solid fa-arrow-trend-down"></i>';
            labelDiferencia.className = "text-xs font-bold uppercase tracking-wider block text-rose-500";
            labelDiferencia.innerText = "Excedido en";
            statDiferencia.className = "text-2xl font-black text-rose-600";
            statDiferencia.innerText = formatearDinero(Math.abs(diferencia));
        } else {
            cardDiferencia.className = "bg-white p-6 rounded-3xl border border-emerald-200 bg-emerald-50/30 shadow-sm flex items-center gap-5 transition-all";
            iconDiferencia.className = "w-12 h-12 rounded-2xl flex items-center justify-center text-xl bg-emerald-100 text-emerald-600";
            iconDiferencia.innerHTML = '<i class="fa-solid fa-scale-balanced"></i>';
            labelDiferencia.className = "text-xs font-bold uppercase tracking-wider block text-emerald-600";
            labelDiferencia.innerText = "A Favor (Restante)";
            statDiferencia.className = "text-2xl font-black text-emerald-700";
            statDiferencia.innerText = formatearDinero(diferencia);
        }

        // Renderizar tabla
        if (gastos.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5">
                        <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <div class="w-12 h-12 bg-white text-slate-300 rounded-full flex items-center justify-center text-lg mx-auto mb-3 shadow-sm">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-600">No has registrado ningún gasto</h3>
                            <p class="text-xs text-slate-400 mt-1">Anota conceptos como comida, decoración o local para iniciar tu tracker.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = gastos.map(gasto => {
            const id = gasto._id || gasto.id;
            const est = Number(gasto.monto_estimado || gasto.estimado || 0);
            const real = Number(gasto.monto_real || gasto.real || 0);
            const difItem = est - real;
            const pagado = (gasto.estado || 'pendiente') === 'pagado';

            let alertaItem = '';
            if (difItem < 0) {
                alertaItem = `<span class="text-[10px] text-rose-500 font-semibold block mt-0.5">Te excediste por ${formatearDinero(Math.abs(difItem))}</span>`;
            } else if (difItem > 0 && pagado) {
                alertaItem = `<span class="text-[10px] text-emerald-500 font-semibold block mt-0.5">Ahorraste ${formatearDinero(difItem)}</span>`;
            }

            const badgeEstado = pagado 
                ? `<span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 bg-emerald-100 text-emerald-700 rounded-md uppercase"><i class="fa-solid fa-check"></i> Pagado</span>`
                : `<span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 bg-amber-100 text-amber-700 rounded-md uppercase"><i class="fa-regular fa-clock"></i> Pendiente</span>`;

            return `
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-4 py-4">
                        <strong class="text-slate-800 block">${gasto.concepto || 'Gasto general'}</strong>
                        ${alertaItem}
                    </td>
                    <td class="px-4 py-4">${badgeEstado}</td>
                    <td class="px-4 py-4 font-medium text-slate-500">${formatearDinero(est)}</td>
                    <td class="px-4 py-4 font-bold text-slate-800">${formatearDinero(real)}</td>
                    <td class="px-4 py-4 text-right">
                        <button onclick="eliminarGasto('${id}')" class="p-2 text-slate-300 hover:text-rose-500 transition-colors" title="Eliminar registro">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // --- GUARDAR GASTO (POST) ---
    function guardarGasto(e) {
        e.preventDefault();

        const payload = {
            concepto: document.getElementById('input-concepto').value,
            monto_estimado: document.getElementById('input-estimado').value,
            monto_real: document.getElementById('input-real').value,
            estado: document.getElementById('input-estado').value
        };

        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(() => {
            toggleModal('modal-nuevo-gasto', false);
            cargarPresupuesto();
            Swal.fire({
                icon: 'success',
                title: '¡Gasto Registrado!',
                showConfirmButton: false,
                timer: 1500,
                customClass: { popup: 'rounded-3xl' }
            });
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'No se pudo registrar el gasto.', 'error');
        });
    }

    // --- ELIMINAR GASTO (DELETE) ---
    function eliminarGasto(id) {
        Swal.fire({
            title: '¿Borrar registro financiero?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, borrar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-3xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                })
                .then(() => {
                    cargarPresupuesto();
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: { popup: 'rounded-3xl' }
                    });
                })
                .catch(err => console.error("Error al borrar gasto:", err));
            }
        });
    }
</script>
@endsection