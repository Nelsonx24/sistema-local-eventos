<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ventas Directas - {{ $displayDate }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; font-size: 10px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #000000; padding-bottom: 15px; }
        .header h1 { font-size: 18px; color: #000000; margin: 0; }
        .header p { color: #64748b; margin: 5px 0; font-size: 11px; }
        .summary { display: flex; justify-content: center; gap: 20px; margin: 15px 0; font-size: 10px; }
        .summary span { padding: 4px 12px; border-radius: 20px; font-weight: bold; }
        .efectivo { background: #d1fae5; color: #047857; }
        .qr { background: #fef3c7; color: #D4AF37; }
        .tarjeta { background: #f1f5f9; color: #475569; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #000000; color: white; padding: 8px 6px; text-align: left; font-size: 9px; text-transform: uppercase; }
        td { padding: 7px 6px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; font-weight: bold; }
        .item-badge { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 8px; display: inline-block; margin: 1px; border: 1px solid #e2e8f0; }
        .total-row { background: #f1f5f9; font-weight: bold; }
        .total-row td { border-top: 2px solid #000000; padding: 10px 6px; }
        .footer { margin-top: 25px; text-align: center; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>VENTAS DIRECTAS</h1>
        <p>{{ $displayDate }}</p>
        <p>Salón de Eventos Gran Cañaveral</p>
        <p style="font-size:9px;color:#94a3b8;">Reporte generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary">
        <span class="efectivo">Efectivo: {{ number_format($efectivo) }} Bs</span>
        <span class="qr">QR: {{ number_format($qr) }} Bs</span>
        <span class="tarjeta">Tarjeta: {{ number_format($tarjeta) }} Bs</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th class="text-center">Método</th>
                <th>Items</th>
                <th class="text-right">Monto</th>
                <th class="text-center">Hora</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
            <tr>
                <td style="color:#94a3b8;">#{{ $sale->id }}</td>
                <td style="font-weight:bold;">{{ $sale->client_name }}</td>
                <td class="text-center">
                    @php
                        $methodLabels = ['Efectivo' => 'Efectivo', 'QR' => 'QR', 'Tarjeta' => 'Tarjeta'];
                    @endphp
                    {{ $methodLabels[$sale->payment_method] ?? $sale->payment_method }}
                </td>
                <td>
                    @foreach($sale->items as $item)
                    <span class="item-badge">{{ $item->quantity }} {{ $item->type }}x {{ $item->name }}</span>
                @endforeach
                </td>
                <td class="text-right">{{ number_format($sale->amount) }} Bs</td>
                <td class="text-center">{{ $sale->created_at->format('H:i') }}</td>
            </tr>
            @endforeach
            @if($sales->isEmpty())
            <tr>
                <td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">No hay ventas en esta fecha.</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align:right;">TOTAL DEL DÍA:</td>
                <td class="text-right">{{ number_format($total) }} Bs</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Gran Cañaveral - Sistema de Gestión de Eventos</p>
    </div>
</body>
</html>
