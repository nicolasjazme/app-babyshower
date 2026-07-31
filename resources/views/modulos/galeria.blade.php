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
                <span class="text-2xl">📸</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Muro de Recuerdos (Galería)</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Monitorea las fotos subidas por tus invitados durante la celebración, revisa la interacción por "Likes" y modera el contenido del feed.
            </p>
        </div>
        
        {{-- Botón para probar/subir fotos manualmente --}}
        <button onclick="toggleModal('modal-nueva-foto', true)" class="inline-flex items-center gap-2 bg-pink-500 hover:bg-pink-600 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-cloud-arrow-up"></i> Subir Enlace (URL)
        </button>
    </div>

    {{-- Grid de Galería --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">🖼️ Imágenes Publicadas en la Fiesta</h2>
            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold font-mono" id="contador-fotos">0 fotos</span>
        </div>

        {{-- Contenedor donde JS inyectará las tarjetas --}}
        <div id="grid-galeria" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div class="col-span-full py-16 text-center text-slate-400">
                <i class="fa-solid fa-spinner fa-spin text-2xl mb-2"></i>
                <p>Cargando galería...</p>
            </div>
        </div>
    </div>
</div>

{{-- Modal para Subir Foto por URL (Simulador / Admin) --}}
<div id="modal-nueva-foto" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-nueva-foto', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">📸 Agregar al Muro</h3>
        <p class="text-xs text-slate-500 mb-6">Pega el link de una imagen (JPG, PNG) para que aparezca en el feed de todos los invitados.</p>

        <form id="form-foto" onsubmit="guardarFoto(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">URL de la Imagen *</label>
                <input type="url" id="input-url" required placeholder="https://ejemplo.com/mifoto.jpg"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-pink-200 transition-all text-sm font-medium">
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pie de Foto (Opcional)</label>
                <input type="text" id="input-pie" placeholder="¡Qué gran momento!"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-pink-200 transition-all text-sm font-medium">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-nueva-foto', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-pink-500 hover:bg-pink-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Publicar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const EVENTO_ID = '{{ $eventoId }}';
    const API_URL = `/api/eventos/${EVENTO_ID}/galeria`;

    document.addEventListener('DOMContentLoaded', cargarDatos);

    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) modal.classList.remove('hidden');
        else {
            modal.classList.add('hidden');
            document.getElementById('form-foto').reset();
        }
    }

    // --- CARGAR GALERÍA (GET) ---
    function cargarDatos() {
        if(!EVENTO_ID) return;

        fetch(API_URL)
            .then(res => res.json())
            .then(data => renderizarGaleria(data))
            .catch(err => {
                console.error("Error cargando galería:", err);
                document.getElementById('grid-galeria').innerHTML = `<p class="col-span-full text-center py-8 text-rose-500 font-medium">Error al conectar con la galería.</p>`;
            });
    }

    // --- DIBUJAR TARJETAS ---
    function renderizarGaleria(fotos) {
        const grid = document.getElementById('grid-galeria');
        const contador = document.getElementById('contador-fotos');
        
        // Asumiendo que tu back manda un array directo, o dentro de 'fotos'
        const arrayFotos = fotos.fotos || fotos || [];
        contador.innerText = `${arrayFotos.length} fotos`;

        if (arrayFotos.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-16">
                    <div class="w-16 h-16 bg-pink-50 text-pink-500 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-images"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-700">El muro de recuerdos está vacío</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Cuando tus invitados suban sus capturas, verás el catálogo social ordenado aquí.</p>
                </div>
            `;
            return;
        }

        // Ordenamos las más recientes primero (opcional, si el back no lo hace)
        const fotosOrdenadas = arrayFotos.reverse();

        grid.innerHTML = fotosOrdenadas.map(foto => `
            <div class="bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden flex flex-col justify-between group hover:shadow-sm transition-all">
                
                <div class="w-full h-48 bg-slate-200 relative overflow-hidden flex items-center justify-center text-slate-400">
                    <img src="${foto.url_imagen || foto.url}" alt="Recuerdo" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>

                <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 truncate">
                                👤 ${foto.nombre_invitado || 'Anfitrión'}
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold shrink-0">
                                ${foto.fecha || 'Reciente'}
                            </span>
                        </div>
                        ${foto.pie_foto ? `<p class="text-xs text-slate-600 mt-2 italic line-clamp-2">"${foto.pie_foto}"</p>` : ''}
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-1 text-pink-600 font-bold text-xs">
                            <i class="fa-solid fa-heart"></i>
                            <span>${foto.likes_count || foto.likes || 0} Likes</span>
                        </div>

                        <button onclick="eliminarFoto('${foto._id || foto.id}')" class="p-1.5 bg-white text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors border border-slate-200" title="Eliminar del feed">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // --- SUBIR FOTO (POST) ---
    function guardarFoto(e) {
        e.preventDefault();
        
        const payload = {
            url_imagen: document.getElementById('input-url').value, // El back puede esperar url o url_imagen
            url: document.getElementById('input-url').value,
            pie_foto: document.getElementById('input-pie').value,
            nombre_invitado: 'Anfitrión', // Como lo sube el admin
            fecha: new Date().toLocaleDateString()
        };

        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            toggleModal('modal-nueva-foto', false);
            cargarDatos();
            Swal.fire({ icon: 'success', title: '¡Subida!', showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-3xl' } });
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'No se pudo publicar.', 'error');
        });
    }

    // --- BORRAR FOTO (DELETE) ---
    function eliminarFoto(id) {
        Swal.fire({
            title: '¿Eliminar foto?',
            text: "Desaparecerá del muro de todos.",
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
                    cargarDatos();
                    Swal.fire({ icon: 'success', title: 'Eliminada', showConfirmButton: false, timer: 1500, customClass: { popup: 'rounded-3xl' } });
                })
                .catch(err => console.error(err));
            }
        });
    }
</script>
@endsection