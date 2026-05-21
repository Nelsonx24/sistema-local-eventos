@extends('layout.main')

@section('title', 'Ventas - Gran Cañaveral')
@section('header-title', 'Ventas')

@section('content')
<div class="flex flex-col gap-8 max-w-4xl mx-auto">
    <div class="text-center space-y-2">
        <h2 class="text-3xl font-extrabold text-slate-900">Punto de Venta de Eventos</h2>
        <p class="text-slate-500">Seleccione un evento activo para comenzar a registrar consumos y ventas.</p>
    </div>

    <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-1 w-fit mx-auto">
        <a href="{{ route('sales.index', ['filter' => 'today']) }}" class="px-4 py-2 rounded-md text-xs font-bold transition-all {{ $filter === 'today' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Hoy</a>
        <a href="{{ route('sales.index', ['filter' => 'upcoming']) }}" class="px-4 py-2 rounded-md text-xs font-bold transition-all {{ $filter === 'upcoming' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Abiertos</a>
        <a href="{{ route('sales.index', ['filter' => 'completed']) }}" class="px-4 py-2 rounded-md text-xs font-bold transition-all {{ $filter === 'completed' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Finalizados</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($events as $event)
        <a href="{{ route('sales.show', $event->id) }}" class="group bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-brand-accent transition-all text-left flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-brand-gold/10 rounded-xl flex items-center justify-center text-brand-accent group-hover:bg-brand-accent group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                </div>
                <div>
                    <p class="font-bold text-slate-900 group-hover:text-brand-accent transition-colors">{{ $event->client_name }}</p>
                    <p class="text-xs text-slate-500">{{ $event->event_type }} • {{ $event->date }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-100 rounded-full text-slate-600 uppercase tracking-wider">{{ $event->id }}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 bg-brand-gold/10 text-brand-gold rounded-full uppercase tracking-wider">{{ $event->event_status === 'completed' ? 'Finalizado' : ($event->event_status === 'cancelled' ? 'Cancelado' : 'Próximo') }}</span>
                    </div>
                </div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-slate-300 group-hover:text-brand-accent group-hover:translate-x-1 transition-all"><path d="m9 18 6-6-6-6"/></svg>
        </a>
        @empty
        <div class="col-span-2 p-12 bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl text-center flex flex-col items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-slate-300"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            <p class="text-slate-500 font-medium">No hay eventos activos para gestionar en este momento.</p>
        </div>
        @endforelse

        @if(Auth::guard('staff')->user()->role === 'Administrador')
        <a href="{{ route('sales.direct') }}" class="group bg-slate-50 p-6 rounded-2xl border border-dashed border-slate-300 hover:border-slate-800 hover:bg-slate-100 transition-all text-left flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-slate-800 group-hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                </div>
                <div>
                    <p class="font-bold text-slate-600 group-hover:text-slate-900">Venta Directa</p>
                    <p class="text-xs text-slate-400">Sin asociación a un evento específico</p>
                </div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-slate-300 group-hover:text-slate-900 transition-all"><path d="m9 18 6-6-6-6"/></svg>
        </a>
        @endif
    </div>
</div>
@endsection
