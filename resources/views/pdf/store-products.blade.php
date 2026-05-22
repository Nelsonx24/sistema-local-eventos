<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventario Tienda</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 5px; }
        .subtitle { text-align: center; font-size: 12px; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #000; color: #D4AF37; padding: 8px 6px; font-size: 10px; text-transform: uppercase; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total { font-weight: bold; margin-top: 15px; text-align: right; font-size: 13px; }
        .category-header { background: #f5f5f5; font-weight: bold; font-size: 12px; padding: 8px 6px; }
        .footer { text-align: center; font-size: 10px; color: #999; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>Inventario - Tienda</h1>
    <p class="subtitle">Generado el {{ date('d/m/Y H:i') }}</p>

    @php
    $grouped = $products->groupBy('category');
    @endphp

    @foreach($grouped as $category => $items)
    <table>
        <thead>
            <tr>
                <th colspan="6" class="category-header">{{ $category }} ({{ $items->count() }})</th>
            </tr>
            <tr>
                <th>Producto</th>
                <th class="text-right">Costo</th>
                <th class="text-right">Precio Venta</th>
                <th class="text-center">Stock</th>
                <th>Caducidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td class="text-right">Bs.{{ rtrim(rtrim(number_format($product->cost, 2), '0'), '.') }}</td>
                <td class="text-right">Bs.{{ rtrim(rtrim(number_format($product->sale_price, 2), '0'), '.') }}</td>
                <td class="text-center">{{ $product->stock }}</td>
                <td>{{ $product->expiration_date ? \Carbon\Carbon::parse($product->expiration_date)->format('d/m/Y') : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    @endforeach

    <p class="total">Total productos: {{ $products->count() }}</p>

    <div class="footer">
        Gran Cañaveral &copy; {{ date('Y') }}
    </div>
</body>
</html>
