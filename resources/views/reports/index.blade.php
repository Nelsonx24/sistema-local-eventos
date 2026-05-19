@extends('layout.main')

@section('title', 'Reportes - Gran Cañaveral')
@section('header-title', 'Reportes')

@section('content')
@if(Auth::guard('staff')->user()->role === 'Administrador')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Ingresos Totales</h3>
        </div>
        <p class="text-3xl font-extrabold text-slate-900">{{ number_format($totalSales) }} Bs</p>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Eventos Cerrados</h3>
        </div>
        <p class="text-3xl font-extrabold text-slate-900">{{ $closedEvents->count() }}</p>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/></svg>
            </div>
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Promedio por Evento</h3>
        </div>
        <p class="text-3xl font-extrabold text-slate-900">{{ $closedEvents->count() > 0 ? number_format($totalSales / $closedEvents->count()) : 0 }} Bs</p>
    </div>
</div>

<div class="flex items-center gap-2 mb-4">
    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[0.65rem] font-bold border border-emerald-200">Efectivo: {{ number_format($totalEfectivo) }} Bs</span>
    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[0.65rem] font-bold border border-indigo-200">QR: {{ number_format($totalQR) }} Bs</span>
    <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-[0.65rem] font-bold border border-slate-200">Tarjeta: {{ number_format($totalTarjeta) }} Bs</span>
</div>
@endif

