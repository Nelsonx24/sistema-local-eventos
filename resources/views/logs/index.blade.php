@extends('layout.main')

@section('title', 'Logs - Gran Cañaveral')
@section('header-title', 'Logs del Sistema')

@section('content')
<div class="flex flex-col gap-4">
    <div class="flex flex-wrap items-center gap-2 bg-white rounded-lg border border-border-subtle shadow-sm px-5 py-3">
        <form method="GET" action="{{ route('logs.index') }}" class="flex flex-wrap items-end gap-3 w-full">
            <div class="flex flex-col gap-1">
                <label class="text-[0.6rem] font-bold text-text-muted uppercase">Tipo</label>
                <select name="type" class="px-3 py-1.5 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                    <option value="">Todos</option>
                    <option value="Evento" {{ request('type') === 'Evento' ? 'selected' : '' }}>Eventos</option>
                    <option value="Inventario" {{ request('type') === 'Inventario' ? 'selected' : '' }}>Inventario</option>
                    <option value="Venta" {{ request('type') === 'Venta' ? 'selected' : '' }}>Ventas</option>
                    <option value="Personal" {{ request('type') === 'Personal' ? 'selected' : '' }}>Personal</option>
                    <option value="Configuración" {{ request('type') === 'Configuración' ? 'selected' : '' }}>Configuración</option>
                    <option value="Otros" {{ request('type') === 'Otros' ? 'selected' : '' }}>Otros</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[0.6rem] font-bold text-text-muted uppercase">Acción</label>
                <select name="action" class="px-3 py-1.5 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                    <option value="">Todas</option>
                    <option value="Crear" {{ request('action') === 'Crear' ? 'selected' : '' }}>Crear</option>
                    <option value="Actualizar" {{ request('action') === 'Actualizar' ? 'selected' : '' }}>Actualizar</option>
                    <option value="Eliminar" {{ request('action') === 'Eliminar' ? 'selected' : '' }}>Eliminar</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[0.6rem] font-bold text-text-muted uppercase">Desde</label>
                <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-1.5 bg-slate-50 border border-border-subtle rounded-lg text-sm">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-[0.6rem] font-bold text-text-muted uppercase">Hasta</label>
                <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-1.5 bg-slate-50 border border-border-subtle rounded-lg text-sm">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-1.5 bg-brand-primary text-white rounded-lg text-xs font-bold hover:bg-slate-800 transition-colors">Filtrar</button>
                <a href="{{ route('logs.index') }}" class="px-4 py-1.5 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-200 transition-colors">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg border border-border-subtle shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="bg-[#f8fafc] border-b border-border-subtle">
                        <th class="px-5 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Fecha / Hora</th>
                        <th class="px-5 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Tipo</th>
                        <th class="px-5 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Acción</th>
                        <th class="px-5 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Descripción</th>
                        <th class="px-5 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f1f5f9]">
                    @forelse($logs as $log)
                    <tr class="hover:bg-[#f8fafc] transition-colors">
                        <td class="px-5 py-3 text-xs text-text-muted whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[0.65rem] font-semibold
                                @switch($log->type)
                                    @case('Evento') bg-blue-50 text-blue-700 @break
                                    @case('Inventario') bg-amber-50 text-amber-700 @break
                                    @case('Venta') bg-emerald-50 text-emerald-700 @break
                                    @case('Personal') bg-purple-50 text-purple-700 @break
                                    @case('Configuración') bg-slate-100 text-slate-700 @break
                                    @default bg-slate-50 text-slate-600
                                @endswitch">
                                {{ $log->type }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[0.65rem] font-semibold
                                @switch($log->action)
                                    @case('Crear') bg-green-50 text-green-700 @break
                                    @case('Actualizar') bg-blue-50 text-blue-700 @break
                                    @case('Eliminar') bg-red-50 text-red-700 @break
                                    @default bg-slate-50 text-slate-600
                                @endswitch">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm text-text-main">{{ $log->description }}</td>
                        <td class="px-5 py-3 text-sm text-text-muted whitespace-nowrap">{{ $log->user_name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">No hay logs registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-2">
        {{ $logs->links() }}
    </div>
</div>
@endsection
