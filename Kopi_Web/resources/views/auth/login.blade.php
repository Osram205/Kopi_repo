<!DOCTYPE html>
<html lang="es" x-data="{ theme: localStorage.getItem('theme') || 'dark' }" x-init="$watch('theme', val => localStorage.setItem('theme', val))" :class="theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopi - Iniciar Sesión</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo/apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-900 text-gray-100 font-sans antialiased min-h-screen flex flex-col justify-center py-12 relative overflow-x-hidden transition-colors">
    
    <button type="button" @click="theme = theme === 'dark' ? 'light' : 'dark'" class="absolute top-6 right-6 z-50 w-12 h-12 bg-black border-4 border-gray-800 flex items-center justify-center text-gray-400 hover:border-primary-500 transition-all transform hover:-translate-y-1 shadow-[4px_4px_0px_rgba(251,191,36,0.3)]">
        <span x-show="theme === 'dark'">☀️</span>
        <span x-show="theme === 'light'">🌙</span>
    </button>

    <div class="w-full max-w-md mx-auto px-4 sm:px-6 relative z-10">
        
        @if(session('error'))
            <div class="mb-6 p-4 bg-accent-500 border-4 border-black text-white font-black flex justify-between items-center uppercase text-sm shadow-[4px_4px_0px_rgba(0,0,0,1)]">
                {{ is_array(session('error')) ? json_encode(session('error')) : session('error') }}
                <button type="button" onclick="this.parentElement.style.display='none'" class="hover:text-black">&times;</button>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 p-4 bg-primary-500 border-4 border-black text-black font-black flex justify-between items-center uppercase text-sm shadow-[4px_4px_0px_rgba(0,0,0,1)]">
                {{ session('success') }}
                <button type="button" onclick="this.parentElement.style.display='none'" class="hover:text-white">&times;</button>
            </div>
        @endif

        <div class="bg-black border-4 border-gray-900 relative shadow-[8px_8px_0px_rgba(0,0,0,1)] transition-colors">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-accent-500 via-orange-500 to-primary-500"></div>
            
            <div class="bg-gray-900 p-8 border-b-4 border-black text-center transition-colors">
                <div class="w-16 h-16 bg-primary-500 flex items-center justify-center mx-auto mb-6 border-2 border-transparent">
                    <img src="{{ asset('logo/android-chrome-192x192.png') }}" alt="Logo KOPI" class="w-10 h-10 object-contain">
                </div>
                <h1 class="text-4xl font-black text-primary-500 tracking-widest mb-2 uppercase">KOPI</h1>
                <p class="text-gray-500 font-bold uppercase text-xs tracking-widest">SISTEMA DE CARPOOLING</p>
            </div>

            <div class="p-8">
                <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">CORREO INSTITUCIONAL</label>
                        <input type="email" name="correo_institucional" placeholder="ALUMNO@UPQ.EDU.MX" value="{{ old('correo_institucional') }}" required
                               class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                        @error('correo_institucional')
                            <p class="mt-2 text-sm text-accent-500 font-bold uppercase">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">CONTRASEÑA</label>
                        <input type="password" name="contrasena" placeholder="••••••••" required
                               class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                        @error('contrasena')
                            <p class="mt-2 text-sm text-accent-500 font-bold uppercase">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="text-right">
                        <a href="{{ route('password.request') }}" class="text-xs font-black text-primary-500 hover:text-white transition-colors uppercase tracking-wider">¿OLVIDASTE TU CONTRASEÑA?</a>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-primary-500 text-black font-black text-lg py-5 hover:bg-white hover:text-black transition-all active:translate-y-1 uppercase border-b-4 border-orange-600 hover:border-gray-400">
                            INGRESAR AL SISTEMA
                        </button>
                    </div>

                    <div class="flex items-center my-6">
                        <hr class="flex-grow border-gray-900 border-2">
                        <span class="px-4 text-xs font-black text-gray-700 uppercase tracking-widest">O</span>
                        <hr class="flex-grow border-gray-900 border-2">
                    </div>

                    <a href="{{ route('registro') }}" class="flex items-center justify-center w-full bg-black border-4 border-gray-800 text-white font-black text-lg py-4 hover:bg-gray-900 hover:border-primary-500 hover:text-primary-500 transition-colors uppercase">
                        CREAR NUEVA CUENTA
                    </a>
                </form>
            </div>

        </div>
        
        <p class="text-center text-gray-700 text-xs font-black mt-8 uppercase tracking-widest">
            EXCLUSIVO PARA LA COMUNIDAD UNIVERSITARIA
        </p>

    </div>

    @livewireScripts
</body>
</html>
