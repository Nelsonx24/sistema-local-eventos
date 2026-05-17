<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index()
    {
        $events = Event::where('status', '!=', 'Cerrado')->orderBy('date')->get();
        return view('sales.index', compact('events'));
    }

    public function show(Event $event)
    {
        $sales = Sale::where('event_id', $event->id)->orderBy('id', 'desc')->get();
        $inventory = Inventory::all();
        return view('sales.show', compact('event', 'sales', 'inventory'));
    }

    public function directSale()
    {
        $inventory = Inventory::all();
        $sales = Sale::where('event_id', 'Venta Directa')->orderBy('id', 'desc')->get();
        return view('sales.direct', compact('inventory', 'sales'));
    }

    public function processSale(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'event_id' => 'nullable|string',
            'payment_method' => 'required|string',
            'cash_received' => 'numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.type' => 'required|in:Caja,Unidad',
        ]);

        $items = $validated['items'];
        $totalAmount = 0;

        foreach ($items as $item) {
            $inventoryItem = Inventory::where('name', $item['name'])->first();
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

        return redirect()->back()->with('success', 'Venta procesada correctamente.');
    }

    public function printTicket(Sale $sale)
    {
        $sale->markAsPrinted();
        return back()->with('success', 'Ticket marcado como impreso.');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return back()->with('success', 'Venta eliminada.');
    }

    public function closeEvent(Event $event)
    {
        $event->update(['status' => 'Cerrado']);
        return back()->with('success', 'Evento cerrado correctamente.');
    }
}