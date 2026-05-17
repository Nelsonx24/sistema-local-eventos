@extends('layout.main')

@section('title', 'Otros - Gran Cañaveral')
@section('header-title', 'Otros')

@section('content')
<div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <a href="{{ route('others.qr') }}" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-indigo-400 transition-all text-left flex flex-col gap-4 group">
        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
        </div>
        <div>
            <h3 class="text-lg font-black text-slate-900 tracking-tight">Gestión de QR</h3>
            <p class="text-sm text-slate-500 mt-1">Configura la imagen del código QR para cobros rápidos.</p>
        </div>
    </a>

    <a href="{{ route('others.assets') }}" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-emerald-400 transition-all text-left flex flex-col gap-4 group">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
        </div>
        <div>
            <h3 class="text-lg font-black text-slate-900 tracking-tight">Activos de Salón</h3>
            <p class="text-sm text-slate-500 mt-1">Inventario de cocina y cantina. Registra activos fijos.</p>
        </div>
    </a>

    <a href="{{ route('others.contract-settings') }}" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-indigo-400 transition-all text-left flex flex-col gap-4 group">
        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div>
            <h3 class="text-lg font-black text-slate-900 tracking-tight">Datos del Contrato</h3>
            <p class="text-sm text-slate-500 mt-1">Configura los datos del salón y representante legal.</p>
        </div>
    </a>

    <a href="{{ route('others.notifications') }}" class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-blue-400 transition-all text-left flex flex-col gap-4 group">
        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        </div>
        <div>
            <h3 class="text-lg font-black text-slate-900 tracking-tight">Notificaciones Móviles</h3>
            <p class="text-sm text-slate-500 mt-1">Configura avisos en tiempo real para el CM y personal.</p>
        </div>
    </a>
</div>
@endsection