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
                <span class="text-2xl">🍽️</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Gestión de Menú & Alergias</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Publica los platillos de la fiesta y revisa en tiempo real las restricciones alimentarias o alergias declaradas por tus invitados.
            </p>
        </div>
        
        <button onclick="toggleModal('modal-nueva-opcion', true)" class="inline-flex items-center gap-2 bg-teal-500 hover:bg-teal-600 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-plus"></i> Añadir Platillo / Opción
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Columna Izquierda: Opciones de Menú --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">🍲 Alternativas Disponibles</h2>

            <div id="contenedor-menu" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-full py-12 text-center text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-teal-500 text-xl mb-2"></i>
                    <p class="text-xs">Cargando menú...</p>
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Restricciones Médicas / Alergias --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-2 flex items-center gap-2">⚠️ Restricciones Médicas</h2>
                <p class="text-xs text-slate-400 mb-6">Reportes de salud enviados por los invitados confirmados.</p>

                <div id="contenedor-alergias" class="space-y-3 max-h-[380px] overflow-y-auto pr-1">
                    <div class="text-center py-8 text-slate-400 text-xs">Cargando reportes médicos...</div>
                </div>
            </div>

            <div class="mt-6 border-t border-slate-100 pt-4 text-xs text-slate-400 flex items-start gap-1.5 leading-relaxed">
                <i class="fa-solid fa-circle-info mt-0.5 text-slate-400 text-sm shrink-0"></i>
                Usa este listado para coordinar los ingredientes y platillos especiales con el servicio de banquetería del salón.
            </div>
        </div>
    </div>
</div>

