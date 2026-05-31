@extends('layouts.app')

@section('contenido')

<header class="mb-10 border-b border-stone-200 pb-6">
                <h1 class="text-3xl font-extrabold text-stone-800 tracking-tight">👶 Configurar Nueva Celebración</h1>
                <p class="text-stone-600 mt-1 text-sm">Completa la información del evento. Puedes rellenar los datos del bebé ahora o dejarlos para después.</p>
            </header>

            <form action="/baby-shower/nuevo" method="POST" class="space-y-8">
                @csrf

                <div>
                    <h2 class="text-base font-bold text-stone-800 mb-5 flex items-center gap-2">🗓️ Información del Evento</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Fecha del Evento *</label>
                            <input type="date" name="fecha" required value="{{ old('fecha') }}"
                                   class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Hora *</label>
                            <input type="time" name="hora" required value="{{ old('hora') }}"
                                   class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Lugar / Establecimiento *</label>
                            <input type="text" name="lugar" required placeholder="Ej. Casa de los abuelos" value="{{ old('lugar') }}"
                                   class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Dirección Exacta *</label>
                            <input type="text" name="direccion" required placeholder="Calle, Número, Comuna" value="{{ old('direccion') }}"
                                   class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Mensaje de Bienvenida (Opcional)</label>
                        <textarea name="mensaje_bienvenida" rows="2" placeholder="¡Bienvenidos a nuestra celebración!..."
                                  class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">{{ old('mensaje_bienvenida') }}</textarea>
                    </div>
                </div>

                <hr class="border-stone-200">

                <div>
                    <h2 class="text-base font-bold text-stone-800 mb-5 flex items-center gap-2">🍼 Información del Bebé (Opcional)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nombre del Bebé</label>
                            <input type="text" name="bebe_nombre" placeholder="Dejar vacío si es sorpresa" value="{{ old('bebe_nombre') }}"
                                   class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Sexo del Bebé</label>
                            <select name="bebe_sexo" class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-bold text-stone-700 cursor-pointer">
                                <option value="Por revelar" {{ old('bebe_sexo') == 'Por revelar' ? 'selected' : '' }}>Por revelar / Sorpresa ❓</option>
                                <option value="Niño" {{ old('bebe_sexo') == 'Niño' ? 'selected' : '' }}>Niño 🧑</option>
                                <option value="Niña" {{ old('bebe_sexo') == 'Niña' ? 'selected' : '' }}>Niña 👧</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Fecha Estimada de Nacimiento</label>
                            <input type="date" name="fecha_estimada" value="{{ old('fecha_estimada') }}"
                                   class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <label class="block text-[10px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Observaciones / Detalles</label>
                        <textarea name="observaciones" rows="2" placeholder="Ej: Código de vestimenta..."
                                  class="w-full px-4 py-3 border border-stone-200 rounded-2xl outline-none focus:ring-2 focus:ring-[#F8E1C6] transition-all bg-white/60 text-sm font-medium">{{ old('observaciones') }}</textarea>
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <a href="/admin" class="px-6 py-3 rounded-2xl border border-stone-300 text-stone-700 font-bold text-xs hover:bg-stone-100 transition-all cursor-pointer">
                        Cancelar
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-2xl bg-[#F8E1C6] hover:bg-[#EAD8C1] text-stone-900 font-extrabold shadow-md transition-all text-xs uppercase tracking-wider cursor-pointer">
                        🚀 Guardar y Publicar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('error'))
        <script>
            Swal.fire({ icon: 'error', title: 'Hubo un problema', text: "{{ session('error') }}", confirmButtonColor: '#e11d48' });
        </script>
    @endif
@endsection