@extends('layout.main')

@section('title', 'Editar Producto - Tienda')
@section('header-title', 'Tienda')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('store.products') }}" class="inline-flex items-center gap-2 text-[0.75rem] font-bold text-text-muted hover:text-brand-accent mb-6 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
        Volver a Productos
    </a>

    <div class="bg-white rounded-2xl border border-border-subtle shadow-sm p-8">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-brand-gold/10 text-brand-gold rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-text-main tracking-tight">Editar Producto</h2>
                <p class="text-[0.8rem] text-text-muted">{{ $product->name }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('store.products.update', $product) }}" class="space-y-5" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Nombre del Producto</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Detalle</label>
                    <textarea name="detail" rows="2"
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all resize-none">{{ old('detail', $product->detail) }}</textarea>
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Costo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-text-muted font-bold text-[0.875rem]">Bs.</span>
                        <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $product->cost) }}" required
                            class="w-full pl-11 pr-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Precio de Venta</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-text-muted font-bold text-[0.875rem]">Bs.</span>
                        <input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" required
                            class="w-full pl-11 pr-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Categoría</label>
                    <select name="category" required
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                        <option value="">Seleccionar tipo...</option>
                        @foreach($types as $type)
                        <option value="{{ $type->name }}" @selected(old('category', $product->category) == $type->name)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Stock</label>
                    <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock) }}" required
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Fecha de Caducidad</label>
                    <input type="date" name="expiration_date" value="{{ old('expiration_date', $product->expiration_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Código de Barras</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" placeholder="Opcional"
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-gold/40 focus:border-brand-accent transition-all">
                </div>

                <div>
                    <label class="block text-[0.75rem] font-bold text-text-muted uppercase tracking-widest mb-2">Imagen</label>
                    @if($product->image_url)
                    <div class="mb-2">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded-lg border border-border-subtle">
                    </div>
                    @endif
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                        class="w-full px-4 py-3 bg-white border border-border-subtle rounded-xl text-[0.875rem] text-text-main file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-slate-100 file:text-[0.75rem] file:font-bold file:text-text-muted hover:file:bg-slate-200 transition-all">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-subtle">
                <a href="{{ route('store.products') }}" class="px-6 py-3 text-[0.75rem] font-bold text-text-muted hover:text-text-main transition-colors">Cancelar</a>
                <button type="submit" class="px-6 py-3 bg-black text-white rounded-xl text-[0.75rem] font-bold hover:bg-slate-800 transition-all shadow-sm">
                    <span class="text-brand-gold">Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
