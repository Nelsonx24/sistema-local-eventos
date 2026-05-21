<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gran Cañaveral')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        'brand-primary': '#000000',
                        'brand-accent': '#D4AF37',
                        'brand-gold': '#D4AF37',
                        'brand-gold-dark': '#B8860B',
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
        <aside id="sidebar" class="w-64 bg-black flex flex-col fixed left-0 top-0 h-screen transition-transform duration-300 z-20">
            <div class="w-full p-6 flex items-center justify-center">
                <img src="{{ asset('img/logo-sidebar-v2.png') }}" alt="Logo" class="w-full h-auto drop-shadow-[0_0_15px_rgba(212,175,55,0.3)]">
            </div>
            
            <nav class="flex-1 py-2">
                @php
                $menuItems = [
                    ['id' => 'dashboard', 'label' => 'Inicio', 'icon' => 'layout-dashboard', 'route' => 'dashboard', 'roles' => ['Administrador']],
                    ['id' => 'events', 'label' => 'Eventos', 'icon' => 'calendar-days', 'route' => 'events.index', 'roles' => ['Administrador', 'Vendedor', 'CM']],
                    ['id' => 'inventory', 'label' => 'Inventario', 'icon' => 'package', 'route' => 'inventory.index', 'roles' => ['Administrador']],
                    ['id' => 'sales', 'label' => 'Ventas', 'icon' => 'dollar-sign', 'route' => 'sales.index', 'roles' => ['Administrador', 'Vendedor']],
                    ['id' => 'staff', 'label' => 'Personal', 'icon' => 'users', 'route' => 'staff.index', 'roles' => ['Administrador']],
                    ['id' => 'logs', 'label' => 'Logs', 'icon' => 'scroll-text', 'route' => 'logs.index', 'roles' => ['Administrador']],
                    ['id' => 'reports', 'label' => 'Reportes', 'icon' => 'pie-chart', 'route' => 'reports.index', 'roles' => ['Administrador', 'Vendedor', 'CM']],
                    ['id' => 'store', 'label' => 'Tienda', 'icon' => 'shopping-bag', 'route' => 'store.index', 'roles' => ['Administrador', 'Vendedor']],
                    ['id' => 'others', 'label' => 'Otros', 'icon' => 'settings', 'route' => 'others.index', 'roles' => ['Administrador', 'CM']],
                ];
                @endphp
                @foreach($menuItems as $item)
                    @if(in_array(Auth::guard('staff')->user()->role, $item['roles']))
                    <a href="{{ route($item['route']) }}" 
                       class="w-full flex items-center gap-3 px-6 py-3 transition-all duration-200 text-sm {{ Request::routeIs($item['route']) ? 'bg-slate-900 text-brand-gold border-l-4 border-brand-gold shadow-[inset_4px_0_0_0_#D4AF37]' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                        <i data-lucide="{{ $item['icon'] }}" size="18"></i>
                        <span class="font-medium">{{ $item['label'] }}</span>
                    </a>
                    @endif
                @endforeach
            </nav>

            <!-- User Info -->
            <div class="p-4 mx-4 mb-4 bg-slate-900/50 rounded-2xl border border-slate-800">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-brand-gold text-black rounded-lg flex items-center justify-center font-bold text-xs shadow-lg shadow-brand-gold/20">
                        {{ substr(Auth::guard('staff')->user()->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-none mb-1">Usuario</p>
                        <p class="text-white text-xs font-bold truncate leading-none">{{ Auth::guard('staff')->user()->name }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 bg-red-500/5 text-red-500 rounded-xl text-[11px] font-bold hover:bg-red-500 hover:text-white transition-all group">
                        <i data-lucide="log-out" size="14" class="group-hover:-translate-x-1 transition-transform"></i>
                        Cerrar Sesión
                    </button>
                </form>
            </div>

            <div class="p-6 text-[10px] text-slate-600 border-t border-slate-900 uppercase font-bold tracking-widest">
                <p>Gran Cañaveral &copy; {{ date('Y') }}</p>
            </div>
        </aside>

        <!-- Main Content -->
        <main id="main-content" class="flex-1 ml-64 min-w-0 transition-all duration-300">
            <!-- Header -->
            <header class="h-16 border-b border-border-subtle bg-white sticky top-0 z-10 px-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button id="sidebar-toggle" class="text-text-muted hover:text-text-main transition-colors p-1.5 -ml-1.5" onclick="toggleSidebar()">
                        <i data-lucide="menu" size="20"></i>
                    </button>
                    <span class="bg-black text-brand-gold text-[9px] uppercase font-black px-2 py-1 rounded border border-brand-gold/20 shadow-sm">ADMIN</span>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] border-l border-slate-200 pl-3">@yield('header-title', 'Gran Cañaveral')</h2>
                </div>
                <div class="flex items-center gap-6">
                    <div class="relative group hidden md:block">
                        <input type="text" placeholder="Buscar registros..." class="pl-4 pr-4 py-[7px] bg-[#f1f5f9] border border-border-subtle rounded-[6px] text-xs w-[250px] focus:outline-none focus:ring-1 focus:ring-brand-gold/40 transition-all text-[#64748b]">
                    </div>
                    <div class="flex items-center gap-4">
                        <button class="text-text-muted hover:text-brand-gold transition-colors relative">
                            <i data-lucide="bell" size="18"></i>
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-brand-gold rounded-full border-2 border-white"></span>
                        </button>
                        <div class="w-[1px] h-6 bg-border-subtle"></div>
                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <p class="text-[0.85rem] font-bold text-text-main leading-none">{{ Auth::guard('staff')->user()->name }}</p>
                                <p class="text-[0.7rem] text-brand-gold font-bold uppercase tracking-wider mt-1">{{ Auth::guard('staff')->user()->role }}</p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-black border border-brand-gold/30 flex items-center justify-center text-brand-gold font-bold text-xs overflow-hidden shadow-sm">
                                {{ strtoupper(substr(Auth::guard('staff')->user()->name, 0, 2)) }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="px-8 py-4 max-w-7xl mx-auto w-full">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('main-content');
            const isHidden = sidebar.classList.toggle('-translate-x-full');
            main.classList.toggle('ml-64', !isHidden);
            main.classList.toggle('ml-0', isHidden);
        }
    </script>
    @stack('scripts')
</body>
</html>