{{-- Modal para Añadir Platillo --}}
<div id="modal-nueva-opcion" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-nueva-opcion', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">🍽️ Añadir Opción al Menú</h3>
        <p class="text-xs text-slate-500 mb-6">Agrega un platillo con sus componentes para estructurar la carta gastronómica.</p>

        <form id="form-menu" onsubmit="guardarPlatillo(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nombre del Platillo *</label>
                <input type="text" id="input-titulo" required placeholder="Ej. Lomo liso con papas duquesas, Risotto de setas"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-teal-200 transition-all text-sm font-medium">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Categoría / Tipo *</label>
                <select id="input-categoria" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-teal-200 transition-all text-sm font-semibold bg-white cursor-pointer text-slate-700">
                    <option value="Tradicional">🥩 Tradicional / Carnes</option>
                    <option value="Vegano/Vegetariano">🌱 Vegano / Vegetariano</option>
                    <option value="Infantil">🍟 Menú Infantil</option>
                    <option value="Celíaco/Especial">🌾 Sin Gluten / Celíaco</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Descripción de Ingredientes / Acompañamiento *</label>
                <textarea id="input-descripcion" required rows="3" placeholder="Ej. Medallón de vacuno bañado en salsa de champiñones..."
                          class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-teal-200 transition-all text-sm font-medium"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-nueva-opcion', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-teal-500 hover:bg-teal-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Publicar Platillo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const EVENTO_ID = '{{ $eventoId }}';
    const API_URL = `/api/eventos/${EVENTO_ID}/menu`;
    const API_ALERGIAS = `/api/eventos/${EVENTO_ID}/alergias`; // Endpoint para restricciones médicas

    document.addEventListener('DOMContentLoaded', () => {
        cargarMenu();
        cargarAlergias();
    });

    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) modal.classList.remove('hidden');
        else {
            modal.classList.add('hidden');
            document.getElementById('form-menu').reset();
        }
    }

    // --- 1. CARGAR MENÚ (GET) ---
    function cargarMenu() {
        if(!EVENTO_ID) return;

        fetch(API_URL)
            .then(res => res.json())
            .then(data => renderizarMenu(data))
            .catch(err => console.error("Error al cargar menú:", err));
    }

    function renderizarMenu(data) {
        const contenedor = document.getElementById('contenedor-menu');
        const opciones = data.menu || data.opcionesMenu || data || [];

        if (!opciones || opciones.length === 0) {
            contenedor.innerHTML = `
                <div class="col-span-full text-center py-16">
                    <div class="w-16 h-16 bg-teal-50 text-teal-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700">No has publicado opciones</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Agrega opciones de comida para que tus invitados elijan al confirmar.</p>
                </div>
            `;
            return;
        }

        contenedor.innerHTML = opciones.map(opcion => `
            <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100 flex flex-col justify-between hover:border-teal-200 transition-all">
                <div>
                    <div class="flex justify-between items-start gap-2">
                        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                            🥗 ${opcion.titulo || opcion.nombre}
                        </h3>
                        <span class="text-[9px] font-black px-2 py-0.5 bg-teal-100 text-teal-800 rounded-md uppercase tracking-wider shrink-0">
                            ${opcion.categoria || 'General'}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">${opcion.descripcion || ''}</p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex justify-end">
                    <button onclick="eliminarPlatillo('${opcion._id || opcion.id}')" class="text-xs text-slate-400 hover:text-rose-500 font-bold transition-colors">
                        <i class="fa-solid fa-trash-can"></i> Eliminar
                    </button>
                </div>
            </div>
        `).join('');
    }

    // --- 2. CARGAR ALERGIAS / RESTRICCIONES (GET) ---
    function cargarAlergias() {
        if(!EVENTO_ID) return;

        fetch(API_ALERGIAS)
            .then(res => res.json())
            .then(data => renderizarAlergias(data))
            .catch(err => {
                // Si el endpoint aún no está listo en el back, manejamos el error sutilmente
                document.getElementById('contenedor-alergias').innerHTML = `<div class="text-center py-6 text-slate-400 text-xs">Sin reportes registrados.</div>`;
            });
    }

    function renderizarAlergias(data) {
        const contenedor = document.getElementById('contenedor-alergias');
        const reportes = data.alergias || data.reportes || data || [];

        if (!reportes || reportes.length === 0) {
            contenedor.innerHTML = `
                <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <p class="text-xs text-slate-400 italic font-medium">Ningún invitado ha reportado alergias o restricciones. ¡Todo limpio! 🎉</p>
                </div>
            `;
            return;
        }

        contenedor.innerHTML = reportes.map(reporte => `
            <div class="p-3 bg-red-50/60 rounded-xl border border-red-100/50 flex flex-col gap-1">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-800">👤 ${reporte.nombre_invitado || reporte.invitado || 'Invitado'}</span>
                    <span class="text-[9px] font-extrabold text-red-600 bg-red-100 px-1.5 py-0.5 rounded uppercase">Cuidado</span>
                </div>
                <p class="text-xs text-red-800 bg-white/60 p-2 rounded-lg font-medium border border-red-100 mt-1">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 mr-1"></i>
                    ${reporte.detalles || reporte.alergia || 'Restricción alimentaria'}
                </p>
            </div>
        `).join('');
    }

    // --- 3. GUARDAR PLATILLO (POST) ---
    function guardarPlatillo(e) {
        e.preventDefault();

        const payload = {
            titulo: document.getElementById('input-titulo').value,
            categoria: document.getElementById('input-categoria').value,
            descripcion: document.getElementById('input-descripcion').value
        };

        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            toggleModal('modal-nueva-opcion', false);
            cargarMenu();
            Swal.fire({
                icon: 'success',
                title: '¡Platillo Publicado!',
                text: 'Ya está disponible para los invitados.',
                confirmButtonColor: '#0d9488',
                customClass: { popup: 'rounded-3xl' }
            });
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'No se pudo publicar el platillo.', 'error');
        });
    }

    // --- 4. ELIMINAR PLATILLO (DELETE) ---
    function eliminarPlatillo(id) {
        Swal.fire({
            title: '¿Quitar este platillo?',
            text: "Desaparecerá de las opciones del menú.",
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
                    cargarMenu();
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: { popup: 'rounded-3xl' }
                    });
                })
                .catch(err => console.error("Error al borrar platillo:", err));
            }
        });
    }
</script>
@endsection