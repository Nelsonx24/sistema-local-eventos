<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Regalos con Imágenes</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 0; padding: 10px; }
        .category-header { font-weight: bold; font-size: 14px; padding: 8px 0 4px; border-bottom: 2px solid #000; margin-bottom: 10px; color: #333; }
        .grid { display: block; margin-bottom: 15px; }
        .card { display: inline-block; width: 100px; margin: 5px; vertical-align: top; text-align: center; border: 1px solid #e5e5e5; border-radius: 6px; padding: 8px; }
        .card img { width: 70px; height: 70px; object-fit: cover; border-radius: 4px; margin-bottom: 5px; }
        .card .name { font-size: 9px; font-weight: bold; margin-bottom: 2px; line-height: 1.2; }
        .card .price { font-size: 9px; color: #B8860B; font-weight: bold; }
        .card .stock { font-size: 8px; color: #666; }
        .card .no-img { width: 70px; height: 70px; background: #f5f5f5; border-radius: 4px; margin-bottom: 5px; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 20px; }
        .footer { text-align: center; font-size: 10px; color: #999; margin-top: 30px; }
    </style>
</head>
<body>
    @php
    $grouped = $gifts->groupBy('category');
    @endphp

    @foreach($grouped as $category => $items)
    <div class="category-header">{{ $category }} ({{ $items->count() }})</div>
    <div class="grid">
        @foreach($items as $gift)
        <div class="card">
            @if($gift->image && file_exists(public_path('storage/'.$gift->image)))
            <img src="{{ public_path('storage/'.$gift->image) }}" alt="">
            @else
            <div class="no-img">—</div>
            @endif
            <div class="name">{{ $gift->name }}</div>
            <div class="price">Bs.{{ rtrim(rtrim(number_format($gift->sale_price, 2), '0'), '.') }}</div>
            @if($gift->detail)
            <div class="stock">{{ $gift->detail }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endforeach

    <p class="subtitle">Total: {{ $gifts->count() }} regalos</p>

    <div class="footer">
        Gran Cañaveral &copy; {{ date('Y') }}
    </div>
</body>
</html>
