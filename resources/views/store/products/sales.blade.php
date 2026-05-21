@extends('layout.main')

@section('title', 'Ventas - Productos')
@section('header-title', 'Tienda')

@section('content')
<div class="flex flex-col gap-6">
    @if(session('success'))
    <div class="px-6 py-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-xl flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="px-6 py-4 bg-red-50 border border-red-200 text-red-600 text-sm font-medium rounded-xl">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-6 py-4 bg-white rounded-lg border border-border-subtle shadow-sm">
        <h3 class="font-semibold text-text-main">Ventas de Productos</h3>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('store.products.sales', ['filter' => 'today']) }}" class="{{ request('filter') === 'today' ? 'bg-brand-gold text-white' : 'bg-white text-slate-700 border border-border-subtle' }} px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-brand-gold/90 hover:text-white flex items-center gap-2 shadow-sm transition-all">
                Hoy
            </a>
            <a href="{{ route('store.products.sales') }}" class="{{ !request('filter') ? 'bg-brand-gold text-white' : 'bg-white text-slate-700 border border-border-subtle' }} px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-brand-gold/90 hover:text-white flex items-center gap-2 shadow-sm transition-all">
                Todos
            </a>
            <a href="{{ route('store.products') }}" class="bg-slate-800 text-white px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-slate-700 flex items-center gap-2 shadow-sm transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
                Productos
            </a>
            <button onclick="openModal('sale-modal')" class="bg-emerald-600 text-white px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-emerald-500 flex items-center gap-2 shadow-sm transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Registrar Venta
            </button>
        </div>
    </div>

    @if($products->isEmpty())
    <div class="bg-white rounded-xl border border-border-subtle shadow-sm p-12 text-center">
        <div class="flex flex-col items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-slate-300"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <p class="text-slate-400 font-medium">No hay productos con stock disponible</p>
            <a href="{{ route('store.products') }}" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-[0.75rem] font-bold hover:bg-slate-700 transition-all">Ir a Productos</a>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg border border-border-subtle shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-[#f8fafc] border-b border-border-subtle">
                    <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Fecha</th>
                    <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Producto</th>
                    <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Cant.</th>
                    <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-right">Precio Uni.</th>
                    <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-right">Total</th>
                    <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-right">Ganancia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f1f5f9]">
                @forelse($sales as $sale)
                <tr class="hover:bg-[#f8fafc] transition-colors">
                    <td class="px-6 py-4">
                        <span class="text-[0.8rem] text-text-muted font-mono">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[0.875rem] font-bold text-text-main">{{ $sale->product->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 bg-brand-gold/10 text-brand-gold rounded-full font-mono font-bold text-[0.8rem]">{{ $sale->quantity }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-[0.8rem] text-text-muted font-mono">Bs.{{ number_format($sale->unit_price, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-[0.875rem] font-bold text-text-main font-mono">Bs.{{ number_format($sale->total_amount, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-[0.8rem] font-bold text-emerald-600 font-mono">+Bs.{{ number_format($sale->profit, 2) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <p class="text-slate-400 font-medium">No hay ventas registradas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sales->links() }}
    </div>
    @endif
</div>

@if($products->isNotEmpty())
<div id="sale-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4" onclick="if(event.target===this)closeModal('sale-modal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-text-main tracking-tight">Registrar Venta</h3>
            <button onclick="closeModal('sale-modal')" class="p-2 text-slate-400 hover:text-text-main rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('store.products.sale.process') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Producto</label>
                <select name="store_product_id" required
                    class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                    <option value="">Seleccionar producto...</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }} — Stock: {{ $product->stock }} — Bs.{{ number_format($product->sale_price, 2) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Cantidad</label>
                <input type="number" name="quantity" min="1" value="1" required
                    class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-border-subtle">
                <button type="button" onclick="closeModal('sale-modal')" class="px-6 py-3 text-[0.75rem] font-bold text-text-muted hover:text-text-main transition-colors">Cancelar</button>
                <button type="submit" class="px-6 py-3 bg-black text-white rounded-xl text-[0.75rem] font-bold hover:bg-slate-800 transition-all shadow-sm">
                    <span class="text-brand-gold">Procesar Venta</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
</script>
@endsection
