@extends('layout.main')

@section('title', 'Venta Directa')
@section('header-title', 'Ventas')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-6 py-4 bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('sales.index') }}" class="p-2 hover:bg-slate-100 rounded-lg text-slate-400 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-lg text-slate-900">Venta Directa</h3>
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-100 text-slate-600 rounded-md border border-slate-200">
                        Sin evento asociado
                    </span>
                </div>
                <p class="text-xs text-slate-500 italic">Terminal de ventas sin associação a evento</p>
            </div>
        </div>
        <button onclick="openModal('sale-modal')" class="flex-1 md:flex-none bg-brand-accent text-white px-6 py-2 rounded-xl text-sm font-bold shadow-lg shadow-blue-200 hover:shadow-xl hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Nueva Venta
        </button>
    </div>

    <div class="flex flex-col gap-3">
        <div class="flex items-center justify-between px-2">
            <h4 class="text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
                Historial de Ventas Directas
            </h4>
            <span class="text-[0.75rem] font-bold text-brand-accent bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                Total: {{ number_format($sales->sum('amount')) }} Bs
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
                                    <span class="px-2 py-1 rounded text-[10px] font-bold {{ $sale->payment_method === 'Efectivo' ? 'bg-emerald-50 text-emerald-600' : ($sale->payment_method === 'QR' ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-50 text-slate-600') }}">
                                        {{ $sale->payment_method }}
                                    </span>
                                    @if(Auth::guard('staff')->user()->role === 'Administrador')
                                    <form method="POST" action="{{ route('sales.destroy', $sale->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-300 hover:text-red-500 transition-colors" title="Eliminar venta">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400 italic">
                                No hay ventas directas registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Sale Modal -->
<div id="sale-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden border border-border-subtle flex flex-col md:flex-row h-[600px]">
        <div class="w-full md:w-1/2 p-6 border-r border-border-subtle flex flex-col gap-4 overflow-y-auto">
            <div class="flex flex-col gap-1 mb-2">
                <h3 class="font-bold text-text-main flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-brand-accent"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    Nueva Venta Directa
                </h3>
                <div class="px-2 py-1 bg-slate-100 text-slate-600 text-[0.65rem] font-bold uppercase rounded border border-slate-200 flex items-center gap-1.5">
                    Venta sin evento
                </div>
            </div>
            
            <form id="sale-form" method="POST" action="{{ route('sales.process') }}">
                @csrf
                <input type="hidden" name="event_id" value="Venta Directa">
                
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Nombre del Comprador</label>
                        <input type="text" name="client_name" placeholder="Nombre..." required class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none focus:ring-1 focus:ring-brand-accent/30">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Tipo de Pago</label>
                        <div class="flex gap-2">
                            <button type="button" onclick="setPaymentMethod('Efectivo')" class="payment-btn flex-1 py-2 rounded-lg text-[0.7rem] font-bold border transition-all bg-slate-800 text-white border-slate-800 shadow-sm" data-method="Efectivo">Efectivo</button>
                            <button type="button" onclick="setPaymentMethod('QR')" class="payment-btn flex-1 py-2 rounded-lg text-[0.7rem] font-bold border transition-all bg-white text-text-muted border-border-subtle hover:bg-slate-50" data-method="QR">QR</button>
                            <button type="button" onclick="setPaymentMethod('Tarjeta')" class="payment-btn flex-1 py-2 rounded-lg text-[0.7rem] font-bold border transition-all bg-white text-text-muted border-border-subtle hover:bg-slate-50" data-method="Tarjeta">Tarjeta</button>
                        </div>
                        <input type="hidden" name="payment_method" id="payment_method" value="Efectivo">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[0.65rem] font-bold text-text-muted uppercase tracking-widest">Agregar Producto</label>
                        <select id="select-item" class="px-3 py-2 bg-slate-50 border border-border-subtle rounded-lg text-sm outline-none" onchange="showItemOptions()">
                            <option value="">Seleccionar...</option>
                            @foreach($inventory as $i)
                            <option value="{{ $i->name }}" data-price-box="{{ $i->price_per_box }}" data-price-unit="{{ $i->price_per_unit }}" data-image-box="{{ $i->image_box ?? '' }}" data-image-unit="{{ $i->image_unit ?? '' }}">{{ $i->name }} ({{ $i->boxes }} cajas / {{ $i->loose_units }} uni)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="direct-product-image" class="hidden items-center gap-3 p-3 bg-slate-50 rounded-lg border border-border-subtle">
                    <img id="direct-selected-img" src="" alt="Producto" class="w-16 h-16 rounded-lg object-cover border border-slate-200">
                    <div>
                        <p id="direct-product-name" class="text-sm font-bold text-slate-800"></p>
                        <p class="text-xs text-slate-500">Imagen referencial</p>
                    </div>
                </div>

                <div id="item-options" class="hidden bg-slate-50 p-3 rounded-lg border border-border-subtle flex flex-col gap-3">
                    <div class="flex gap-2">
                        <button type="button" onclick="setItemType('Caja')" class="type-btn flex-1 py-1.5 rounded-md text-xs font-bold transition-all bg-brand-accent text-white shadow-sm" data-type="Caja">Caja</button>
                        <button type="button" onclick="setItemType('Unidad')" class="type-btn flex-1 py-1.5 rounded-md text-xs font-bold transition-all bg-white text-text-muted border border-border-subtle" data-type="Unidad">Unidad</button>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex-1 flex flex-col gap-1">
                            <label class="text-[0.6rem] font-bold text-text-muted uppercase">Cantidad</label>
                            <input type="number" id="item-qty" min="1" value="1" class="w-full px-3 py-1.5 bg-white border border-border-subtle rounded-md text-sm outline-none">
                        </div>
                        <button type="button" onclick="addToCart()" class="mt-4 bg-slate-800 text-white p-2 rounded-md hover:bg-slate-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="w-full md:w-1/2 bg-slate-50 flex flex-col h-full">
            <div class="p-4 border-b border-border-subtle flex justify-between items-center bg-white shadow-sm">
                <span class="font-bold text-xs uppercase text-text-muted tracking-widest">Resumen y Cobro</span>
                <button onclick="closeModal('sale-modal')" class="hover:bg-slate-100 p-1 rounded transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            
            <div class="flex-1 p-4 overflow-y-auto flex flex-col gap-2" id="cart-items">
                <div class="flex flex-col items-center justify-center h-full text-text-muted opacity-30" id="empty-cart">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <p class="text-[0.65rem] font-bold mt-2 uppercase">Carrito vacío</p>
                </div>
            </div>

            <div class="p-6 bg-white border-t border-border-subtle mt-auto shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
                <div class="flex flex-col gap-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-[0.7rem] font-bold text-text-muted uppercase tracking-widest">Total a Pagar</span>
                        <span class="text-2xl font-bold text-brand-primary" id="total-display">0 Bs</span>
                    </div>
                    
                    <div class="flex flex-col gap-1 p-3 rounded-xl transition-colors bg-emerald-50 border border-emerald-100" id="cash-section">
                        <label class="text-[0.6rem] font-bold text-text-muted uppercase flex items-center justify-between">
                            <span>Monto Recibido</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-500"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </label>
                        <input type="number" name="cash_received" id="cash_received" class="w-full bg-transparent text-xl font-bold text-text-main outline-none" value="0" onchange="updateChange()">
                    </div>

                    <div class="flex justify-between items-center p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                        <span class="text-[0.65rem] font-bold text-blue-600 uppercase">Cambio sugerido</span>
                        <span class="text-xl font-bold text-blue-700" id="change-display">0 Bs</span>
                    </div>
                </div>

                <button type="button" onclick="submitSale()" class="w-full bg-slate-900 text-white py-3.5 rounded-xl font-bold hover:bg-black transition-all shadow-lg active:scale-[0.98]" id="submit-btn" disabled>
                    Confirmar Operación
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let selectedItem = '';
let selectedType = 'Caja';

function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function setPaymentMethod(method) {
    document.getElementById('payment_method').value = method;
    document.querySelectorAll('.payment-btn').forEach(btn => {
        if (btn.dataset.method === method) {
            btn.className = 'payment-btn flex-1 py-2 rounded-lg text-[0.7rem] font-bold border transition-all bg-slate-800 text-white border-slate-800 shadow-sm';
        } else {
            btn.className = 'payment-btn flex-1 py-2 rounded-lg text-[0.7rem] font-bold border transition-all bg-white text-text-muted border-border-subtle hover:bg-slate-50';
        }
    });
    const cashSection = document.getElementById('cash-section');
    if (method === 'Efectivo') {
        cashSection.className = 'flex flex-col gap-1 p-3 rounded-xl transition-colors bg-emerald-50 border border-emerald-100';
    } else {
        cashSection.className = 'flex flex-col gap-1 p-3 rounded-xl transition-colors bg-slate-50 border border-border-subtle';
    }
}

function showItemOptions() {
    const select = document.getElementById('select-item');
    selectedItem = select.value;
    const option = select.options[select.selectedIndex];
    const imgDisplay = document.getElementById('direct-product-image');
    const img = document.getElementById('direct-selected-img');
    const name = document.getElementById('direct-product-name');
    
    if (selectedItem) {
        document.getElementById('item-options').classList.remove('hidden');
        
        const imageBox = option.dataset.imageBox;
        const imageUnit = option.dataset.imageUnit;
        
        if (imageBox || imageUnit) {
            const imageUrl = imageBox ? '/storage/' + imageBox : '/storage/' + imageUnit;
            img.src = imageUrl;
            img.style.display = 'block';
            name.textContent = selectedItem;
            imgDisplay.classList.remove('hidden');
            imgDisplay.classList.add('flex');
        } else {
            imgDisplay.classList.add('hidden');
            imgDisplay.classList.remove('flex');
        }
    } else {
        document.getElementById('item-options').classList.add('hidden');
        imgDisplay.classList.add('hidden');
        imgDisplay.classList.remove('flex');
    }
}

function setItemType(type) {
    selectedType = type;
    document.querySelectorAll('.type-btn').forEach(btn => {
        if (btn.dataset.type === type) {
            btn.className = 'type-btn flex-1 py-1.5 rounded-md text-xs font-bold transition-all bg-brand-accent text-white shadow-sm';
        } else {
            btn.className = 'type-btn flex-1 py-1.5 rounded-md text-xs font-bold transition-all bg-white text-text-muted border border-border-subtle';
        }
    });
}

function addToCart() {
    if (!selectedItem) return;
    const qty = parseInt(document.getElementById('item-qty').value) || 1;
    const inventory = @json($inventory);
    const item = inventory.find(i => i.name === selectedItem);
    const price = selectedType === 'Caja' ? item.price_per_box : item.price_per_unit;
    const subtotal = price * qty;
    
    cart.push({ name: selectedItem, quantity: qty, type: selectedType, subtotal });
    renderCart();
    
    document.getElementById('select-item').value = '';
    document.getElementById('item-options').classList.add('hidden');
    document.getElementById('item-qty').value = 1;
}

function renderCart() {
    const container = document.getElementById('cart-items');
    const emptyCart = document.getElementById('empty-cart');
    
    if (cart.length === 0) {
        emptyCart.classList.remove('hidden');
        container.innerHTML = '';
        container.appendChild(emptyCart);
    } else {
        emptyCart.classList.add('hidden');
        let html = '';
        cart.forEach((it, idx) => {
            html += `
                <div class="bg-white p-3 rounded-lg border border-border-subtle flex justify-between items-center shadow-sm">
                    <div>
                        <p class="text-[0.8rem] font-bold text-text-main leading-none">${it.name}</p>
                        <p class="text-[0.65rem] text-text-muted font-medium">${it.quantity} ${it.type}(s)</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-[0.8rem] font-bold text-brand-accent">${it.subtotal.toLocaleString()} Bs</span>
                        <button onclick="removeFromCart(${idx})" class="text-red-300 hover:text-red-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }
    updateTotal();
}

function removeFromCart(idx) {
    cart.splice(idx, 1);
    renderCart();
}

function updateTotal() {
    const total = cart.reduce((acc, curr) => acc + curr.subtotal, 0);
    document.getElementById('total-display').textContent = total.toLocaleString() + ' Bs';
    
    const method = document.getElementById('payment_method').value;
    if (method !== 'Efectivo') {
        document.getElementById('cash_received').value = total;
    }
    
    updateChange();
    
    const submitBtn = document.getElementById('submit-btn');
    const cash = parseFloat(document.getElementById('cash_received').value) || 0;
    submitBtn.disabled = cart.length === 0 || (method === 'Efectivo' && cash < total);
}

function updateChange() {
    const total = cart.reduce((acc, curr) => acc + curr.subtotal, 0);
    const cash = parseFloat(document.getElementById('cash_received').value) || 0;
    const change = cash > total ? cash - total : 0;
    document.getElementById('change-display').textContent = change.toLocaleString() + ' Bs';
    updateTotal();
}

function submitSale() {
    if (cart.length === 0) return;
    
    const method = document.getElementById('payment_method').value;
    const cash = method === 'Efectivo' ? parseFloat(document.getElementById('cash_received').value) : 0;
    
    let form = document.getElementById('sale-form');
    let itemsInput = document.createElement('input');
    itemsInput.type = 'hidden';
    itemsInput.name = 'items';
    itemsInput.value = JSON.stringify(cart);
    form.appendChild(itemsInput);
    
    if (method === 'Efectivo') {
        let cashInput = document.createElement('input');
        cashInput.type = 'hidden';
        cashInput.name = 'cash_received';
        cashInput.value = cash;
        form.appendChild(cashInput);
    }
    
    form.submit();
}
</script>
@endsection