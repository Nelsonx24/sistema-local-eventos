<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Inventory;
use App\Models\Log;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::orderBy('date');

        $filter = $request->get('filter', 'today');
        if ($filter === 'today') {
            $query->whereDate('date', now()->toDateString());
        } elseif ($filter === 'completed') {
            $query->where('event_status', 'completed');
        } else {
            $query->where('event_status', 'upcoming');
        }

        $events = $query->get();

        return view('sales.index', compact('events', 'filter'));
    }

    public function show(Event $event)
    {
        $sales = Sale::where('event_id', $event->id)->orderBy('id', 'desc')->get();
        $inventory = Inventory::all();
        $clientNames = Sale::distinct()->pluck('client_name')->merge(
            Event::distinct()->pluck('client_name')
        )->unique()->sort()->values();

        return view('sales.show', compact('event', 'sales', 'inventory', 'clientNames'));
    }

    public function directSale()
    {
        $inventory = Inventory::all();
        $sales = Sale::where('event_id', 'Venta Directa')->orderBy('id', 'desc')->get();
        $clientNames = Sale::distinct()->pluck('client_name')->merge(
            Event::distinct()->pluck('client_name')
        )->unique()->sort()->values();

        return view('sales.direct', compact('inventory', 'sales', 'clientNames'));
    }

    public function processSale(Request $request)
    {
        $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'event_id' => 'nullable|string',
            'payment_method' => 'required|string',
            'cash_received' => 'numeric|min:0',
        ]);

        if (! $items || ! is_array($items) || count($items) < 1) {
            return back()->withErrors(['items' => 'Debe agregar al menos un producto.']);
        }

        $totalAmount = 0;

        foreach ($items as $item) {
            $inventoryItem = Inventory::where('name', $item['name'])->first();
            if (! $inventoryItem) {
                return back()->withErrors(['items' => "Producto {$item['name']} no encontrado en inventario."]);
            }

            if ($item['type'] === 'Caja' && $inventoryItem->boxes < $item['quantity']) {
                return back()->withInput()->with('stock_error', "Stock insuficiente de {$item['name']}: hay {$inventoryItem->boxes} caja(s), solicitó {$item['quantity']}.")->with('cart_data', $request->items);
            }

            if ($item['type'] !== 'Caja') {
                $totalUnits = ($inventoryItem->boxes * $inventoryItem->units_per_box) + $inventoryItem->loose_units;
                if ($totalUnits < $item['quantity']) {
                    return back()->withInput()->with('stock_error', "Stock insuficiente de {$item['name']}: hay {$totalUnits} unidad(es), solicitó {$item['quantity']}.")->with('cart_data', $request->items);
                }
            }

            if ($inventoryItem) {
                $price = $item['type'] === 'Caja'
                    ? $inventoryItem->price_per_box
                    : $inventoryItem->price_per_unit;
                $totalAmount += $price * $item['quantity'];

                $inventoryItem->subtractStock($item['quantity'], $item['type']);
            }
        }

        $cashReceived = $validated['payment_method'] === 'Efectivo'
            ? ($validated['cash_received'] ?? $totalAmount)
            : $totalAmount;

        $changeGiven = $cashReceived > $totalAmount ? $cashReceived - $totalAmount : 0;

        $sale = Sale::create([
            'event_id' => $validated['event_id'] ?? 'Venta Directa',
            'client_name' => $validated['client_name'],
            'amount' => $totalAmount,
            'cash_received' => $cashReceived,
            'change_given' => $changeGiven,
            'date' => now()->toDateString(),
            'payment_method' => $validated['payment_method'],
            'status' => 'Paid',
            'seller_name' => Auth::user()->name,
            'is_printed' => false,
        ]);

        foreach ($items as $item) {
            $inventoryItem = Inventory::where('name', $item['name'])->first();
            $price = $item['type'] === 'Caja'
                ? $inventoryItem->price_per_box
                : $inventoryItem->price_per_unit;

            SaleItem::create([
                'sale_id' => $sale->id,
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'type' => $item['type'],
                'subtotal' => $price * $item['quantity'],
            ]);
        }

        Log::record('Venta', 'Crear', "Venta de Bs {$totalAmount} a {$validated['client_name']} ({$validated['payment_method']})");

        return redirect()->back()->with('success', 'Venta procesada correctamente.');
    }

    public function printTicket(Sale $sale)
    {
        $sale->markAsPrinted();

        return back()->with('success', 'Ticket marcado como impreso.');
    }

    public function destroy(Sale $sale)
    {
        $clientName = $sale->client_name;
        $amount = $sale->amount;
        $sale->delete();

        Log::record('Venta', 'Eliminar', "Venta de Bs {$amount} a {$clientName} eliminada");

        return back()->with('success', 'Venta eliminada.');
    }

    public function closeEvent(Event $event)
    {
        $event->update(['status' => 'Cerrado']);

        Log::record('Venta', 'Actualizar', "Evento {$event->client_name} cerrado desde ventas");

        return back()->with('success', 'Evento cerrado correctamente.');
    }
}
