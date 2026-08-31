<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celebraciones - Admin Invita App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#FDFBF7] text-stone-800 font-sans antialiased selection:bg-amber-500 selection:text-white min-h-screen flex flex-col">

    {{-- Barra de Navegación Lúdica --}}
    <nav class="w-full bg-white/80 backdrop-blur-md border-b border-stone-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/admin" class="flex items-center gap-2 group cursor-pointer hover:text-indigo-600 transition-colors">
                    <span class="text-xl group-hover:-translate-x-1 transition-transform">👈</span>
                    <span class="font-black text-sm tracking-tight text-stone-500 group-hover:text-indigo-600">Volver al Mando</span>
                </a>
                
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🎉</span>
                    <span class="font-black text-xl tracking-tight text-stone-900 hidden sm:block">Celebraciones Activas</span>
                </div>
            </div>
        </div>
    </nav>

    {{-- Contenido Principal --}}
    <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
        <div class="max-w-7xl mx-auto space-y-8">
            
            <div class="text-center mb-10 mt-4">
                <h1 class="text-3xl md:text-4xl font-black text-stone-900 tracking-tight">
                    Directorio de Eventos
                </h1>
                <p class="text-stone-500 mt-2 text-sm font-medium">Revisa y audita todas las fiestas creadas en la plataforma.</p>
            </div>

            {{-- Grid de Tarjetas de Eventos (Reemplaza a la tabla aburrida) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($eventos ?? [] as $evento)
                    <div class="bg-white p-6 rounded-[2.5rem] border-2 border-stone-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                        
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                    {{ $evento['tipo_evento'] === 'baby_shower' ? '🍼' : ($evento['tipo_evento'] === 'matrimonio' ? '💍' : '🎈') }}
                                </div>
                                <span class="px-3 py-1 bg-stone-100 text-stone-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $evento['tipo_evento'] ?? 'general') }}
                                </span>
                            </div>
                            
                            <h3 class="text-xl font-black text-stone-800 leading-tight mb-1">
                                {{ $evento['nombre_evento'] ?? 'Celebración Mágica' }}
                            </h3>
                            <p class="text-xs text-stone-400 font-bold mb-4">Organiza: {{ $evento['creador_email'] ?? 'Anfitrión' }}</p>
                            
                            <div class="space-y-2 bg-stone-50 p-4 rounded-2xl border border-stone-100 mb-4">
                                <div class="flex items-center gap-2 text-xs font-medium text-stone-600">
                                    <span>📅</span> {{ $evento['fecha_evento'] ?? 'Por definir' }}
                                </div>
                                <div class="flex items-center gap-2 text-xs font-medium text-stone-600">
                                    <span>🧩</span> {{ count($evento['modulos_activos'] ?? []) }} módulos encendidos
                                </div>
                            </div>
                        </div>

                        <button onclick="auditarEvento('{{ $evento['_id'] ?? $evento['id'] }}', '{{ addslashes($evento['nombre_evento'] ?? '') }}')" 
                                class="w-full py-3 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-colors shadow-sm active:scale-95">
                            🔍 Auditar Evento
                        </button>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center p-12 bg-white rounded-[3rem] border-2 border-dashed border-stone-200 text-center">
                        <span class="text-5xl mb-4 opacity-50">📭</span>
                        <h3 class="text-xl font-black text-stone-800">Todo tranquilo por aquí</h3>
                        <p class="text-stone-500 text-sm mt-2 font-medium">Aún no hay eventos registrados en la base de datos.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </main>

    <script>
    function auditarEvento(id, nombre) {
        Swal.fire({
            title: `Auditoría: ${nombre}`,
            html: `
                <div class="text-left text-sm space-y-3 p-4 bg-stone-50 rounded-3xl border border-stone-200 mt-2">
                    <p class="font-medium text-stone-700"><strong>ID Único:</strong> <br><code class="bg-white border border-stone-200 px-3 py-1.5 rounded-xl text-xs text-indigo-600 font-bold block mt-1">${id}</code></p>
                    <p class="text-stone-500 text-xs leading-relaxed mt-3">Estado del nodo: 🟢 Conectado a MongoDB Atlas. Datos sincronizados.</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: '¡Entendido!',
            confirmButtonColor: '#4f46e5',
            customClass: { popup: 'rounded-[2.5rem]' }
        });
    }
    </script>
</body>
</html>