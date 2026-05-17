@extends('layout.main')

@section('title', 'Reporte - ' . $event->client_name)
@section('header-title', 'Reportes')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('reports.index') }}" class="flex items-center gap-2 text-slate-500 hover:text-slate-900 font-bold text-sm transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            Volver a la lista
        </a>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.pdf', $event->id) }}" target="_blank" class="flex items-center gap-2 text-xs font-bold bg-brand-accent text-white px-4 py-2 rounded-xl hover:bg-blue-600 transition-all shadow-md active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Descargar PDF
            </a>
            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold uppercase tracking-widest border border-slate-200">
                ID: {{ $event->id }}
            </span>
        </div>
    </div>

    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xl shadow-slate-100 relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <p class="text-brand-accent font-bold text-xs uppercase tracking-[0.2em] mb-2">{{ $event->event_type }}</p>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight">{{ $event->client_name }}</h1>
                    <div class="flex items-center gap-4 mt-4 text-slate-500 text-sm font-medium">
                        <span class="flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg> {{ $event->date }}</span>
                        <span class="flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> {{ $event->guests }} invitados</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 text-right">Recaudación Total</p>
                    <p class="text-5xl font-black text-brand-accent tracking-tighter">{{ number_format($totalAmount) }} <span class="text-2xl">Bs</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-4">
        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-accent"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
            Detalle de Ventas Realizadas
        </h3>
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full border-collapse text-left">
                <thead class="bg-[#f8fafc] border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Ticket</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Cliente</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-center">Vendedor</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest">Items</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest text-right">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-900 text-sm">#{{ $sale->id }}</p>
                            <p class="text-[10px] text-slate-400 font-medium">{{ $sale->date }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-700 leading-none">{{ $sale->client_name }}</p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold">{{ $sale->payment_method }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $sale->seller_name ?? 'Sistema' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($sale->items as $item)
                                <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-medium border border-slate-200">
                                    {{ $item->quantity }} {{ $item->type }}x {{ $item->name }}
                                </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-extrabold text-slate-900 text-sm">{{ number_format($sale->amount) }} Bs</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                            No hay registros de ventas para este evento.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-900 text-white">
                    <tr>
                        <td colspan="4" class="px-6 py-5 text-right font-bold text-xs uppercase tracking-widest opacity-60">Total del Evento</td>
                        <td class="px-6 py-5 text-right font-black text-xl">{{ number_format($totalAmount) }} Bs</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection