@extends('layout.main')

@section('title', 'Dashboard - Gran Cañaveral')
@section('header-title', 'Dashboard')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-5">
        <div class="bg-white p-5 rounded-lg border border-border-subtle shadow-[0_1px_3px_rgba(0,0,0,0.05)]">
            <p class="text-[0.75rem] text-text-muted font-bold uppercase tracking-wider mb-2">Eventos este Mes</p>
            <h3 class="text-2xl font-bold text-brand-primary tracking-tight">{{ $eventsThisMonth }}</h3>
            <p class="text-[0.75rem] mt-2 font-medium text-emerald-500">▲ 3 nuevos hoy</p>
        </div>
        <div class="bg-white p-5 rounded-lg border border-border-subtle shadow-[0_1px_3px_rgba(0,0,0,0.05)]">
            <p class="text-[0.75rem] text-text-muted font-bold uppercase tracking-wider mb-2">Alertas Inventario</p>
            <h3 class="text-2xl font-bold text-brand-primary tracking-tight">{{ $inventoryAlerts }}</h3>
            <p class="text-[0.75rem] mt-2 font-medium text-red-500">▼ Bajo stock</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white flex flex-col h-full min-h-[400px] rounded-lg border border-border-subtle shadow-[0_1px_3px_rgba(0,0,0,0.05)]">
            <div class="px-6 py-4 border-b border-border-subtle flex justify-between items-center">
                <h3 class="font-semibold text-text-main">Ventas de Paceña</h3>
            </div>
            <div class="p-6 flex-1">
                <div class="h-[300px] flex items-end justify-around gap-2">
                    @php $maxPacena = max(array_column($chartDataPacena, 'value')) ?: 1; @endphp
                    @foreach($chartDataPacena as $data)
                    <div class="flex flex-col items-center gap-2 max-w-[60px]">
                        <div class="w-full bg-red-600 rounded-t" style="height: {{ $data['value'] > 0 ? ($data['value'] / $maxPacena * 200) : 4 }}px; min-height: 4px;"></div>
                        <span class="text-[0.7rem] text-text-muted">{{ $data['name'] }}</span>
                        <span class="text-[0.65rem] font-bold text-text-muted">{{ $data['value'] }} cajas</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white flex flex-col h-full min-h-[400px] rounded-lg border border-border-subtle shadow-[0_1px_3px_rgba(0,0,0,0.05)]">
            <div class="px-6 py-4 border-b border-border-subtle">
                <h3 class="font-semibold text-text-main">Ventas Huari</h3>
            </div>
            <div class="p-6 flex-1">
                <div class="h-[300px] flex items-end justify-around gap-2">
                    @php $maxHuari = max(array_column($chartDataHuari, 'value')) ?: 1; @endphp
                    @foreach($chartDataHuari as $data)
                    <div class="flex flex-col items-center gap-2 max-w-[60px]">
                        <div class="w-full bg-black rounded-t" style="height: {{ $data['value'] > 0 ? ($data['value'] / $maxHuari * 200) : 4 }}px; min-height: 4px;"></div>
                        <span class="text-[0.7rem] text-text-muted">{{ $data['name'] }}</span>
                        <span class="text-[0.65rem] font-bold text-text-muted">{{ $data['value'] }} cajas</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection