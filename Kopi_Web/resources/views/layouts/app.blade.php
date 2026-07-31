<!DOCTYPE html>
<html lang="es" x-data="{ theme: localStorage.getItem('theme') || 'dark' }" x-init="$watch('theme', val => localStorage.setItem('theme', val))" :class="theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopi - Premium Carpooling</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo/apple-touch-icon.png') }}">
    
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Vite Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-brand-900 text-gray-100 font-sans antialiased min-h-screen relative overflow-x-hidden selection:bg-primary-500 selection:text-black transition-colors">

    <!-- Sticky Header -->
    <header class="sticky top-0 z-50 w-full bg-black border-b-4 border-primary-500 transition-colors">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-accent-500 via-orange-500 to-primary-500"></div>
        <div class="max-w-5xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ session()->has('jwt_token') ? route('viajes.index') : route('login') }}" class="flex items-center gap-3 text-2xl font-black text-white tracking-tighter hover:text-primary-500 transition-colors uppercase">
                <div class="w-10 h-10 bg-primary-500 flex items-center justify-center transform -skew-x-12">
                    <img src="{{ asset('logo/android-chrome-192x192.png') }}" alt="Logo KOPI" class="w-6 h-6 object-contain transform skew-x-12">
                </div>
                <span>KOPI</span>
            </a>
            
            <nav class="flex items-center gap-4" x-data="{ open: false }">
                <button type="button" @click="theme = theme === 'dark' ? 'light' : 'dark'" class="w-10 h-10 bg-gray-900 border-2 border-gray-800 flex items-center justify-center text-gray-400 hover:text-primary-500 hover:border-primary-500 transition-all transform hover:-translate-y-1">
                    <span x-show="theme === 'dark'">☀️</span>
                    <span x-show="theme === 'light'">🌙</span>
                </button>

                @if(session()->has('jwt_token'))
                    <a href="{{ route('viajes.index') }}" class="hidden md:block text-sm font-bold text-gray-300 hover:text-primary-500 uppercase tracking-wider transition-colors">Buscar rutas</a>
                    
                    <div class="relative">
                        <button @click="open = !open" @click.away="open = false" class="w-10 h-10 bg-primary-500 text-black flex items-center justify-center transform hover:scale-105 transition-transform font-bold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        
                        <div x-show="open" style="display: none;" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-64 bg-black border-4 border-gray-800 shadow-[8px_8px_0px_rgba(251,191,36,1)] py-2 z-50 rounded-none transition-colors">
                            
                            <a href="{{ route('viajes.mios') }}" class="block px-5 py-3 text-sm font-bold text-gray-300 hover:bg-primary-500 hover:text-black transition-colors uppercase tracking-widest">Mis viajes</a>
                            
                            @if(session('es_conductor') == true || session('estatus_verificacion') == 'aprobado')
                                <div class="border-t-2 border-gray-800 my-1"></div>
                                <p class="px-5 py-2 text-xs font-black text-accent-500 uppercase tracking-widest">Conductor</p>
                                <a href="{{ route('conductor.panel') }}" class="block px-5 py-2 text-sm font-bold text-gray-300 hover:bg-primary-500 hover:text-black transition-colors uppercase">Vehículos</a>
                                <a href="{{ route('viajes.publicar') }}" class="block px-5 py-2 text-sm font-bold text-gray-300 hover:bg-primary-500 hover:text-black transition-colors uppercase">Publicar ruta</a>
                                <a href="{{ route('reservaciones.gestionar') }}" class="block px-5 py-2 text-sm font-bold text-gray-300 hover:bg-primary-500 hover:text-black transition-colors uppercase">Solicitudes</a>
                            @endif
                            
                            <div class="border-t-2 border-gray-800 my-1"></div>
                            <p class="px-5 py-2 text-xs font-black text-accent-500 uppercase tracking-widest">Cuenta</p>
                            <a href="{{ route('perfil') }}" class="block px-5 py-2 text-sm font-bold text-gray-300 hover:bg-primary-500 hover:text-black transition-colors uppercase">Perfil</a>
                            
                            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="w-full text-left block px-5 py-3 text-sm font-black text-white bg-accent-500 hover:bg-red-600 transition-colors uppercase">Cerrar Sesión</button>
                            </form>
                        </div>
                    </div>
                @endif
            </nav>
        </div>
    </header>

    <main class="w-full mx-auto pb-12">
        {{ $slot }}
    </main>

    @livewireScripts
    <!-- Leaflet JS for Maps -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>
