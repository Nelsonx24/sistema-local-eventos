<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gran Cañaveral')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
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
    @stack('styles')
</head>
<body class="bg-surface-base text-text-main font-sans min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-[240px] bg-[#0f172a] flex flex-col fixed left-0 top-0 h-screen border-r border-[#1e293b]">
            <div class="p-6 h-16 flex items-center gap-3 border-b border-[#1e293b]">
                <div class="w-6 h-6 bg-brand-accent rounded"></div>
                <h1 class="text-lg font-bold tracking-tight text-[#f8fafc]">Gran Cañaveral</h1>
            </div>
            
            <nav class="flex-1 py-4">
                @php
                $menuItems = [
                    ['id' => 'dashboard', 'label' => 'Inicio', 'icon' => 'layout-dashboard', 'route' => 'dashboard', 'roles' => ['Administrador']],
                    ['id' => 'events', 'label' => 'Eventos', 'icon' => 'calendar-days', 'route' => 'events.index', 'roles' => ['Administrador', 'Vendedor', 'CM']],
                    ['id' => 'inventory', 'label' => 'Inventario', 'icon' => 'package', 'route' => 'inventory.index', 'roles' => ['Administrador']],
                    ['id' => 'sales', 'label' => 'Ventas', 'icon' => 'dollar-sign', 'route' => 'sales.index', 'roles' => ['Administrador', 'Vendedor']],
                    ['id' => 'staff', 'label' => 'Personal', 'icon' => 'users', 'route' => 'staff.index', 'roles' => ['Administrador']],
                    ['id' => 'reports', 'label' => 'Reportes', 'icon' => 'pie-chart', 'route' => 'reports.index', 'roles' => ['Administrador', 'Vendedor', 'CM']],
                    ['id' => 'others', 'label' => 'Otros', 'icon' => 'settings', 'route' => 'others.index', 'roles' => ['Administrador', 'CM']],
                    ['id' => 'settings', 'label' => 'Configuración', 'icon' => 'settings', 'route' => 'settings.index', 'roles' => ['Administrador']],
                ];
                @endphp
                @foreach($menuItems as $item)
                    @if(in_array(Auth::guard('staff')->user()->role, $item['roles']))
                    <a href="{{ route($item['route']) }}" 
                       class="w-full flex items-center gap-3 px-6 py-3 transition-all duration-200 text-sm {{ Request::routeIs($item['route']) ? 'bg-[#1e293b] text-white border-l-4 border-brand-accent' : 'text-[#f8fafc]/70 hover:bg-[#1e293b] hover:text-[#f8fafc]' }}">
                        <i data-lucide="{{ $item['icon'] }}" size="18"></i>
                        <span class="font-medium">{{ $item['label'] }}</span>
                    </a>
                    @endif
                @endforeach
            </nav>

            <!-- User Info -->
            <div class="p-4 mx-4 mb-4 bg-slate-800/50 rounded-2xl border border-slate-700/30">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-brand-accent text-white rounded-lg flex items-center justify-center font-bold text-xs shadow-lg shadow-brand-accent/20">
                        {{ substr(Auth::guard('staff')->user()->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-none mb-1">Usuario</p>
                        <p class="text-white text-xs font-bold truncate">{{ Auth::guard('staff')->user()->role }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 bg-red-500/10 text-red-500 rounded-xl text-[11px] font-bold hover:bg-red-500 hover:text-white transition-all group">
                        <i data-lucide="log-out" size="14" class="group-hover:-translate-x-1 transition-transform"></i>
                        Cerrar Sesión
                    </button>
                </form>
            </div>

            <div class="p-6 text-[0.75rem] text-[#f8fafc]/30 border-t border-[#1e293b]">
                <p class="font-medium">Gran Cañaveral &copy; 2024</p>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 min-w-0">
            <!-- Header -->
            <header class="h-16 border-b border-border-subtle bg-white sticky top-0 z-10 px-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="bg-slate-100 text-slate-600 text-[10px] uppercase font-bold px-2 py-1 rounded border border-slate-200">Uso Interno Administrativo</span>
                    <h2 class="text-sm font-semibold text-text-muted uppercase tracking-widest border-l border-slate-200 pl-3">@yield('header-title', 'Gran Cañaveral')</h2>
                </div>
                <div class="flex items-center gap-6">
                    <div class="relative group hidden md:block">
                        <input type="text" placeholder="Buscar registros, facturas o clientes..." class="pl-4 pr-4 py-[7px] bg-[#f1f5f9] border border-border-subtle rounded-[6px] text-xs w-[300px] focus:outline-none focus:ring-1 focus:ring-brand-accent/40 transition-all text-[#64748b]">
                    </div>
                    <div class="flex items-center gap-4">
                        <button class="text-text-muted hover:text-text-main transition-colors relative">
                            <i data-lucide="bell" size="18"></i>
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                        </button>
                        <div class="w-[1px] h-6 bg-border-subtle"></div>
                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <p class="text-[0.85rem] font-semibold text-text-main leading-none">{{ Auth::guard('staff')->user()->name }}</p>
                                <p class="text-[0.7rem] text-text-muted mt-1">{{ Auth::guard('staff')->user()->role }}</p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-brand-accent flex items-center justify-center text-white font-bold text-xs overflow-hidden shadow-sm">
                                {{ strtoupper(substr(Auth::guard('staff')->user()->name, 0, 2)) }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-8 max-w-7xl mx-auto w-full">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>