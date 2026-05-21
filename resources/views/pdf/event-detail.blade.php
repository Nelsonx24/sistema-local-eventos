<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte - {{ $event->client_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000000; padding-bottom: 15px; }
        .header h1 { font-size: 18px; color: #000000; margin: 0; }
        .header p { color: #64748b; margin: 3px 0; font-size: 11px; }
        .event-info { margin-bottom: 20px; }
        .event-info td { padding: 3px 10px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #000000; color: white; padding: 6px; text-align: left; font-size: 8px; text-transform: uppercase; }
        td { padding: 5px 6px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
        .amount { text-align: right; font-weight: bold; }
        .total-row td { border-top: 2px solid #000000; padding: 8px 6px; font-weight: bold; font-size: 11px; }
        .footer { margin-top: 30px; text-align: center; color: #64748b; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE VENTAS</h1>
        <p>{{ $event->client_name }} — {{ $event->event_type }}</p>
        <p>{{ $event->date->format('d/m/Y') }}</p>
        <p style="font-size:8px;color:#94a3b8;">Generado el: {{ now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Cliente</th>
                <th>Método</th>
                <th>Vendedor</th>
                <th>Items</th>
                <th class="amount">Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
            <tr>
                <td>#{{ $sale->id }}<br><span style="color:#94a3b8;font-size:8px;">{{ $sale->date->format('d/m/Y') }}</span></td>
                <td>{{ $sale->client_name }}</td>
                <td>{{ $sale->payment_method }}</td>
                <td>{{ $sale->seller_name ?? 'Sistema' }}</td>
                <td>
                    @foreach($sale->items as $item)
                    <div>{{ $item->quantity }} {{ $item->type }}x {{ $item->name }}</div>
                    @endforeach
                </td>
                <td class="amount">{{ number_format($sale->amount) }} Bs</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">No hay ventas registradas</td>
            </tr>
            @endforelse
        </tbody>
        @if($sales->isNotEmpty())
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align:right;">TOTAL:</td>
                <td class="amount">{{ number_format($totalAmount) }} Bs</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Salón de Eventos Gran Cañaveral — Sistema de Gestión</p>
    </div>
</body>
</html>

