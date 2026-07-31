<div class="flex items-center justify-center min-h-[calc(100vh-100px)] py-12">
    
    <div class="w-full max-w-md px-6">
        
        @if(session()->has('error'))
            <div class="mb-6 p-4 bg-accent-500 border-4 border-black text-white font-black flex justify-between items-center uppercase text-sm shadow-[4px_4px_0px_rgba(0,0,0,1)]">
                {{ session('error') }}
                <button type="button" onclick="this.parentElement.style.display='none'" class="hover:text-black">&times;</button>
            </div>
        @endif

        <div class="bg-black border-4 border-gray-900 relative shadow-[8px_8px_0px_rgba(0,0,0,1)] transition-colors">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-accent-500 via-orange-500 to-primary-500"></div>
            
            <div class="bg-gray-900 p-8 border-b-4 border-black text-center transition-colors">
                <div class="w-16 h-16 bg-primary-500 flex items-center justify-center mx-auto mb-4 border-2 border-transparent">
                    <span class="text-3xl">{{ $paso == 1 ? '🪪' : '🔐' }}</span>
                </div>
                <h3 class="text-2xl font-black text-white tracking-widest mb-1 uppercase">RECUPERACIÓN DE ACCESO</h3>
                <p class="text-gray-500 font-bold uppercase text-xs tracking-widest">
                    {{ $paso == 1 ? 'PASO 1: CONFIRMA TUS CREDENCIALES.' : 'PASO 2: GENERA TU NUEVA CONTRASEÑA.' }}
                </p>
            </div>

            <div class="p-8">
                @if($paso == 1)
                    <form wire:submit="verificarIdentidad" class="space-y-6">
                        <div>
                            <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">CORREO INSTITUCIONAL</label>
                            <input type="email" wire:model="correo_institucional" placeholder="ALUMNO@UPQ.EDU.MX" required
                                   class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                            @error('correo_institucional') 
                                <p class="mt-2 text-sm text-accent-500 font-bold uppercase">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">MATRÍCULA ESCOLAR</label>
                            <input type="text" wire:model="matricula" placeholder="123456" required
                                   class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                            @error('matricula') 
                                <p class="mt-2 text-sm text-accent-500 font-bold uppercase">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-4">
                            <button type="submit" wire:loading.attr="disabled" class="w-full bg-primary-500 text-black font-black text-lg py-5 hover:bg-white hover:text-black transition-all active:translate-y-1 uppercase border-b-4 border-orange-600 hover:border-gray-400">
                                <span wire:loading.remove wire:target="verificarIdentidad">VALIDAR DATOS</span>
                                <span wire:loading wire:target="verificarIdentidad">⏳ COMPROBANDO...</span>
                            </button>
                        </div>
                    </form>
                
                @else
                    <div class="mb-6 p-4 bg-black border-4 border-[#10B981] text-[#10B981] uppercase font-bold text-xs tracking-wider">
                        <strong class="font-black text-white block mb-1">✅ CÓDIGO ENVIADO</strong> 
                        REVISA LA BANDEJA DE ENTRADA DE <strong class="text-white">{{ $correo_institucional }}</strong> E INGRESA EL CÓDIGO DE 6 DÍGITOS.
                    </div>

                    <form wire:submit="cambiarContrasena" class="space-y-6">
                        <div>
                            <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">CÓDIGO DE SEGURIDAD (6 DÍGITOS)</label>
                            <input type="text" wire:model="codigo_otp" placeholder="000000" maxlength="6" required
                                   class="w-full bg-black border-4 border-primary-500 rounded-none py-3 px-4 text-primary-500 focus:outline-none focus:border-white transition-colors text-center text-3xl font-black tracking-[0.5em] uppercase">
                            @error('codigo_otp') 
                                <p class="mt-2 text-sm text-accent-500 font-bold uppercase text-center">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">CREAR NUEVA CONTRASEÑA</label>
                            <input type="password" wire:model="nueva_contrasena" placeholder="NUEVA CONTRASEÑA" required minlength="6"
                                   class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                            @error('nueva_contrasena') 
                                <p class="mt-2 text-sm text-accent-500 font-bold uppercase">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-4">
                            <button type="submit" wire:loading.attr="disabled" class="w-full bg-white text-black font-black text-lg py-5 hover:bg-primary-500 transition-all active:translate-y-1 uppercase border-b-4 border-gray-400 hover:border-orange-600">
                                <span wire:loading.remove wire:target="cambiarContrasena">GUARDAR CAMBIOS</span>
                                <span wire:loading wire:target="cambiarContrasena">VALIDANDO CÓDIGO...</span>
                            </button>
                        </div>
                    </form>
                @endif

                <div class="text-center mt-6">
                    <a href="{{ route('login') }}" class="text-gray-500 font-black text-xs hover:text-white transition-colors uppercase tracking-widest">
                        ← CANCELAR Y VOLVER AL INICIO
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
