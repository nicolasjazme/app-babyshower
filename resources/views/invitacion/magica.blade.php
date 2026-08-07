<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Invitación Especial</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <style>
        /* Animaciones personalizadas */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        
        /* Ocultar barra de scroll para limpieza visual */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>

<body class="min-h-screen font-sans text-slate-800 bg-slate-50 relative overflow-x-hidden selection:bg-indigo-200">

    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-purple-300 rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-float"></div>
        <div class="absolute top-[20%] right-[-10%] w-[50%] h-[50%] bg-indigo-300 rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-[-10%] left-[20%] w-[60%] h-[60%] bg-pink-300 rounded-full mix-blend-multiply filter blur-[120px] opacity-60 animate-float" style="animation-delay: 4s;"></div>
    </div>

    <main class="w-full max-w-2xl mx-auto p-4 relative z-10 py-12 flex flex-col min-h-screen justify-center">
        
        <header class="mb-8 text-center animate-fade-in-up">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/80 backdrop-blur-xl shadow-xl mb-6 text-4xl border border-white">
                💌
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight drop-shadow-sm">
                ¡Hola, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">{{ $invitado['nombre'] }}</span>!
            </h1>
            <p class="text-slate-600 mt-4 text-lg font-medium">
                Eres una persona muy especial para nosotros y queremos que seas parte de esta gran celebración.
            </p>
        </header>

        <div id="seccion-asistencia" class="{{ $invitado['estadoAsistencia'] === 'pendiente' ? '' : 'hidden' }} bg-white/70 backdrop-blur-xl p-8 md:p-10 rounded-[2rem] shadow-2xl border border-white/50 text-center mb-8 transform transition-all hover:scale-[1.01]">
            <h2 class="text-2xl font-extrabold text-slate-800 mb-2">¿Nos acompañarás?</h2>
            <p class="text-slate-500 mb-8">Confirma tu asistencia para asegurar tu lugar en nuestra lista VIP.</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="responderAsistencia('confirmado')" class="group relative bg-gradient-to-r from-emerald-400 to-teal-500 hover:from-emerald-500 hover:to-teal-600 text-white px-8 py-4 rounded-2xl font-black shadow-lg shadow-emerald-200 transition-all overflow-hidden">
                    <span class="relative z-10"><i class="fa-solid fa-check mr-2"></i> ¡Sí, ahí estaré!</span>
                    <div class="absolute inset-0 h-full w-full bg-white/20 scale-0 group-hover:scale-100 transition-transform origin-center rounded-2xl"></div>
                </button>
                <button onclick="responderAsistencia('rechazado')" class="bg-white hover:bg-rose-50 text-rose-500 border-2 border-rose-100 px-8 py-4 rounded-2xl font-bold shadow-sm transition-all">
                    <i class="fa-solid fa-xmark mr-2"></i> No podré asistir
                </button>
            </div>
        </div>

        <div id="seccion-regalos" class="{{ $invitado['estadoAsistencia'] !== 'pendiente' ? '' : 'hidden' }}">
            
            @if($invitado['estadoAsistencia'] === 'confirmado')
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 backdrop-blur-xl border border-emerald-100 p-8 rounded-[2rem] text-center mb-8 shadow-lg shadow-emerald-100/50">
                    <div class="w-16 h-16 mx-auto bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-3xl mb-4 shadow-inner">🎉</div>
                    <h2 class="text-2xl font-black text-emerald-800 mb-2">¡Asistencia Confirmada!</h2>
                    <p class="text-emerald-600 font-medium">Estamos felices de contar contigo. Si deseas hacernos un presente, aquí tienes nuestra lista sugerida.</p>
                </div>
            @elseif($invitado['estadoAsistencia'] === 'rechazado')
                <div class="bg-gradient-to-r from-rose-50 to-pink-50 backdrop-blur-xl border border-rose-100 p-8 rounded-[2rem] text-center mb-8 shadow-lg shadow-rose-100/50">
                    <div class="w-16 h-16 mx-auto bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-3xl mb-4 shadow-inner">🥺</div>
                    <h2 class="text-2xl font-black text-rose-800 mb-2">Te echaremos de menos</h2>
                    <p class="text-rose-600 font-medium">Entendemos que no puedas venir. Si de todas formas deseas enviarnos un detalle, la lista sigue disponible para ti.</p>
                </div>
            @endif

            @if(isset($invitado['regaloSeleccionado']) && $invitado['regaloSeleccionado'] != null)
                <div class="bg-white/80 backdrop-blur-xl p-8 rounded-[2rem] shadow-2xl border border-white/50 text-center relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                    <h3 class="text-xl font-extrabold text-slate-800 mb-6">Tu Regalo Reservado 🎁</h3>
                    
                    <div class="inline-flex items-center gap-4 bg-indigo-50 border border-indigo-100 text-indigo-900 px-6 py-4 rounded-2xl font-black text-lg shadow-inner mb-6">
                        <i class="fa-solid fa-gift text-2xl text-indigo-400"></i>
                        {{ $invitado['regaloSeleccionado']['nombre'] }}
                    </div>
                    <br>
                    <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 px-4 py-2 rounded-full text-xs font-black tracking-widest uppercase">
                        <i class="fa-solid fa-circle-check"></i> Reservado con éxito
                    </span>
                    <p class="text-sm mt-6 text-slate-500 font-medium">¡Muchísimas gracias por tu hermoso y valioso detalle!</p>
                </div>
            
            @else
                <div id="lista-regalos" class="space-y-4">
                    <h3 class="text-lg font-black text-slate-800 ml-2 mb-4"><i class="fa-solid fa-list-ul text-indigo-500 mr-2"></i> Opciones Disponibles:</h3>
                    
                    @if(isset($regalos) && count($regalos) > 0)
                        @foreach($regalos as $regalo)
                            @if($regalo['estado'] === 'disponible' && $regalo['cantidad_disponible'] > 0)
                                <div class="bg-white/70 backdrop-blur-xl p-5 rounded-3xl shadow-lg border border-white hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col sm:flex-row items-center gap-5 group">
                                    
                                    @if(isset($regalo['url_imagen']) && $regalo['url_imagen'] != null)
                                        <img src="{{ $regalo['url_imagen'] }}" alt="{{ $regalo['nombre'] }}" class="w-24 h-24 object-cover rounded-2xl shadow-md border-2 border-white shrink-0 group-hover:scale-105 transition-transform">
                                    @else
                                        <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-4xl shadow-md border-2 border-white shrink-0 group-hover:scale-105 transition-transform">
                                            🎁
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1 text-center sm:text-left w-full">
                                        <h4 class="text-xl font-bold text-slate-800 mb-1">{{ $regalo['nombre'] }}</h4>
                                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-4"><i class="fa-solid fa-layer-group text-slate-400"></i> Disponibles: <span class="text-indigo-600">{{ $regalo['cantidad_disponible'] }}</span></p>
                                        
                                        <button onclick="reservarRegalo('{{ $regalo['_id'] }}', '{{ $regalo['nombre'] }}')" class="w-full sm:w-auto bg-slate-900 hover:bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm tracking-wide shadow-md transition-colors">
                                            Reservar
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="bg-white/60 backdrop-blur-md p-10 rounded-[2rem] shadow-sm border border-white text-center">
                            <div class="text-5xl opacity-50 mb-4">🛍️</div>
                            <p class="text-slate-500 font-bold text-lg">La lista de regalos se ha completado.</p>
                            <p class="text-slate-400 text-sm mt-1">¡Gracias a todos por su generosidad!</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </main>

    <script>
        const token = '{{ $token }}';
        const apiUrl = 'http://localhost:3000/api/invitacion'; 

        // Disparar confeti si el usuario ya está confirmado al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            const estadoActual = "{{ $invitado['estadoAsistencia'] }}";
            if(estadoActual === 'confirmado' && !sessionStorage.getItem('confetiLanzado')) {
                lanzarConfeti();
                sessionStorage.setItem('confetiLanzado', 'true');
            }
        });

        // Animación de confeti profesional
        function lanzarConfeti() {
            var duration = 3 * 1000;
            var end = Date.now() + duration;
            (function frame() {
                confetti({ particleCount: 5, angle: 60, spread: 55, origin: { x: 0 }, colors: ['#4f46e5', '#10b981', '#f43f5e'] });
                confetti({ particleCount: 5, angle: 120, spread: 55, origin: { x: 1 }, colors: ['#4f46e5', '#10b981', '#f43f5e'] });
                if (Date.now() < end) requestAnimationFrame(frame);
            }());
        }

        // CONFIRMAR ASISTENCIA CON SWEETALERT
        async function responderAsistencia(estado) {
            const esConfirmado = estado === 'confirmado';
            
            // Si rechaza, le preguntamos amablemente si está seguro
            if(!esConfirmado) {
                const { isConfirmed } = await Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Nos encantaría que vinieras.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f43f5e',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Sí, no podré ir',
                    cancelButtonText: 'Cancelar'
                });
                if(!isConfirmed) return;
            }

            // Mostrar Loading de UI Premium
            Swal.fire({
                title: 'Procesando respuesta...',
                text: 'Guardando en la lista de invitados',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch(`${apiUrl}/${token}/asistencia`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ estado })
                });

                if(response.ok) {
                    // Si confirma asistencia, lanzamos confeti visual ANTES de recargar
                    if(esConfirmado) { sessionStorage.removeItem('confetiLanzado'); }
                    
                    Swal.fire({
                        icon: 'success',
                        title: esConfirmado ? '¡Genial!' : 'Entendido',
                        text: esConfirmado ? 'Tu asistencia ha sido confirmada.' : 'Hemos registrado tu respuesta.',
                        confirmButtonColor: '#4f46e5',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error('Error al registrar');
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Hubo un problema de conexión. Intenta nuevamente.', confirmButtonColor: '#4f46e5' });
            }
        }

        // RESERVAR REGALO CON SWEETALERT
        async function reservarRegalo(regaloId, nombreRegalo) {
            const { isConfirmed } = await Swal.fire({
                title: '🎁 Reservar Regalo',
                html: `¿Quieres reservar <b>${nombreRegalo}</b>?<br><span class="text-sm text-slate-500">Nadie más podrá elegir este detalle.</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Sí, quiero llevarlo',
                cancelButtonText: 'Cancelar'
            });

            if(!isConfirmed) return;

            Swal.fire({ title: 'Reservando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});

            try {
                const response = await fetch(`${apiUrl}/${token}/reservar`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ regaloId })
                });

                if(response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Reservado!',
                        text: 'Muchas gracias por tu hermoso detalle.',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    const data = await response.json();
                    Swal.fire({ icon: 'error', title: 'Lo sentimos', text: data.mensaje, confirmButtonColor: '#f43f5e' });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Error de conexión. Intenta más tarde.', confirmButtonColor: '#4f46e5' });
            }
        }
    </script>
</body>
</html>