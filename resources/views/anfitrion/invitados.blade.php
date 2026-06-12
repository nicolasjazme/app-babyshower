@extends('layouts.app')

@section('contenido')


 
            
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 bg-white/80 backdrop-blur-md p-6 rounded-3xl shadow-sm border border-stone-200">
                <div>
                    <h1 class="text-3xl font-extrabold text-stone-800 tracking-tight">👥 Gestión de Invitados</h1>
                    <p class="text-stone-600 mt-1 text-sm">Administra la asistencia y el control de tus seres queridos.</p>
                </div>
                
                @if(isset($evento))
                    <button onclick="copiarEnlace('{{ url('/e/'.$evento['slug']) }}')" class="bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 px-5 py-3 rounded-2xl text-xs font-bold transition-all shadow-sm flex items-center gap-2 cursor-pointer">
                        🔗 Copiar Link de Invitación
                    </button>
                @endif
            </header>

            @if(session('success'))
                <div class="bg-[#D4EFDF] border-l-4 border-[#186A3B] text-[#186A3B] p-4 rounded-2xl mb-6 font-bold text-sm shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-[#FADBD8] border-l-4 border-[#7B241C] text-[#7B241C] p-4 rounded-2xl mb-6 font-bold text-sm shadow-sm">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            <h3 class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-3">📈 Control de Confirmaciones</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-[#D4EFDF] p-6 rounded-3xl shadow-sm border border-[#A9DFBF] flex items-center">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl mr-4">✅</div>
                    <div>
                        <p class="text-xs text-[#186A3B] font-bold uppercase tracking-wider">Confirmados</p>
                        <p class="text-2xl font-black text-stone-800 mt-0.5">{{ $metricas['confirmados'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="bg-[#FADBD8] p-6 rounded-3xl shadow-sm border border-[#F5B7B1] flex items-center">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl mr-4">❌</div>
                    <div>
                        <p class="text-xs text-[#7B241C] font-bold uppercase tracking-wider">No asistirán</p>
                        <p class="text-2xl font-black text-stone-800 mt-0.5">{{ $metricas['rechazados'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="bg-[#FCF3CF] p-6 rounded-3xl shadow-sm border border-[#F9E79F] flex items-center">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl mr-4">⏳</div>
                    <div>
                        <p class="text-xs text-[#7D6608] font-bold uppercase tracking-wider">En Espera / Pendientes</p>
                        <p class="text-2xl font-black text-stone-800 mt-0.5">{{ $metricas['pendientes'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-[#E8EAF6] p-6 rounded-3xl shadow-sm mb-8 border border-[#C5CAE9] flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h4 class="text-xs font-bold text-[#3949AB] uppercase tracking-wider">📢 Recordatorios de Asistencia</h4>
                    <p class="text-[11px] text-stone-600 mt-0.5">Despacha de forma automatizada un correo vía Nodemailer a todos los invitados que aún no han respondido la invitación.</p>
                </div>
                <form action="{{ route('hosts.invitados.remind') }}" method="POST" class="m-0 select-none">
                    @csrf
                    <button type="submit" 
                            @if(($metricas['pendientes'] ?? 0) === 0) disabled class="bg-white/50 text-[#9FA8DA] px-5 py-3 rounded-2xl font-bold text-xs shadow-sm cursor-not-allowed transition-all border border-[#C5CAE9]" 
                            @else class="bg-[#5C6BC0] hover:bg-[#3949AB] text-white px-5 py-3 rounded-2xl font-bold transition-all text-xs shadow-sm cursor-pointer" @endif>
                        📧 Recordar a Pendientes
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="space-y-6">
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-3xl shadow-sm border border-stone-200">
                        <h2 class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-4 flex items-center gap-1">➕ Registro Individual</h2>
                        <form action="{{ route('hosts.guests.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold text-stone-500 uppercase mb-1">Nombre Completo</label>
                                <input type="text" name="nombre" required placeholder="Ej: Juan Pérez" class="w-full border border-stone-200 p-3 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-stone-500 uppercase mb-1">Correo Electrónico</label>
                                <input type="email" name="correo" required placeholder="juan@correo.com" class="w-full border border-stone-200 p-3 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60">
                            </div>
                            <button type="submit" class="w-full bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 py-3 rounded-2xl font-bold text-xs shadow-sm transition-all cursor-pointer">
                                Registrar Invitado
                            </button>
                        </form>
                    </div>

                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-3xl shadow-sm border border-stone-200">
                        <h2 class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-2 flex items-center gap-1">📥 Carga Masiva</h2>
                        <p class="text-[11px] text-stone-500 mb-4">Sube un archivo <strong class="text-stone-700">.CSV</strong>. La primera columna debe ser el Nombre y la segunda el Correo.</p>
                        
                        <form action="{{ route('hosts.guests.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            <div class="relative flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-stone-300 border-dashed rounded-3xl cursor-pointer bg-white/50 hover:bg-[#F8E1C6]/30 transition-all">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-2">
                                        <span class="text-2xl mb-1">📄</span>
                                        <p class="text-xs font-bold text-stone-600">Seleccionar archivo CSV</p>
                                        <p class="text-[10px] text-stone-400 mt-0.5">Formatos admitidos: .csv o .txt</p>
                                    </div>
                                    <input type="file" name="archivo_csv" accept=".csv,.txt" required class="hidden" onchange="actualizarNombreArchivo(this)" />
                                </label>
                            </div>
                            <p id="nombre-archivo-subido" class="text-[11px] text-[#3949AB] font-medium text-center hidden"></p>

                            <button type="submit" class="w-full bg-stone-700 hover:bg-stone-900 text-white py-3 rounded-2xl font-bold text-xs shadow-sm transition-all cursor-pointer">
                                ⚡ Importar Lista Completa
                            </button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2 h-fit">
                    
                    @if(isset($datosLista['listaBloqueada']) && $datosLista['listaBloqueada'] == true)
                        <div class="bg-white/90 backdrop-blur-md border border-stone-200 rounded-3xl p-12 text-center shadow-sm flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-[#FCF3CF] text-[#7D6608] rounded-3xl flex items-center justify-center text-3xl mb-4 border border-[#F9E79F] shadow-inner select-none">🔒</div>
                            <h3 class="text-base font-bold text-stone-800">Listado De Invitados Protegida</h3>
                            <p class="text-xs text-stone-500 max-w-sm mx-auto mt-2 leading-relaxed">
                                Para mantener a salvo la confidencialidad y el factor sorpresa de los regalos, los nombres de la lista de asistentes se revelarán automáticamente cuando falten **2 días o menos** para el evento.
                            </p>
                            <div class="mt-4 bg-stone-100 text-stone-600 font-extrabold px-4 py-2 rounded-2xl text-[10px] uppercase tracking-wider border border-stone-200">
                                ⏳ Tiempo restante estimado: {{ $datosLista['diasRestantes'] ?? 'X' }} días para liberar
                            </div>

                            <a href="?revelar=true" class="mt-6 inline-flex items-center gap-2 bg-stone-800 hover:bg-black text-white text-xs font-bold px-5 py-3 rounded-2xl shadow-md transition-all cursor-pointer">
                                👁️‍🗨️ Revelar Listado de Invitados
                            </a>
                        </div>
                    @else
                        <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-sm overflow-hidden border border-stone-200">
                            
                            <div class="bg-stone-100/50 p-4 border-b border-stone-200 flex justify-between items-center px-4">
                                <span class="text-stone-700 font-bold text-xs uppercase tracking-wider">👥 Lista de Invitados Activa</span>
                                @if(isset($datosLista['reveladoManualmente']) && $datosLista['reveladoManualmente'] == true)
                                    <a href="?revelar=false" class="bg-[#FCF3CF] text-[#7D6608] border border-[#F9E79F] px-3 py-1.5 rounded-xl font-bold text-[10px] uppercase hover:bg-[#F9E79F] transition-all">
                                        🔒 Volver a Ocultar
                                    </a>
                                @endif
                            </div>

                            <table class="w-full text-left border-collapse">
                                <thead class="bg-stone-50 border-b border-stone-200">
                                    <tr>
                                        <th class="p-4 font-bold text-stone-600 uppercase text-xs tracking-wider">Invitado</th>
                                        <th class="p-4 font-bold text-stone-600 uppercase text-xs tracking-wider">Confirmación</th>
                                        <th class="p-4 font-bold text-stone-600 uppercase text-xs tracking-wider text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-100 text-xs">
                                    @forelse($datosLista['invitados'] ?? [] as $guest)
                                    <tr class="hover:bg-stone-50 transition-colors">
                                        <td class="p-4">
                                            <p class="font-bold text-stone-800 text-sm flex items-center gap-1">
                                                {{ $guest['nombre'] }}
                                                @if($guest['recordatorioEnviado'] ?? false)
                                                    <span class="text-[9px] bg-[#E8EAF6] text-[#3949AB] px-2 py-0.5 rounded-lg font-black uppercase tracking-wider border border-[#C5CAE9]">📨 Recordado</span>
                                                @endif
                                            </p>
                                            <p class="text-[11px] text-stone-400 mt-0.5 font-mono">{{ $guest['correo'] ?: 'Sin correo registrado' }}</p>
                                        </td>
                                        <td class="p-4 align-middle">
                                            <form action="{{ route('hosts.guests.update', $guest['_id'] ?? '') }}" method="POST" class="inline-flex items-center gap-1">
                                                @csrf 
                                                @method('PUT')
                                                <select name="estadoAsistencia" onchange="this.form.submit()" class="bg-white border border-stone-200 px-3 py-2 rounded-xl text-xs font-bold text-stone-700 outline-none cursor-pointer focus:ring-2 focus:ring-[#F8E1C6]">
                                                    <option value="pendiente" {{ ($guest['estadoAsistencia'] ?? '') === 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                                    <option value="confirmado" {{ ($guest['estadoAsistencia'] ?? '') === 'confirmado' ? 'selected' : '' }}>✅ Confirmado</option>
                                                    <option value="rechazado" {{ ($guest['estadoAsistencia'] ?? '') === 'rechazado' ? 'selected' : '' }}>❌ Rechazado</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="p-4 align-middle text-right space-x-1 whitespace-nowrap">
                                            <button onclick="abrirModalEditar('{{ $guest['_id'] ?? '' }}', '{{ $guest['nombre'] ?? '' }}', '{{ $guest['correo'] ?? '' }}')" class="text-stone-500 hover:bg-stone-100 border border-transparent hover:border-stone-200 px-3 py-2 rounded-xl transition-all font-bold cursor-pointer">
                                                ✏️ Editar
                                            </button>
                                            <form action="{{ route('hosts.guests.destroy', $guest['_id'] ?? '') }}" method="POST" onsubmit="return confirm('¿Remover a este invitado de la lista?')" class="inline-block m-0">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 hover:bg-red-50 px-3 py-2 rounded-xl transition-all font-bold cursor-pointer">
                                                    🗑️ Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="p-12 text-center text-stone-400 font-medium">Tu lista de invitados está vacía. Registra uno manualmente o importa un archivo CSV.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </main>

    <div id="modalEditar" class="hidden fixed inset-0 bg-stone-900/40 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-[#FDFBF7] p-8 rounded-3xl shadow-xl border border-stone-200 w-full max-w-md mx-4">
            <h3 class="text-base font-extrabold text-stone-800 mb-6 flex items-center gap-2">✏️ Modificar Datos del Invitado</h3>
            
            <form id="formEditarInvitado" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-stone-500 uppercase mb-1">Nombre Completo</label>
                    <input type="text" id="edit_nombre" name="nombre" required class="w-full border border-stone-200 p-3 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-500 uppercase mb-1">Correo Electrónico</label>
                    <input type="email" id="edit_correo" name="correo" required class="w-full border border-stone-200 p-3 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white">
                </div>
                <div class="flex justify-end gap-3 pt-4 text-xs font-bold">
                    <button type="button" onclick="cerrarModalEditar()" class="bg-stone-200 hover:bg-stone-300 text-stone-700 px-5 py-2.5 rounded-2xl transition-all cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 px-5 py-2.5 rounded-2xl transition-all shadow-sm cursor-pointer">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function copiarEnlace(url) {
            navigator.clipboard.writeText(url).then(() => {
                alert('¡Enlace de invitación copiado al portapapeles! 🔗');
            }).catch(err => {
                console.error('Error al copiar: ', err);
            });
        }

        function actualizarNombreArchivo(input) {
            const label = document.getElementById('nombre-archivo-subido');
            if(input.files && input.files[0]) {
                label.innerText = `📂 Archivo: ${input.files[0].name}`;
                label.classList.remove('hidden');
            }
        }

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
    </script>

 <style>
    /* Evita de forma definitiva que aparezca la barra horizontal */
    body {
        overflow-x: hidden !important;
    }

    /* Animación fluida para que los globos suban */
    .globo-animado {
        animation: subirGlobos linear forwards;
        pointer-events: none; /* Evita que el usuario haga clic en los globos por accidente */
    }

    /* El motor de la animación: Desde abajo de la pantalla hasta arriba */
    @keyframes subirGlobos {
        0% {
            transform: translateY(100vh) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(-110vh) rotate(20deg);
            opacity: 0;
        }
    }
</style>

<script>
    // Función para animar los globos
    function lanzarGlobos() {
        for (let i = 0; i < 15; i++) { 
            const globo = document.createElement('div');
            globo.classList.add('globo-animado');
            globo.innerHTML = '🎈';
            
            // 🎯 Forzamos que se queden fijos en una capa superior sin romper el diseño
            globo.style.position = 'fixed';
            globo.style.bottom = '-50px';
            globo.style.zIndex = '9999';
            globo.style.fontSize = (24 + Math.random() * 20) + 'px'; // Tamaños variados muy lindos
            
            // 🛑 Restringimos el ancho para que no toquen el borde derecho (máximo 90vw)
            globo.style.left = (5 + Math.random() * 85) + 'vw';
            globo.style.animationDuration = (3 + Math.random() * 3) + 's';
            
            document.body.appendChild(globo);
            setTimeout(() => globo.remove(), 6000);
        }
    }

    // Lógica de Alertas (Ejecutadas al cargar la página)
    @if(session('success'))
        @if(session('show_balloons'))
            // Solo lanzamos globos si viene la bandera marcada desde el controlador
            lanzarGlobos();
            Swal.fire({
                title: '¡Hurra! 🎈',
                text: "{{ session('success') }}",
                iconHtml: '🎈',
                confirmButtonText: '¡Genial!',
                confirmButtonColor: '#F8E1C6',
                customClass: { icon: 'no-border' }
            });
        @else
            // Si es un éxito común (eliminar/recordar), solo mostramos la alerta sin globos
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#F8E1C6'
            });
        @endif
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Hubo un problema',
            text: "{{ session('error') }}",
            confirmButtonColor: '#e11d48'
        });
    @endif
</script>
@endsection