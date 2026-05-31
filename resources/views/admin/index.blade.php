<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Panel Admin - Métricas de Control</title>
</head>
<body class="min-h-screen flex font-sans text-stone-800" style="background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); background-size: cover; background-attachment: fixed; background-position: center;">

    <aside class="w-64 bg-[#F8E1C6]/90 backdrop-blur-md text-stone-800 flex flex-col shadow-lg shrink-0 border-r border-stone-200">
        <div class="h-16 flex items-center justify-center border-b border-stone-200/50 bg-[#F8E1C6]/80">
            <span class="text-xl font-bold tracking-wider text-stone-800">Admin Plataforma 🖥️</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/admin" class="block px-4 py-2 bg-[#EAD8C1] rounded-xl font-bold text-stone-900 transition-colors flex items-center gap-2">
                📊 Métricas Globales
            </a>
            <a href="/admin/baby-showers" class="block px-4 py-2 hover:bg-stone-200/50 rounded-xl transition-colors font-medium flex items-center gap-2 text-stone-700">
                🍼 Ver Baby Showers
            </a>
            <div class="border-t border-stone-300/50 my-4"></div>
            <a href="/baby-shower" class="block px-4 py-2 hover:bg-stone-200/50 rounded-xl transition-colors font-medium text-xs flex items-center gap-1.5 text-stone-600">
                🏠 Ir a Pantalla Principal
            </a>
        </nav>
        <div class="p-4 border-t border-stone-200/50">
            <form action="/logout" method="POST" class="m-0">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-stone-600 hover:text-stone-900 font-bold transition-colors cursor-pointer">
                    🚪 Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-8 h-screen overflow-y-auto">
        <div class="max-w-5xl mx-auto">
            
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 bg-[#FAD7B9]/90 backdrop-blur-sm p-6 rounded-3xl shadow-sm border border-[#F8E1C6]">
                <div>
                    <h1 class="text-3xl font-extrabold text-stone-800 tracking-tight">Panel de Control General</h1>
                    <p class="text-stone-600 mt-1 text-sm">Monitorea las métricas en tiempo real y el inventario de regalos de la plataforma.</p>
                </div>
                <div class="bg-[#F8E1C6] px-4 py-2.5 rounded-2xl font-bold text-xs text-stone-800 uppercase tracking-wide">
                    Admin: {{ Session::get('usuario_logueado')['nombre'] ?? 'Administrador' }}
                </div>
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

            <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-sm overflow-hidden border border-stone-200 mb-8 mt-4">
                <div class="bg-stone-100/50 p-4 border-b border-stone-200">
                    <h3 class="text-xs font-black text-stone-700 uppercase tracking-wider">📋 Auditoría en Tiempo Real por Celebración Activa</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-stone-50 border-b border-stone-200 font-bold text-stone-600 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="p-4">Celebración / Enlace Único</th>
                                <th class="p-4 text-center">Asistencias Confirmadas</th>
                                <th class="p-4 text-center">Total Catálogo</th>
                                <th class="p-4 text-center">Reservados</th>
                                <th class="p-4 text-center">Disponibles</th>
                                <th class="p-4 text-right">Auditar Inventario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($metricasRegalos ?? [] as $show)
                            <tr class="hover:bg-stone-50/50 transition-colors">
                                <td class="p-4">
                                    <p class="font-extrabold text-stone-800 text-sm">👶 Baby Shower: {{ $show['bebeNombre'] ?? 'Sin nombre' }}</p>
                                    <p class="text-[#3949AB] font-mono text-[10px] mt-0.5 tracking-tight">🔗 /e/{{ $show['slug'] }}</p>
                                </td>
                                <td class="p-4 text-center font-bold text-stone-700 text-sm">
                                    <span id="confirmados-{{ $show['slug'] }}" class="bg-[#E8EAF6] text-[#3949AB] px-3 py-1 rounded-xl border border-[#C5CAE9] font-mono text-[11px]">
                                        👥 {{ $show['invitadosConfirmados'] ?? 0 }} Sí
                                    </span>
                                </td>
                                <td class="p-4 text-center font-bold text-stone-700 text-sm">
                                    {{ $show['totalRegalos'] ?? 0 }}
                                </td>
                                <td class="p-4 text-center">
                                    <span id="reservados-{{ $show['slug'] }}" class="bg-[#FCF3CF] text-[#7D6608] font-bold px-2.5 py-0.5 rounded-lg border border-[#F9E79F] font-mono text-[11px]">
                                        🔥 {{ $show['regalosReservados'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span id="disponibles-{{ $show['slug'] }}" class="bg-[#D4EFDF] text-[#186A3B] font-bold px-2.5 py-0.5 rounded-lg border border-[#A9DFBF] font-mono text-[11px]">
                                        ✅ {{ $show['regalosDisponibles'] ?? 0 }}
                                    </span>
                                </td>
                                <td class="p-4 text-right align-middle space-x-2 whitespace-nowrap">
                                    <button type="button" onclick="mostrarModalRegalos('modal-regalos-{{ $loop->index }}', '{{ addslashes($show['bebeNombre'] ?? 'Sin nombre') }}')" class="text-stone-700 hover:text-stone-900 hover:bg-stone-200 font-bold cursor-pointer select-none tracking-wide uppercase text-[9px] bg-stone-100 px-3 py-2 rounded-xl inline-block transition-all border border-stone-200 shadow-sm">
                                        👁️ Ver Artículos
                                    </button>

                                    <div id="modal-regalos-{{ $loop->index }}" class="hidden">
                                        <div class="text-left space-y-2 font-sans mt-2">
                                            @forelse($show['listadoRegalos'] ?? [] as $regalo)
                                            <div class="border-b border-stone-100 last:border-0 pb-3 mb-2 bg-white/60 p-3 rounded-2xl border border-stone-200">
                                                <div class="flex justify-between items-start text-[12px] gap-2 mb-1">
                                                    <span class="font-extrabold text-stone-800 block whitespace-normal pr-2">
                                                        {{ $regalo['nombre'] }} 
                                                        <span class="text-[#3949AB] font-mono text-[11px] ml-1 bg-white px-1.5 py-0.5 rounded-lg border border-stone-200 shadow-inner">
                                                            ({{ $regalo['cantidad_disponible'] ?? 0 }} / {{ $regalo['cantidad_solicitada'] ?? 1 }})
                                                        </span>
                                                    </span>
                                                    <span class="text-[9px] uppercase font-black px-2 py-0.5 rounded-lg border tracking-wider shrink-0 {{ ($regalo['estado'] ?? 'disponible') === 'reservado' ? 'bg-[#FADBD8] text-[#7B241C] border-[#F5B7B1]' : 'bg-[#D4EFDF] text-[#186A3B] border-[#A9DFBF]' }}">
                                                        {{ $regalo['estado'] ?? 'disponible' }}
                                                    </span>
                                                </div>
                                                
                                                @if(!empty($regalo['lista_invitados']) && count($regalo['lista_invitados']) > 0)
                                                    <div class="mt-2.5 space-y-1.5">
                                                        @foreach($regalo['lista_invitados'] as $reserva)
                                                            <div class="flex justify-between items-center bg-white/80 p-2 rounded-xl border border-stone-200 shadow-sm">
                                                                <p class="text-[10px] text-stone-600 font-medium whitespace-normal flex items-center gap-1.5">
                                                                    👤 <span class="bg-[#FCF3CF] text-[#7D6608] px-2 py-1 rounded-lg font-bold uppercase tracking-wider text-[9px] border border-[#F9E79F]">{{ $reserva['nombre'] ?? 'Invitado' }}</span>
                                                                </p>
                                                                <form action="{{ route('admin.regalos.liberar_reserva', $regalo['_id'] ?? '') }}" method="POST" class="m-0 inline" onsubmit="return confirm('¿Seguro que deseas liberar esta reserva? El regalo volverá a estar disponible.')">
                                                                    @csrf
                                                                    <input type="hidden" name="reserva_id" value="{{ $reserva['_id'] ?? '' }}">
                                                                    <button type="submit" class="text-red-500 hover:text-white hover:bg-red-500 font-black px-2.5 py-1 text-[9px] uppercase tracking-wider rounded-lg bg-red-50 border border-red-200 transition-all cursor-pointer">
                                                                        ✕ Anular
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            @empty
                                            <p class="text-stone-400 font-medium italic text-[12px] py-4 text-center">El anfitrión no ha cargado regalos sugeridos.</p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <form action="{{ route('admin.babyshowers.destroy', $show['_id'] ?? '') }}" method="POST" onsubmit="return confirm('⚠️ ADVERTENCIA CRÍTICA: ¿Estás seguro de que deseas eliminar permanentemente este Baby Shower y todo su inventario asociado?')" class="inline m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:bg-red-50 hover:text-red-700 border border-transparent hover:border-red-200 px-3 py-2 rounded-xl transition-all font-bold cursor-pointer uppercase text-[9px] tracking-wider ml-1">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-stone-400 font-medium italic">No hay registros de baby showers en la Plataforma.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <h3 class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-3">🍼 Estado de Celebraciones en la Plataforma</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white/80 backdrop-blur-md p-6 rounded-3xl shadow-sm border border-stone-200 flex items-center">
                    <div class="w-12 h-12 bg-[#D4EFDF] text-[#186A3B] rounded-2xl flex items-center justify-center text-2xl mr-4">🌐</div>
                    <div>
                        <p class="text-xs text-stone-500 font-bold uppercase tracking-wider">Eventos Publicados</p>
                        <p class="text-2xl font-black text-stone-800 mt-0.5">{{ $metricasEvents['publicados'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-md p-6 rounded-3xl shadow-sm border border-stone-200 flex items-center">
                    <div class="w-12 h-12 bg-[#FCF3CF] text-[#7D6608] rounded-2xl flex items-center justify-center text-2xl mr-4">👁️‍🗨️</div>
                    <div>
                        <p class="text-xs text-stone-500 font-bold uppercase tracking-wider">Eventos Ocultos</p>
                        <p class="text-2xl font-black text-stone-800 mt-0.5">{{ $metricasEvents['ocultos'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="bg-white/80 backdrop-blur-md p-6 rounded-3xl shadow-sm border border-stone-200 flex items-center">
                    <div class="w-12 h-12 bg-[#FADBD8] text-[#7B241C] rounded-2xl flex items-center justify-center text-2xl mr-4">🔒</div>
                    <div>
                        <p class="text-xs text-stone-500 font-bold uppercase tracking-wider">Eventos Cerrados</p>
                        <p class="text-2xl font-black text-stone-800 mt-0.5">{{ $metricasEvents['cerrados'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-md p-8 rounded-3xl shadow-sm mb-6 border border-stone-200 mt-10">
                <h2 class="text-base font-extrabold text-stone-800 mb-5 flex items-center gap-2">✨ Añadir Nuevo Regalo al Catálogo Base Global</h2>
                <form action="{{ route('gifts.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Nombre del Artículo *</label>
                        <input type="text" name="nombre" placeholder="Ej: Cuna Mecedora de Madera" required class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 font-medium">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Descripción Detallada</label>
                        <input type="text" name="descripcion" placeholder="Ej: Color blanco con colchón antiahogo" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 font-medium">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">URL Imagen Referencial</label>
                        <input type="text" name="imagen" placeholder="http://tienda.com/foto.jpg" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 font-mono">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Categoría</label>
                        <select name="categoria" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 cursor-pointer font-bold text-stone-700">
                            <option value="Higiene">🧻 Higiene y Aseo</option>
                            <option value="Dormitorio">🛏️ Dormitorio y Cunas</option>
                            <option value="Alimentación">🍼 Alimentación y Lactancia</option>
                            <option value="Ropa">👕 Vestuario y Ropa</option>
                            <option value="Transporte">🚗 Transporte y Paseo</option>
                            <option value="General" selected>📦 General / Otros</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Tipo de Artículo</label>
                        <select name="tipo" id="select-tipo" onchange="conmutarCantidad(this.value)" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 cursor-pointer font-bold text-stone-700">
                            <option value="unico" selected>🔒 Único (Solo 1 unidad)</option>
                            <option value="repetible">🔄 Repetible (Múltiples unidades)</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Cantidad Requerida</label>
                        <input type="number" id="input-cantidad" name="cantidad_solicitada" value="1" min="1" required class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-stone-200 text-stone-400 font-bold" readonly>
                    </div>
                    <div class="flex flex-col gap-1.5 md:col-span-2 lg:col-span-3">
                        <label class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">Enlace Externo de Compra Opcional</label>
                        <input type="text" name="link_referencia" placeholder="https://www.tienda.com/producto" class="w-full border border-stone-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] text-xs bg-white/60 font-mono">
                    </div>
                    <div class="md:col-span-2 lg:col-span-3 flex justify-end pt-2">
                        <button type="submit" class="bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 px-10 py-4 rounded-3xl font-extrabold transition-all text-xs whitespace-nowrap shadow-md cursor-pointer tracking-wider uppercase">
                            ➕ Guardar Regalo Nuevo
                        </button>
                    </div>
                </form>
            </div>

            <h3 class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-3 mt-6">📨 Incidencias y Solicitudes de Soporte Activas</h3>
            <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-sm border border-stone-200 p-8 mb-12">
                <div class="divide-y divide-stone-200/60">
                    @if(isset($incidencias) && count($incidencias) > 0)
                        @foreach($incidencias as $incidencia)
                            <div class="py-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 text-xs">
                                <div>
                                    <span class="font-bold text-[#3949AB]">👤 {{ $incidencia['anfitrion'] ?? 'Anfitrión' }}</span>
                                    <span class="text-stone-300 mx-1.5">|</span>
                                    <span class="text-stone-500 font-medium">{{ date('d/m/Y H:i', strtotime($incidencia['createdAt'])) }}</span>
                                    <p class="text-stone-700 mt-2 font-medium bg-white/50 p-3.5 rounded-2xl border border-stone-200 max-w-2xl leading-relaxed">
                                        "{{ $incidencia['mensaje'] }}"
                                    </p>
                                </div>
                                <form action="{{ route('incidencias.complete', $incidencia['_id'] ?? $loop->index) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="bg-stone-800 hover:bg-black text-white px-5 py-3 rounded-2xl font-bold transition-all text-[10px] uppercase tracking-wider shadow-sm cursor-pointer whitespace-nowrap">
                                        ✓ Resolver ticket
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        <div class="py-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-2 text-xs">
                            <div>
                                <span class="font-bold text-[#3949AB]"> 📨 Canal de Comunicación Interno (Soporte)</span>
                                <span class="text-stone-300 mx-1.5">|</span>
                                <span class="text-stone-500 font-medium">Bandeja de Entrada Global</span>
                                <p class="text-stone-600 mt-2 font-medium bg-white/50 p-3.5 rounded-2xl border border-stone-200">
                                    Las solicitudes de soporte o incidencias de stock enviadas por los anfitriones aparecerán listadas aquí.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>

    <script>
        // Conexión directa a tu backend de Node.js en el puerto 3000
        const socket = io('http://localhost:3000');

        socket.on('connect', () => {
            console.log('🔌 Conectado exitosamente al canal en tiempo real.');
        });

        // 🔥 ESCUCHADOR EN TIEMPO REAL PARA INVITADOS CONFIRMADOS
        socket.on('nuevo-invitado', function(invitado) {
            // Buscamos la celda correspondiente al slug del Baby Shower del invitado
            const celdaAsistencias = document.getElementById(`confirmados-${invitado.slug}`);
            
            if (celdaAsistencias && invitado.estadoAsistencia === 'confirmado') {
                // Notificación emergente Toast (Arriba a la derecha)
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: `¡Asistencia confirmada de ${invitado.nombre}!`,
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });

                // Extraemos el número actual, sumamos 1 y actualizamos visualmente
                let numActual = parseInt(celdaAsistencias.innerText.replace(/[^0-9]/g, '')) || 0;
                celdaAsistencias.innerHTML = `👥 ${numActual + 1} Sí`;
            }
        });

        // 🔥 ESCUCHADOR EN TIEMPO REAL PARA CUANDO SE RESERVA UN REGALO
        socket.on('regalo-reservado', function(data) {
            const badgeReservados = document.getElementById(`reservados-${data.slug}`);
            const badgeDisponibles = document.getElementById(`disponibles-${data.slug}`);

            if (badgeReservados && badgeDisponibles) {
                let reservados = parseInt(badgeReservados.innerText.replace(/[^0-9]/g, '')) || 0;
                let disponibles = parseInt(badgeDisponibles.innerText.replace(/[^0-9]/g, '')) || 0;

                badgeReservados.innerHTML = `🔥 ${reservados + 1}`;
                if (disponibles > 0) {
                    badgeDisponibles.innerHTML = `✅ ${disponibles - 1}`;
                }
            }
        });

        function mostrarModalRegalos(idElemento, nombreBebe) {
            const contenidoHTML = document.getElementById(idElemento).innerHTML;
            
            Swal.fire({
                title: `🎁 Inventario: ${nombreBebe}`,
                html: contenidoHTML,
                showCloseButton: true,
                showConfirmButton: false,
                width: '36em', 
                customClass: {
                    popup: 'rounded-3xl shadow-2xl border border-stone-200 bg-[#FDFBF7]',
                    title: 'text-base font-extrabold text-stone-800 text-left w-full border-b border-stone-200 pb-3',
                    htmlContainer: 'p-1'
                }
            });
        }

        function conmutarCantidad(tipo) {
            const input = document.getElementById('input-cantidad');
            if (tipo === 'unico') {
                input.value = 1;
                input.readOnly = true;
                input.classList.replace('bg-white/60', 'bg-stone-200');
                input.classList.replace('text-stone-800', 'text-stone-400');
            } else {
                input.readOnly = false;
                input.value = 5; 
                input.classList.replace('bg-stone-200', 'bg-white/60');
                input.classList.replace('text-stone-400', 'text-stone-800');
            }
        }
    </script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#0B6658', 
                timer: 4500,
                timerProgressBar: true,
                customClass: { popup: 'rounded-3xl' }
            });
        </script>
    @endif
</body>
</html>