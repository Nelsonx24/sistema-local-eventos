<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; font-size: 10px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000000; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #000000; margin: 0; }
        .header h2 { font-size: 14px; color: #334155; margin: 5px 0; font-weight: normal; }
        .header p { color: #64748b; margin: 5px 0; }
        .subtitle { font-size: 10px; color: #94a3b8; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #000000; color: white; padding: 8px 6px; text-align: left; font-size: 9px; text-transform: uppercase; }
        td { padding: 8px 6px; border-bottom: 1px solid #e2e8f0; }
        .status-paid { color: #10b981; font-weight: bold; }
        .status-pending { color: #f59e0b; font-weight: bold; }
        .amount { text-align: right; font-weight: bold; }
        .balance { text-align: right; color: #dc2626; }
        .total-row { background: #f1f5f9; font-weight: bold; }
        .total-row td { border-top: 2px solid #000000; padding: 10px 6px; }
        .footer { margin-top: 30px; text-align: center; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE EVENTOS</h1>
        <h2>{{ $title }}</h2>
        <p>Salón de Eventos Gran Cañaveral</p>
        <p class="subtitle">Reporte generado el: {{ now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>Tipo</th>
                <th class="text-right">Total</th>
                <th class="text-right">Saldo</th>
                <th>Estado</th>
                <th>Pago</th>
            </tr>
        </thead>
        <tbody>
            @foreach($events as $event)
            <tr>
                <td>{{ $event->date->format('d/m/Y') }}</td>
                <td>{{ $event->client_name }}</td>
                <td>{{ $event->client_phone ?? '—' }}</td>
                <td>{{ $event->event_type }}</td>
<td class="amount">{{ rtrim(rtrim(number_format($event->total_amount, 2), '0'), '.') }} Bs</td>
                <td class="balance">{{ rtrim(rtrim(number_format($event->balance_pending, 2), '0'), '.') }} Bs</td>
                <td>
                    @if($event->event_status === 'completed')
                    <span style="color:#64748b;font-weight:bold;">Finalizado</span>
                    @elseif($event->event_status === 'cancelled')
                    <span style="color:#dc2626;font-weight:bold;">Cancelado</span>
                    @else
                    <span style="color:#D4AF37;font-weight:bold;">Próximo</span>
                    @endif
                </td>
                <td>
                    @if($event->payment_status === 'paid')
                    <span class="status-paid">Pagado</span>
                    @elseif($event->payment_status === 'cancelled')
                    <span style="color:#dc2626;font-weight:bold;">Cancelado</span>
                    @else
                    <span class="status-pending">Pendiente</span>
                    @endif
                </td>
            </tr>
            @endforeach
            @if($events->isEmpty())
            <tr>
                <td colspan="8" class="text-center" style="color: #94a3b8; padding: 30px;">No se encontraron eventos</td>
            </tr>
            @endif
        </tbody>
        @if($events->isNotEmpty())
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">TOTALES:</td>
<td class="amount">{{ rtrim(rtrim(number_format($events->sum('total_amount'), 2), '0'), '.') }} Bs</td>
                <td class="balance">{{ rtrim(rtrim(number_format($events->sum('balance_pending'), 2), '0'), '.') }} Bs</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Gran Cañaveral - Sistema de Gestión de Eventos</p>
    </div>
</body>
</html>

