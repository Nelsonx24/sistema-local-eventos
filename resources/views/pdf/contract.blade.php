<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contrato - {{ $event->client_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; font-size: 12px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 18px; margin-bottom: 10px; }
        .section { margin-bottom: 20px; }
        .section-title { font-weight: bold; margin-bottom: 10px; }
        .text-justify { text-align: justify; }
        .signature { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 45%; }
        .signature-line { border-top: 1px solid #000; margin-top: 60px; padding-top: 5px; }
    </style>
</head>
<body>
    @if($watermark)
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; text-align: center; padding-top: 25%; opacity: 0.1;">
        <img src="{{ $watermark }}" style="width: 80%;">
    </div>
    @endif
    <div class="header">
        <h1>CONTRATO DE PRESTACIÓN DE SERVICIOS</h1>
        <p>"{{ strtoupper($settings['salon_name']) }}"</p>
    </div>

    <div class="section text-justify">
        <p>Por una parte, <strong>{{ $event->client_name }}</strong>, con documento de identidad N° {{ $event->client_id }}{{ $event->client_phone ? ', teléfono '.$event->client_phone : '' }}, en adelante denominado/a "EL CLIENTE". Y por otra parte, <strong>{{ $settings['representative'] }}</strong>, representante legal del {{ $settings['salon_name'] }}, en adelante denominado "EL PROVEEDOR". Ambas partes acuerdan celebrar el presente contrato de prestación de servicios, el cual se regirá por las siguientes cláusulas:</p>
    </div>

    <div class="section">
        <p class="section-title">PRIMERA: OBJETO DEL CONTRATO</p>
        <p class="text-justify">EL PROVEEDOR se compromete a brinda el servicio de alquiler del salón de eventos, incluyendo el uso de sus instalaciones para la realización del evento detallado por EL CLIENTE.</p>
    </div>

    <div class="section">
        <p class="section-title">SEGUNDA: DESCRIPCIÓN DEL SERVICIO</p>
        <p class="text-justify">El servicio incluye:</p>
        <ul>
            <li>Uso del salón principal</li>
            <li>Uso de mesas y sillas disponibles</li>
            <li>Iluminación básica del ambiente</li>
            <li>Baños y áreas comunes</li>
            <li>Uso de cocina y cantina (bajo responsabilidad del cliente o personal autorizado)</li>
        </ul>
    </div>

    <div class="section">
        <p class="section-title">TERCERA: FECHA, DURACIÓN Y HORARIO</p>
        <p class="text-justify">El evento se realizará el día <strong>{{ $event->date->format('d/m/Y') }}</strong>.</p>
        <p>Horario establecido:</p>
        <ul>
            <li>Inicio: 08:00 a.m.</li>
            <li>Finalización: 23:59 p.m.</li>
        </ul>
        <p>En caso de eventos de más de un día, el uso del salón se extenderá desde las 14:00 p.m. hasta las 20:00 p.m. del día siguiente, previa coordinación.</p>
        <p>En caso de que el evento exceda el tiempo límite de servicio y uso del local establecido en el presente contrato, EL CLIENTE deberá abonar un recargo adicional equivalente al 20% del monto total contratado.</p>
    </div>

    <div class="section">
        <p class="section-title">CUARTA: PRECIO Y FORMA DE PAGO</p>
        <p>El costo total del servicio es de <strong>Bs. {{ number_format($event->total_amount) }}</strong>.</p>
        <p>Forma de pago:</p>
        <ul>
            <li>Anticipo: <strong>Bs. {{ number_format($event->advance_payment) }}</strong> al momento de la firma del contrato</li>
            <li>Saldo restante: deberá ser cancelado hasta <strong>{{ \Carbon\Carbon::parse($event->payment_due_date)->format('d/m/Y') }}</strong></li>
        </ul>
        <p>El incumplimiento de pago dentro del plazo establecido podrá generar la suspensión del servicio sin derecho a reclamo.</p>
    </div>

    <div class="section">
        <p class="section-title">QUINTA: RESPONSABILIDADES DEL CLIENTE</p>
        <p class="text-justify">EL CLIENTE se compromete a:</p>
        <ul>
            <li>Hacer buen uso de las instalaciones</li>
            <li>Responder por daños ocasionados por los asistentes</li>
            <li>Respetar los horarios establecidos</li>
            <li>Cumplir las normas de convivencia y seguridad del salón</li>
            <li>Mantener el orden de los invitados</li>
        </ul>
    </div>

    <div class="section">
        <p class="section-title">SÉPTIMA: CANCELACIONES</p>
        <p class="text-justify">En caso de cancelación del evento por parte de EL CLIENTE, el monto entregado como adelanto no será reembolsable bajo ninguna circunstancia, debido a que dicho pago garantiza la reserva de la fecha y la disponibilidad del salón.</p>
        <p>En caso de fuerza mayor debidamente justificada, ambas partes podrán renegociar la fecha del evento.</p>
    </div>

    <div class="section">
        <p class="section-title">OCTAVA: DAÑOS, PÉRDIDAS Y MULTAS</p>
        <p class="text-justify">Cualquier daño ocasionado a las instalaciones, mobiliario o equipos será responsabilidad del CLIENTE, quien deberá cubrir los costos de reparación o reposición.</p>
    </div>

    <div class="section">
        <p class="section-title">NOVENA: ACEPTACIÓN</p>
        <p class="text-justify">Ambas partes declaran haber leído y aceptado todas las cláusulas del presente contrato, firmando en señal de conformidad.</p>
    </div>

    <table style="width: 100%; margin-top: 140px;">
        <tr>
            <td style="width: 50%; text-align: center; padding: 0 10px;">
                <div style="border-top: 1px solid #000; padding-top: 5px;">EL CLIENTE</div>
                <p>{{ $event->client_name }}</p>
                <p>CI: {{ $event->client_id }}</p>
            </td>
            <td style="width: 50%; text-align: center; padding: 0 10px;">
                <div style="border-top: 1px solid #000; padding-top: 5px;">EL PROVEEDOR</div>
                <p>{{ $settings['representative'] }}</p>
                <p>CI: {{ $settings['representative_ci'] }}</p>
            </td>
        </tr>
    </table>

    <div style="margin-top: 30px; text-align: center; font-size: 10px;">
        <p>Firmado en la ciudad de {{ $settings['city'] }}, el día {{ date('d/m/Y') }}</p>
    </div>
</body>
</html>