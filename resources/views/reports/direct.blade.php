@extends('layout.main')

@section('title', "Ventas Directas - $displayDate")
@section('header-title', 'Ventas Directas')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('reports.index') }}" class="flex items-center gap-2 text-slate-500 hover:text-slate-900 font-bold text-sm transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            Volver a Reportes
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.direct.pdf', $date) }}" target="_blank" class="flex items-center gap-2 text-xs font-bold bg-brand-accent text-white px-4 py-2 rounded-xl hover:bg-blue-600 transition-all shadow-md active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Descargar PDF
            </a>
            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[0.65rem] font-bold border border-emerald-200">Efectivo: {{ number_format($efectivo) }} Bs</span>
            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[0.65rem] font-bold border border-indigo-200">QR: {{ number_format($qr) }} Bs</span>
            <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-[0.65rem] font-bold border border-slate-200">Tarjeta: {{ number_format($tarjeta) }} Bs</span>
        </div>
    </div>

    <div class="bg-white px-6 py-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center font-bold text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
            <h2 class="text-xl font-black text-slate-900">{{ $displayDate }}</h2>
        </div>
        <p class="text-2xl font-black text-brand-accent">{{ number_format($total) }} Bs</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full border-collapse text-left">
            <thead class="bg-[#f8fafc] border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">#</th>
                    <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Cliente</th>
                    <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center">Método</th>
                    <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Items</th>
                    <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-right">Monto</th>
                    <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center">Hora</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sales as $sale)
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
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach($sale->items as $item)
                            <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[0.6rem] font-medium border border-slate-200">
                                {{ $item->quantity }} {{ $item->type }}x {{ $item->name }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-extrabold text-slate-900">{{ number_format($sale->amount) }} Bs</td>
                    <td class="px-6 py-4 text-center text-xs text-slate-500">{{ $sale->created_at->format('H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">No hay ventas en esta fecha.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-900 text-white">
                <tr>
                    <td colspan="5" class="px-6 py-5 text-right font-bold text-xs uppercase tracking-widest opacity-60">Total del día</td>
                    <td class="px-6 py-5 text-right font-black text-xl">{{ number_format($total) }} Bs</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection