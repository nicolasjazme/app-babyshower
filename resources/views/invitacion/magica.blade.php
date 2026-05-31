<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Invitación Baby Shower</title>
</head>
<body class="min-h-screen flex items-center justify-center font-sans text-stone-800 p-4" style="background-image: url('https://i.postimg.cc/3JjzxRK9/fondo-babyshower.png'); background-size: cover; background-attachment: fixed; background-position: center;">

    <main class="w-full max-w-2xl">
        
        <header class="mb-6 bg-[#FAD7B9]/90 backdrop-blur-sm p-8 rounded-3xl shadow-sm border border-[#F8E1C6] text-center">
            <h1 class="text-3xl font-extrabold text-stone-800 tracking-tight">¡Hola, <span class="text-[#d97706]">{{ $invitado['nombre'] }}</span>! 🍼</h1>
            <p class="text-stone-600 mt-2 text-sm font-medium">Tienes una invitación muy especial para nuestro Baby Shower.</p>
        </header>

        <div id="seccion-asistencia" class="{{ $invitado['estadoAsistencia'] === 'pendiente' ? '' : 'hidden' }} bg-white/80 backdrop-blur-md p-8 rounded-3xl shadow-sm border border-stone-200 text-center mb-6">
            <h2 class="text-xl font-extrabold text-stone-800 mb-3">¿Nos acompañarás en este día especial?</h2>
            <p class="text-stone-600 mb-8 text-sm">Por favor, confirma tu asistencia para considerarte en nuestra lista VIP.</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="responderAsistencia('confirmado')" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-2xl font-bold shadow-md transition-all">
                    ✅ Sí, Confirmar Asistencia
                </button>
                <button onclick="responderAsistencia('rechazado')" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-3 rounded-2xl font-bold shadow-md transition-all">
                    ❌ No podré asistir
                </button>
            </div>
        </div>

        <div id="seccion-regalos" class="{{ $invitado['estadoAsistencia'] !== 'pendiente' ? '' : 'hidden' }}">
            
            @if($invitado['estadoAsistencia'] === 'confirmado')
                <div class="bg-emerald-50/90 backdrop-blur-sm border border-emerald-200 p-6 rounded-3xl text-center mb-6 shadow-sm">
                    <h2 class="text-xl font-bold text-emerald-700 mb-1">¡Gracias por confirmar! 🍼</h2>
                    <p class="text-emerald-600 text-sm">Ya que estarás con nosotros, aquí tienes la lista de regalos. ¡Elige el que prefieras!</p>
                </div>
            @elseif($invitado['estadoAsistencia'] === 'rechazado')
                <div class="bg-rose-50/90 backdrop-blur-sm border border-rose-200 p-6 rounded-3xl text-center mb-6 shadow-sm">
                    <h2 class="text-xl font-bold text-rose-700 mb-1">¡Qué lástima que no puedas venir! 😢</h2>
                    <p class="text-rose-600 text-sm">Entendemos tus motivos. De todas formas, si deseas tener un detalle con nosotros, aquí tienes la lista.</p>
                </div>
            @endif

            @if(isset($invitado['regaloSeleccionado']) && $invitado['regaloSeleccionado'] != null)
                <div class="bg-white/80 backdrop-blur-md p-8 rounded-3xl shadow-sm border border-stone-200 text-center">
                    <h3 class="text-lg font-extrabold text-stone-800 mb-5">Tu Regalo Seleccionado 🎁</h3>
                    <div class="inline-block bg-[#F8E1C6] text-stone-800 px-6 py-3 rounded-2xl font-bold text-lg shadow-sm mb-4 border border-[#EAD8C1]">
                        {{ $invitado['regaloSeleccionado']['nombre'] }}
                    </div>
                    <br>
                    <span class="inline-block bg-indigo-100 text-indigo-700 px-4 py-1.5 rounded-full text-xs font-bold tracking-wide">Reservado con éxito</span>
                    <p class="text-sm mt-5 text-stone-500 font-medium">¡Muchas gracias por tu hermoso detalle!</p>
                </div>
            @else
                <div id="lista-regalos" class="space-y-4">
                    @if(isset($regalos) && count($regalos) > 0)
                        @foreach($regalos as $regalo)
                            @if($regalo['estado'] === 'disponible' && $regalo['cantidad_disponible'] > 0)
                                <div class="bg-white/80 backdrop-blur-md p-5 rounded-3xl shadow-sm border border-stone-200 flex items-center gap-5 hover:bg-white/95 transition-all">
                                    @if(isset($regalo['url_imagen']) && $regalo['url_imagen'] != null)
                                        <img src="{{ $regalo['url_imagen'] }}" alt="{{ $regalo['nombre'] }}" class="w-20 h-20 object-cover rounded-2xl shadow-sm bg-stone-100 shrink-0">
                                    @else
                                        <div class="w-20 h-20 rounded-2xl bg-[#F8E1C6]/50 flex items-center justify-center text-3xl shadow-sm shrink-0 border border-[#EAD8C1]/50">🎁</div>
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="text-lg font-bold text-stone-800 mb-1">{{ $regalo['nombre'] }}</h4>
                                        <p class="text-xs text-stone-500 font-medium mb-3">Unidades disponibles: {{ $regalo['cantidad_disponible'] }}</p>
                                        <button onclick="reservarRegalo('{{ $regalo['_id'] }}')" class="w-full sm:w-auto bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-sm transition-colors">
                                            Reservar Regalo
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="bg-white/80 backdrop-blur-md p-8 rounded-3xl shadow-sm border border-stone-200 text-center">
                            <p class="text-stone-500 font-medium italic">En este momento no hay regalos disponibles.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </main>

    <script>
        const token = '{{ $token }}';
        const apiUrl = 'http://localhost:3000/api/invitacion'; 

        async function responderAsistencia(estado) {
            try {
                document.getElementById('seccion-asistencia').innerHTML = '<p class="font-bold text-stone-500">Procesando...</p>';
                const response = await fetch(`${apiUrl}/${token}/asistencia`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ estado })
                });
                window.location.reload();
            } catch (error) {
                alert('Error de conexión.');
                window.location.reload();
            }
        }

        async function reservarRegalo(regaloId) {
            if(!confirm('¿Reservar este regalo?')) return;
            try {
                const response = await fetch(`${apiUrl}/${token}/reservar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ regaloId })
                });
                if(response.ok) {
                    alert('¡Regalo reservado!');
                    window.location.reload();
                } else {
                    const data = await response.json();
                    alert(data.mensaje);
                }
            } catch (error) {
                alert('Error de conexión.');
            }
        }
    </script>
</body>
</html>