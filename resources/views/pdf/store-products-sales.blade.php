<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas - Tienda</title>
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
        .footer { text-align: center; font-size: 10px; color: #999; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>Reporte de Ventas - Tienda</h1>
    <p class="subtitle">Generado el {{ date('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Cant.</th>
                <th class="text-right">P. Unitario</th>
                <th class="text-right">Total</th>
                <th class="text-right">Ganancia</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
            <tr>
                <td>{{ $sale->product->name }}</td>
                <td class="text-center">{{ $sale->quantity }}</td>
                <td class="text-right">Bs.{{ rtrim(rtrim(number_format($sale->unit_price, 2), '0'), '.') }}</td>
                <td class="text-right">Bs.{{ rtrim(rtrim(number_format($sale->total_amount, 2), '0'), '.') }}</td>
                <td class="text-right">Bs.{{ rtrim(rtrim(number_format($sale->profit, 2), '0'), '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Sin ventas registradas</td></tr>
            @endforelse
        </tbody>
    </table>

    @php
    $totalVentas = $sales->sum('total_amount');
    $totalGanancias = $sales->sum('profit');
    @endphp
    <p class="total">Total Ventas: Bs.{{ rtrim(rtrim(number_format($totalVentas, 2), '0'), '.') }} | Ganancia Total: Bs.{{ rtrim(rtrim(number_format($totalGanancias, 2), '0'), '.') }}</p>

    <div class="footer">
        Gran Cañaveral &copy; {{ date('Y') }}
    </div>
</body>
</html>
