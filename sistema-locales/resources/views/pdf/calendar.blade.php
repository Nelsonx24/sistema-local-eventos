<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Calendario de Eventos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0f172a; padding-bottom: 15px; }
        .header h1 { font-size: 22px; color: #0f172a; margin: 0; }
        .header p { color: #64748b; margin: 5px 0; }
        .subtitle { font-size: 10px; color: #94a3b8; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #0f172a; color: white; padding: 12px; text-align: left; font-size: 10px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        tr:hover { background: #f8fafc; }
        .status-pending { color: #f59e0b; font-weight: bold; }
        .status-confirmed { color: #10b981; font-weight: bold; }
        .status-closed { color: #64748b; font-weight: bold; }
        .amount { text-align: right; font-weight: bold; }
        .balance { text-align: right; color: #dc2626; }
        .footer { margin-top: 30px; text-align: center; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CALENDARIO DE EVENTOS</h1>
        <p>Salón de Eventos Gran Cañaveral</p>
        <p class="subtitle">Reporte generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Tipo</th>
                <th>Vendedor</th>
                <th class="text-center">Pax</th>
                <th class="text-right">Total</th>
                <th class="text-right">Saldo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td>{{ $event->date }}</td>
                <td>{{ $event->client_name }}</td>
                <td>{{ $event->event_type }}</td>
                <td>{{ $event->seller_name ?? 'N/A' }}</td>
                <td class="text-center">{{ $event->guests }}</td>
                <td class="amount">{{ number_format($event->total_amount) }} Bs</td>
                <td class="balance">{{ number_format($event->balance_pending) }} Bs</td>
                <td>
                    @if($event->status === 'Confirmado')
                    <span class="status-confirmed">Confirmado</span>
                    @elseif($event->status === 'Cerrado')
                    <span class="status-closed">Cerrado</span>
                    @else
                    <span class="status-pending">Pendiente</span>
                    @endif
                </td>
            </tr>
            @endforeach
            @if($events->isEmpty())
            <tr>
                <td colspan="8" class="text-center" style="color: #94a3b8; padding: 30px;">No hay eventos registrados</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr style="background: #f1f5f9;">
                <td colspan="5" style="text-align: right; font-weight: bold;">TOTALES:</td>
                <td class="amount">{{ number_format($events->sum('total_amount')) }} Bs</td>
                <td class="balance">{{ number_format($events->sum('balance_pending')) }} Bs</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Gran Cañaveral - Sistema de Gestión de Eventos</p>
    </div>
</body>
</html>