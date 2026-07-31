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
                <span class="text-2xl">🎁</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Lista de Deseos (Regalos)</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Agrega los productos que te gustaría recibir y tus invitados podrán reservarlos directamente desde la invitación.
            </p>
        </div>
        
        <button onclick="toggleModal('modal-regalo', true)" class="inline-flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-plus"></i> Añadir Regalo
        </button>
    </div>

    {{-- Contenedor del Listado --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center justify-between">
            <span class="flex items-center gap-2">🛒 Tus Regalos Añadidos</span>
            <span id="badge-total-regalos" class="text-xs bg-rose-100 text-rose-800 px-2.5 py-1 rounded-full font-bold font-mono">0 ítems</span>
        </h2>

        {{-- JS Inyectará las tarjetas de regalos aquí --}}
        <div id="contenedor-regalos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="col-span-full text-center py-16">
                <i class="fa-solid fa-spinner fa-spin text-rose-500 text-2xl mb-2"></i>
                <p class="text-xs text-slate-400 font-medium">Cargando lista de regalos...</p>
            </div>
        </div>
    </div>
</div>

{{-- Modal Añadir Regalo --}}
<div id="modal-regalo" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-regalo', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">🎁 Añadir Producto a tu Lista</h3>
        <p class="text-xs text-slate-500 mb-6">Completa la información para que tus invitados sepan exactamente qué necesitas.</p>

        <form id="form-regalo" onsubmit="guardarRegalo(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nombre del Regalo *</label>
                <input type="text" id="input-nombre" required placeholder="Ej. Silla de comer para bebé, Cafetera, etc."
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-rose-200 transition-all text-sm font-medium">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Descripción o Especificaciones</label>
                <textarea id="input-descripcion" rows="2" placeholder="Ej. Color gris, marca específica, tamaño o talla..."
                          class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-rose-200 transition-all text-sm font-medium"></textarea>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Enlace Externo de la Tienda (Opcional)</label>
                <input type="url" id="input-link" placeholder="https://tienda.com/producto-especifico"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-rose-200 transition-all text-sm font-medium">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-regalo', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-rose-500 hover:bg-rose-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Guardar Regalo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const EVENTO_ID = '{{ $eventoId }}';
    const API_URL = `/api/eventos/${EVENTO_ID}/regalos`;

    document.addEventListener('DOMContentLoaded', cargarRegalos);

    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) modal.classList.remove('hidden');
        else {
            modal.classList.add('hidden');
            document.getElementById('form-regalo').reset();
        }
    }

    // --- LEER REGALOS (GET) ---
    function cargarRegalos() {
        if(!EVENTO_ID) return;

        fetch(API_URL)
            .then(res => res.json())
            .then(data => renderizarRegalos(data))
            .catch(err => {
                console.error("Error al cargar regalos:", err);
                document.getElementById('contenedor-regalos').innerHTML = `
                    <div class="col-span-full text-center py-8 text-rose-500 font-medium text-xs">
                        Error al conectar con el servidor para recuperar la lista de deseos.
                    </div>
                `;
            });
    }

    // --- DIBUJAR TARJETAS DE REGALOS ---
    function renderizarRegalos(response) {
        const contenedor = document.getElementById('contenedor-regalos');
        const badge = document.getElementById('badge-total-regalos');
        
        const listaRegalos = response.regalos || response || [];
        badge.innerText = `${listaRegalos.length} ítems`;

        if (!listaRegalos || listaRegalos.length === 0) {
            contenedor.innerHTML = `
                <div class="col-span-full text-center py-16">
                    <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">No hay regalos en tu lista</h3>
                    <p class="text-slate-400 text-xs mt-1 max-w-sm mx-auto">Comienza agregando ideas o enlaces externos de tiendas para orientar a tus invitados.</p>
                </div>
            `;
            return;
        }

        contenedor.innerHTML = listaRegalos.map(regalo => {
            const id = regalo._id || regalo.id;
            const estado = regalo.estado || (regalo.reservado ? 'reservado' : 'disponible');
            const esReservado = estado === 'reservado' || regalo.reservado === true;
            const invitado = regalo.nombre_invitado || regalo.reservado_por || 'Invitado';
            const link = regalo.link_externo || regalo.url || '';

            const badgeEstado = esReservado
                ? `<span class="text-[10px] font-bold px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full uppercase">🔒 Reservado por ${invitado}</span>`
                : `<span class="text-[10px] font-bold px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full uppercase">✨ Disponible</span>`;

            return `
                <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50 flex flex-col justify-between hover:border-rose-200 transition-all">
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            ${badgeEstado}
                        </div>

                        <h3 class="font-bold text-slate-800 text-base line-clamp-1">${regalo.nombre || regalo.nombre_articulo}</h3>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">${regalo.descripcion || 'Sin descripción adicional.'}</p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                        ${link ? `
                            <a href="${link}" target="_blank" class="text-xs font-bold text-rose-500 hover:text-rose-600 flex items-center gap-1.5 truncate">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver Tienda
                            </a>
                        ` : `
                            <span class="text-xs text-slate-400 italic">Sin enlace</span>
                        `}

                        <div class="flex gap-1 shrink-0">
                            <button onclick="eliminarRegalo('${id}')" class="p-2 text-slate-400 hover:text-red-500 transition-colors cursor-pointer" title="Eliminar regalo">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // --- GUARDAR REGALO (POST) ---
    function guardarRegalo(e) {
        e.preventDefault();

        const payload = {
            nombre: document.getElementById('input-nombre').value,
            nombre_articulo: document.getElementById('input-nombre').value, // Compatibilidad con backends antiguos
            descripcion: document.getElementById('input-descripcion').value,
            link_externo: document.getElementById('input-link').value
        };

        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(() => {
            toggleModal('modal-regalo', false);
            cargarRegalos();
            Swal.fire({
                icon: 'success',
                title: '¡Regalo Añadido!',
                text: 'Ya está visible para tus invitados.',
                confirmButtonColor: '#f43f5e',
                customClass: { popup: 'rounded-3xl' }
            });
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'No se pudo guardar el regalo.', 'error');
        });
    }

    // --- ELIMINAR REGALO (DELETE) ---
    function eliminarRegalo(id) {
        Swal.fire({
            title: '¿Quitar este regalo?',
            text: "Desaparecerá permanentemente de tu lista.",
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
                    cargarRegalos();
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: { popup: 'rounded-3xl' }
                    });
                })
                .catch(err => console.error("Error al borrar regalo:", err));
            }
        });
    }
</script>
@endsection