@if($lastEvent)
<div class="flex flex-wrap gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-slate-800 text-white rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Último Evento Cerrado</p>
                <p class="font-bold text-slate-900">{{ $lastEvent->client_name }} <span class="font-normal text-slate-400">—</span> {{ $lastEvent->event_type }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
            </div>
            <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">Facturas</p>
        </div>
        <p class="text-2xl font-extrabold text-slate-900">{{ $lastEventSalesCount }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-1.5 bg-amber-50 text-amber-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">Venta Más Alta</p>
        </div>
        <p class="text-2xl font-extrabold text-slate-900">{{ number_format($lastEventBiggestSale) }} Bs</p>
        <p class="text-[0.6rem] text-slate-400 mt-1">{{ $lastEventBiggestSaleClient }}</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">Total Evento</p>
        </div>
        <p class="text-2xl font-extrabold text-brand-accent">{{ number_format($lastEventSales->sum('amount')) }} Bs</p>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-1.5 bg-purple-50 text-purple-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">Métodos de Pago</p>
        </div>
        <div class="flex flex-col gap-1">
            <p class="text-xs font-bold text-slate-700">Efectivo: <span class="text-emerald-600">{{ number_format($lastEventEfectivo) }} Bs</span></p>
            <p class="text-xs font-bold text-slate-700">QR: <span class="text-indigo-600">{{ number_format($lastEventQR) }} Bs</span></p>
            <p class="text-xs font-bold text-slate-700">Tarjeta: <span class="text-slate-600">{{ number_format($lastEventTarjeta) }} Bs</span></p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-1.5 bg-orange-50 text-orange-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
            </div>
            <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">Productos Vendidos</p>
        </div>
        <div class="flex flex-col gap-2">
            @forelse($productDetails as $pd)
            <div class="flex items-center justify-between gap-4 bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                <span class="text-sm font-bold text-slate-800">{{ $pd['name'] }}</span>
                <div class="flex gap-3 text-xs">
                    @if($pd['boxes'] > 0)<span class="text-orange-600 font-bold">{{ $pd['boxes'] }} caja(s)</span>@endif
                    @if($pd['units'] > 0)<span class="text-blue-600 font-bold">{{ $pd['units'] }} unidad(es)</span>@endif
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400 italic">Sin productos vendidos.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-accent"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-widest">Distribución de Productos</h3>
    </div>
    @if(count($productPercentages) > 0)
    @php
        $colors = ['#f97316', '#3b82f6', '#10b981', '#8b5cf6', '#ec4899', '#14b8a6', '#f59e0b', '#ef4444'];
        $conic = '';
        $angle = 0;
        $legend = [];
        foreach ($productPercentages as $i => $p) {
            $deg = round(($p['percentage'] / 100) * 360);
            if ($deg > 0) {
                $end = $angle + $deg;
                $conic .= ($i > 0 ? ', ' : '') . $colors[$i % count($colors)] . ' ' . $angle . 'deg ' . $end . 'deg';
                $legend[] = ['name' => $p['name'], 'count' => $p['count'], 'pct' => $p['percentage'], 'color' => $colors[$i % count($colors)]];
                $angle = $end;
            }
        }
    @endphp
    <div class="flex items-center gap-4">
        <div class="w-24 h-24 rounded-full shrink-0" style="background: conic-gradient({{ $conic }})"></div>
        <div class="flex flex-col gap-1">
            @foreach($legend as $l)
            <div class="flex items-center gap-2 text-xs">
                <span class="w-2.5 h-2.5 rounded-sm shrink-0" style="background: {{ $l['color'] }}"></span>
                <span class="font-medium text-slate-700">{{ $l['name'] }}</span>
                <span class="text-slate-400">{{ $l['pct'] }}%</span>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <p class="text-sm text-slate-400 italic">Sin ventas en este evento.</p>
    @endif
</div>
@endif

<div x-data="{ tab: 'eventos' }" class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <div class="flex gap-1 bg-white rounded-lg border border-slate-200 p-1 shadow-sm w-fit">
            <button @click="tab = 'eventos'" :class="tab === 'eventos' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-500 hover:bg-slate-50'" class="px-5 py-2 rounded-md text-[0.75rem] font-bold transition-all border border-slate-200">
                Ventas por Evento
            </button>
            <button @click="tab = 'directas'" :class="tab === 'directas' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-500 hover:bg-slate-50'" class="px-5 py-2 rounded-md text-[0.75rem] font-bold transition-all border border-slate-200">
                Ventas Directas
            </button>
        </div>
        <div x-show="tab === 'directas'" class="flex gap-1">
            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[0.6rem] font-bold border border-emerald-200">Efectivo: {{ number_format($directSalesEfectivo) }} Bs</span>
            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[0.6rem] font-bold border border-indigo-200">QR: {{ number_format($directSalesQR) }} Bs</span>
            <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-[0.6rem] font-bold border border-slate-200">Tarjeta: {{ number_format($directSalesTarjeta) }} Bs</span>
        </div>
    </div>

    <div x-show="tab === 'eventos'">
        <div class="overflow-x-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full border-collapse text-left">
                <thead class="bg-[#f8fafc] border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Evento</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center">Fecha</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center">Ventas</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-right">Total</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($closedEvents as $event)
                    @php $eventTotal = App\Models\Sale::where('event_id', $event->id)->sum('amount'); @endphp
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center font-bold text-xs uppercase group-hover:bg-brand-accent group-hover:text-white transition-colors">
                                    {{ substr($event->client_name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 leading-tight">{{ $event->client_name }}</p>
                                    <p class="text-[0.65rem] text-slate-400 uppercase tracking-tighter">{{ $event->event_type }} • {{ $event->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-xs font-medium text-slate-600">{{ $event->date->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[0.65rem] font-bold rounded-full border border-blue-100">
                                {{ App\Models\Sale::where('event_id', $event->id)->count() }} facturas
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <p class="text-sm font-extrabold text-slate-900">{{ number_format($eventTotal) }} Bs</p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <a href="{{ route('reports.show', $event->id) }}" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-300 group-hover:text-brand-accent group-hover:bg-blue-50 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                            No hay eventos cerrados con reportes disponibles aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>

    <div x-show="tab === 'directas'">
        <div class="overflow-x-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full border-collapse text-left">
                    <thead class="bg-[#f8fafc] border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">#</th>
                            <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Cliente</th>
                            <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center">Método</th>
                            <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center">Items</th>
                            <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-right">Monto</th>
                            <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($directSales as $sale)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-xs font-bold text-slate-400">#{{ $sale->id }}</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900 text-sm">{{ $sale->client_name }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $methodColors = ['Efectivo' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'QR' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'Tarjeta' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[0.6rem] font-bold border {{ $methodColors[$sale->payment_method] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-xs font-medium text-slate-600">{{ $sale->items->count() }} producto(s)</td>
                            <td class="px-6 py-4 text-right text-sm font-extrabold text-slate-900">{{ number_format($sale->amount) }} Bs</td>
                            <td class="px-6 py-4 text-center text-xs text-slate-500">{{ $sale->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">No hay ventas directas registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
