@extends('layout.main')

@section('title', 'Nuevo Regalo - Tienda')
@section('header-title', 'Tienda')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('store.gifts') }}" class="inline-flex items-center gap-2 text-[0.75rem] font-bold text-text-muted hover:text-brand-accent mb-6 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a Regalos
    </a>

    <div class="bg-white rounded-2xl border border-border-subtle shadow-sm p-8">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-brand-gold/10 text-brand-gold rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5 4.8 4.8 0 0 1 4.5 5 4.8 4.8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-text-main tracking-tight">Nuevo Regalo</h2>
                <p class="text-[0.8rem] text-text-muted">Registra un artículo de regalo para la tienda</p>
            </div>
        </div>

        <form method="POST" action="{{ route('store.gifts.store') }}" class="space-y-5" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Nombre del Regalo</label>
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
                    <select name="category" required
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                        <option value="">Seleccionar tipo...</option>
                        @foreach($types as $type)
                        <option value="{{ $type->name }}" @selected(old('category') == $type->name)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Stock Inicial</label>
                    <input type="number" min="0" name="stock" value="{{ old('stock', 0) }}" required
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Código de Barras</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Opcional"
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Imagen</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-100 file:text-[0.75rem] file:font-bold file:text-text-muted hover:file:bg-slate-200 transition-all">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-subtle">
                <a href="{{ route('store.gifts') }}" class="px-6 py-3 text-[0.75rem] font-bold text-text-muted hover:text-text-main transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-3 bg-black text-white rounded-xl text-[0.75rem] font-bold hover:bg-slate-800 transition-all shadow-sm">
                    <span class="text-brand-gold">Registrar Regalo</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
