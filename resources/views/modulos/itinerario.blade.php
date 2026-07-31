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
                <span class="text-2xl">⏳</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Itinerario / Cronograma</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Planifica los momentos clave de tu celebración y muéstrales a tus invitados una línea de tiempo elegante con actividades paso a paso.
            </p>
        </div>
        
        <button onclick="toggleModal('modal-nuevo-hito', true)" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-plus"></i> Agregar Actividad
        </button>
    </div>

    {{-- Contenedor de la Línea de Tiempo --}}
    <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 mb-8 flex items-center gap-2">🕒 Línea de Tiempo del Evento</h2>

        {{-- JS Inyectará la línea de tiempo aquí --}}
        <div id="contenedor-itinerario">
            <div class="text-center py-16">
                <i class="fa-solid fa-spinner fa-spin text-amber-500 text-2xl mb-2"></i>
                <p class="text-xs text-slate-400 font-medium">Cargando itinerario...</p>
            </div>
        </div>
    </div>
</div>

{{-- Modal para Agregar Actividad --}}
<div id="modal-nuevo-hito" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-nuevo-hito', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">⏳ Planificar Actividad</h3>
        <p class="text-xs text-slate-500 mb-6">Agrega un momento programado a la agenda oficial del evento.</p>

        <form id="form-hito" onsubmit="guardarActividad(event)" class="space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Título del Bloque *</label>
                    <input type="text" id="input-titulo" required placeholder="Ej. Recepción, Cóctel, Vals"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-amber-200 transition-all text-sm font-medium">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Hora *</label>
                    <input type="time" id="input-hora" required
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-amber-200 transition-all text-sm font-semibold">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Ícono Representativo *</label>
                <select id="input-icono" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-amber-200 transition-all text-sm font-semibold bg-white cursor-pointer text-slate-700">
                    <option value="fa-solid fa-door-open">🚪 Entrada / Apertura</option>
                    <option value="fa-solid fa-champagne-glasses">🥂 Brindis / Cóctel</option>
                    <option value="fa-solid fa-utensils">🍽️ Almuerzo / Cena</option>
                    <option value="fa-solid fa-cake-candles">🎂 Pastel / Celebración</option>
                    <option value="fa-solid fa-music">🎵 Baile / Fiesta</option>
                    <option value="fa-solid fa-camera">📸 Sesión de Fotos</option>
                    <option value="fa-solid fa-gift">🎁 Apertura de Regalos</option>
                    <option value="fa-solid fa-star" selected>⭐ Hito Especial / Sorpresa</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Breve Descripción (Opcional)</label>
                <textarea id="input-descripcion" rows="2" placeholder="Ej. Palabras de bienvenida y ubicación en las mesas correspondientes..."
                          class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-amber-200 transition-all text-sm font-medium"></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-nuevo-hito', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Guardar Actividad
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const EVENTO_ID = '{{ $eventoId }}';
    const API_URL = `/api/eventos/${EVENTO_ID}/itinerario`;

    document.addEventListener('DOMContentLoaded', cargarItinerario);

    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) modal.classList.remove('hidden');
        else {
            modal.classList.add('hidden');
            document.getElementById('form-hito').reset();
        }
    }

    // --- LEER ITINERARIO (GET) ---
    function cargarItinerario() {
        if(!EVENTO_ID) return;

        fetch(API_URL)
            .then(res => res.json())
            .then(data => renderizarItinerario(data))
            .catch(err => {
                console.error("Error cargando itinerario:", err);
                document.getElementById('contenedor-itinerario').innerHTML = `
                    <div class="text-center py-8 text-rose-500 text-xs font-bold">
                        No se pudo conectar con el servidor para obtener la línea de tiempo.
                    </div>
                `;
            });
    }

    // --- DIBUJAR LÍNEA DE TIEMPO ---
    function renderizarItinerario(data) {
        const contenedor = document.getElementById('contenedor-itinerario');
        const listaHitos = data.itinerario || data || [];

        if (!listaHitos || listaHitos.length === 0) {
            contenedor.innerHTML = `
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700">El cronograma está vacío</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Define actividades como el recibimiento de invitados, banquetes o sorpresas para estructurar tu fiesta.</p>
                </div>
            `;
            return;
        }

        // Ordenar cronológicamente por hora
        const hitosOrdenados = listaHitos.sort((a, b) => (a.hora > b.hora) ? 1 : -1);

        contenedor.innerHTML = `
            <div class="relative border-l-2 border-slate-100 ml-4 md:ml-32 space-y-8 pb-4">
                ${hitosOrdenados.map(hito => {
                    const id = hito._id || hito.id;
                    const horaFormateada = hito.hora ? hito.hora.substring(0, 5) : '00:00';
                    const icono = hito.icono || 'fa-solid fa-star';

                    return `
                        <div class="relative pl-8 group">
                            
                            {{-- Hora en Pantallas Grandes --}}
                            <div class="hidden md:block absolute -left-36 top-1 w-28 text-right">
                                <span class="font-mono font-black text-slate-800 text-base">
                                    ${horaFormateada}
                                </span>
                                <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider">HRS</span>
                            </div>

                            {{-- Círculo / Ícono en la Línea --}}
                            <div class="absolute -left-[21px] top-0 w-10 h-10 rounded-full bg-white border-2 border-amber-400 text-amber-500 flex items-center justify-center text-sm shadow-sm group-hover:scale-110 transition-transform">
                                <i class="${icono} w-4 text-center"></i>
                            </div>

                            {{-- Tarjeta del Bloque --}}
                            <div class="bg-slate-50/70 rounded-2xl p-5 border border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 hover:border-amber-200 transition-all">
                                <div>
                                    {{-- Hora en Móviles --}}
                                    <div class="md:hidden flex items-center gap-1 text-amber-600 font-mono font-bold text-xs mb-1">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>${horaFormateada} hrs</span>
                                    </div>
                                    
                                    <h3 class="font-bold text-slate-800 text-base">${hito.titulo || 'Sin título'}</h3>
                                    ${hito.descripcion ? `<p class="text-xs text-slate-500 mt-1 leading-relaxed max-w-xl">${hito.descripcion}</p>` : ''}
                                </div>

                                <div class="flex shrink-0 sm:self-center">
                                    <button onclick="eliminarActividad('${id}')" class="p-2 text-slate-400 hover:text-rose-500 transition-colors" title="Eliminar actividad">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }

    // --- GUARDAR ACTIVIDAD (POST) ---
    function guardarActividad(e) {
        e.preventDefault();

        const payload = {
            titulo: document.getElementById('input-titulo').value,
            hora: document.getElementById('input-hora').value,
            icono: document.getElementById('input-icono').value,
            descripcion: document.getElementById('input-descripcion').value
        };

        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            toggleModal('modal-nuevo-hito', false);
            cargarItinerario();
            Swal.fire({
                icon: 'success',
                title: '¡Actividad Programada!',
                text: 'Se ha agregado al cronograma del evento.',
                confirmButtonColor: '#f59e0b',
                customClass: { popup: 'rounded-3xl' }
            });
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'No se pudo guardar la actividad.', 'error');
        });
    }

    // --- ELIMINAR ACTIVIDAD (DELETE) ---
    function eliminarActividad(id) {
        Swal.fire({
            title: '¿Quitar actividad?',
            text: "Se eliminará de la línea de tiempo.",
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
                    cargarItinerario();
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminada',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: { popup: 'rounded-3xl' }
                    });
                })
                .catch(err => console.error("Error al borrar hito:", err));
            }
        });
    }
</script>
@endsection