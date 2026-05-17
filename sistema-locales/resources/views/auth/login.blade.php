<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gran Cañaveral</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
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
<body class="bg-[#f8f9fc] flex items-center justify-center p-6 bg-gradient-to-br from-slate-100 to-slate-200 min-h-screen">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
            <div class="p-8 bg-slate-900 text-white text-center space-y-2">
                <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <h1 class="text-2xl font-black tracking-tight">GRAN CAÑAVERAL</h1>
                <p class="text-slate-400 text-sm font-medium">Portal de Gestión Administrativa</p>
            </div>

            <div class="p-10 space-y-8">
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-800">Bienvenido de nuevo</h2>
                    <p class="text-slate-500 text-sm">Ingrese sus credenciales para acceder al sistema.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    @if($errors->any())
                        <div class="p-4 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-xl animate-pulse">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-brand-accent transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <input type="text" name="username" placeholder="Usuario" required
                                class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all">
                        </div>

                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-brand-accent transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input type="password" name="password" placeholder="Contraseña" required
                                class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-2 group">
                        Acceder al Sistema
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-1 transition-transform"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </button>
                </form>
                
                <div class="pt-6 border-t border-slate-100 italic text-[10px] text-slate-400 text-center">
                    <p>Módulo de Control de Acceso</p>
                </div>
            </div>
        </div>
        
        <p class="mt-8 text-center text-slate-400 text-xs font-medium">
            Powered by Gran Cañaveral Systems &copy; 2024
        </p>
    </div>
</body>
</html>