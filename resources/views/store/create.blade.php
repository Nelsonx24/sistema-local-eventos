@extends('layout.main')

@section('title', 'Nuevo Producto - Tienda')
@section('header-title', 'Tienda')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('store.index') }}" class="inline-flex items-center gap-2 text-[0.75rem] font-bold text-text-muted hover:text-brand-accent mb-6 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a Tienda
    </a>

    <div class="bg-white rounded-2xl border border-border-subtle shadow-sm p-8">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-brand-gold/10 text-brand-gold rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-text-main tracking-tight">Nuevo Producto</h2>
                <p class="text-[0.8rem] text-text-muted">Registra un producto para la tienda</p>
            </div>
        </div>

        <form method="POST" action="{{ route('store.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Nombre del Producto</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Costo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-text-muted font-bold text-[0.875rem]">$</span>
                        <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost') }}" required
                            class="w-full pl-8 pr-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Precio de Venta</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-text-muted font-bold text-[0.875rem]">$</span>
                        <input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price') }}" required
                            class="w-full pl-8 pr-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Categoría</label>
                    <input type="text" name="category" value="{{ old('category') }}" required placeholder="Ej: Chocolates, Bebidas..."
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Stock Inicial</label>
                    <input type="number" min="0" name="stock" value="{{ old('stock', 0) }}" required
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Fecha de Caducidad</label>
                    <input type="date" name="expiration_date" value="{{ old('expiration_date') }}"
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Código de Barras</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Opcional"
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-subtle">
                <a href="{{ route('store.index') }}" class="px-6 py-3 text-[0.75rem] font-bold text-text-muted hover:text-text-main transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-3 bg-black text-white rounded-xl text-[0.75rem] font-bold hover:bg-slate-800 transition-all shadow-sm">
                    <span class="text-brand-gold">Registrar Producto</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
