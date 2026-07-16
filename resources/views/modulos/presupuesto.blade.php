@extends('layouts.app')

@section('contenido')
@php
    $eventoActivo = Session::get('evento_activo') ?? [];
    $eventoId = $eventoActivo['_id'] ?? $eventoActivo['id'] ?? null;

    // Fallback de datos simulando la respuesta del backend
    $gastos = $presupuestoGastos ?? [];
    
    // Cálculos automáticos del presupuesto
    $totalEstimado = collect($gastos)->sum('monto_estimado');
    $totalReal = collect($gastos)->sum('monto_real');
    $diferencia = $totalEstimado - $totalReal;
    $estaExcedido = $diferencia < 0;
@endphp

<div class="space-y-8">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 bg-slate-800 text-white text-[10px] font-bold px-3 py-1 rounded-bl-lg flex items-center gap-1">
            <i class="fa-solid fa-lock text-[8px]"></i> 100% Privado
        </div>

        <div>
            <div class="flex items-center gap-3 mt-2 sm:mt-0">
                <span class="text-2xl">💰</span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tracker de Presupuesto</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">
                Lleva el control de tus finanzas. Anota tus gastos estimados vs los reales. (Los invitados nunca verán esta pantalla).
            </p>
        </div>
        
        <button onclick="toggleModal('modal-nuevo-gasto', true)" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white font-bold px-5 py-3 rounded-2xl transition-all shadow-sm hover:shadow-md text-xs uppercase tracking-wider cursor-pointer">
            <i class="fa-solid fa-plus"></i> Registrar Gasto
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Presupuesto Estimado</span>
                <span class="text-2xl font-black text-slate-900">${{ number_format($totalEstimado, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Gasto Real (Pagado)</span>
                <span class="text-2xl font-black text-slate-900">${{ number_format($totalReal, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border {{ $estaExcedido ? 'border-rose-200 bg-rose-50/30' : 'border-emerald-200 bg-emerald-50/30' }} shadow-sm flex items-center gap-5">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl {{ $estaExcedido ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600' }}">
                <i class="fa-solid {{ $estaExcedido ? 'fa-arrow-trend-down' : 'fa-scale-balanced' }}"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider block {{ $estaExcedido ? 'text-rose-500' : 'text-emerald-600' }}">
                    {{ $estaExcedido ? 'Excedido en' : 'A Favor (Restante)' }}
                </span>
                <span class="text-2xl font-black {{ $estaExcedido ? 'text-rose-600' : 'text-emerald-700' }}">
                    ${{ number_format(abs($diferencia), 0, ',', '.') }}
                </span>
            </div>
        </div>

    </div>

    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">📊 Desglose de Gastos</h2>

        @if(empty($gastos))
            <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <div class="w-12 h-12 bg-white text-slate-300 rounded-full flex items-center justify-center text-lg mx-auto mb-3 shadow-sm">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-600">No has registrado ningún gasto</h3>
                <p class="text-xs text-slate-400 mt-1">Anota conceptos como comida, decoración o local para iniciar tu tracker.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3 rounded-tl-xl">Concepto / Detalle</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Costo Estimado</th>
                            <th class="px-4 py-3">Costo Real</th>
                            <th class="px-4 py-3 rounded-tr-xl text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($gastos as $gasto)
                            @php
                                $diferenciaItem = ($gasto['monto_estimado'] ?? 0) - ($gasto['monto_real'] ?? 0);
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-4 py-4">
                                    <strong class="text-slate-800 block">{{ $gasto['concepto'] }}</strong>
                                    @if($diferenciaItem < 0)
                                        <span class="text-[10px] text-rose-500 font-semibold block mt-0.5">Te excediste por ${{ number_format(abs($diferenciaItem), 0, ',', '.') }}</span>
                                    @elseif($diferenciaItem > 0 && ($gasto['estado'] ?? 'pendiente') === 'pagado')
                                        <span class="text-[10px] text-emerald-500 font-semibold block mt-0.5">Ahorraste ${{ number_format($diferenciaItem, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if(($gasto['estado'] ?? 'pendiente') === 'pagado')
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 bg-emerald-100 text-emerald-700 rounded-md uppercase">
                                            <i class="fa-solid fa-check"></i> Pagado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-1 bg-amber-100 text-amber-700 rounded-md uppercase">
                                            <i class="fa-regular fa-clock"></i> Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 font-medium text-slate-500">
                                    ${{ number_format($gasto['monto_estimado'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 font-bold text-slate-800">
                                    ${{ number_format($gasto['monto_real'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <form action="/eventos/modulos/presupuesto/{{ $gasto['_id'] ?? $gasto['id'] }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas borrar este registro financiero?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-300 hover:text-rose-500 transition-colors" title="Eliminar registro">
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
</div>

<div id="modal-nuevo-gasto" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-200">
        
        <button onclick="toggleModal('modal-nuevo-gasto', false)" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-black text-slate-900 mb-2">💸 Registrar Nuevo Gasto</h3>
        <p class="text-xs text-slate-500 mb-6">Anota un concepto financiero para llevar el tracking correcto de tu evento.</p>

        <form action="/eventos/modulos/presupuesto" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="evento_id" value="{{ $eventoId }}">

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Concepto / Descripción *</label>
                <input type="text" name="concepto" required placeholder="Ej. Arriendo de Salón, Fotógrafo, Torta"
                       class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-slate-400 transition-all text-sm font-medium">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Costo Estimado *</label>
                    <input type="number" name="monto_estimado" required placeholder="Ej. 50000" min="0"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-slate-400 transition-all text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Costo Real Pagado</label>
                    <input type="number" name="monto_real" placeholder="Ej. 55000" min="0" value="0"
                           class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-slate-400 transition-all text-sm font-semibold">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Estado del Pago *</label>
                <select name="estado" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-slate-400 transition-all text-sm font-bold text-slate-700 bg-white cursor-pointer">
                    <option value="pendiente">⏳ Pendiente de Pago</option>
                    <option value="pagado">✅ Pagado Totalmente</option>
                </select>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modal-nuevo-gasto', false)" class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-500 font-bold text-xs uppercase hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                    Añadir Gasto
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