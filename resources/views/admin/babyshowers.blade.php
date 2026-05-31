<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Panel Admin - Supervisión de Celebraciones</title>
</head>
<body class="min-h-screen flex font-sans text-stone-800" style="background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); background-size: cover; background-attachment: fixed; background-position: center;">

    <aside class="w-64 bg-[#F8E1C6]/90 backdrop-blur-md text-stone-800 flex flex-col shadow-lg shrink-0 border-r border-stone-200">
        <div class="h-16 flex items-center justify-center border-b border-stone-200/50 bg-[#F8E1C6]/80">
            <span class="text-xl font-bold tracking-wider text-stone-800">Admin Plataforma 🖥️</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/admin" class="block px-4 py-2 hover:bg-stone-200/50 rounded-xl transition-colors font-medium flex items-center gap-2 text-stone-700">
                📊 Métricas Globales
            </a>
            <a href="/admin/baby-showers" class="block px-4 py-2 bg-[#EAD8C1] rounded-xl font-bold text-stone-900 transition-colors flex items-center gap-2">
                🍼 Ver Baby Showers
            </a>
        </nav>
        <div class="p-4 border-t border-stone-200/50">
            <form action="/logout" method="POST" class="m-0">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-stone-600 hover:text-stone-900 font-bold transition-colors">
                    🚪 Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-8 h-screen overflow-y-auto">
        <div class="max-w-6xl mx-auto">

            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 bg-white/80 backdrop-blur-md p-6 rounded-3xl shadow-sm border border-stone-200">
                <div>
                    <h1 class="text-3xl font-extrabold text-stone-800 tracking-tight">🍼 Supervisión de Baby Showers</h1>
                    <p class="text-stone-600 mt-1 text-sm">Monitorea los eventos creados, cambia visibilidades y audita las listas de invitados.</p>
                </div>
            </header>

            @if(session('success'))
                <div class="bg-[#D4EFDF] border-l-4 border-[#186A3B] text-[#186A3B] p-4 rounded-2xl mb-6 text-sm font-bold shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-sm overflow-hidden border border-stone-200">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-stone-100/50 border-b border-stone-200 text-stone-600 font-bold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="p-4">Celebración / Bebé</th>
                            <th class="p-4">Anfitrión</th>
                            <th class="p-4">Fecha y Lugar</th>
                            <th class="p-4 text-center">Total Invitados</th>
                            <th class="p-4">Estado Web</th>
                            <th class="p-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-xs">
                        @forelse($events as $event)
                        <tr class="hover:bg-stone-50/50 transition-colors">
                            <td class="p-4">
                                <p class="font-bold text-stone-800 text-sm">"{{ $event['bebeNombre'] ?? 'Bebé' }}"</p>
                                <p class="text-[11px] text-[#3949AB] font-mono mt-0.5">Slug: /e/{{ $event['slug'] }}</p>
                            </td>
                            <td class="p-4 text-stone-600 font-medium">
                                {{ $event['organizadorId']['nombre'] ?? 'Organizador' }}
                            </td>
                            <td class="p-4">
                                <p class="text-stone-700 font-bold">{{ isset($event['fecha']) ? date('d/m/Y', strtotime($event['fecha'])) : '--/--/----' }}</p>
                                <p class="text-stone-500 text-[11px] mt-0.5">{{ $event['lugar'] ?? 'No registrado' }}</p>
                            </td>
                            <td class="p-4 align-middle text-center">
                                <span id="total-invitados-{{ $event['_id'] }}" class="bg-[#E8EAF6] text-[#3949AB] px-3 py-1.5 rounded-xl font-bold text-[11px] border border-[#C5CAE9]">
                                    👥 {{ count($event['invitados'] ?? []) }} 
                                </span>
                            </td>
                            <td class="p-4 align-middle">
                                <span class="capitalize px-3 py-1 rounded-xl font-black text-[10px] border 
                                    {{ $event['estado'] === 'publicado' ? 'bg-[#D4EFDF] text-[#186A3B] border-[#A9DFBF]' : ($event['estado'] === 'cerrado' ? 'bg-[#FADBD8] text-[#7B241C] border-[#F5B7B1]' : 'bg-[#FCF3CF] text-[#7D6608] border-[#F9E79F]') }}">
                                    {{ $event['estado'] }}
                                </span>
                            </td>
                            <td class="p-4 align-middle text-right space-x-2 whitespace-nowrap">
                                <button onclick="toggleInvitados('{{ $event['_id'] }}')" class="bg-stone-200 hover:bg-stone-300 text-stone-700 px-3 py-2 rounded-xl font-bold transition-all cursor-pointer">
                                    👁️ Ver Invitados
                                </button>

                                <form action="{{ route('admin.babyshowers.status', $event['_id']) }}" method="POST" class="inline-block m-0">
                                    @csrf
                                    <select name="estado" onchange="this.form.submit()" class="bg-white/60 border border-stone-200 p-2 rounded-xl font-bold text-stone-700 outline-none cursor-pointer focus:ring-2 focus:ring-[#F8E1C6] text-xs">
                                        <option value="oculto" {{ $event['estado'] === 'oculto' ? 'selected' : '' }}>Ocultar</option>
                                        <option value="publicado" {{ $event['estado'] === 'publicado' ? 'selected' : '' }}>Publicar</option>
                                        <option value="cerrado" {{ $event['estado'] === 'cerrado' ? 'selected' : '' }}>Cerrar</option>
                                    </select>
                                </form>
                            </td>
                        </tr>

                        <tr id="lista-{{ $event['_id'] }}" class="hidden bg-stone-50/50 shadow-inner">
                            <td colspan="6" class="p-0">
                                <div class="bg-white/90 p-6 rounded-3xl border border-stone-200 max-w-4xl mx-auto shadow-sm my-6">
                                    <h4 class="font-extrabold text-stone-700 mb-4 flex items-center gap-2 text-xs uppercase tracking-wider text-[#3949AB] border-b border-stone-200 pb-3">
                                        📋 Auditoría de Invitados - Evento "{{ $event['bebeNombre'] }}"
                                    </h4>
                                    <table class="w-full text-left border-collapse text-[11px]">
                                        <thead class="bg-stone-50 border-b border-stone-200 font-bold text-stone-500 uppercase tracking-wider">
                                            <tr>
                                                <th class="p-3">Nombre</th>
                                                <th class="p-3">Correo</th>
                                                <th class="p-3 text-center">Estado Confirmación</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-invitados-{{ $event['_id'] }}" class="divide-y divide-stone-100">
                                            @forelse($event['invitados'] ?? [] as $invitado)
                                            <tr class="hover:bg-stone-50/80 transition-colors">
                                                <td class="p-3 font-bold text-stone-800">👤 {{ $invitado['nombre'] }}</td>
                                                <td class="p-3 text-stone-500 font-mono text-[10px]">{{ $invitado['correo'] ?: 'Sin registrar' }}</td>
                                                <td class="p-3 text-center">
                                                    @if(($invitado['estadoConfirmacion'] ?? 'pendiente') === 'confirmado')
                                                        <span class="bg-[#D4EFDF] text-[#186A3B] font-bold px-2.5 py-1 rounded-lg border border-[#A9DFBF] uppercase text-[9px] tracking-wider">✅ Confirmado</span>
                                                    @elseif(($invitado['estadoConfirmacion'] ?? 'pendiente') === 'rechazado')
                                                        <span class="bg-[#FADBD8] text-[#7B241C] font-bold px-2.5 py-1 rounded-lg border border-[#F5B7B1] uppercase text-[9px] tracking-wider">✕ Rechazado</span>
                                                    @else
                                                        <span class="bg-[#FCF3CF] text-[#7D6608] font-bold px-2.5 py-1 rounded-lg border border-[#F9E79F] uppercase text-[9px] tracking-wider">⏳ Pendiente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr class="fila-vacia">
                                                <td colspan="3" class="p-6 text-center text-stone-400 font-medium italic">Este evento aún no tiene invitados registrados en la base de datos.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-stone-400 font-medium">No existen Baby Showers registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script>
        const socket = io('http://localhost:3000');

        // ESCUCHADOR PARA INSERTAR INVITADOS EN LA TABLA EN VIVO
        socket.on('nuevo-invitado', function(invitado) {
            const tablaBody = document.getElementById(`tabla-invitados-${invitado.eventoId}`);
            const badgeTotal = document.getElementById(`total-invitados-${invitado.eventoId}`);

            // 1. Actualizar el badge del total del contador general
            if (badgeTotal) {
                let totalActual = parseInt(badgeTotal.innerText.replace(/[^0-9]/g, '')) || 0;
                badgeTotal.innerHTML = `👥 ${totalActual + 1}`;
            }

            // 2. Insertar dinámicamente la fila en el desglose
            if (tablaBody) {
                const filaVacia = tablaBody.querySelector('.fila-vacia');
                if (filaVacia) filaVacia.remove();

                let estadoBadge = '';
        if (invitado.estadoAsistencia === 'confirmado') {
            estadoBadge = '<span class="bg-[#D4EFDF] text-[#186A3B] font-bold px-2.5 py-1 rounded-lg border border-[#A9DFBF] uppercase text-[9px] tracking-wider">✅ Confirmado</span>';
        } else if (invitado.estadoAsistencia === 'rechazado') {
            estadoBadge = '<span class="bg-[#FADBD8] text-[#7B241C] font-bold px-2.5 py-1 rounded-lg border border-[#F5B7B1] uppercase text-[9px] tracking-wider">✕ Rechazado</span>';
        } else {
            estadoBadge = '<span class="bg-[#FCF3CF] text-[#7D6608] font-bold px-2.5 py-1 rounded-lg border border-[#F9E79F] uppercase text-[9px] tracking-wider">⏳ Pendiente</span>';
        }

        const nuevaFila = `
            <tr class="hover:bg-stone-50/80 transition-colors bg-blue-50/30 animate-pulse">
                <td class="p-3 font-bold text-stone-800">👤 ${invitado.nombre}</td>
                <td class="p-3 text-stone-500 font-mono text-[10px]">${invitado.correo || 'Sin registrar'}</td>
                <td class="p-3 text-center">${estadoBadge}</td>
            </tr>
        `;
        tablaBody.insertAdjacentHTML('afterbegin', nuevaFila);
    }
});;

        function toggleInvitados(id) {
            const fila = document.getElementById('lista-' + id);
            fila.classList.toggle('hidden');
        }
    </script>
</body>
</html>