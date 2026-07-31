@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;
@endphp

<div class="space-y-8">
    
    {{-- Cabecera --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm relative overflow-hidden">
        
        <div class="absolute top-0 right-0 bg-rose-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg flex items-center gap-1 animate-pulse">
            <i class="fa-solid fa-circle text-[6px]"></i> Modo Puerta
        </div>

        <div>
            <div class="flex items-center gap-3 mt-2 sm:mt-0">
                <span class="text-2xl">🎟️</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Control de Check-In</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Escanea el código QR de tus invitados al ingresar o búscalo manualmente para validar su acceso.
            </p>
        </div>
    </div>

    {{-- Métricas de la Puerta --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Confirmados</span>
                <span id="stat-confirmados" class="text-2xl font-black text-slate-900">0</span>
            </div>
        </div>

        <div class="bg-cyan-50 p-6 rounded-3xl border border-cyan-100 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500 text-white flex items-center justify-center text-xl shadow-md">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-cyan-600 uppercase tracking-wider block">Ya Ingresaron</span>
                <span id="stat-ingresados" class="text-2xl font-black text-cyan-900">0</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-person-walking-arrow-right"></i>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Faltan por llegar</span>
                <span id="stat-faltan" class="text-2xl font-black text-slate-900">0</span>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        {{-- Módulo de Escáner --}}
        <div class="bg-slate-900 p-6 rounded-3xl border border-slate-800 shadow-lg flex flex-col items-center justify-center text-center min-h-[400px] relative overflow-hidden">
            
            <h2 class="text-lg font-bold text-white mb-2 flex items-center gap-2 z-10">
                <i class="fa-solid fa-camera"></i> Escáner de Acceso
            </h2>
            <p class="text-xs text-slate-400 mb-8 z-10">Apunta la cámara al código QR de la invitación.</p>

            <div class="w-64 h-64 border-2 border-dashed border-cyan-500 rounded-3xl relative flex items-center justify-center bg-slate-800 z-10 group">
                <div class="absolute w-full h-1 bg-cyan-400/50 shadow-[0_0_15px_rgba(34,211,238,0.8)] top-0 left-0 animate-[scan_2s_ease-in-out_infinite]"></div>
                
                <button onclick="simularEscaner()" class="bg-cyan-500 hover:bg-cyan-400 text-white font-bold py-3 px-6 rounded-2xl transition-all shadow-lg text-sm z-20 group-hover:scale-105">
                    Activar Cámara
                </button>
            </div>
            
            <p class="text-[10px] text-slate-500 mt-6 z-10">
                *El escáner requiere permisos de navegador o integración con librería JS (ej. html5-qrcode).
            </p>
        </div>

        {{-- Panel de Listados (Historial / Búsqueda) --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col">
            
            {{-- Tabs --}}
            <div class="flex border-b border-slate-100 mb-4">
                <button id="tab-btn-historial" onclick="cambiarTab('historial')" class="px-4 py-2 text-sm font-bold text-cyan-600 border-b-2 border-cyan-600 transition-colors">
                    Historial Reciente
                </button>
                <button id="tab-btn-busqueda" onclick="cambiarTab('busqueda')" class="px-4 py-2 text-sm font-bold text-slate-400 hover:text-slate-600 border-b-2 border-transparent transition-colors">
                    Búsqueda Manual
                </button>
            </div>

            {{-- Contenedor Historial --}}
            <div id="tab-historial" class="flex-1 max-h-[350px] overflow-y-auto pr-2 space-y-3">
                <div class="text-center py-8 text-slate-400"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Cargando...</div>
            </div>

            {{-- Contenedor Búsqueda Manual --}}
            <div id="tab-busqueda" class="hidden flex-1 flex flex-col max-h-[350px] overflow-hidden">
                <div class="relative mb-4 shrink-0">
                    <input type="text" id="input-busqueda" onkeyup="filtrarBusqueda()" placeholder="Buscar invitado por nombre..." class="w-full bg-slate-50 border border-slate-200 text-sm rounded-xl px-4 py-2.5 pl-10 focus:outline-none focus:border-cyan-400">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400"></i>
                </div>
                
                <div id="lista-busqueda" class="flex-1 overflow-y-auto pr-2 space-y-3">
                    </div>
            </div>
            
            <div class="mt-4 border-t border-slate-100 pt-3 text-center shrink-0">
                <span class="text-[10px] text-slate-400 font-medium">
                    <i class="fa-solid fa-shield-halved"></i> Sistema protegido contra duplicidad de entradas.
                </span>
            </div>
        </div>

    </div>
</div>

<style>
    @keyframes scan {
        0%, 100% { top: 0%; }
        50% { top: 100%; }
    }
    
    /* Scrollbar estético para las listas */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<script>
    const EVENTO_ID = '{{ $eventoId }}';
    const API_URL = `/api/eventos/${EVENTO_ID}/invitados`;
    let todosLosInvitados = [];

    document.addEventListener('DOMContentLoaded', cargarInvitados);

    // --- CARGAR DATOS DESDE NODE.JS ---
    function cargarInvitados() {
        if(!EVENTO_ID) return;
        
        fetch(API_URL)
            .then(res => res.json())
            .then(data => {
                todosLosInvitados = data.invitados || data || [];
                actualizarMetricas();
                renderizarListas();
            })
            .catch(err => console.error("Error cargando invitados:", err));
    }

    // --- ACTUALIZAR NÚMEROS DE ARRIBA ---
    function actualizarMetricas() {
        // Consideramos "confirmado" a los que tienen estado true o similar. Ajusta según tu DB.
        const confirmados = todosLosInvitados.filter(i => i.confirmado === true || i.estado_rsvp === 'confirmado');
        const ingresados = todosLosInvitados.filter(i => i.ingreso === true || i.check_in === true);
        
        const totalConf = confirmados.length;
        const totalIngr = ingresados.length;
        const faltan = totalConf > totalIngr ? totalConf - totalIngr : 0;

        // Actualizar UI
        document.getElementById('stat-confirmados').innerText = totalConf;
        document.getElementById('stat-ingresados').innerText = totalIngr;
        document.getElementById('stat-faltan').innerText = faltan;
    }

    // --- CAMBIAR ENTRE PESTAÑAS ---
    function cambiarTab(tab) {
        const tHistorial = document.getElementById('tab-historial');
        const tBusqueda = document.getElementById('tab-busqueda');
        const btnHistorial = document.getElementById('tab-btn-historial');
        const btnBusqueda = document.getElementById('tab-btn-busqueda');

        if(tab === 'historial') {
            tHistorial.classList.remove('hidden');
            tBusqueda.classList.add('hidden');
            
            btnHistorial.className = "px-4 py-2 text-sm font-bold text-cyan-600 border-b-2 border-cyan-600 transition-colors";
            btnBusqueda.className = "px-4 py-2 text-sm font-bold text-slate-400 hover:text-slate-600 border-b-2 border-transparent transition-colors";
        } else {
            tHistorial.classList.add('hidden');
            tBusqueda.classList.remove('hidden');
            tBusqueda.classList.add('flex'); // Para mantener el layout flex interno

            btnBusqueda.className = "px-4 py-2 text-sm font-bold text-cyan-600 border-b-2 border-cyan-600 transition-colors";
            btnHistorial.className = "px-4 py-2 text-sm font-bold text-slate-400 hover:text-slate-600 border-b-2 border-transparent transition-colors";
        }
    }

    // --- DIBUJAR LAS LISTAS ---
    function renderizarListas() {
        const contenedorHistorial = document.getElementById('tab-historial');
        const contenedorBusqueda = document.getElementById('lista-busqueda');

        // Los que ya entraron (Historial)
        const ingresados = todosLosInvitados.filter(i => i.ingreso === true || i.check_in === true).reverse();
        
        if (ingresados.length === 0) {
            contenedorHistorial.innerHTML = `<div class="text-center py-8 text-slate-400 text-sm">Aún no hay ingresos registrados.</div>`;
        } else {
            contenedorHistorial.innerHTML = ingresados.map(i => `
                <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <strong class="block text-sm text-slate-800">${i.nombre}</strong>
                            <span class="block text-[10px] text-slate-500 font-medium">Acompañantes: ${i.acompanantes || 0}</span>
                        </div>
                    </div>
                    <span class="text-[9px] font-black px-2 py-1 bg-emerald-200 text-emerald-800 rounded uppercase">Adentro</span>
                </div>
            `).join('');
        }

        // Los que faltan por entrar (Búsqueda Manual)
        const pendientes = todosLosInvitados.filter(i => !i.ingreso && !i.check_in);
        
        if (pendientes.length === 0) {
            contenedorBusqueda.innerHTML = `<div class="text-center py-8 text-slate-400 text-sm">Todos los invitados ya ingresaron.</div>`;
        } else {
            contenedorBusqueda.innerHTML = pendientes.map(i => `
                <div class="item-busqueda p-3 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between" data-nombre="${i.nombre.toLowerCase()}">
                    <div>
                        <strong class="block text-sm text-slate-800">${i.nombre}</strong>
                        <span class="block text-[10px] text-slate-500 font-medium">Cupos: ${(i.acompanantes || 0) + 1}</span>
                    </div>
                    <button onclick="marcarIngreso('${i._id}')" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                        Marcar Llegada
                    </button>
                </div>
            `).join('');
        }
    }

    // --- FILTRO DE BÚSQUEDA ---
    function filtrarBusqueda() {
        const texto = document.getElementById('input-busqueda').value.toLowerCase();
        const items = document.querySelectorAll('.item-busqueda');
        
        items.forEach(item => {
            const nombre = item.getAttribute('data-nombre');
            if (nombre.includes(texto)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // --- ACCIÓN DE MARCAR INGRESO ---
    function marcarIngreso(id) {
        fetch(`${API_URL}/${id}/checkin`, { 
            method: 'PUT',
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            // Recargamos los datos para actualizar métricas y mover a la lista de Historial
            cargarInvitados();
            Swal.fire({
                icon: 'success',
                title: '¡Acceso Concedido!',
                text: 'Invitado registrado correctamente.',
                showConfirmButton: false,
                timer: 1500,
                customClass: { popup: 'rounded-3xl' }
            });
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'No se pudo marcar el ingreso.', 'error');
        });
    }

    // --- SIMULAR ESCÁNER (Opcional visual) ---
    function simularEscaner() {
        Swal.fire({
            title: 'Escáner QR',
            text: 'Para usar el escáner real se requiere instalar una librería como html5-qrcode. Usa la pestaña "Búsqueda Manual" por ahora.',
            icon: 'info',
            confirmButtonColor: '#0ea5e9',
            customClass: { popup: 'rounded-3xl' }
        });
    }
</script>
@endsection