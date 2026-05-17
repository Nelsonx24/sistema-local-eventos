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
@endif

<div class="flex flex-col gap-4">
    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-accent"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
        Reporte de Ventas por Eventos Cerrados
    </h2>

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
                        <span class="text-xs font-medium text-slate-600">{{ $event->date }}</span>
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
@endsection