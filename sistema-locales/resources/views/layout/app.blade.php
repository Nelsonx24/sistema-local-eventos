<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gran Cañaveral')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'brand-primary': '#0f172a',
                        'brand-accent': '#3b82f6',
                        'surface-base': '#f8fafc',
                        'border-subtle': '#e2e8f0',
                        'text-main': '#1e293b',
                        'text-muted': '#64748b',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-surface-base text-text-main font-sans">
    @yield('content')
</body>
</html>