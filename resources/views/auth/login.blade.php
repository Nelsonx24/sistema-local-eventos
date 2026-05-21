<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gran Cañaveral</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
        .bg-curve {
            border-bottom-right-radius: 40% 100%;
        }
        .input-focus:focus-within {
            border-color: #000;
            box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-white h-screen overflow-hidden flex flex-col lg:flex-row">
    <!-- Left Section: Branding -->
    <div class="hidden lg:flex lg:w-[45%] bg-black relative flex-col justify-center items-center p-12 z-20" style="clip-path: url(#free_curve);">
        <!-- Background Elements -->
        <!-- Concentric Golden Circles (Top Left) -->
        <div class="absolute top-[-10%] left-[-10%] w-[150%] h-[150%] pointer-events-none opacity-20">
            <svg viewBox="0 0 1000 1000" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                <circle cx="0" cy="0" r="100" stroke="#D4AF37" stroke-width="0.5"/>
                <circle cx="0" cy="0" r="150" stroke="#D4AF37" stroke-width="0.5"/>
                <circle cx="0" cy="0" r="200" stroke="#D4AF37" stroke-width="0.5"/>
                <circle cx="0" cy="0" r="250" stroke="#D4AF37" stroke-width="0.5"/>
                <circle cx="0" cy="0" r="300" stroke="#D4AF37" stroke-width="0.5"/>
                <circle cx="0" cy="0" r="350" stroke="#D4AF37" stroke-width="0.5"/>
                <circle cx="0" cy="0" r="400" stroke="#D4AF37" stroke-width="0.5"/>
                <circle cx="0" cy="0" r="450" stroke="#D4AF37" stroke-width="0.5"/>
                <circle cx="0" cy="0" r="500" stroke="#D4AF37" stroke-width="0.5"/>
                <circle cx="0" cy="0" r="600" stroke="#D4AF37" stroke-width="0.5"/>
                <circle cx="0" cy="0" r="700" stroke="#D4AF37" stroke-width="0.5"/>
            </svg>
        </div>

        <!-- Subtle Glow -->
        <div class="absolute top-[15%] left-[10%] w-64 h-64 bg-[#D4AF37] opacity-10 rounded-full blur-[100px]"></div>
        
        <div class="relative z-10 text-center space-y-8 max-w-lg">
            <img src="{{ asset('img/logo.png') }}" alt="Gran Cañaveral Logo" class="w-[450px] mx-auto">
            <div class="space-y-1">
                <p class="text-xl text-white/90 font-medium tracking-wide">El lugar de tus recuerdos inolvidables</p>
            </div>
        </div>
    </div>

    <!-- Golden Curve Details (Outside clipped div for full visibility) -->
    <div class="hidden lg:block absolute inset-y-0 left-0 w-[45%] pointer-events-none z-30">
        <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full overflow-visible">
            <!-- Golden Stroke exactly on the curve boundary -->
            <path d="M 85,0 C 100,20 100,80 85,100" stroke="#D4AF37" stroke-width="1.5" fill="none" vector-effect="non-scaling-stroke" />
        </svg>
    </div>

    <!-- Hidden SVG definition for the clip-path -->
    <svg width="0" height="0" class="absolute pointer-events-none">
        <defs>
            <clipPath id="free_curve" clipPathUnits="objectBoundingBox">
                <path d="M 0,0 L 0.85,0 C 1,0.2 1,0.8 0.85,1 L 0,1 Z" />
            </clipPath>
        </defs>
    </svg>

    <!-- Right Section: Login Form -->
    <div class="w-full lg:w-[55%] flex flex-col items-center justify-center p-6 md:p-12 relative lg:-mt-12">
        <div class="w-full max-w-[480px] bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] pt-6 pb-8 px-8 md:pt-10 md:pb-10 md:px-14 border border-slate-50">
            <!-- Card Logo -->
            <div class="flex justify-center mb-4">
                <img src="{{ asset('img/logo_blanco.png') }}" alt="Gran Cañaveral Logo" class="w-[150px] h-auto">
            </div>

            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-slate-900 mb-1">Bienvenido</h1>
                <p class="text-slate-500 font-medium">Inicia sesión en tu cuenta</p>
                
                <!-- Diamond Separator -->
                <div class="flex items-center justify-center gap-4 mt-4">
                    <div class="h-[1px] bg-slate-200 w-12"></div>
                    <div class="text-[10px] text-slate-300">◆</div>
                    <div class="h-[1px] bg-slate-200 w-12"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                @if($errors->any())
                    <div class="p-4 bg-red-50 border border-red-100 text-red-600 text-sm font-medium rounded-xl mb-6">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- User Input -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-slate-900">
                        <svg class="h-5 w-5 text-slate-400 group-focus-within:text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input type="text" name="username" placeholder="Usuario" autocomplete="username" required
                        class="block w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all outline-none">
                </div>

                <!-- Password Input -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-slate-900">
                        <svg class="h-5 w-5 text-slate-400 group-focus-within:text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input type="password" name="password" placeholder="Contraseña" autocomplete="current-password" required
                        class="block w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-900 focus:border-slate-900 transition-all outline-none">
                </div>

                <!-- Remember Me and Forgot Password -->
                <div class="flex items-center justify-between py-2">
                    <label class="flex items-center space-x-3 cursor-pointer group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="remember" class="peer h-5 w-5 cursor-pointer appearance-none rounded border border-slate-300 checked:bg-slate-900 checked:border-slate-900 transition-all">
                            <svg class="absolute h-3.5 w-3.5 text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </div>
                        <span class="text-sm text-slate-600 font-medium">Recordarme</span>
                    </label>
                    <a href="#" class="text-sm text-amber-600 hover:text-amber-700 font-semibold transition-colors">¿Olvidaste tu contraseña?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-black text-white py-4 rounded-xl font-bold uppercase tracking-[0.2em] hover:bg-slate-900 transition-all shadow-lg active:scale-[0.98]">
                    <span class="text-[#D4AF37]">INGRESAR</span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <p class="text-slate-400 text-sm">
                &copy; {{ date('Y') }} Gran Cañaveral. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
