<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::orderBy('name')->get();

        return view('inventory.index', compact('inventory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'units_per_box' => 'required|integer|min:1',
            'price_per_box' => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'image_box' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_unit' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $validated['boxes'] = 0;
        $validated['loose_units'] = 0;
        $validated['status'] = 'In Stock';

        if ($request->hasFile('image_box')) {
            $validated['image_box'] = $request->file('image_box')->store('inventory', 'public');
        }

        if ($request->hasFile('image_unit')) {
            $validated['image_unit'] = $request->file('image_unit')->store('inventory', 'public');
        }

        $item = Inventory::create($validated);

        Log::record('Inventario', 'Crear', "Producto {$item->name} registrado en inventario");

        return redirect()->route('inventory.index')->with('success', 'Producto registrado.');
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'category' => 'string',
            'units_per_box' => 'integer|min:1',
            'price_per_box' => 'numeric|min:0',
            'price_per_unit' => 'numeric|min:0',
            'image_box' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_unit' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image_box')) {
            $validated['image_box'] = $request->file('image_box')->store('inventory', 'public');
        }
        if ($request->hasFile('image_unit')) {
            $validated['image_unit'] = $request->file('image_unit')->store('inventory', 'public');
        }

        $inventory->update($validated);

        Log::record('Inventario', 'Actualizar', "Producto {$inventory->name} actualizado");

        return redirect()->route('inventory.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Inventory $inventory)
    {
        $name = $inventory->name;
        $inventory->delete();

        Log::record('Inventario', 'Eliminar', "Producto {$name} eliminado del inventario");

        return redirect()->route('inventory.index')->with('success', 'Producto eliminado.');
    }

    public function restock(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'boxes_to_add' => 'integer|min:0',
            'loose_to_add' => 'integer|min:0',
        ]);

        $inventory = Inventory::find($validated['inventory_id']);
        $inventory->boxes += $validated['boxes_to_add'] ?? 0;
        $inventory->loose_units += $validated['loose_to_add'] ?? 0;
        $inventory->save();

        Log::record('Inventario', 'Actualizar', "Stock reabastecido: {$inventory->name} (+{$validated['boxes_to_add']} cajas, +{$validated['loose_to_add']} unidades)");

        return back()->with('success', 'Stock reabastecido.');
    }

    public function audit(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'physical_boxes' => 'required|integer|min:0',
            'physical_loose' => 'required|integer|min:0',
        ]);

        $inventory = Inventory::find($validated['inventory_id']);
        $inventory->boxes = $validated['physical_boxes'];
        $inventory->loose_units = $validated['physical_loose'];
        $inventory->save();

        Log::record('Inventario', 'Actualizar', "Cotejo realizado para {$inventory->name} ({$validated['physical_boxes']} cajas, {$validated['physical_loose']} unidades)");

        return back()->with('success', 'Cotejo de inventario sincronizado.');
    }

    public function updatePrices(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'price_per_box' => 'required|numeric|min:0',
            'price_per_unit' => 'required|numeric|min:0',
        ]);

        $inventory = Inventory::find($validated['inventory_id']);
        $inventory->update([
            'price_per_box' => $validated['price_per_box'],
            'price_per_unit' => $validated['price_per_unit'],
        ]);

        Log::record('Inventario', 'Actualizar', "Precios actualizados para {$inventory->name}");

        return back()->with('success', 'Precios actualizados.');
    }

    public function downloadPdf()
    {
        $inventory = Inventory::orderBy('name')->get();

        $html = view('pdf.inventory', compact('inventory'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('Inventario_GranCanaveral_'.date('Y-m-d').'.pdf');
    }
}
