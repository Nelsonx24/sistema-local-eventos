@extends('layout.main')

@section('title', 'Regalos - Tienda')
@section('header-title', 'Tienda')

@section('content')
<div class="flex flex-col gap-6">
    @if(session('success'))
    <div class="lg:col-span-3 px-6 py-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-border-subtle shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-border-subtle bg-[#f8fafc] flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('store.index') }}" class="p-1.5 text-slate-400 hover:text-brand-accent rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
                    </a>
                    <div class="w-10 h-10 bg-brand-gold/10 text-brand-gold rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5 4.8 4.8 0 0 1 4.5 5 4.8 4.8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-text-main tracking-tight">Regalos</h2>
                        <p class="text-[0.7rem] text-text-muted">{{ $gifts->total() }} regalos registrados</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if(Auth::guard('staff')->user()->role === 'Administrador')
                    <button onclick="openModal('types-modal')" class="bg-white text-slate-700 border border-border-subtle px-3 py-2 rounded-[6px] text-[0.7rem] font-bold hover:bg-slate-50 flex items-center gap-1.5 shadow-sm transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>
                        Tipos
                    </button>
                    @endif
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="bg-amber-500 text-white px-3 py-2 rounded-[6px] text-[0.7rem] font-bold hover:bg-amber-400 flex items-center gap-1.5 shadow-sm transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                            Reporte
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute right-0 top-full mt-1 bg-white rounded-xl border border-border-subtle shadow-lg py-1 min-w-[200px] z-40">
                            <a href="{{ route('store.gifts.report') }}" target="_blank" class="block px-4 py-2.5 text-[0.75rem] font-medium text-text-main hover:bg-[#f8fafc] transition-colors">Regalos Actual</a>
                            <a href="{{ route('store.gifts.report.images') }}" target="_blank" class="block px-4 py-2.5 text-[0.75rem] font-medium text-text-main hover:bg-[#f8fafc] transition-colors">Regalos con Imágenes</a>
                            <a href="{{ route('store.gifts.report.sales') }}" target="_blank" class="block px-4 py-2.5 text-[0.75rem] font-medium text-text-main hover:bg-[#f8fafc] transition-colors">Ventas</a>
                        </div>
                    </div>
                    @if(Auth::guard('staff')->user()->role === 'Administrador')
                    <button onclick="openModal('create-modal')" class="bg-black text-white px-3 py-2 rounded-[6px] text-[0.7rem] font-bold hover:bg-slate-800 flex items-center gap-1.5 shadow-sm transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        <span class="text-brand-gold">Nuevo</span>
                    </button>
                    @endif
                </div>
            </div>

            @if($gifts->isEmpty())
            <div class="p-12 text-center">
                <div class="flex flex-col items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-slate-300"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5 4.8 4.8 0 0 1 4.5 5 4.8 4.8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"/></svg>
                    <p class="text-slate-400 font-medium">No hay regalos registrados</p>
                    @if(Auth::guard('staff')->user()->role === 'Administrador')
                    <button onclick="openModal('create-modal')" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-[0.75rem] font-bold hover:bg-slate-700 transition-all">Agregar Regalo</button>
                    @endif
                </div>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-[#f8fafc] border-b border-border-subtle">
                            <th class="px-6 py-3 text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Regalo</th>
                            <th class="px-6 py-3 text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Cat.</th>
                            <th class="px-6 py-3 text-[0.65rem] font-bold text-text-muted uppercase tracking-widest text-right">Costo</th>
                            <th class="px-6 py-3 text-[0.65rem] font-bold text-text-muted uppercase tracking-widest text-right">Venta</th>
                            <th class="px-6 py-3 text-[0.65rem] font-bold text-text-muted uppercase tracking-widest text-center">Stock</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f1f5f9]">
                        @foreach($gifts as $gift)
                        <tr class="hover:bg-[#f8fafc] transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($gift->image_url)
                                    <img src="{{ $gift->image_url }}" alt="" class="w-10 h-10 rounded-lg object-cover border border-border-subtle flex-shrink-0">
                                    @endif
                                    <div>
                                        <p class="text-[0.85rem] font-bold text-text-main leading-none mb-0.5">{{ $gift->name }}</p>
                                        @if($gift->detail)
                                        <p class="text-[0.6rem] text-text-muted truncate max-w-[180px]">{{ $gift->detail }}</p>
                                        @endif
                                        @if($gift->barcode)
                                        <p class="text-[0.6rem] text-text-muted font-mono">{{ $gift->barcode }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[0.7rem] text-text-muted">{{ $gift->category }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-[0.75rem] text-text-muted font-mono font-bold">Bs.{{ rtrim(rtrim(number_format($gift->cost, 2), '0'), '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-[0.75rem] font-bold text-brand-accent font-mono">Bs.{{ rtrim(rtrim(number_format($gift->sale_price, 2), '0'), '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($gift->stock > 5)
                                <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-50 text-emerald-700 rounded-full font-mono font-bold text-[0.75rem]">{{ $gift->stock }}</span>
                                @elseif($gift->stock > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 bg-amber-50 text-amber-700 rounded-full font-mono font-bold text-[0.75rem]">{{ $gift->stock }}</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 bg-red-50 text-red-600 rounded-full font-mono font-bold text-[0.75rem]">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button onclick="showDetail('{{ $gift->id }}', 'gifts')" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Ver Detalles">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    @if(Auth::guard('staff')->user()->role === 'Administrador')
                                    <button onclick="openStockModal('{{ $gift->id }}', '{{ $gift->name }}', 'gifts')" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Agregar Stock">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><line x1="12" x2="12" y1="9" y2="15"/><line x1="9" x2="15" y1="12" y2="12"/></svg>
                                    </button>
                                    <a href="{{ route('store.gifts.edit', $gift) }}" class="p-1.5 text-slate-400 hover:text-brand-accent hover:bg-brand-gold/5 rounded-lg transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('store.gifts.destroy', $gift) }}" onsubmit="return confirm('¿Eliminar {{ $gift->name }}?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="px-6 py-3 border-t border-border-subtle bg-[#f8fafc] flex items-center justify-between text-[0.7rem] text-text-muted">
                <span>{{ $gifts->where('stock', 0)->count() }} sin stock en esta página</span>
                <div class="flex items-center gap-4">
                    <a href="{{ route('store.gifts.sales') }}" class="font-bold text-brand-accent hover:text-brand-gold-dark transition-colors flex items-center gap-1.5">
                        Ver Ventas
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                    {{ $gifts->links() }}
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-border-subtle shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-border-subtle bg-[#f8fafc] flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-black text-text-main tracking-tight">Ventas</h2>
                    <p class="text-[0.7rem] text-text-muted">Últimas ventas de regalos</p>
                </div>
            </div>
            @php
            $recentSales = \App\Models\StoreGiftSale::with('gift')->latest()->take(5)->get();
            @endphp
            @if($recentSales->isEmpty())
            <div class="p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-slate-300 mx-auto mb-2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <p class="text-slate-400 text-[0.8rem] font-medium">Sin ventas aún</p>
            </div>
            @else
            <div class="divide-y divide-[#f1f5f9]">
                @foreach($recentSales as $sale)
                <div class="px-6 py-3.5 flex items-center justify-between hover:bg-[#f8fafc] transition-colors">
                    <div>
                        <p class="text-[0.8rem] font-bold text-text-main leading-none mb-0.5">{{ $sale->gift->name }}</p>
                        <p class="text-[0.65rem] text-text-muted">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }} · {{ $sale->quantity }} uni.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[0.8rem] font-bold text-text-main font-mono">Bs.{{ rtrim(rtrim(number_format($sale->total_amount, 2), '0'), '.') }}</p>
                        <p class="text-[0.65rem] font-bold text-emerald-600 font-mono">+Bs.{{ rtrim(rtrim(number_format($sale->profit, 2), '0'), '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            <a href="{{ route('store.gifts.sales') }}" class="block px-6 py-3.5 border-t border-border-subtle text-center text-[0.75rem] font-bold text-brand-accent hover:bg-[#f8fafc] transition-colors">
                Registrar Venta
            </a>
        </div>
    </div>
</div>

<div id="detail-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('detail-modal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-text-main tracking-tight">Detalles del Regalo</h3>
            <button onclick="closeModal('detail-modal')" class="p-2 text-slate-400 hover:text-text-main rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>
        <div id="detail-content" class="space-y-3 text-[0.85rem]">
        </div>
    </div>
</div>

<div id="stock-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('stock-modal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-text-main tracking-tight">Agregar Stock</h3>
            <button onclick="closeModal('stock-modal')" class="p-2 text-slate-400 hover:text-text-main rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>

        <form id="stock-form" method="POST" action="" class="space-y-5">
            @csrf
            <p id="stock-item-name" class="text-[0.9rem] font-bold text-text-main"></p>

            <div>
                <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Cantidad a agregar</label>
                <input type="number" name="quantity" min="1" value="1" required
                    class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-border-subtle">
                <button type="button" onclick="closeModal('stock-modal')" class="px-6 py-3 text-[0.75rem] font-bold text-text-muted hover:text-text-main transition-colors">Cancelar</button>
                <button type="submit" class="px-6 py-3 bg-black text-white rounded-xl text-[0.75rem] font-bold hover:bg-slate-800 transition-all shadow-sm">
                    <span class="text-brand-gold">Agregar Stock</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="types-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('types-modal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-black text-text-main tracking-tight">Tipos de Regalos</h3>
            <button onclick="closeModal('types-modal')" class="p-2 text-slate-400 hover:text-text-main rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('store.gifts.types.store') }}" class="flex gap-2 mb-5">
            @csrf
            <input type="text" name="name" required placeholder="Nuevo tipo..."
                class="flex-1 px-3 py-2 bg-white border border-border-subtle rounded-lg text-[0.8rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
            <button type="submit" class="px-4 py-2 bg-black text-white rounded-lg text-[0.7rem] font-bold hover:bg-slate-800 transition-all shadow-sm">
                <span class="text-brand-gold">Agregar</span>
            </button>
        </form>

        <div class="space-y-1 max-h-64 overflow-y-auto">
            @forelse($types as $type)
            <div class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-[#f8fafc] transition-colors">
                <span class="text-[0.8rem] font-medium text-text-main">{{ $type->name }}</span>
                <form method="POST" action="{{ route('store.gifts.types.destroy', $type) }}" onsubmit="return confirm('¿Eliminar este tipo?')">
                    @csrf
                    <button class="p-1 text-slate-400 hover:text-red-500 rounded transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                    </button>
                </form>
            </div>
            @empty
            <p class="text-center text-[0.75rem] text-slate-400 py-6">No hay tipos registrados</p>
            @endforelse
        </div>
    </div>
</div>

<div id="create-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4 overflow-y-auto" onclick="if(event.target===this)closeModal('create-modal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-8 p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-text-main tracking-tight">Nuevo Regalo</h3>
            <button onclick="closeModal('create-modal')" class="p-2 text-slate-400 hover:text-text-main rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('store.gifts.store') }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            <div>
                <label class="block text-[0.7rem] font-bold text-text-muted uppercase tracking-widest mb-1.5">Nombre</label>
                <input type="text" name="name" required
                    class="w-full px-4 py-2.5 bg-white border border-border-subtle rounded-xl text-[0.85rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
            </div>

            <div>
                <label class="block text-[0.7rem] font-bold text-text-muted uppercase tracking-widest mb-1.5">Detalle</label>
                <textarea name="detail" rows="2"
                    class="w-full px-4 py-2.5 bg-white border border-border-subtle rounded-xl text-[0.85rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[0.7rem] font-bold text-text-muted uppercase tracking-widest mb-1.5">Costo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-text-muted font-bold text-[0.85rem]">Bs.</span>
                        <input type="number" step="0.01" min="0" name="cost" required
                            class="w-full pl-10 pr-3.5 py-2.5 bg-white border border-border-subtle rounded-xl text-[0.85rem] text-text-main focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-[0.7rem] font-bold text-text-muted uppercase tracking-widest mb-1.5">Precio Venta</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-text-muted font-bold text-[0.85rem]">Bs.</span>
                        <input type="number" step="0.01" min="0" name="sale_price" required
                            class="w-full pl-10 pr-3.5 py-2.5 bg-white border border-border-subtle rounded-xl text-[0.85rem] text-text-main focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[0.7rem] font-bold text-text-muted uppercase tracking-widest mb-1.5">Categoría</label>
                    <select name="category" required
                        class="w-full px-3.5 py-2.5 bg-white border border-border-subtle rounded-xl text-[0.85rem] text-text-main focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                        <option value="">Seleccionar...</option>
                        @foreach($types as $type)
                        <option value="{{ $type->name }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[0.7rem] font-bold text-text-muted uppercase tracking-widest mb-1.5">Stock Inicial</label>
                    <input type="number" min="0" name="stock" value="0"
                        class="w-full px-3.5 py-2.5 bg-white border border-border-subtle rounded-xl text-[0.85rem] text-text-main focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[0.7rem] font-bold text-text-muted uppercase tracking-widest mb-1.5">Código Barras</label>
                    <input type="text" name="barcode" placeholder="Opcional"
                        class="w-full px-3.5 py-2.5 bg-white border border-border-subtle rounded-xl text-[0.85rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>
                <div>
                    <label class="block text-[0.7rem] font-bold text-text-muted uppercase tracking-widest mb-1.5">Imagen</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="w-full px-3.5 py-2.5 bg-white border border-border-subtle rounded-xl text-[0.85rem] text-text-main file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-100 file:text-[0.7rem] file:font-bold file:text-text-muted hover:file:bg-slate-200 transition-all">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-border-subtle">
                <button type="button" onclick="closeModal('create-modal')" class="px-5 py-2.5 text-[0.75rem] font-bold text-text-muted hover:text-text-main transition-colors">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 bg-black text-white rounded-xl text-[0.75rem] font-bold hover:bg-slate-800 transition-all shadow-sm">
                    <span class="text-brand-gold">Registrar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function fmt(n) {
    return parseFloat(n.toFixed(2)).toString();
}

function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
function showDetail(id, type) {
    var route = '/store/' + (type === 'gifts' ? 'regalos/' : 'productos/') + id;
    fetch(route)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var html = '';
            if (data.image_url) html += '<div class=\"flex justify-center py-3\"><img src=\"' + data.image_url + '\" alt=\"\" class=\"w-28 h-28 object-cover rounded-xl border border-border-subtle shadow-sm\"></div>';
            html += '<div class=\"flex justify-between py-2 border-b border-border-subtle\"><span class=\"font-bold text-text-muted\">Nombre</span><span class=\"text-text-main text-right\">' + data.name + '</span></div>';
            if (data.detail) html += '<div class=\"flex justify-between py-2 border-b border-border-subtle\"><span class=\"font-bold text-text-muted\">Detalle</span><span class=\"text-text-main text-right\">' + data.detail + '</span></div>';
            if (data.barcode) html += '<div class=\"flex justify-between py-2 border-b border-border-subtle\"><span class=\"font-bold text-text-muted\">Código de Barras</span><span class=\"text-text-main text-right font-mono\">' + data.barcode + '</span></div>';
            html += '<div class=\"flex justify-between py-2 border-b border-border-subtle\"><span class=\"font-bold text-text-muted\">Categoría</span><span class=\"text-text-main text-right\">' + (data.category || '—') + '</span></div>';
            html += '<div class=\"flex justify-between py-2 border-b border-border-subtle\"><span class=\"font-bold text-text-muted\">Costo</span><span class=\"text-text-main text-right font-mono\">Bs.' + fmt(data.cost) + '</span></div>';
            html += '<div class=\"flex justify-between py-2 border-b border-border-subtle\"><span class=\"font-bold text-text-muted\">Precio de Venta</span><span class=\"text-text-main text-right font-mono font-bold text-brand-accent\">Bs.' + fmt(data.sale_price) + '</span></div>';
            var profit = data.sale_price - data.cost;
            var profitPct = data.cost > 0 ? ((profit / data.cost) * 100).toFixed(1) : 0;
            html += '<div class=\"flex justify-between py-2 border-b border-border-subtle\"><span class=\"font-bold text-text-muted\">Ganancia</span><span class=\"text-text-main text-right font-mono\">Bs.' + fmt(profit) + ' (' + profitPct + '%)</span></div>';
            html += '<div class=\"flex justify-between py-2\"><span class=\"font-bold text-text-muted\">Stock</span><span class=\"text-text-main text-right font-mono font-bold\">' + data.stock + '</span></div>';
            document.getElementById('detail-content').innerHTML = html;
            openModal('detail-modal');
        });
}
function openStockModal(id, name, type) {
    document.getElementById('stock-item-name').textContent = name;
    var form = document.getElementById('stock-form');
    var existing = form.querySelector('input[name="product_id"], input[name="gift_id"]');
    if (existing) existing.remove();
    if (type === 'products') {
        form.action = '{{ route("store.products.add-stock") }}';
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'product_id';
        input.value = id;
        form.appendChild(input);
    } else {
        form.action = '{{ route("store.gifts.add-stock") }}';
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'gift_id';
        input.value = id;
        form.appendChild(input);
    }
    openModal('stock-modal');
}
</script>
@endsection
