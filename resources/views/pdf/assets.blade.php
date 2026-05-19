<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inventario de Activos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; font-size: 11px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0f172a; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #0f172a; margin: 0; }
        .section-title { background: #0f172a; color: white; padding: 10px; margin-top: 25px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase; color: #64748b; }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: center; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVENTARIO DE ACTIVOS</h1>
        <p>Gran Cañaveral - Gestión de Equipamiento</p>
    </div>

    <div class="section-title">1. Área de Cocina</div>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th class="text-center">Cantidad</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets->where('category', 'Cocina') as $asset)
            <tr>
                <td>{{ $asset->name }}</td>
                <td class="text-center">{{ $asset->quantity }}</td>
                <td>{{ $asset->condition }}</td>
            </tr>
            @endforeach
            @if($assets->where('category', 'Cocina')->isEmpty())
            <tr><td colspan="3" class="text-center">Sin activos en esta categoría</td></tr>
            @endif
        </tbody>
    </table>

    <div class="section-title">2. Área de Cantina</div>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th class="text-center">Cantidad</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets->where('category', 'Cantina') as $asset)
            <tr>
                <td>{{ $asset->name }}</td>
                <td class="text-center">{{ $asset->quantity }}</td>
                <td>{{ $asset->condition }}</td>
            </tr>
            @endforeach
            @if($assets->where('category', 'Cantina')->isEmpty())
            <tr><td colspan="3" class="text-center">Sin activos en esta categoría</td></tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Generado el: {{ now()->format('d/m/Y') }}</p>
    </div>
</body>
</html>