@extends('layouts.app')

@section('contenido')
@php
    $invitados = $datosLista['invitados'] ?? [];
    $evento = $eventoActivo ?? Session::get('evento_activo') ?? [];
    $slugEvento = $evento['slug'] ?? '';
    $idEvento = $evento['_id'] ?? $evento['id'] ?? '';
@endphp

<div class="space-y-8">
    
    {{-- ENCABEZADO Y COPIAR LINK PÚBLICO --}}
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-200/80">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">👥 Gestión Nominal de Invitados</h1>
            <p class="text-slate-500 text-xs md:text-sm mt-1">Administra las confirmaciones RSVP y envía recordatorios masivos por correo.</p>
        </div>
        
        @if(!empty($slugEvento))
            <button onclick="copiarEnlace('{{ url('/e/'.$slugEvento) }}')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-2xl text-xs font-bold transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-link"></i> Copiar Link de Invitación
            </button>
        @endif
    </header>

    {{-- TARJETAS DE ESTADO RSVP --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-emerald-50/80 p-6 rounded-3xl shadow-sm border border-emerald-100 flex items-center">
            <div class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center text-xl mr-4 shadow-sm shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[10px] text-emerald-800 font-bold uppercase tracking-wider">Confirmados</p>
                <p id="cnt-confirmados" class="text-2xl font-black text-slate-900 mt-0.5">{{ $metricas['confirmados'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="bg-amber-50/80 p-6 rounded-3xl shadow-sm border border-amber-100 flex items-center">
            <div class="w-12 h-12 bg-amber-500 text-white rounded-2xl flex items-center justify-center text-xl mr-4 shadow-sm shrink-0">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-[10px] text-amber-800 font-bold uppercase tracking-wider">Pendientes</p>
                <p id="cnt-pendientes" class="text-2xl font-black text-slate-900 mt-0.5">{{ $metricas['pendientes'] ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-rose-50/80 p-6 rounded-3xl shadow-sm border border-rose-100 flex items-center">
            <div class="w-12 h-12 bg-rose-500 text-white rounded-2xl flex items-center justify-center text-xl mr-4 shadow-sm shrink-0">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
                <p class="text-[10px] text-rose-800 font-bold uppercase tracking-wider">No Asistirán</p>
                <p id="cnt-rechazados" class="text-2xl font-black text-slate-900 mt-0.5">{{ $metricas['rechazados'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- FRANJA RECORDATORIOS AUTOMATIZADOS --}}
    <div class="bg-indigo-900 text-white p-6 rounded-3xl shadow-md flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider flex items-center gap-2 text-indigo-200">
                <i class="fa-solid fa-paper-plane"></i> Recordatorios Masivos Automáticos
            </h4>
            <p class="text-xs text-indigo-100 mt-1">Despacha una notificación por correo electrónico a todos los invitados que aún no han confirmado su asistencia.</p>
        </div>
        <form action="{{ route('anfitrion.invitados.remind') }}" method="POST" class="m-0 shrink-0">
            @csrf
            <button type="submit" 
                    @if(($metricas['pendientes'] ?? 0) === 0) disabled class="bg-indigo-800 text-indigo-400 px-5 py-3 rounded-2xl font-bold text-xs shadow-sm cursor-not-allowed border border-indigo-700" 
                    @else class="bg-indigo-500 hover:bg-indigo-400 text-white px-5 py-3 rounded-2xl font-bold transition-all text-xs shadow-md cursor-pointer" @endif>
                <i class="fa-solid fa-envelope"></i> Enviar Recordatorio
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- COLUMNA IZQUIERDA: REGISTRO MANUAL E IMPORTACIÓN CSV --}}
        <div class="space-y-6">
            
            {{-- Formulario Registro Individual --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-indigo-500"></i> Registro Individual
                </h2>
                <form action="{{ route('anfitrion.guests.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nombre Completo *</label>
                        <input type="text" name="nombre" required placeholder="Ej: Juan Pérez" class="w-full border border-slate-200 p-3 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 text-xs bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Correo Electrónico *</label>
                        <input type="email" name="correo" required placeholder="juan@correo.com" class="w-full border border-slate-200 p-3 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 text-xs bg-slate-50">
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-2xl font-bold text-xs shadow-sm transition-all cursor-pointer uppercase tracking-wider">
                        Registrar Invitado
                    </button>
                </form>
            </div>

            {{-- Formulario Carga Masiva CSV --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-file-csv text-emerald-500"></i> Importación Masiva CSV
                </h2>
                <p class="text-[11px] text-slate-400 mb-4">Sube un archivo <strong class="text-slate-600">.CSV</strong> (Columna 1: Nombre, Columna 2: Correo).</p>
                
                <form action="{{ route('anfitrion.guests.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div class="relative flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-slate-200 border-dashed rounded-2xl cursor-pointer bg-slate-50/50 hover:bg-slate-100 transition-all">
                            <div class="flex flex-col items-center justify-center text-center px-2">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-1"></i>
                                <p class="text-xs font-bold text-slate-600">Seleccionar CSV</p>
                            </div>
                            <input type="file" name="archivo_csv" accept=".csv,.txt" required class="hidden" onchange="actualizarNombreArchivo(this)" />
                        </label>
                    </div>
                    <p id="nombre-archivo-subido" class="text-[11px] text-indigo-600 font-bold text-center hidden"></p>

                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white py-3 rounded-2xl font-bold text-xs shadow-sm transition-all cursor-pointer uppercase tracking-wider">
                        ⚡ Importar Lote Completo
                    </button>
                </form>
            </div>
        </div>

        {{-- COLUMNA DERECHA: TABLA CON BUSCADOR EN VIVO Y SOCKETS --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-200/80">
                
                {{-- BARRA BUSCADOR EN VIVO --}}
                <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <span class="text-slate-800 font-black text-xs uppercase tracking-wider">📋 Lista de Asistentes</span>
                    <div class="relative w-full sm:w-64">
                        <input type="text" id="input-buscador" onkeyup="filtrarInvitados()" placeholder="Buscar por nombre o correo..." 
                               class="w-full bg-white border border-slate-200 text-xs rounded-xl pl-9 pr-3 py-2 outline-none focus:ring-2 focus:ring-indigo-200 font-medium">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                </div>

                {{-- TABLA NOMINAL --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="tabla-invitados">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="p-4 font-bold text-slate-400 uppercase text-[10px] tracking-wider">Invitado</th>
                                <th class="p-4 font-bold text-slate-400 uppercase text-[10px] tracking-wider">Estado RSVP</th>
                                <th class="p-4 font-bold text-slate-400 uppercase text-[10px] tracking-wider text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs" id="tbody-invitados">
                            @forelse($invitados as $guest)
                                @php
                                    $idGuest = $guest['_id'] ?? $guest['id'] ?? '';
                                    $estadoG = $guest['estadoConfirmacion'] ?? 'pendiente';
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors fila-invitado">
                                    <td class="p-4">
                                        <p class="font-bold text-slate-900 text-sm campo-nombre">{{ $guest['nombre'] ?? '' }}</p>
                                        <p class="text-[11px] text-slate-400 font-mono campo-correo">{{ $guest['correo'] ?: 'Sin correo registrado' }}</p>
                                    </td>
                                    <td class="p-4 align-middle">
                                        <form action="{{ route('anfitrion.guests.update', $idGuest) }}" method="POST" class="inline-block">
                                            @csrf 
                                            @method('PUT')
                                            <select name="estadoAsistencia" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 outline-none cursor-pointer focus:ring-2 focus:ring-indigo-200">
                                                <option value="pendiente" {{ $estadoG === 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                                <option value="confirmado" {{ $estadoG === 'confirmado' ? 'selected' : '' }}>✅ Confirmado</option>
                                                <option value="rechazado" {{ $estadoG === 'rechazado' ? 'selected' : '' }}>❌ No Asistirá</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="p-4 align-middle text-right space-x-1 whitespace-nowrap">
                                        <button onclick="abrirModalEditar('{{ $idGuest }}', '{{ addslashes($guest['nombre'] ?? '') }}', '{{ addslashes($guest['correo'] ?? '') }}')" class="text-slate-500 hover:text-slate-800 hover:bg-slate-100 px-3 py-2 rounded-xl transition-all font-bold cursor-pointer">
                                            <i class="fa-solid fa-pen"></i> Editar
                                        </button>
                                        <form action="{{ route('anfitrion.guests.destroy', $idGuest) }}" method="POST" onsubmit="return confirm('¿Remover a este invitado de la lista?')" class="inline-block m-0">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-3 py-2 rounded-xl transition-all font-bold cursor-pointer">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr id="row-empty">
                                    <td colspan="3" class="p-12 text-center text-slate-400 font-medium italic">
                                        Tu lista de invitados está vacía. Registra uno manualmente o importa un archivo CSV.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- MODAL EDICIÓN INVITADO --}}
<div id="modalEditar" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 w-full max-w-md">
        <h3 class="text-base font-black text-slate-900 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-user-pen text-indigo-500"></i> Modificar Datos del Invitado
        </h3>
        
        <form id="formEditarInvitado" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nombre Completo</label>
                <input type="text" id="edit_nombre" name="nombre" required class="w-full border border-slate-200 p-3 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 text-xs bg-slate-50 font-medium">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Correo Electrónico</label>
                <input type="email" id="edit_correo" name="correo" required class="w-full border border-slate-200 p-3 rounded-2xl outline-none focus:ring-2 focus:ring-indigo-200 text-xs bg-slate-50 font-medium">
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="cerrarModalEditar()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-2xl transition-all text-xs font-bold cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-2xl transition-all text-xs font-bold shadow-sm cursor-pointer">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>

<script>
    // 1. COPIAR ENLACE AL PORTAPAPELES
    function copiarEnlace(url) {
        navigator.clipboard.writeText(url).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '¡Enlace de invitación copiado al portapapeles!',
                showConfirmButton: false,
                timer: 3000
            });
        }).catch(err => console.error('Error al copiar: ', err));
    }

    // 2. MOSTRAR NOMBRE DE ARCHIVO CSV SELECCIONADO
    function actualizarNombreArchivo(input) {
        const label = document.getElementById('nombre-archivo-subido');
        if(input.files && input.files[0]) {
            label.innerText = `📂 Archivo: ${input.files[0].name}`;
            label.classList.remove('hidden');
        }
    }

    // 3. MODAL EDITAR INVITADO
    function abrirModalEditar(id, nombre, correo) {
        const modal = document.getElementById('modalEditar');
        const form = document.getElementById('formEditarInvitado');
        form.action = `/anfitrion/invitados/${id}`;
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_correo').value = correo;
        modal.classList.remove('hidden');
    }

    function cerrarModalEditar() {
        document.getElementById('modalEditar').classList.add('hidden');
    }

    // 4. FILTRO DE BÚSQUEDA EN TIEMPO REAL
    function filtrarInvitados() {
        const busqueda = document.getElementById('input-buscador').value.toLowerCase();
        const filas = document.querySelectorAll('.fila-invitado');

        filas.forEach(fila => {
            const nombre = fila.querySelector('.campo-nombre').innerText.toLowerCase();
            const correo = fila.querySelector('.campo-correo').innerText.toLowerCase();
            if (nombre.includes(busqueda) || correo.includes(busqueda)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    }

    // 5. SINCRONIZACIÓN EN TIEMPO REAL VÍA SOCKET.IO
    const socket = io('http://localhost:3000');

    socket.on('nuevo-invitado', function(invitado) {
        // Alerta flotante Toast
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: `¡Asistencia interactiva de ${invitado.nombre}!`,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });

        // Ocultar mensaje vacío si existe
        const rowEmpty = document.getElementById('row-empty');
        if (rowEmpty) rowEmpty.remove();

        const tbody = document.getElementById('tbody-invitados');
        if (tbody) {
            let badgeEstado = '';
            if (invitado.estadoAsistencia === 'confirmado') {
                badgeEstado = '<span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-xl text-xs font-bold">✅ Confirmado</span>';
            } else if (invitado.estadoAsistencia === 'rechazado') {
                badgeEstado = '<span class="bg-rose-100 text-rose-800 px-3 py-1 rounded-xl text-xs font-bold">❌ No Asistirá</span>';
            } else {
                badgeEstado = '<span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-xl text-xs font-bold">⏳ Pendiente</span>';
            }

            const nuevaFila = `
                <tr class="hover:bg-slate-50 transition-colors fila-invitado bg-indigo-50/40 animate-pulse">
                    <td class="p-4">
                        <p class="font-bold text-slate-900 text-sm campo-nombre">${invitado.nombre}</p>
                        <p class="text-[11px] text-slate-400 font-mono campo-correo">${invitado.correo || 'Sin correo registrado'}</p>
                    </td>
                    <td class="p-4 align-middle">${badgeEstado}</td>
                    <td class="p-4 align-middle text-right text-slate-400 font-bold text-xs italic">
                        Actualizado en vivo
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('afterbegin', nuevaFila);
        }
    });

    // 6. ALERTAS DE SESIÓN LARAVEL
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Operación Exitosa!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#4f46e5'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Atención',
            text: "{{ session('error') }}",
            confirmButtonColor: '#e11d48'
        });
    @endif
</script>
@endsection