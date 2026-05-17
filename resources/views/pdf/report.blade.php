<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte - {{ $event->client_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0f172a; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #0f172a; margin: 0; }
        .header .subtitle { color: #64748b; margin-top: 5px; }
        .info-box { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; }
        .info-item { text-align: center; }
        .info-label { font-size: 10px; color: #64748b; text-transform: uppercase; }
        .info-value { font-size: 14px; font-weight: bold; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #0f172a; color: white; padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #0f172a; color: white; font-weight: bold; }
        .total-label { text-align: right; padding-right: 20px; }
        .footer { margin-top: 30px; text-align: center; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RESUMEN DE VENTAS DE EVENTO</h1>
        <p class="subtitle">Salón de Eventos Gran Cañaveral - Reporte de Evento</p>
    </div>

    <div class="info-box">
        <div class="info-item">
            <div class="info-label">Cliente</div>
            <div class="info-value">{{ $event->client_name }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Tipo de Evento</div>
            <div class="info-value">{{ $event->event_type }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Fecha</div>
            <div class="info-value">{{ $event->date }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Total Generado</div>
            <div class="info-value" style="color: #3b82f6; font-size: 18px;">{{ number_format($totalAmount) }} Bs</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Items</th>
                <th>Método</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>#{{ $sale->id }}</td>
                <td>{{ $sale->client_name }}</td>
                <td>{{ $sale->seller_name ?? 'SISTEMA' }}</td>
                <td>
                    @foreach($sale->items as $item)
                        {{ $item->quantity }} {{ $item->type }}x {{ $item->name }}<br>
                    @endforeach
                </td>
                <td class="text-center">{{ $sale->payment_method }}</td>
                <td class="text-right">{{ number_format($sale->amount) }} Bs</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="total-label">TOTAL:</td>
                <td class="text-right">{{ number_format($totalAmount) }} Bs</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
        <p>Gran Cañaveral - Sistema de Gestión Administrativa</p>
    </div>
</body>
</html>