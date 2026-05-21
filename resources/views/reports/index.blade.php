@extends('layout.main')

@section('title', 'Reportes - Gran Cañaveral')
@section('header-title', 'Reportes')

@section('content')


@if($lastEvent)
<div class="flex flex-wrap gap-2 mb-3">
    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex-1 min-w-[150px]">
        <div class="flex items-center gap-2">
            <div class="p-1.5 bg-slate-800 text-white rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">Último Evento Cerrado</p>
                <p class="text-sm font-bold text-slate-900">{{ $lastEvent->client_name }} <span class="font-normal text-slate-400">—</span> {{ $lastEvent->event_type }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex-1 min-w-[150px]">
        <div class="flex items-center gap-1.5 mb-1.5">
            <div class="p-1 bg-brand-gold/10 text-brand-gold rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
            </div>
            <p class="text-[0.55rem] font-bold text-slate-400 uppercase tracking-widest">Facturas</p>
        </div>
        <p class="text-xl font-extrabold text-slate-900">{{ $lastEventSalesCount }}</p>
    </div>
    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex-1 min-w-[150px]">
        <div class="flex items-center gap-1.5 mb-1.5">
            <div class="p-1 bg-amber-50 text-amber-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <p class="text-[0.55rem] font-bold text-slate-400 uppercase tracking-widest">Venta Más Alta</p>
        </div>
        <p class="text-xl font-extrabold text-slate-900">{{ number_format($lastEventBiggestSale) }} Bs</p>
        <p class="text-[0.55rem] text-slate-400">{{ $lastEventBiggestSaleClient }}</p>
    </div>
    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex-1 min-w-[150px]">
        <div class="flex items-center gap-1.5 mb-1.5">
            <div class="p-1 bg-emerald-50 text-emerald-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <p class="text-[0.55rem] font-bold text-slate-400 uppercase tracking-widest">Total Evento</p>
        </div>
        <p class="text-xl font-extrabold text-brand-accent">{{ number_format($lastEventSales->sum('amount')) }} Bs</p>
    </div>
    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex-1 min-w-[150px]">
        <div class="flex items-center gap-1.5 mb-1.5">
            <div class="p-1 bg-purple-50 text-purple-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <p class="text-[0.55rem] font-bold text-slate-400 uppercase tracking-widest">Métodos de Pago</p>
        </div>
        <div class="flex flex-col gap-0.5">
            <p class="text-[0.7rem] font-bold text-slate-700">Efectivo: <span class="text-emerald-600">{{ number_format($lastEventEfectivo) }} Bs</span></p>
            <p class="text-[0.7rem] font-bold text-slate-700">QR: <span class="text-brand-gold-dark">{{ number_format($lastEventQR) }} Bs</span></p>
            <p class="text-[0.7rem] font-bold text-slate-700">Tarjeta: <span class="text-slate-600">{{ number_format($lastEventTarjeta) }} Bs</span></p>
        </div>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-1.5 mb-2">
            <div class="p-1 bg-orange-50 text-orange-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
            </div>
            <p class="text-[0.55rem] font-bold text-slate-400 uppercase tracking-widest">Productos Vendidos</p>
        </div>
        <div class="flex flex-col gap-1.5">
            @forelse($productDetails as $pd)
            <div class="flex items-center justify-between gap-2 bg-slate-50 px-[7px] py-1.5 rounded-lg border border-slate-100">
                <span class="text-xs font-bold text-slate-800 truncate min-w-0">{{ $pd['name'] }}</span>
                <div class="flex gap-1.5 text-[0.65rem] shrink-0">
                    @if($pd['boxes'] > 0)<span class="text-orange-600 font-bold whitespace-nowrap">{{ $pd['boxes'] }} caja(s)</span>@endif
                    @if($pd['units'] > 0)<span class="text-brand-gold font-bold whitespace-nowrap">{{ $pd['units'] }} unidad(es)</span>@endif
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400 italic">Sin productos vendidos.</p>
            @endforelse
        </div>
    </div>
    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-1.5 mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-accent"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-widest">Distribución de Productos</h3>
        </div>
        @if(count($productPercentages) > 0)
        @php
            $colors = ['#f97316', '#D4AF37', '#10b981', '#8b5cf6', '#ec4899', '#14b8a6', '#f59e0b', '#ef4444'];
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
        <div class="flex items-start gap-4 flex-wrap">
            <div class="w-20 h-20 rounded-full shrink-0" style="background: conic-gradient({{ $conic }})"></div>
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
</div>
@endif

@if(Auth::guard('staff')->user()->role === 'Administrador')
<div x-data="{ tab: 'eventos' }" class="flex flex-col gap-3">
    <div class="flex items-center justify-between">
        <div class="flex gap-1 bg-white rounded-lg border border-slate-200 p-0.5 shadow-sm w-fit">
            <button @click="tab = 'eventos'" :class="tab === 'eventos' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-500 hover:bg-slate-50'" class="px-4 py-1.5 rounded-md text-[0.7rem] font-bold transition-all border border-slate-200">
                Ventas por Evento
            </button>
            <button @click="tab = 'directas'" :class="tab === 'directas' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-500 hover:bg-slate-50'" class="px-4 py-1.5 rounded-md text-[0.7rem] font-bold transition-all border border-slate-200">
                Ventas Directas
            </button>
        </div>
        <div x-show="tab === 'directas'" class="flex gap-1">
            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full text-[0.55rem] font-bold border border-emerald-200">Efectivo: {{ number_format($directSalesEfectivo) }} Bs</span>
            <span class="px-2 py-0.5 bg-brand-gold/10 text-brand-gold rounded-full text-[0.55rem] font-bold border border-brand-gold/20">QR: {{ number_format($directSalesQR) }} Bs</span>
            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full text-[0.55rem] font-bold border border-slate-200">Tarjeta: {{ number_format($directSalesTarjeta) }} Bs</span>
        </div>
    </div>
@else
<div x-data="{ tab: 'eventos' }" class="flex flex-col gap-3">
    <div class="flex gap-1 bg-white rounded-lg border border-slate-200 p-0.5 shadow-sm w-fit">
        <button @click="tab = 'eventos'" :class="tab === 'eventos' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white text-slate-500 hover:bg-slate-50'" class="px-4 py-1.5 rounded-md text-[0.7rem] font-bold transition-all border border-slate-200">
            Ventas por Evento
        </button>
    </div>
@endif

    <div x-show="tab === 'eventos'">
        <div class="overflow-x-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full border-collapse text-left">
                <thead class="bg-[#f8fafc] border-b border-slate-200">
                    <tr>
                        <th class="px-[7px] py-1.5 text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">Evento</th>
                        <th class="px-[7px] py-1.5 text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest text-center">Fecha</th>
                        <th class="px-[7px] py-1.5 text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest text-center">Ventas</th>
                        <th class="px-[7px] py-1.5 text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest text-right">Total</th>
                        <th class="px-[7px] py-1.5 text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest text-center"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($closedEvents as $event)
                    @php $eventTotal = App\Models\Sale::where('event_id', $event->id)->sum('amount'); @endphp
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-[7px] py-1.5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-slate-100 text-slate-500 rounded-lg flex items-center justify-center font-bold text-xs uppercase group-hover:bg-brand-accent group-hover:text-white transition-colors">
                                    {{ substr($event->client_name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 leading-tight">{{ $event->client_name }}</p>
                                    <p class="text-[0.6rem] text-slate-400 uppercase tracking-tighter">{{ $event->event_type }} • {{ $event->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-[7px] py-1.5 text-center">
                            <span class="text-[0.7rem] font-medium text-slate-600">{{ $event->date->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-[7px] py-1.5 text-center">
                            <span class="px-2 py-0.5 bg-brand-gold/10 text-brand-gold text-[0.6rem] font-bold rounded-full border border-brand-gold/20">
                                {{ App\Models\Sale::where('event_id', $event->id)->count() }} facturas
                            </span>
                        </td>
                        <td class="px-[7px] py-1.5 text-right">
                            <p class="text-xs font-extrabold text-slate-900">{{ number_format($eventTotal) }} Bs</p>
                        </td>
                        <td class="px-[7px] py-1.5 text-center">
                            <a href="{{ route('reports.show', $event->id) }}" class="w-7 h-7 rounded-full flex items-center justify-center text-slate-300 group-hover:text-brand-accent group-hover:bg-brand-gold/10 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-400 italic">
                            No hay eventos cerrados con reportes disponibles aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
        @if($closedEvents->hasPages())
        <div class="mt-2">
            {{ $closedEvents->links() }}
        </div>
        @endif
    </div>

    @if(Auth::guard('staff')->user()->role === 'Administrador')
    <div x-show="tab === 'directas'">
        <div class="overflow-x-auto">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                <table class="w-full border-collapse text-left">
                    <thead class="bg-[#f8fafc] border-b border-slate-200">
                        <tr>
                            <th class="px-[7px] py-1.5 text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">Fecha</th>
                            <th class="px-[7px] py-1.5 text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest text-center">Ventas</th>
                            <th class="px-[7px] py-1.5 text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest text-right">Total</th>
                            <th class="px-[7px] py-1.5 text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest text-center"></th>
                        </tr>
                    </thead>
                    @forelse($directSalesByDate as $dateKey => $group)
                    @php $displayDate = \Carbon\Carbon::parse($dateKey)->format('d/m/Y'); @endphp
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50 transition-colors cursor-pointer" onclick="window.location='{{ route('reports.direct', $dateKey) }}'">
                            <td class="px-[7px] py-1.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-slate-100 text-slate-500 rounded-lg flex items-center justify-center font-bold text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                    </div>
                                    <span class="text-sm font-bold text-slate-900">{{ $displayDate }}</span>
                                </div>
                            </td>
                            <td class="px-[7px] py-1.5 text-center">
                                <span class="px-2 py-0.5 bg-brand-gold/10 text-brand-gold text-[0.6rem] font-bold rounded-full border border-brand-gold/20">
                                    {{ $group['count'] }} factura(s)
                                </span>
                            </td>
                            <td class="px-[7px] py-1.5 text-right">
                                <p class="text-xs font-extrabold text-slate-900">{{ number_format($group['total']) }} Bs</p>
                            </td>
                            <td class="px-[7px] py-1.5 text-center">
                                <a href="{{ route('reports.direct', $dateKey) }}" class="w-7 h-7 rounded-full flex items-center justify-center text-slate-300 hover:text-brand-accent hover:bg-brand-gold/10 transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                    @empty
                    <tbody>
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 italic">No hay ventas directas registradas.</td>
                        </tr>
                    </tbody>
                    @endforelse
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

