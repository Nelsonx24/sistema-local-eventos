@extends('layout.main')

@section('title', 'Ventas - ' . $event->client_name)
@section('header-title', 'Ventas')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Event Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-6 py-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('sales.index') }}" class="p-2 hover:bg-slate-100 rounded-lg text-slate-400 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-lg text-slate-900">{{ $event->client_name }}</h3>
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-blue-100 text-blue-700 rounded-md border border-blue-200">
                        ID: {{ $event->id }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 italic">Terminal de ventas activo para este evento</p>
            </div>
        </div>
        @if($event->event_status !== 'completed')
        <button onclick="openModal('sale-modal')" class="flex-1 md:flex-none bg-brand-accent text-white px-6 py-2 rounded-xl text-sm font-bold shadow-lg shadow-blue-200 hover:shadow-xl hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Nueva Venta
        </button>
        @endif
    </div>

    <!-- Sales History -->
    <div class="flex flex-col gap-3">
        <div class="flex items-center justify-between px-2">
            <h4 class="text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
                Historial de Ventas del Evento
            </h4>
            <span class="text-[0.75rem] font-bold text-brand-accent bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                Total Acumulado: {{ number_format($sales->sum('amount')) }} Bs
            </span>
        </div>

        <div class="bg-white rounded-lg border border-border-subtle shadow-[0_1px_3px_rgba(0,0,0,0.05)] overflow-hidden">
            <div class="max-h-[500px] overflow-y-auto">
                <table class="w-full border-collapse text-left">
                    <thead class="sticky top-0 z-10 bg-[#f8fafc] border-b border-border-subtle shadow-sm">
                        <tr>
                            <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center w-24">ID</th>
                            <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Comprador</th>
                            <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Vendedor</th>
                            <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Detalle de Compra</th>
                            <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-right">Monto</th>
                            <th class="px-6 py-3 text-[0.7rem] font-bold text-text-muted uppercase tracking-widest text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 italic">
                        @forelse($sales as $sale)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <span class="text-[0.75rem] font-mono font-bold text-brand-accent">#{{ $sale->id }}</span>
                                <p class="text-[0.6rem] text-slate-400">{{ $sale->date }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-800 leading-none">{{ $sale->client_name }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[0.65rem] font-bold text-slate-400 uppercase">{{ $sale->seller_name ?? 'Sistema' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($sale->items as $it)
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200">
                                        {{ $it->quantity }} {{ $it->type }}x {{ $it->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="text-sm font-bold text-slate-900">{{ number_format($sale->amount) }} Bs</p>
                                @if($sale->change_given > 0)
                                <p class="text-[10px] text-emerald-600">Vuelto: {{ number_format($sale->change_given) }} Bs</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if(!$sale->is_printed)
                                    <form method="POST" action="{{ route('sales.print', $sale->id) }}">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-lg transition-all text-slate-400 hover:text-brand-accent hover:bg-blue-50" title="Imprimir Ticket">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-slate-200 text-xs">Impreso</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-4 opacity-10"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                <p class="text-sm font-medium">Aún no se han registrado ventas para este evento.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Close Event -->
    @if($event->event_status !== 'completed')
    <div class="mt-8 pt-8 border-t border-slate-200 flex flex-col items-center gap-4">
        <div class="text-center">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Zona de Seguridad</p>
            <p class="text-[0.65rem] text-slate-400">Una vez cerrado, no se podrán añadir más productos al inventario de este evento</p>
        </div>
        <form method="POST" action="{{ route('events.close', $event->id) }}">
            @csrf
            <button type="submit" class="flex items-center gap-4 px-10 py-4 bg-red-50 text-red-600 rounded-2xl border border-red-100 hover:bg-red-600 hover:text-white transition-all shadow-sm hover:shadow-xl active:scale-95">
                <div class="w-10 h-10 bg-white text-red-600 rounded-xl flex items-center justify-center group-hover:bg-red-700 group-hover:text-white transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-sm font-bold uppercase tracking-tight">Finalizar y Cerrar Evento</p>
                    <p class="text-[10px] opacity-70">Cierra la terminal de ventas para: {{ $event->client_name }}</p>
                </div>
            </button>
        </form>
    </div>
    @endif
</div>

<!-- Sale Modal -->
<div id="sale-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden border border-border-subtle flex flex-col md:flex-row h-[600px]">
        <div class="w-full md:w-1/2 p-6 border-r border-border-subtle flex flex-col gap-4 overflow-y-auto">
            <h3 class="font-bold text-text-main flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-accent"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                Nueva Venta
            </h3>
            
            <form method="POST" action="{{ route('sales.process') }}" id="sale-form">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-[0.65rem] font-bold text-text-muted uppercase">Nombre del Comprador</label>
                        <input type="text" name="client_name" required class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[0.65rem] font-bold text-text-muted uppercase">Tipo de Pago</label>
                        <div class="flex gap-2">
                            @foreach(['Efectivo', 'QR', 'Tarjeta'] as $method)
                            <button type="button" onclick="setPaymentMethod('{{ $method }}')" id="btn-{{ $method }}" class="flex-1 py-2 rounded-lg text-[0.7rem] font-bold border transition-all bg-white text-text-muted border-border-subtle hover:bg-slate-50">
                                {{ $method }}
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="payment_method" id="payment_method" value="Efectivo">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[0.65rem] font-bold text-text-muted uppercase">Agregar Producto</label>
                        <select id="product-select" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none" onchange="showProductImage()">
                            <option value="">Seleccionar...</option>
                            @foreach($inventory as $item)
                            <option value="{{ $item->name }}" 
                                data-price-box="{{ $item->price_per_box }}" 
                                data-price-unit="{{ $item->price_per_unit }}"
                                data-image-box="{{ $item->image_box ?? '' }}"
                                data-image-unit="{{ $item->image_unit ?? '' }}">
                                {{ $item->name }} ({{ $item->boxes }} cajas / {{ $item->loose_units }} uni)
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="product-image-display" class="hidden items-center gap-3 p-3 bg-slate-50 rounded-lg border border-border-subtle">
                        <img id="selected-product-img" src="" alt="Producto" class="w-16 h-16 rounded-lg object-cover border border-slate-200">
                        <div>
                            <p id="selected-product-name" class="text-sm font-bold text-slate-800"></p>
                            <p id="selected-product-type" class="text-xs text-slate-500"></p>
                        </div>
                    </div>

                    <div id="product-options" class="hidden bg-slate-50 p-3 rounded-lg border border-border-subtle flex flex-col gap-3">
                        <div class="flex gap-2">
                            <button type="button" onclick="setSaleType('Caja')" id="btn-type-Caja" class="flex-1 py-1.5 rounded-md text-xs font-bold bg-brand-accent text-white shadow-sm">Caja</button>
                            <button type="button" onclick="setSaleType('Unidad')" id="btn-type-Unidad" class="flex-1 py-1.5 rounded-md text-xs font-bold bg-white text-text-muted border border-border-subtle">Unidad</button>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex-1 flex flex-col gap-1">
                                <label class="text-[0.6rem] font-bold text-text-muted uppercase">Cantidad</label>
                                <input type="number" id="sale-qty" min="1" value="1" class="px-3 py-1.5 bg-white border border-border-subtle rounded-md text-sm outline-none">
                            </div>
                            <button type="button" onclick="addToCart()" class="mt-4 bg-slate-800 text-white p-2 rounded-md hover:bg-slate-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="w-full md:w-1/2 bg-slate-50 flex flex-col h-full">
            <div class="p-4 border-b border-border-subtle flex justify-between items-center bg-white shadow-sm">
                <span class="font-bold text-xs uppercase text-text-muted tracking-widest">Resumen y Cobro</span>
                <button onclick="closeModal('sale-modal')" class="hover:bg-slate-100 p-1 rounded"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
            </div>
            
            <div class="flex-1 p-4 overflow-y-auto flex flex-col gap-2" id="cart-items">
                <!-- Cart items will appear here -->
            </div>

            <div class="p-6 bg-white border-t border-border-subtle mt-auto shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
                <div class="flex flex-col gap-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-[0.7rem] font-bold text-text-muted uppercase">Total a Pagar</span>
                        <span class="text-2xl font-bold text-brand-primary" id="total-display">0 Bs</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                        <span class="text-[0.65rem] font-bold text-emerald-600 uppercase">Monto Recibido</span>
                        <input type="number" id="cash-received" name="cash_received" class="bg-transparent text-xl font-bold text-text-main outline-none w-32 text-right" value="0">
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                        <span class="text-[0.65rem] font-bold text-blue-600 uppercase">Cambio sugerido</span>
                        <span class="text-xl font-bold text-blue-700" id="change-display">0 Bs</span>
                    </div>
                </div>
                <button type="submit" form="sale-form" class="w-full bg-slate-900 text-white py-3.5 rounded-xl font-bold hover:bg-black transition-all shadow-lg active:scale-[0.98]">
                    Confirmar Operación
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentType = 'Caja';
let cart = [];

function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function showProductImage() {
    const select = document.getElementById('product-select');
    const option = select.options[select.selectedIndex];
    const imgDisplay = document.getElementById('product-image-display');
    const img = document.getElementById('selected-product-img');
    const name = document.getElementById('selected-product-name');
    const type = document.getElementById('selected-product-type');
    
    if (option.value) {
        const imageBox = option.dataset.imageBox;
        const imageUnit = option.dataset.imageUnit;
        const showUnit = currentType === 'Unidad';
        
        if (showUnit && imageUnit) {
            img.src = '/storage/' + imageUnit;
            img.style.display = 'block';
        } else if (imageBox) {
            img.src = '/storage/' + imageBox;
            img.style.display = 'block';
        } else if (imageUnit) {
            img.src = '/storage/' + imageUnit;
            img.style.display = 'block';
        } else {
            imgDisplay.classList.add('hidden');
            imgDisplay.classList.remove('flex');
            return;
        }
        name.textContent = option.value;
        type.textContent = showUnit ? 'Imagen por unidad' : 'Imagen por caja';
        imgDisplay.classList.remove('hidden');
        imgDisplay.classList.add('flex');
    } else {
        imgDisplay.classList.add('hidden');
        imgDisplay.classList.remove('flex');
    }
}

function setPaymentMethod(method) {
    document.getElementById('payment_method').value = method;
    document.querySelectorAll('[id^="btn-Efectivo"], [id^="btn-QR"], [id^="btn-Tarjeta"]').forEach(btn => {
        btn.classList.remove('bg-slate-800', 'text-white', 'shadow-sm');
        btn.classList.add('bg-white', 'text-text-muted', 'border-border-subtle');
    });
    document.getElementById('btn-' + method).classList.add('bg-slate-800', 'text-white', 'shadow-sm');
    document.getElementById('btn-' + method).classList.remove('bg-white', 'text-text-muted', 'border-border-subtle');
}

function setSaleType(type) {
    currentType = type;
    document.getElementById('btn-type-Caja').className = type === 'Caja' ? 'flex-1 py-1.5 rounded-md text-xs font-bold bg-brand-accent text-white shadow-sm' : 'flex-1 py-1.5 rounded-md text-xs font-bold bg-white text-text-muted border border-border-subtle';
    document.getElementById('btn-type-Unidad').className = type === 'Unidad' ? 'flex-1 py-1.5 rounded-md text-xs font-bold bg-brand-accent text-white shadow-sm' : 'flex-1 py-1.5 rounded-md text-xs font-bold bg-white text-text-muted border border-border-subtle';
    showProductImage();
}

document.getElementById('product-select').addEventListener('change', function() {
    document.getElementById('product-options').classList.toggle('hidden', !this.value);
});

setPaymentMethod('Efectivo');

function addToCart() {
    const select = document.getElementById('product-select');
    const qty = parseInt(document.getElementById('sale-qty').value) || 1;
    const option = select.options[select.selectedIndex];
    
    if (!option.value) return;
    
    const priceBox = parseFloat(option.dataset.priceBox);
    const priceUnit = parseFloat(option.dataset.priceUnit);
    const price = currentType === 'Caja' ? priceBox : priceUnit;
    const subtotal = price * qty;
    
    cart.push({ name: option.value, quantity: qty, type: currentType, subtotal: subtotal });
    updateCart();
}

function updateCart() {
    const container = document.getElementById('cart-items');
    container.innerHTML = cart.map((item, idx) => `
        <div class="bg-white p-3 rounded-lg border border-border-subtle flex justify-between items-center shadow-sm">
            <div>
                <p class="text-[0.8rem] font-bold text-text-main leading-none">${item.name}</p>
                <p class="text-[0.65rem] text-text-muted font-medium">${item.quantity} ${item.type}(s)</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[0.8rem] font-bold text-brand-accent">${item.subtotal.toLocaleString()} Bs</span>
                <button type="button" onclick="removeFromCart(${idx})" class="text-red-300 hover:text-red-500"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
            </div>
        </div>
    `).join('');
    
    const total = cart.reduce((sum, item) => sum + item.subtotal, 0);
    document.getElementById('total-display').textContent = total.toLocaleString() + ' Bs';
    
    const cash = parseFloat(document.getElementById('cash-received').value) || 0;
    document.getElementById('change-display').textContent = (cash > total ? cash - total : 0).toLocaleString() + ' Bs';
}

document.getElementById('cash-received').addEventListener('input', updateCart);

function removeFromCart(idx) {
    cart.splice(idx, 1);
    updateCart();
}

document.getElementById('sale-form').addEventListener('submit', function(e) {
    if (cart.length === 0) {
        e.preventDefault();
        alert('Agregue productos al carrito');
        return;
    }
    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'items';
    input.value = JSON.stringify(cart);
    this.appendChild(input);
    input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'cash_received';
    input.value = document.getElementById('cash-received').value || '0';
    this.appendChild(input);
});
</script>
@endsection