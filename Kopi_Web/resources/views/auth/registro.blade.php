<!DOCTYPE html>
<html lang="es" x-data="{ theme: localStorage.getItem('theme') || 'dark' }" x-init="$watch('theme', val => localStorage.setItem('theme', val))" :class="theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopi - Crear Cuenta</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo/apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-900 text-gray-100 font-sans antialiased min-h-screen flex flex-col justify-center py-12 relative overflow-x-hidden transition-colors">
    
    <button type="button" @click="theme = theme === 'dark' ? 'light' : 'dark'" class="absolute top-6 right-6 z-50 w-12 h-12 bg-black border-4 border-gray-800 flex items-center justify-center text-gray-400 hover:border-primary-500 transition-all transform hover:-translate-y-1 shadow-[4px_4px_0px_rgba(251,191,36,0.3)]">
        <span x-show="theme === 'dark'">☀️</span>
        <span x-show="theme === 'light'">🌙</span>
    </button>

    <div class="w-full max-w-lg mx-auto px-4 sm:px-6 relative z-10">
        
        @if(session('error'))
            <div class="mb-6 p-4 bg-accent-500 border-4 border-black text-white font-black flex justify-between items-center uppercase text-sm shadow-[4px_4px_0px_rgba(0,0,0,1)]">
                {{ is_array(session('error')) ? json_encode(session('error')) : session('error') }}
                <button type="button" onclick="this.parentElement.style.display='none'" class="hover:text-black">&times;</button>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="mb-6 p-4 bg-accent-500 border-4 border-black text-white font-black uppercase text-sm shadow-[4px_4px_0px_rgba(0,0,0,1)]">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-black border-4 border-gray-900 relative shadow-[8px_8px_0px_rgba(0,0,0,1)] transition-colors">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-accent-500 via-orange-500 to-primary-500"></div>
            
            <div class="bg-gray-900 p-8 border-b-4 border-black text-center transition-colors">
                <div class="w-16 h-16 bg-primary-500 flex items-center justify-center mx-auto mb-4 border-2 border-transparent">
                    <img src="{{ asset('logo/android-chrome-192x192.png') }}" alt="Logo KOPI" class="w-10 h-10 object-contain">
                </div>
                <h2 class="text-3xl font-black text-primary-500 tracking-widest mb-1 uppercase">ÚNETE A KOPI</h2>
                <p class="text-gray-500 font-bold uppercase text-xs tracking-widest">REGISTRA TUS DATOS INSTITUCIONALES</p>
            </div>

            <div class="p-8">
                <form action="{{ route('registro.post') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">NOMBRES</label>
                            <input type="text" name="nombre" placeholder="TU NOMBRE(S)" value="{{ old('nombre') }}" required
                                   maxlength="50" pattern="^[a-zA-Z\sÁÉÍÓÚáéíóúÑñ]+$"
                                   oninput="this.value = this.value.replace(/[^a-zA-Z\sÁÉÍÓÚáéíóúÑñ]/g, '').slice(0, 50)"
                                   class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                        </div>
                        <div>
                            <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">APELLIDOS</label>
                            <input type="text" name="apellidos" placeholder="TUS APELLIDOS" value="{{ old('apellidos') }}" required
                                   maxlength="50" pattern="^[a-zA-Z\sÁÉÍÓÚáéíóúÑñ]+$"
                                   oninput="this.value = this.value.replace(/[^a-zA-Z\sÁÉÍÓÚáéíóúÑñ]/g, '').slice(0, 50)"
                                   class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">MATRÍCULA</label>
                            <input type="text" id="matricula" name="matricula" placeholder="123456" value="{{ old('matricula') }}" required
                                   minlength="9" maxlength="9" pattern="[0-9]{9}"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 9)"
                                   class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                        </div>
                        <div>
                            <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">PROGRAMA EDUCATIVO</label>
                            <select name="carrera" required class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase cursor-pointer">
                                <option value="" disabled selected>SELECCIONA TU CARRERA...</option>
                                <option value="Ingeniería Automotriz">INGENIERÍA AUTOMOTRIZ</option>
                                <option value="Ingeniería en Mecatrónica">INGENIERÍA EN MECATRÓNICA</option>
                                <option value="Ingeniería en Sistemas Computacionales">INGENIERÍA EN SISTEMAS COMPUTACIONALES</option>
                                <option value="Ingeniería en Redes y Telecomunicaciones">INGENIERÍA EN REDES Y TELECOMUNICACIONES</option>
                                <option value="Ingeniería en Tecnologías de Manufactura">INGENIERÍA EN TECNOLOGÍAS DE MANUFACTURA</option>
                                <option value="Licenciatura en Administración y Gestión Empresarial">LIC. ADMINISTRACIÓN Y GESTIÓN EMPRESARIAL</option>
                                <option value="Licenciatura en Negocios Internacionales">LIC. NEGOCIOS INTERNACIONALES</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">CORREO INSTITUCIONAL</label>
                        <input type="email" id="correo_institucional" name="correo_institucional" placeholder="ALUMNO@UPQ.EDU.MX" value="{{ old('correo_institucional') }}" required
                               maxlength="100"
                               class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">TELÉFONO CELULAR (WHATSAPP)</label>
                        <input type="tel" name="telefono" placeholder="442..." value="{{ old('telefono') }}" required
                               minlength="10" maxlength="10" pattern="[0-9]{10}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                               class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">CREAR CONTRASEÑA</label>
                            <input type="password" name="contrasena" placeholder="MÍN. 6 CARACTERES" required
                                   minlength="6" maxlength="32"
                                   class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                        </div>
                    </div>
                    


                    <div class="pt-4">
                        <button type="submit" class="w-full bg-primary-500 text-black font-black text-lg py-5 hover:bg-white hover:text-black transition-all active:translate-y-1 uppercase border-b-4 border-orange-600 hover:border-gray-400">
                            REGISTRARME
                        </button>
                    </div>

                    <div class="text-center mt-6">
                        <span class="text-gray-500 font-black text-xs uppercase tracking-widest">¿YA TIENES CUENTA?</span>
                        <a href="{{ route('login') }}" class="text-primary-500 font-black ml-2 hover:text-white transition-colors text-xs uppercase tracking-widest">INICIA SESIÓN</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @livewireScripts
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const matriculaInput = document.getElementById('matricula');
            const correoInput = document.getElementById('correo_institucional');
            let correoModificadoManualmente = false;

            correoInput.addEventListener('input', function() {
                if (this.value.trim() === '') {
                    correoModificadoManualmente = false;
                } else {
                    correoModificadoManualmente = true;
                }
            });

            matriculaInput.addEventListener('input', function() {
                if (!correoModificadoManualmente) {
                    const matricula = this.value;
                    if (matricula) {
                        correoInput.value = matricula + '@upq.edu.mx';
                    } else {
                        correoInput.value = '';
                    }
                }
            });
        });
    </script>
</body>
</html>
