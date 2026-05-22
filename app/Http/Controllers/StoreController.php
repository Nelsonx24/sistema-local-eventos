<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\StoreGift;
use App\Models\StoreGiftSale;
use App\Models\StoreGiftType;
use App\Models\StoreProduct;
use App\Models\StoreProductType;
use App\Models\StoreSale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function index()
    {
        $productCount = StoreProduct::count();
        $giftCount = StoreGift::count();

        $recentProductSales = StoreSale::with('product')->latest()->take(5)->get();
        $recentGiftSales = StoreGiftSale::with('gift')->latest()->take(5)->get();

        return view('store.index', compact('productCount', 'giftCount', 'recentProductSales', 'recentGiftSales'));
    }

    public function products()
    {
        $types = StoreProductType::orderBy('name')->get();
        $products = StoreProduct::orderBy('name')->paginate(6);

        return view('store.products.index', compact('products', 'types'));
    }

    public function productShow(StoreProduct $storeProduct)
    {
        return response()->json($storeProduct->only([
            'id', 'name', 'detail', 'cost', 'sale_price', 'stock',
            'expiration_date', 'barcode', 'category', 'product_type_id', 'image_url',
        ]));
    }

    public function productCreate()
    {
        $types = StoreProductType::orderBy('name')->get();

        return view('store.products.create', compact('types'));
    }

    public function productStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string|max:1000',
            'cost' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'expiration_date' => 'nullable|date',
            'stock' => 'required|integer|min:0',
            'barcode' => 'nullable|string|max:100|unique:store_products,barcode',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('store/products', 'public');
        }

        $product = StoreProduct::create($validated);

        Log::record('Tienda', 'Crear', "Producto {$product->name} registrado en tienda");

        return redirect()->route('store.products')->with('success', 'Producto registrado en tienda.');
    }

    public function productEdit(StoreProduct $storeProduct)
    {
        $types = StoreProductType::orderBy('name')->get();
        $product = $storeProduct;

        return view('store.products.edit', compact('product', 'types'));
    }

    public function productUpdate(Request $request, StoreProduct $storeProduct)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string|max:1000',
            'cost' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'expiration_date' => 'nullable|date',
            'stock' => 'required|integer|min:0',
            'barcode' => 'nullable|string|max:100|unique:store_products,barcode,'.$storeProduct->id,
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($storeProduct->image && str_starts_with($storeProduct->image, 'store/products/')) {
                Storage::disk('public')->delete($storeProduct->image);
            }
            $validated['image'] = $request->file('image')->store('store/products', 'public');
        }

        $storeProduct->update($validated);

        Log::record('Tienda', 'Actualizar', "Producto {$storeProduct->name} actualizado");

        return redirect()->route('store.products')->with('success', 'Producto actualizado.');
    }

    public function productDestroy(StoreProduct $storeProduct)
    {
        $name = $storeProduct->name;
        $storeProduct->delete();

        Log::record('Tienda', 'Eliminar', "Producto {$name} eliminado de tienda");

        return redirect()->route('store.products')->with('success', 'Producto eliminado.');
    }

    public function productSales(Request $request)
    {
        $products = StoreProduct::where('stock', '>', 0)->orderBy('name')->get();
        $query = StoreSale::with('product');
        if ($request->query('filter') === 'today') {
            $query->whereDate('date', today());
        }
        $sales = $query->latest()->paginate(20);

        return view('store.products.sales', compact('products', 'sales'));
    }

    public function productProcessSale(Request $request)
    {
        $validated = $request->validate([
            'store_product_id' => 'required|exists:store_products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = StoreProduct::findOrFail($validated['store_product_id']);

        if ($product->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stock insuficiente. Disponible: '.$product->stock]);
        }

        $quantity = $validated['quantity'];
        $totalAmount = $quantity * $product->sale_price;
        $profit = $quantity * ($product->sale_price - $product->cost);

        StoreSale::create([
            'store_product_id' => $product->id,
            'quantity' => $quantity,
            'unit_cost' => $product->cost,
            'unit_price' => $product->sale_price,
            'total_amount' => $totalAmount,
            'profit' => $profit,
            'date' => now()->toDateString(),
        ]);

        $product->decrement('stock', $quantity);

        Log::record('Tienda', 'Vender', "Venta de {$quantity} {$product->name} por Bs.{$totalAmount}");

        return redirect()->route('store.products.sales')->with('success', "Venta de {$quantity} {$product->name} registrada.");
    }

    public function gifts()
    {
        $types = StoreGiftType::orderBy('name')->get();
        $gifts = StoreGift::orderBy('name')->paginate(6);

        return view('store.gifts.index', compact('gifts', 'types'));
    }

    public function giftShow(StoreGift $storeGift)
    {
        return response()->json($storeGift->only([
            'id', 'name', 'detail', 'cost', 'sale_price', 'stock',
            'barcode', 'category', 'gift_type_id', 'image_url',
        ]));
    }

    public function giftCreate()
    {
        $types = StoreGiftType::orderBy('name')->get();

        return view('store.gifts.create', compact('types'));
    }

    public function giftStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string|max:1000',
            'cost' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'barcode' => 'nullable|string|max:100|unique:store_gifts,barcode',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('store/gifts', 'public');
        }

        $gift = StoreGift::create($validated);

        Log::record('Tienda', 'Crear', "Regalo {$gift->name} registrado en tienda");

        return redirect()->route('store.gifts')->with('success', 'Regalo registrado en tienda.');
    }

    public function giftEdit(StoreGift $storeGift)
    {
        $types = StoreGiftType::orderBy('name')->get();
        $gift = $storeGift;

        return view('store.gifts.edit', compact('gift', 'types'));
    }

    public function giftUpdate(Request $request, StoreGift $storeGift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string|max:1000',
            'cost' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'barcode' => 'nullable|string|max:100|unique:store_gifts,barcode,'.$storeGift->id,
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($storeGift->image && str_starts_with($storeGift->image, 'store/gifts/')) {
                Storage::disk('public')->delete($storeGift->image);
            }
            $validated['image'] = $request->file('image')->store('store/gifts', 'public');
        }

        $storeGift->update($validated);

        Log::record('Tienda', 'Actualizar', "Regalo {$storeGift->name} actualizado");

        return redirect()->route('store.gifts')->with('success', 'Regalo actualizado.');
    }

    public function giftDestroy(StoreGift $storeGift)
    {
        $name = $storeGift->name;
        $storeGift->delete();

        Log::record('Tienda', 'Eliminar', "Regalo {$name} eliminado de tienda");

        return redirect()->route('store.gifts')->with('success', 'Regalo eliminado.');
    }

    public function giftSales(Request $request)
    {
        $gifts = StoreGift::where('stock', '>', 0)->orderBy('name')->get();
        $query = StoreGiftSale::with('gift');
        if ($request->query('filter') === 'today') {
            $query->whereDate('date', today());
        }
        $sales = $query->latest()->paginate(20);

        return view('store.gifts.sales', compact('gifts', 'sales'));
    }

    public function giftProcessSale(Request $request)
    {
        $validated = $request->validate([
            'store_gift_id' => 'required|exists:store_gifts,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $gift = StoreGift::findOrFail($validated['store_gift_id']);

        if ($gift->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stock insuficiente. Disponible: '.$gift->stock]);
        }

        $quantity = $validated['quantity'];
        $totalAmount = $quantity * $gift->sale_price;
        $profit = $quantity * ($gift->sale_price - $gift->cost);

        StoreGiftSale::create([
            'store_gift_id' => $gift->id,
            'quantity' => $quantity,
            'unit_cost' => $gift->cost,
            'unit_price' => $gift->sale_price,
            'total_amount' => $totalAmount,
            'profit' => $profit,
            'date' => now()->toDateString(),
        ]);

        $gift->decrement('stock', $quantity);

        Log::record('Tienda', 'Vender', "Venta de {$quantity} {$gift->name} por Bs.{$totalAmount}");

        return redirect()->route('store.gifts.sales')->with('success', "Venta de {$quantity} {$gift->name} registrada.");
    }

    public function productAddStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:store_products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = StoreProduct::findOrFail($validated['product_id']);
        $product->increment('stock', $validated['quantity']);

        Log::record('Tienda', 'Actualizar', "Stock agregado: {$validated['quantity']} a {$product->name}");

        return back()->with('success', "{$validated['quantity']} unidades agregadas a {$product->name}.");
    }

    public function giftAddStock(Request $request)
    {
        $validated = $request->validate([
            'gift_id' => 'required|exists:store_gifts,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $gift = StoreGift::findOrFail($validated['gift_id']);
        $gift->increment('stock', $validated['quantity']);

        Log::record('Tienda', 'Actualizar', "Stock agregado: {$validated['quantity']} a {$gift->name}");

        return back()->with('success', "{$validated['quantity']} unidades agregadas a {$gift->name}.");
    }

    public function productReport()
    {
        $products = StoreProduct::orderBy('name')->get();

        $html = view('pdf.store-products', compact('products'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('Inventario_Tienda_'.date('Y-m-d').'.pdf');
    }

    public function productImagesReport()
    {
        $products = StoreProduct::whereNotNull('image')->orderBy('name')->get();

        $html = view('pdf.store-products-images', compact('products'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('Productos_con_Imagen_'.date('Y-m-d').'.pdf');
    }

    public function productSalesReport()
    {
        $sales = StoreSale::with('product')->latest('date')->get();

        $html = view('pdf.store-products-sales', compact('sales'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('Ventas_Tienda_'.date('Y-m-d').'.pdf');
    }

    public function giftReport()
    {
        $gifts = StoreGift::orderBy('name')->get();

        $html = view('pdf.store-gifts', compact('gifts'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('Inventario_Regalos_'.date('Y-m-d').'.pdf');
    }

    public function giftImagesReport()
    {
        $gifts = StoreGift::whereNotNull('image')->orderBy('name')->get();

        $html = view('pdf.store-gifts-images', compact('gifts'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('Regalos_con_Imagen_'.date('Y-m-d').'.pdf');
    }

    public function giftSalesReport()
    {
        $sales = StoreGiftSale::with('gift')->latest('date')->get();

        $html = view('pdf.store-gifts-sales', compact('sales'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('Ventas_Regalos_'.date('Y-m-d').'.pdf');
    }

    public function productTypeStore(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);

        StoreProductType::create($validated);

        Log::record('Tienda', 'Crear', "Tipo de producto creado: {$validated['name']}");

        return back()->with('success', 'Tipo creado correctamente.');
    }

    public function productTypeDestroy(StoreProductType $storeProductType)
    {
        $name = $storeProductType->name;
        $storeProductType->delete();

        Log::record('Tienda', 'Actualizar', "Tipo de producto eliminado: {$name}");

        return back()->with('success', 'Tipo eliminado correctamente.');
    }

    public function giftTypeStore(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);

        StoreGiftType::create($validated);

        Log::record('Tienda', 'Crear', "Tipo de regalo creado: {$validated['name']}");

        return back()->with('success', 'Tipo creado correctamente.');
    }

    public function giftTypeDestroy(StoreGiftType $storeGiftType)
    {
        $name = $storeGiftType->name;
        $storeGiftType->delete();

        Log::record('Tienda', 'Actualizar', "Tipo de regalo eliminado: {$name}");

        return back()->with('success', 'Tipo eliminado correctamente.');
    }
}
