<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inventario - Gran Cañaveral</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0f172a; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #0f172a; margin: 0; }
        .header p { color: #64748b; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #0f172a; color: white; padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .stock-low { color: #dc2626; font-weight: bold; }
        .stock-ok { color: #059669; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVENTARIO GENERAL</h1>
        <p>Salón de Eventos Gran Cañaveral</p>
        <p>Generado el: {{ now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Categoría</th>
                <th class="text-center">Cajas</th>
                <th class="text-center">Unidades</th>
                <th class="text-right">Precio Caja</th>
                <th class="text-right">Precio Unidad</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventory as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td class="text-center">{{ $item->category }}</td>
                <td class="text-center">{{ $item->boxes }}</td>
                <td class="text-center">{{ $item->loose_units }}</td>
                <td class="text-right">{{ number_format($item->price_per_box) }} Bs</td>
                <td class="text-right">{{ number_format($item->price_per_unit) }} Bs</td>
                <td class="text-center">
                    @if($item->boxes <= 2)
                    <span class="stock-low">Stock Bajo</span>
                    @else
                    <span class="stock-ok">Disponible</span>
                    @endif
                </td>
            </tr>
            @endforeach
            @if($inventory->isEmpty())
            <tr>
                <td colspan="7" class="text-center" style="color: #94a3b8; padding: 30px;">No hay productos en inventario</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr style="background: #f1f5f9;">
                <td colspan="2" class="text-right font-bold">TOTALES:</td>
                <td class="text-center font-bold">{{ $inventory->sum('boxes') }}</td>
                <td class="text-center font-bold">{{ $inventory->sum('loose_units') }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Gran Cañaveral - Sistema de Gestión de Eventos</p>
    </div>
</body>
</html>