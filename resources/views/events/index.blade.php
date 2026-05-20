@extends('layout.main')

@section('title', 'Eventos - Gran Cañaveral')
@section('header-title', 'Eventos')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-6 py-4 bg-white rounded-lg border border-border-subtle shadow-sm">
        <div class="flex items-center gap-4">
            <div class="flex border border-border-subtle rounded-lg overflow-hidden bg-slate-50">
                <button onclick="switchView('calendar')" id="btn-calendar" class="view-btn p-2.5 hover:bg-white transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-accent"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                </button>
                <button onclick="switchView('list')" id="btn-list" class="view-btn p-2.5 hover:bg-white transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-slate-400"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>
                </button>
            </div>
            <div id="calendar-nav" class="flex items-center gap-2 hidden">
                <button onclick="changeMonth(-1)" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <h3 id="current-month-label" class="text-base font-bold uppercase tracking-wide min-w-[160px] text-center"></h3>
                <button onclick="changeMonth(1)" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold hover:bg-emerald-100 transition-all border border-emerald-200 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    Reportes
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" :class="open ? 'rotate-180' : ''" class="transition-transform"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" class="absolute top-full left-0 mt-2 w-72 bg-white rounded-xl shadow-xl border border-slate-200 py-2 z-50" style="display: none;">
                    <a href="{{ route('events.calendar-pdf') }}" target="_blank" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-600"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <div>
                            <p class="font-bold text-slate-800">Calendario Actual</p>
                            <p class="text-xs text-slate-500 font-normal">Eventos próximos en calendario PDF</p>
                        </div>
                    </a>
                    <hr class="mx-3 border-slate-100">
                    <button onclick="openModal('report-filter-modal')" class="w-full text-left flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-blue-600"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/></svg>
                        <div>
                            <p class="font-bold text-slate-800">Reporte por Fechas</p>
                            <p class="text-xs text-slate-500 font-normal">Filtrar por año, mes o rango de fechas</p>
                        </div>
                    </button>
                </div>
            </div>
            <button onclick="openModal('types-modal')" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold hover:bg-slate-200 transition-all border border-slate-200 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>
                Tipos
            </button>
            <button onclick="openModal('event-modal')" class="bg-brand-primary text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold hover:bg-slate-800 transition-all shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Nuevo Evento
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-1 w-fit mb-4">
        <a href="{{ route('events.index', ['filter' => 'upcoming']) }}" class="px-4 py-2 rounded-md text-xs font-bold transition-all {{ $filter === 'upcoming' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Próximos</a>
        <a href="{{ route('events.index', ['filter' => 'past']) }}" class="px-4 py-2 rounded-md text-xs font-bold transition-all {{ $filter === 'past' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Finalizados</a>
        <a href="{{ route('events.index', ['filter' => 'all']) }}" class="px-4 py-2 rounded-md text-xs font-bold transition-all {{ $filter === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">Todos</a>
    </div>

    <!-- Calendar View -->
    <div id="calendar-view" class="hidden">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="grid grid-cols-7 bg-slate-50 border-b border-slate-200">
                @foreach(['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'] as $day)
                <div class="px-3 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                    {{ $day }}
                </div>
                @endforeach
            </div>
            <div id="calendar-grid" class="grid grid-cols-7"></div>
        </div>
    </div>

    <!-- List View -->
    <div id="list-view">
        <div class="bg-white rounded-lg border border-border-subtle shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-[#f8fafc] border-b border-border-subtle">
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest w-40">Evento / Fecha</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Cliente</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Tipo</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Estado Contrato</th>
                        <th class="px-6 py-4 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($events as $event)
                    <tr class="hover:bg-[#f8fafc] transition-colors group">
                        <td class="px-6 py-4">
                            <p class="text-[0.75rem] font-bold text-slate-900 mb-0.5">{{ $event->id }}</p>
                            <div class="flex items-center gap-1.5 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                <span class="text-[0.7rem] font-medium">{{ $event->date->format('d/m/Y') }}</span>
                            </div>
                        </td>
                    <td class="px-6 py-4">
                        <p class="text-[0.875rem] font-bold text-slate-800 leading-tight">{{ $event->client_name }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[0.65rem] font-bold border {{ $event->event_type === 'Boda' ? 'bg-blue-50 text-blue-700 border-blue-100' : ($event->event_type === 'Corporativo' ? 'bg-purple-50 text-purple-700 border-purple-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100') }}">
                            {{ $event->event_type }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col items-center gap-1">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.6rem] font-bold border {{ $event->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($event->payment_status === 'cancelled' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                                {{ $event->payment_status === 'paid' ? 'Pagado' : ($event->payment_status === 'cancelled' ? 'Cancelado' : 'Pte. Pago') }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.6rem] font-bold border {{ $event->event_status === 'completed' ? 'bg-slate-100 text-slate-600 border-slate-200' : ($event->event_status === 'cancelled' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200') }}">
                                {{ $event->event_status === 'completed' ? 'Finalizado' : ($event->event_status === 'cancelled' ? 'Cancelado' : 'Próximo') }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2 text-slate-400">
                            <button onclick="viewEvent({{ $event->id }})" class="p-2 hover:bg-slate-100 hover:text-brand-accent rounded-lg transition-all" title="Ver Detalles">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <button onclick="editEvent({{ $event->id }})" class="p-2 hover:bg-slate-100 hover:text-amber-600 rounded-lg transition-all" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                            </button>
                            <a href="{{ route('events.download-contract', $event->id) }}" target="_blank" class="p-2 hover:bg-slate-100 hover:text-blue-600 rounded-lg transition-all" title="Descargar Contrato">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                            </a>
                            @if(Auth::guard('staff')->user()->role === 'Administrador')
                            <button onclick="deleteEvent({{ $event->id }})" class="p-2 hover:bg-red-50 hover:text-red-500 rounded-lg transition-all" title="Eliminar registro">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                        No hay eventos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($events->hasPages())
<div class="mt-4">
    {{ $events->links() }}
</div>
@endif

<!-- Create Event Modal -->
<div id="event-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 class="font-bold text-text-main">Registrar Nuevo Evento</h3>
            <button onclick="closeModal('event-modal')" class="text-text-muted hover:text-text-main transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('events.store') }}" class="p-6 flex flex-col gap-4">
            @csrf
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Nombre del Cliente</label>
                <input type="text" name="client_name" required class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm focus:ring-1 focus:ring-brand-accent/40 outline-none">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">CI / Documento</label>
                <input type="text" name="client_id" required class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm focus:ring-1 focus:ring-brand-accent/40 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Teléfono / Contacto</label>
                    <input type="text" name="client_phone" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none">
                </div>
                <div></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Fecha</label>
                    <input type="date" name="date" id="event-date-input" required class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Tipo</label>
                    <select name="event_type" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none">
                        @foreach($eventTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Monto Total ($)</label>
                    <input type="number" name="total_amount" required class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Adelanto ($)</label>
                    <input type="number" name="advance_payment" required class="px-4 py-2 bg-emerald-50 border border-emerald-100 rounded-[6px] text-sm outline-none text-emerald-800">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Fecha Límite Pago</label>
                    <div class="px-4 py-2 bg-amber-50 border border-amber-100 rounded-[6px] text-sm text-amber-800 font-medium flex items-center justify-between gap-2">
                        <span id="payment-due-display">—</span>
                        <span class="text-[0.55rem] text-amber-500 font-normal">(1 día antes del evento)</span>
                    </div>
                    <input type="hidden" name="payment_due_date" id="payment-due-input">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-brand-accent text-white py-2.5 rounded-[6px] font-bold hover:bg-blue-600 transition-all shadow-md">
                Confirmar Reserva
            </button>
        </form>
    </div>
</div>

<!-- Edit Event Modal -->
<div id="edit-event-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 class="font-bold text-text-main">Editar Evento</h3>
            <button onclick="closeModal('edit-event-modal')" class="text-text-muted hover:text-text-main transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <form id="edit-event-form" method="POST" class="p-6 flex flex-col gap-4">
            @csrf
            @method('PUT')
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Nombre del Cliente</label>
                <input type="text" name="client_name" id="edit-client_name" required class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm focus:ring-1 focus:ring-brand-accent/40 outline-none">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">CI / Documento</label>
                <input type="text" name="client_id" id="edit-client_id" required class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm focus:ring-1 focus:ring-brand-accent/40 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Teléfono / Contacto</label>
                    <input type="text" name="client_phone" id="edit-client_phone" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none">
                </div>
                <div></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Fecha</label>
                    <input type="date" name="date" id="edit-date" required class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Tipo</label>
                    <select name="event_type" id="edit-event_type" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none">
                        @foreach($eventTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Monto Total ($)</label>
                    <input type="number" name="total_amount" id="edit-total_amount" required class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-[6px] text-sm outline-none">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Adelanto ($)</label>
                    <input type="number" name="advance_payment" id="edit-advance_payment" required class="px-4 py-2 bg-emerald-50 border border-emerald-100 rounded-[6px] text-sm outline-none text-emerald-800">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Fecha Límite Pago</label>
                    <div class="px-4 py-2 bg-amber-50 border border-amber-100 rounded-[6px] text-sm text-amber-800 font-medium flex items-center justify-between gap-2">
                        <span id="edit-payment-due-display">—</span>
                        <span class="text-[0.55rem] text-amber-500 font-normal">(1 día antes del evento)</span>
                    </div>
                    <input type="hidden" name="payment_due_date" id="edit-payment-due-input">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-amber-500 text-white py-2.5 rounded-[6px] font-bold hover:bg-amber-600 transition-all shadow-md">
                Guardar Cambios
            </button>
        </form>
    </div>
</div>

<!-- Manage Types Modal -->
<div id="types-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 class="font-bold text-text-main">Gestionar Tipos de Eventos</h3>
            <button onclick="closeModal('types-modal')" class="text-text-muted hover:text-text-main transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <div class="p-6 flex flex-col gap-4">
            <form method="POST" action="{{ route('events.types') }}" class="flex gap-2">
                @csrf
                <input type="text" name="new_type" placeholder="Nuevo tipo (ej. Bautizo)" class="flex-1 px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none">
                <button type="submit" class="bg-slate-800 text-white p-2 rounded-lg hover:bg-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                </button>
            </form>
            <div class="flex flex-col gap-2 max-h-[300px] overflow-y-auto">
                @foreach($eventTypes as $type)
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-100 group">
                    <span class="text-sm font-medium text-slate-700">{{ $type }}</span>
                    <form method="POST" action="{{ route('events.types') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="type" value="{{ $type }}">
                        <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
const eventsData = @json($eventsJson);
const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
let currentView = 'list';
let currentDate = new Date();
currentDate.setDate(1);

function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function showToast(msg) {
    const existing = document.getElementById('toast-msg');
    if (existing) existing.remove();
    const t = document.createElement('div');
    t.id = 'toast-msg';
    t.className = 'fixed top-6 left-1/2 -translate-x-1/2 z-[100] bg-slate-900 text-white px-6 py-3 rounded-xl shadow-2xl text-sm font-bold flex items-center gap-3 transition-all duration-300 opacity-0 -translate-y-4';
    t.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-400 shrink-0"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg><span>' + msg + '</span>';
    document.body.appendChild(t);
    requestAnimationFrame(() => { t.classList.remove('opacity-0', '-translate-y-4'); t.classList.add('opacity-100', 'translate-y-0'); });
    setTimeout(() => { t.classList.remove('opacity-100', 'translate-y-0'); t.classList.add('opacity-0', '-translate-y-4'); setTimeout(() => t.remove(), 300); }, 3000);
}

function switchView(view) {
    currentView = view;
    document.getElementById('calendar-nav').classList.toggle('hidden', view !== 'calendar');
    document.getElementById('calendar-view').classList.toggle('hidden', view !== 'calendar');
    document.getElementById('list-view').classList.toggle('hidden', view !== 'list');
    
    document.getElementById('btn-calendar').innerHTML = view === 'calendar' 
        ? '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-accent"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>'
        : '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-slate-400"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>';
    document.getElementById('btn-list').innerHTML = view === 'list'
        ? '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-accent"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>'
        : '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-slate-400"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>';
    
    if (view === 'calendar') renderCalendar();
}

function changeMonth(delta) {
    currentDate.setMonth(currentDate.getMonth() + delta);
    renderCalendar();
}

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    document.getElementById('current-month-label').textContent = `${monthNames[month]} ${year}`;
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDay = firstDay.getDay();
    const daysInMonth = lastDay.getDate();
    
    const today = new Date();
    const todayYear = today.getFullYear();
    const todayMonth = today.getMonth();
    const todayDay = today.getDate();
    const isToday = (todayYear === year && todayMonth === month);
    
    const typeColors = {
        'Boda': 'bg-blue-500',
        'Corporativo': 'bg-purple-500',
        'Social': 'bg-emerald-500',
        'Cumpleaños': 'bg-orange-500',
        '15 Años': 'bg-pink-500',
        'Bautizo': 'bg-cyan-500'
    };
    
    let html = '';
    
    for (let i = 0; i < startDay; i++) {
        html += '<div class="min-h-[120px] bg-slate-50/50 border-r border-b border-slate-100"></div>';
    }
    
    for (let day = 1; day <= daysInMonth; day++) {
        const monthStr = String(month + 1).padStart(2, '0');
        const dayStr = String(day).padStart(2, '0');
        const dateStr = `${year}-${monthStr}-${dayStr}`;
        const isTodayDay = isToday && day === todayDay;
        
        const dayEvents = eventsData.filter(e => e.date === dateStr);
        
        html += `
            <div class="min-h-[120px] p-2 border-r border-b border-slate-100 hover:bg-slate-50 transition-colors ${isTodayDay ? 'bg-blue-50/50' : 'bg-white'}">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-semibold ${isTodayDay ? 'bg-brand-accent text-white w-7 h-7 rounded-full flex items-center justify-center' : 'text-slate-600'}">${day}</span>
                    ${dayEvents.length > 0 ? `<span class="text-xs font-bold text-brand-accent bg-blue-100 px-2 py-0.5 rounded-full">${dayEvents.length}</span>` : ''}
                </div>
                <div class="space-y-1 overflow-hidden">`;
        
        dayEvents.forEach(e => {
            const color = typeColors[e.type] || 'bg-slate-500';
            html += `<button onclick="viewEvent(${e.id})" class="w-full text-left p-1.5 rounded-lg text-xs font-medium text-white truncate transition-transform hover:scale-[1.02] ${color}">${e.title.length > 15 ? e.title.substring(0, 15) + '...' : e.title}</button>`;
        });
        
        html += `</div></div>`;
    }
    
    const remainingCells = 7 - ((startDay + daysInMonth) % 7);
    if (remainingCells < 7) {
        for (let i = 0; i < remainingCells; i++) {
            html += '<div class="min-h-[120px] bg-slate-50/50 border-r border-b border-slate-100"></div>';
        }
    }
    
    document.getElementById('calendar-grid').innerHTML = html;
}

function viewEvent(id) {
    fetch(`/events/${id}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('event-detail-content').innerHTML = html;
            openModal('event-detail-modal');
        })
        .catch(() => showToast('Error al cargar los detalles del evento'));
}

function editEvent(id) {
    fetch(`/events/${id}/edit-data`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit-client_name').value = data.client_name;
            document.getElementById('edit-client_id').value = data.client_id;
            document.getElementById('edit-client_phone').value = data.client_phone || '';
            document.getElementById('edit-date').value = data.date;
            document.getElementById('edit-event_type').value = data.event_type;

            document.getElementById('edit-total_amount').value = data.total_amount;
            document.getElementById('edit-advance_payment').value = data.advance_payment;

            const dueDate = new Date(data.date);
            dueDate.setDate(dueDate.getDate() - 1);
            const y = dueDate.getFullYear();
            const m = String(dueDate.getMonth() + 1).padStart(2, '0');
            const d = String(dueDate.getDate()).padStart(2, '0');
            document.getElementById('edit-payment-due-input').value = `${y}-${m}-${d}`;
            document.getElementById('edit-payment-due-display').textContent = `${d}/${m}/${y}`;

            document.getElementById('edit-event-form').action = `/events/${id}`;
            openModal('edit-event-modal');
        })
        .catch(() => showToast('Error al cargar datos del evento'));
}

document.getElementById('edit-date').addEventListener('change', function() {
    const date = new Date(this.value);
    date.setDate(date.getDate() - 1);
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    document.getElementById('edit-payment-due-input').value = `${y}-${m}-${d}`;
    document.getElementById('edit-payment-due-display').textContent = `${d}/${m}/${y}`;
});

document.getElementById('event-date-input').addEventListener('change', function() {
    const date = new Date(this.value);
    date.setDate(date.getDate() - 1);
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    document.getElementById('payment-due-input').value = `${y}-${m}-${d}`;
    document.getElementById('payment-due-display').textContent = `${d}/${m}/${y}`;
});

function deleteEvent(id) {
    if (confirm('¿Estás seguro de eliminar este evento?')) {
        fetch(`/events/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => window.location.reload());
    }
}
</script>

<!-- Event Detail Modal -->
<div id="event-detail-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 class="font-bold text-text-main">Detalles del Evento</h3>
            <button onclick="closeModal('event-detail-modal')" class="text-text-muted hover:text-text-main">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <div id="event-detail-content" class="p-6"></div>
    </div>
</div>

<!-- Report Filter Modal -->
<div id="report-filter-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 class="font-bold text-text-main">Reporte por Fechas</h3>
            <button onclick="closeModal('report-filter-modal')" class="text-text-muted hover:text-text-main">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <form method="GET" action="{{ route('events.report-pdf') }}" target="_blank" class="p-6 space-y-5" onsubmit="closeModal('report-filter-modal')">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Año</label>
                    <select name="year" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-accent/30 focus:border-brand-accent bg-white">
                        <option value="">Todos</option>
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mes</label>
                    <select name="month" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-accent/30 focus:border-brand-accent bg-white">
                        <option value="">Todos</option>
                        @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $m)
                        <option value="{{ $i + 1 }}" {{ (now()->month === $i + 1) ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="border-t border-slate-100 pt-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">O filtrar por rango de fechas</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Desde</label>
                        <input type="date" name="from" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-accent/30 focus:border-brand-accent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Hasta</label>
                        <input type="date" name="to" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-accent/30 focus:border-brand-accent">
                    </div>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-brand-primary text-white px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-slate-800 transition-all shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline mr-1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    Generar PDF
                </button>
                <button type="button" onclick="closeModal('report-filter-modal')" class="px-4 py-2.5 rounded-lg text-sm font-bold text-slate-600 hover:bg-slate-100 transition-all border border-slate-200">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
