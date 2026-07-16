@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;
    
    // Fallback de datos si el backend no ha devuelto configuraciones aún
    $configuracion = $cuotasConfig ?? [
        'monto_persona' => 0,
        'banco' => '',
        'tipo_cuenta' => '',
        'numero_cuenta' => '',
        'rut' => '',
        'titular' => '',
        'email_confirmacion' => ''
    ];
    
    $pagos = $cuotasPagos ?? [];
    $totalRecaudado = collect($pagos)->where('estado', 'aprobado')->sum('monto');
@endphp

<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-2xl">🐮</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Módulo Cuotas (La Vaca)</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Define el costo por persona, configura tus datos de transferencia y registra el dinero recaudado de forma segura.
            </p>
        </div>
        
        <button onclick="toggleModal('modal-config-vaca', true)" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-piggy-bank"></i> Configurar Datos Bancarios
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-comment-dollar"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Cuota por Persona</span>
                <span class="text-2xl font-black text-slate-900">${{ number_format($configuracion['monto_persona'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Recaudado</span>
                <span class="text-2xl font-black text-slate-900">${{ number_format($totalRecaudado, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pagos Pendientes</span>
                <span class="text-2xl font-black text-slate-900">{{ collect($pagos)->where('estado', 'pendiente')->count() }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">📝 Control de Aportes</h2>
            
            @if(empty($pagos))
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center text-xl mx-auto mb-4">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-600">No hay registros todavía</h3>
                    <p class="text-xs text-slate-400 mt-1">Los comprobantes que envíen tus invitados aparecerán listados aquí para que los apruebes.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-500">
                        <thead class="text-xs text-slate-400 uppercase bg-slate-50 rounded-xl">
                            <tr>
                                <th class="px-4 py-3">Invitado</th>
                                <th class="px-4 py-3">Monto Aportado</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pagos as $pago)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-4 font-semibold text-slate-800">{{ $pago['nombre_invitado'] }}</td>
                                    <td class="px-4 py-4 font-bold text-slate-900">${{ number_format($pago['monto'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-4">
                                        @if($pago['estado'] === 'aprobado')
                                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full uppercase">Aprobado</span>
                                        @else
                                            <span class="text-[10px] font-bold px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full uppercase">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-right flex justify-end gap-2">
                                        @if($pago['estado'] === 'pendiente')
                                            <form action="/eventos/modulos/cuotas/pagos/{{ $pago['_id'] ?? $pago['id'] }}/aprobar" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="p-2 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-100 transition-colors" title="Aprobar Pago">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="/eventos/modulos/cuotas/pagos/{{ $pago['_id'] ?? $pago['id'] }}" method="POST" onsubmit="return confirm('¿Deseas descartar este pago?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition-colors" title="Descartar">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">🏦 Tus Datos de Transferencia</h2>
                
                @if(empty($configuracion['banco']))
                    <div class="text-center py-8">
                        <p class="text-xs text-slate-400 italic">No has configurado tu cuenta. Los invitados no sabrán dónde transferir.</p>
                    </div>
                @else
                    <div class="space-y-4 text-sm bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Titular</span>
                            <span class="font-bold text-slate-800">{{ $configuracion['titular'] }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Rut / Documento</span>
                            <span class="font-bold text-slate-800">{{ $configuracion['rut'] }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Banco</span>
                            <span class="font-bold text-slate-800">{{ $configuracion['banco'] }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Tipo Cuenta</span>
                            <span class="font-bold text-slate-800">{{ $configuracion['tipo_cuenta'] }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Nº Cuenta</span>
                            <span class="font-mono font-bold text-slate-800">{{ $configuracion['numero_cuenta'] }}</span>
                        </div>
                        @if(!empty($configuracion['email_confirmacion']))
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Email de Confirmación</span>
                                <span class="font-bold text-slate-800 text-xs">{{ $configuracion['email_confirmacion'] }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="mt-8 border-t border-slate-100 pt-4">
                <span class="text-xs text-slate-400 flex items-start gap-1.5 leading-relaxed">
                    <i class="fa-solid fa-circle-info mt-0.5 text-slate-400 text-sm shrink-0"></i>
                    Estos datos se mostrarán directamente en la invitación pública para que los invitados puedan copiar y pagar rápidamente.
                </span>
            </div>
        </div>
    </div>
</div>

<div id="modal-config-vaca" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl border border-slate-100 relative max-h-[90vh] overflow-y-auto animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-config-vaca', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">🐮 Configurar La Vaca</h3>
        <p class="text-xs text-slate-500 mb-6">Establece la cuota y los datos bancarios para las transferencias de los invitados.</p>

        <form action="/eventos/modulos/cuotas/configurar" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="evento_id" value="{{ $eventoId }}">

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Cuota por Persona *</label>
                <input type="number" name="monto_persona" required min="0" placeholder="Ej. 10000" value="{{ $configuracion['monto_persona'] }}"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-semibold">
            </div>

            <hr class="border-slate-100 my-4">

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nombre Titular *</label>
                    <input type="text" name="titular" required placeholder="Ej. Nicolás Pérez" value="{{ $configuracion['titular'] }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">RUT Titular *</label>
                    <input type="text" name="rut" required placeholder="Ej. 12.345.678-9" value="{{ $configuracion['rut'] }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Banco *</label>
                    <input type="text" name="banco" required placeholder="Ej. Banco Estado" value="{{ $configuracion['banco'] }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-medium">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipo de Cuenta *</label>
                    <select name="tipo_cuenta" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-semibold bg-white cursor-pointer">
                        <option value="Cuenta Vista / RUT" {{ $configuracion['tipo_cuenta'] === 'Cuenta Vista / RUT' ? 'selected' : '' }}>Cuenta Vista / RUT</option>
                        <option value="Cuenta Corriente" {{ $configuracion['tipo_cuenta'] === 'Cuenta Corriente' ? 'selected' : '' }}>Cuenta Corriente</option>
                        <option value="Cuenta de Ahorro" {{ $configuracion['tipo_cuenta'] === 'Cuenta de Ahorro' ? 'selected' : '' }}>Cuenta de Ahorro</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Número de Cuenta *</label>
                    <input type="text" name="numero_cuenta" required placeholder="Ej. 12345678" value="{{ $configuracion['numero_cuenta'] }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-medium">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email de aviso (Opcional)</label>
                <input type="email" name="email_confirmacion" placeholder="transferencias@correo.com" value="{{ $configuracion['email_confirmacion'] }}"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-200 transition-all text-sm font-medium">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-config-vaca', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Guardar Configuración
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id, show) {
        const modal = document.getElementById(id);
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection