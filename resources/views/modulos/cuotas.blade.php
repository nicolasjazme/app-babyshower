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
                <span class="text-2xl">🐮</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Módulo Cuotas (La Vaca)</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Define el costo por persona, configura tus datos de transferencia y registra el dinero recaudado de forma segura.
            </p>
        </div>
        
        <button onclick="toggleModal('modal-config-vaca', true)" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-piggy-bank"></i> Configurar Datos Bancarios
        </button>
    </div>

    {{-- Métricas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-comment-dollar"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Cuota por Persona</span>
                <span id="stat-cuota" class="text-2xl font-black text-slate-900">$0</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Recaudado</span>
                <span id="stat-recaudado" class="text-2xl font-black text-slate-900">$0</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pagos Pendientes</span>
                <span id="stat-pendientes" class="text-2xl font-black text-slate-900">0</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Tabla de Aportes / Pagos --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">📝 Control de Aportes</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-500">
                    <thead class="text-xs text-slate-400 uppercase bg-slate-50 rounded-xl">
                        <tr>
                            <th class="px-4 py-3">Invitado</th>
                            <th class="px-4 py-3">Monto Aportado</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-pagos" class="divide-y divide-slate-100">
                        <tr>
                            <td colspan="4" class="text-center py-12">
                                <i class="fa-solid fa-spinner fa-spin text-slate-300 text-2xl mb-2"></i>
                                <p class="text-slate-400">Cargando pagos...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Panel de Datos Bancarios --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between h-full">
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">🏦 Tus Datos de Transferencia</h2>
                
                <div id="contenedor-banco">
                    {{-- Se inyecta por JS --}}
                </div>
            </div>

            <div class="mt-8 border-t border-slate-100 pt-4">
                <span class="text-xs text-slate-400 flex items-start gap-1.5 leading-relaxed">
                    <i class="fa-solid fa-circle-info mt-0.5 text-slate-400 text-sm shrink-0"></i>
                    Estos datos se mostrarán directamente en la invitación pública para que los invitados puedan copiar y pagar rápidamente.
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Modal Configuración Bancaria --}}
<div id="modal-config-vaca" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-slate-100 relative max-h-[90vh] overflow-y-auto animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-config-vaca', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">🐮 Configurar La Vaca</h3>
        <p class="text-xs text-slate-500 mb-6">Establece la cuota y los datos bancarios para las transferencias de los invitados.</p>

        <form id="form-config" onsubmit="guardarConfig(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Cuota por Persona *</label>
                <input type="number" id="input-monto-persona" required min="0" placeholder="Ej. 10000"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-semibold">
            </div>

            <hr class="border-slate-100 my-4">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nombre Titular *</label>
                    <input type="text" id="input-titular" required placeholder="Ej. Nicolás Pérez"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">RUT Titular *</label>
                    <input type="text" id="input-rut" required placeholder="Ej. 12.345.678-9"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Banco *</label>
                    <input type="text" id="input-banco" required placeholder="Ej. Banco Estado"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipo de Cuenta *</label>
                    <select id="input-tipo-cuenta" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-semibold bg-white cursor-pointer">
                        <option value="Cuenta Vista / RUT">Cuenta Vista / RUT</option>
                        <option value="Cuenta Corriente">Cuenta Corriente</option>
                        <option value="Cuenta de Ahorro">Cuenta de Ahorro</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Número de Cuenta *</label>
                    <input type="text" id="input-numero-cuenta" required placeholder="Ej. 12345678"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-medium">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email de aviso (Opcional)</label>
                <input type="email" id="input-email" placeholder="transferencias@correo.com"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-medium">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-config-vaca', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- VARIABLES Y CONFIGURACIÓN ---
    const EVENTO_ID = '{{ $eventoId }}';
    const API_CONFIG = `/api/eventos/${EVENTO_ID}/cuotas/config`; 
    const API_PAGOS = `/api/eventos/${EVENTO_ID}/cuotas`; 

    // Helper para dar formato $ 10.000 a los números
    const formatearDinero = (monto) => new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 0 }).format(monto || 0);

    document.addEventListener('DOMContentLoaded', () => {
        cargarConfiguracion();
        cargarPagos();
    });

    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) modal.classList.remove('hidden');
        else modal.classList.add('hidden');
    }

    // --- 1. GESTIÓN DE CONFIGURACIÓN BANCARIA ---
    function cargarConfiguracion() {
        if(!EVENTO_ID) return;
        fetch(API_CONFIG)
            .then(res => res.json())
            .then(data => renderizarConfiguracion(data))
            .catch(err => console.error("No se pudo cargar la configuración bancaria:", err));
    }

    function renderizarConfiguracion(config) {
        const contenedor = document.getElementById('contenedor-banco');
        const statCuota = document.getElementById('stat-cuota');

        // Llenar Modal para editar más tarde
        if(config) {
            document.getElementById('input-monto-persona').value = config.monto_persona || '';
            document.getElementById('input-titular').value = config.titular || '';
            document.getElementById('input-rut').value = config.rut || '';
            document.getElementById('input-banco').value = config.banco || '';
            document.getElementById('input-tipo-cuenta').value = config.tipo_cuenta || 'Cuenta Vista / RUT';
            document.getElementById('input-numero-cuenta').value = config.numero_cuenta || '';
            document.getElementById('input-email').value = config.email_confirmacion || '';
            
            statCuota.innerText = formatearDinero(config.monto_persona);
        }

        // Llenar Tarjeta Visual
        if (!config || !config.banco) {
            contenedor.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-xl mx-auto mb-3"><i class="fa-solid fa-piggy-bank"></i></div>
                    <p class="text-xs text-slate-400 italic">No has configurado tu cuenta. Los invitados no sabrán dónde transferir.</p>
                </div>
            `;
            return;
        }

        contenedor.innerHTML = `
            <div class="space-y-4 text-sm bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Titular</span>
                    <span class="font-bold text-slate-800">${config.titular}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Rut / Documento</span>
                    <span class="font-bold text-slate-800">${config.rut}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Banco</span>
                    <span class="font-bold text-slate-800">${config.banco}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Tipo Cuenta</span>
                    <span class="font-bold text-slate-800">${config.tipo_cuenta}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Nº Cuenta</span>
                    <span class="font-mono font-bold text-slate-800">${config.numero_cuenta}</span>
                </div>
                ${config.email_confirmacion ? `
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Email de Confirmación</span>
                    <span class="font-bold text-slate-800 text-xs">${config.email_confirmacion}</span>
                </div>` : ''}
            </div>
        `;
    }

    function guardarConfig(e) {
        e.preventDefault();
        const payload = {
            monto_persona: document.getElementById('input-monto-persona').value,
            titular: document.getElementById('input-titular').value,
            rut: document.getElementById('input-rut').value,
            banco: document.getElementById('input-banco').value,
            tipo_cuenta: document.getElementById('input-tipo-cuenta').value,
            numero_cuenta: document.getElementById('input-numero-cuenta').value,
            email_confirmacion: document.getElementById('input-email').value
        };

        fetch(API_CONFIG, {
            method: 'POST', // o 'PUT' según tu backend
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            toggleModal('modal-config-vaca', false);
            cargarConfiguracion(); // Refresca visualmente
            Swal.fire({ icon: 'success', title: '¡Guardado!', text: 'Datos bancarios actualizados.', confirmButtonColor: '#10b981', customClass: { popup: 'rounded-3xl' } });
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'No se pudo guardar la configuración.', 'error');
        });
    }

    // --- 2. GESTIÓN DE PAGOS Y APORTES ---
    function cargarPagos() {
        if(!EVENTO_ID) return;
        fetch(API_PAGOS)
            .then(res => res.json())
            .then(data => renderizarPagos(data))
            .catch(err => {
                console.error("Error cargando pagos:", err);
                document.getElementById('tabla-pagos').innerHTML = `<tr><td colspan="4" class="text-center py-8 text-rose-500 font-medium">No se pudieron cargar los pagos.</td></tr>`;
            });
    }

    function renderizarPagos(pagos) {
        const tbody = document.getElementById('tabla-pagos');
        
        // Calcular métricas
        const totalRecaudado = pagos.filter(p => p.estado === 'aprobado').reduce((acc, curr) => acc + Number(curr.monto), 0);
        const pagosPendientes = pagos.filter(p => p.estado === 'pendiente').length;

        document.getElementById('stat-recaudado').innerText = formatearDinero(totalRecaudado);
        document.getElementById('stat-pendientes').innerText = pagosPendientes;

        // Renderizar tabla
        if (!pagos || pagos.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4">
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-600">No hay registros todavía</h3>
                            <p class="text-xs text-slate-400 mt-1">Los comprobantes que envíen tus invitados aparecerán listados aquí para que los apruebes.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = pagos.map(pago => {
            const esAprobado = pago.estado === 'aprobado';
            const badge = esAprobado 
                ? `<span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full uppercase">Aprobado</span>`
                : `<span class="text-[10px] font-bold px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full uppercase">Pendiente</span>`;

            const btnAprobar = !esAprobado ? `
                <button onclick="aprobarPago('${pago._id || pago.id}')" class="p-2 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-100 transition-colors" title="Aprobar Pago">
                    <i class="fa-solid fa-check"></i>
                </button>
            ` : '';

            return `
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-4 py-4 font-semibold text-slate-800">${pago.nombre_invitado || pago.nombre}</td>
                    <td class="px-4 py-4 font-bold text-slate-900">${formatearDinero(pago.monto)}</td>
                    <td class="px-4 py-4">${badge}</td>
                    <td class="px-4 py-4 text-right flex justify-end gap-2">
                        ${btnAprobar}
                        <button onclick="eliminarPago('${pago._id || pago.id}')" class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition-colors" title="Descartar">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function aprobarPago(id) {
        // Asumiendo que tu backend tiene un endpoint para cambiar estado
        fetch(`${API_PAGOS}/${id}/aprobar`, { 
            method: 'PUT', 
            headers: { 'Accept': 'application/json' }
        })
        .then(() => {
            cargarPagos(); // Recarga la lista y actualiza métricas
            Swal.fire({ icon: 'success', title: 'Aprobado', text: 'El dinero se sumó al total recaudado.', showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-3xl' }});
        })
        .catch(err => console.error(err));
    }

    function eliminarPago(id) {
        Swal.fire({
            title: '¿Descartar este pago?',
            text: "Se eliminará el registro de este aporte.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, descartar',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-3xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`${API_PAGOS}/${id}`, { 
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                })
                .then(() => {
                    cargarPagos();
                    Swal.fire({ icon: 'success', title: 'Descartado', showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-3xl' } });
                })
                .catch(err => console.error(err));
            }
        });
    }
</script>
@endsection