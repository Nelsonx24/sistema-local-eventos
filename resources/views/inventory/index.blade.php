@extends('layout.main')

@section('title', 'Inventario - Gran Cañaveral')
@section('header-title', 'Inventario')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-6 py-4 bg-white rounded-lg border border-border-subtle shadow-sm">
        <h3 class="font-semibold text-text-main">Administración de Inventario</h3>
        @if(Auth::guard('staff')->user()->role === 'Administrador')
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('inventory.pdf') }}" target="_blank" class="bg-amber-500 text-white px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-amber-400 flex items-center gap-2 shadow-sm transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Descargar Inventario
            </a>
            <button onclick="openModal('audit-modal')" class="bg-purple-600 text-white px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-purple-500 flex items-center gap-2 shadow-sm transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Cotejo Físico
            </button>
            <button onclick="openModal('register-modal')" class="bg-slate-800 text-white px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-slate-700 flex items-center gap-2 shadow-sm transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Registrar Nuevo Producto
            </button>
            <button onclick="openModal('stock-modal')" class="bg-emerald-600 text-white px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-emerald-500 flex items-center gap-2 shadow-sm transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                Reabastecer Stock
            </button>
            <button onclick="openModal('prices-modal')" class="bg-brand-gold text-white px-4 py-2 rounded-[6px] text-[0.75rem] font-bold hover:bg-brand-gold-dark flex items-center gap-2 shadow-sm transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Actualizar Precios
            </button>
        </div>
        @endif
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg border border-border-subtle shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-[#f8fafc] border-b border-border-subtle">
                    <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest w-[30%]">Producto</th>
                    <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Cajas</th>
                    <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Unidades</th>
                    <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Precios (Caja/Uni)</th>
                    <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Estado</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f1f5f9]">
                @forelse($inventory as $item)
                <tr class="hover:bg-[#f8fafc] transition-colors" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-category="{{ $item->category }}" data-boxes="{{ $item->boxes }}" data-loose="{{ $item->loose_units }}" data-units-per-box="{{ $item->units_per_box }}" data-price-box="{{ $item->price_per_box }}" data-price-unit="{{ $item->price_per_unit }}" data-image-box="{{ $item->image_box ? asset('storage/'.$item->image_box) : '' }}" data-image-unit="{{ $item->image_unit ? asset('storage/'.$item->image_unit) : '' }}" data-total-units="{{ $item->total_units }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($item->image_box || $item->image_unit)
                            <img src="{{ $item->image_box ? asset('storage/'.$item->image_box) : asset('storage/'.$item->image_unit) }}" alt="{{ $item->name }}" class="w-12 h-12 rounded-lg object-cover border border-slate-200">
                            @else
                            <div class="p-2 bg-slate-100 rounded-lg text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                            </div>
                            @endif
                            <div>
                                <p class="text-[0.875rem] font-bold text-text-main leading-none mb-1">{{ $item->name }}</p>
                                <p class="text-[0.7rem] text-text-muted">{{ $item->category }} • {{ $item->units_per_box }} uni/caja</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-brand-gold/10 text-brand-gold rounded-full font-mono font-bold text-[0.875rem]">
                            {{ $item->boxes }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 rounded-full font-mono font-bold text-[0.875rem]">
                            {{ $item->loose_units }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-[0.8rem] font-bold text-text-main">${{ rtrim(rtrim(number_format($item->price_per_box, 2), '0'), '.') }} <small class="text-text-muted font-normal">/caja</small></span>
                            <span class="text-[0.8rem] font-bold text-brand-accent">${{ rtrim(rtrim(number_format($item->price_per_unit, 2), '0'), '.') }} <small class="text-text-muted font-normal">/unidad</small></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-[0.7rem] font-semibold {{ $item->boxes <= 2 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                            {{ $item->boxes <= 2 ? 'Stock Bajo' : 'Disponible' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if(Auth::guard('staff')->user()->role === 'Administrador')
                        <div class="flex justify-end gap-2 text-text-muted">
                            <button onclick="viewItem({{ $item->id }})" class="p-1 hover:text-brand-accent transition-colors" title="Ver detalles">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <button onclick="editItem('{{ $item->id }}', '{{ addslashes($item->name) }}', '{{ $item->category }}', '{{ $item->units_per_box }}', '{{ $item->price_per_box }}', '{{ $item->price_per_unit }}')" class="p-1 hover:text-amber-600 transition-colors" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                            </button>
                            <form method="POST" action="{{ route('inventory.destroy', $item->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 hover:text-red-500 transition-colors" title="Eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">
                        No hay productos en inventario.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Register Modal -->
<div id="register-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 class="font-bold text-text-main flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Registrar Nuevo Producto
            </h3>
            <button onclick="closeModal('register-modal')"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('inventory.store') }}" class="p-6 flex flex-col gap-4" enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col gap-1.5 text-left">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase">Nombre del Producto</label>
                <input required name="name" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4 text-left">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Categoría</label>
                    <select name="category" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                        <option value="Bebidas">Bebidas</option>
                        <option value="Consumibles">Consumibles</option>
                        <option value="Mobiliario">Mobiliario</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Uni x Caja</label>
                    <input type="number" name="units_per_box" required class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-left">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Precio Caja ($)</label>
                    <input type="number" step="any" name="price_per_box" required class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Precio Unidad ($)</label>
                    <input type="number" step="any" name="price_per_unit" required class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-left">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Imagen por Caja</label>
                    <input type="file" name="image_box" accept="image/*" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Imagen por Unidad</label>
                    <input type="file" name="image_unit" accept="image/*" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-slate-800 text-white py-2.5 rounded-lg font-bold">Crear Ficha de Producto</button>
        </form>
    </div>
</div>

<!-- Stock Modal -->
<div id="stock-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-emerald-50">
            <h3 class="font-bold text-emerald-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                Reabastecer Stock
            </h3>
            <button onclick="closeModal('stock-modal')"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('inventory.restock') }}" class="p-6 flex flex-col gap-4 text-left">
            @csrf
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase">Seleccionar Producto</label>
                <select required name="inventory_id" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                    <option value="">Elegir producto...</option>
                    @foreach($inventory as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} (Actual: {{ $item->boxes }} cajas / {{ $item->loose_units }} uni)</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Cajas a Añadir</label>
                    <input type="number" name="boxes_to_add" value="0" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Uni a Añadir</label>
                    <input type="number" name="loose_to_add" value="0" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-emerald-600 text-white py-2.5 rounded-lg font-bold">Sumar al Inventario</button>
        </form>
    </div>
</div>

<!-- Prices Modal -->
<div id="prices-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-brand-gold/10">
            <h3 class="font-bold text-brand-gold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Actualizar Precios
            </h3>
            <button onclick="closeModal('prices-modal')"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        </div>
        <form method="POST" action="{{ route('inventory.prices') }}" class="p-6 flex flex-col gap-4 text-left">
            @csrf
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase">Producto</label>
                <select required name="inventory_id" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                    <option value="">Elegir producto...</option>
                    @foreach($inventory as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Precio Caja ($)</label>
                    <input type="number" step="any" name="price_per_box" required class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Precio Unidad ($)</label>
                    <input type="number" step="any" name="price_per_unit" required class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-brand-gold text-white py-2.5 rounded-lg font-bold hover:bg-brand-gold-dark transition-all">Publicar Nuevos Precios</button>
        </form>
    </div>
</div>

<!-- Audit Modal -->
<div id="audit-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-purple-200">
        <div class="flex justify-between items-center px-6 py-4 border-b border-purple-200 bg-purple-50">
            <h3 class="font-bold text-purple-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Cotejo de Inventario Físico
            </h3>
            <button onclick="closeModal('audit-modal')" class="text-purple-600 hover:text-purple-800">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('inventory.audit') }}" class="p-6 flex flex-col gap-4 text-left">
            @csrf
            <div class="flex flex-col gap-1.5">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase">Producto a Cotejar</label>
                <select required name="inventory_id" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" onchange="updateAuditFields()">
                    <option value="">Elegir producto...</option>
                    @foreach($inventory as $item)
                    <option value="{{ $item->id }}" data-boxes="{{ $item->boxes }}" data-loose="{{ $item->loose_units }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Cajas (Físico)</label>
                    <input type="number" name="physical_boxes" required min="0" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" id="audit_boxes" value="0">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Unidades (Físico)</label>
                    <input type="number" name="physical_loose" required min="0" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm" id="audit_loose" value="0">
                </div>
            </div>
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <p class="text-[0.7rem] text-amber-700 font-medium">El sistema comparará el conteo físico con el registro actual y actualizará el inventario automáticamente.</p>
            </div>
            <button type="submit" class="mt-4 bg-purple-600 text-white py-2.5 rounded-lg font-bold hover:bg-purple-700">Confirmar Cotejo</button>
        </form>
    </div>
</div>

<!-- View Detail Modal -->
<div id="view-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-slate-50">
            <h3 class="font-bold text-text-main flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                Detalles del Producto
            </h3>
            <button onclick="closeModal('view-modal')"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        </div>
        <div class="p-6 flex flex-col gap-5" id="view-content">
            <div class="flex items-center gap-4">
                <div>
                    <p id="view-name" class="text-lg font-bold text-text-main"></p>
                    <p id="view-category" class="text-sm text-text-muted"></p>
                </div>
            </div>
            <div class="flex gap-4" id="view-images">
                <div class="flex-1">
                    <p class="text-[0.6rem] font-bold text-text-muted uppercase tracking-wider mb-1">Imagen Caja</p>
                    <div id="view-image-box-placeholder" class="p-4 bg-slate-100 rounded-lg text-slate-500 inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    </div>
                    <img id="view-image-box" class="w-20 h-20 rounded-xl object-cover border border-slate-200 hidden" alt="">
                </div>
                <div class="flex-1">
                    <p class="text-[0.6rem] font-bold text-text-muted uppercase tracking-wider mb-1">Imagen Unidad</p>
                    <div id="view-image-unit-placeholder" class="p-4 bg-slate-100 rounded-lg text-slate-500 inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    </div>
                    <img id="view-image-unit" class="w-20 h-20 rounded-xl object-cover border border-slate-200 hidden" alt="">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-[0.6rem] font-bold text-text-muted uppercase tracking-wider">Cajas</p>
                    <p id="view-boxes" class="text-xl font-bold text-brand-gold"></p>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-[0.6rem] font-bold text-text-muted uppercase tracking-wider">Unidades</p>
                    <p id="view-loose" class="text-xl font-bold text-amber-700"></p>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-[0.6rem] font-bold text-text-muted uppercase tracking-wider">Uni x Caja</p>
                    <p id="view-units-per-box" class="text-lg font-bold text-text-main"></p>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-[0.6rem] font-bold text-text-muted uppercase tracking-wider">Total Unidades</p>
                    <p id="view-total-units" class="text-lg font-bold text-text-main"></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-[0.6rem] font-bold text-text-muted uppercase tracking-wider">Precio por Caja</p>
                    <p id="view-price-box" class="text-lg font-bold text-text-main"></p>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-[0.6rem] font-bold text-text-muted uppercase tracking-wider">Precio por Unidad</p>
                    <p id="view-price-unit" class="text-lg font-bold text-brand-accent"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md border border-border-subtle">
        <div class="flex justify-between items-center px-6 py-4 border-b border-border-subtle bg-amber-50">
            <h3 class="font-bold text-amber-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                Editar Producto
            </h3>
            <button onclick="closeModal('edit-modal')"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        </div>
        <form method="POST" action="" class="p-6 flex flex-col gap-4" id="edit-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="flex flex-col gap-1.5 text-left">
                <label class="text-[0.65rem] font-bold text-text-muted uppercase">Nombre del Producto</label>
                <input required name="name" id="edit-name" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4 text-left">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Categoría</label>
                    <select name="category" id="edit-category" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                        <option value="Bebidas">Bebidas</option>
                        <option value="Consumibles">Consumibles</option>
                        <option value="Mobiliario">Mobiliario</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Uni x Caja</label>
                    <input type="number" name="units_per_box" required id="edit-units-per-box" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-left">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Precio Caja ($)</label>
                    <input type="number" step="any" name="price_per_box" required id="edit-price-box" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Precio Unidad ($)</label>
                    <input type="number" step="any" name="price_per_unit" required id="edit-price-unit" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-left">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Imagen por Caja</label>
                    <input type="file" name="image_box" accept="image/*" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-[0.65rem] font-bold text-text-muted uppercase">Imagen por Unidad</label>
                    <input type="file" name="image_unit" accept="image/*" class="px-4 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-amber-600 text-white py-2.5 rounded-lg font-bold hover:bg-amber-500">Guardar Cambios</button>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function updateAuditFields() {
    const select = document.querySelector('select[name="inventory_id"]');
    const option = select.options[select.selectedIndex];
    if (option.value) {
        document.getElementById('audit_boxes').value = option.dataset.boxes || 0;
        document.getElementById('audit_loose').value = option.dataset.loose || 0;
    } else {
        document.getElementById('audit_boxes').value = 0;
        document.getElementById('audit_loose').value = 0;
    }
}

function viewItem(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (!row) return;
    document.getElementById('view-name').textContent = row.dataset.name;
    document.getElementById('view-category').textContent = row.dataset.category;
    document.getElementById('view-boxes').textContent = row.dataset.boxes;
    document.getElementById('view-loose').textContent = row.dataset.loose;
    document.getElementById('view-units-per-box').textContent = row.dataset.unitsPerBox;
    document.getElementById('view-total-units').textContent = row.dataset.totalUnits;
    document.getElementById('view-price-box').textContent = '$' + parseFloat(row.dataset.priceBox);
    document.getElementById('view-price-unit').textContent = '$' + parseFloat(row.dataset.priceUnit);

    ['box', 'unit'].forEach(type => {
        const img = document.getElementById('view-image-' + type);
        const placeholder = document.getElementById('view-image-' + type + '-placeholder');
        const url = row.dataset['image' + (type === 'box' ? 'Box' : 'Unit')];
        if (url) {
            img.src = url;
            img.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else {
            img.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    });

    openModal('view-modal');
}

function editItem(id, name, category, unitsPerBox, priceBox, priceUnit) {
    document.getElementById('edit-form').action = '{{ url("inventory") }}/' + id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-category').value = category;
    document.getElementById('edit-units-per-box').value = unitsPerBox;
    document.getElementById('edit-price-box').value = priceBox;
    document.getElementById('edit-price-unit').value = priceUnit;
    openModal('edit-modal');
}
</script>
@endsection
