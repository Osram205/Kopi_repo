<div class="max-w-5xl mx-auto px-6 mt-12 pb-12">
    <div class="mb-8 border-b-4 border-gray-900 pb-4">
        <h3 class="text-3xl font-black text-white uppercase">PANEL DE CONDUCTOR</h3>
        <p class="text-gray-500 mt-2 font-bold uppercase">Gestiona tu estatus y la información de tu vehículo.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-primary-500 border-4 border-black text-black font-black flex justify-between items-center uppercase">
            {{ session('success') }}
            <button type="button" onclick="this.parentElement.style.display='none'" class="hover:text-white">&times;</button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-accent-500 border-4 border-black text-white font-black flex justify-between items-center uppercase">
            {{ session('error') }}
            <button type="button" onclick="this.parentElement.style.display='none'" class="hover:text-black">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        
        <!-- Left Column: Driver Status -->
        <div class="md:col-span-5 space-y-6">
            @if(session('es_conductor') == false && (session('estatus_verificacion') == null || session('estatus_verificacion') == 'pendiente'))
                <div class="bg-black border-4 border-gray-900">
                    <div class="bg-primary-500 p-6 flex items-center justify-between border-b-4 border-black">
                        <h4 class="font-black text-black text-xl uppercase tracking-widest">VUÉLVETE CONDUCTOR</h4>
                        <span class="text-3xl">🏎️</span>
                    </div>
                    <div class="p-6 relative">
                        <div class="absolute top-0 left-0 w-2 h-full bg-primary-500"></div>
                        <p class="text-gray-500 mb-6 font-bold uppercase ml-4">Completa tu perfil subiendo tus documentos oficiales para poder publicar viajes y generar ingresos.</p>
                        
                        <form wire:submit="enviarSolicitudConduccion" class="space-y-6 ml-4">
                            <div>
                                <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">CREDENCIAL ESCOLAR (FRENTE)</label>
                                <input type="file" wire:model="foto_credencial_frente" class="w-full text-sm text-gray-400 file:mr-4 file:py-3 file:px-4 file:border-0 file:text-sm file:font-black file:bg-gray-900 file:text-primary-500 hover:file:bg-black file:uppercase file:cursor-pointer border-2 border-gray-800 focus:border-primary-500 transition-colors">
                                @error('foto_credencial_frente') <span class="text-accent-500 text-xs mt-2 block font-bold uppercase">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">CREDENCIAL ESCOLAR (REVERSO)</label>
                                <input type="file" wire:model="foto_credencial_trasera" class="w-full text-sm text-gray-400 file:mr-4 file:py-3 file:px-4 file:border-0 file:text-sm file:font-black file:bg-gray-900 file:text-primary-500 hover:file:bg-black file:uppercase file:cursor-pointer border-2 border-gray-800 focus:border-primary-500 transition-colors">
                                @error('foto_credencial_trasera') <span class="text-accent-500 text-xs mt-2 block font-bold uppercase">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">LICENCIA DE CONDUCIR</label>
                                <input type="file" wire:model="foto_licencia" class="w-full text-sm text-gray-400 file:mr-4 file:py-3 file:px-4 file:border-0 file:text-sm file:font-black file:bg-gray-900 file:text-primary-500 hover:file:bg-black file:uppercase file:cursor-pointer border-2 border-gray-800 focus:border-primary-500 transition-colors">
                                @error('foto_licencia') <span class="text-accent-500 text-xs mt-2 block font-bold uppercase">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">TARJETA DE CIRCULACIÓN</label>
                                <input type="file" wire:model="tarjeta_circulacion" class="w-full text-sm text-gray-400 file:mr-4 file:py-3 file:px-4 file:border-0 file:text-sm file:font-black file:bg-gray-900 file:text-primary-500 hover:file:bg-black file:uppercase file:cursor-pointer border-2 border-gray-800 focus:border-primary-500 transition-colors">
                                @error('tarjeta_circulacion') <span class="text-accent-500 text-xs mt-2 block font-bold uppercase">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" wire:loading.attr="disabled" class="w-full bg-primary-500 text-black font-black py-4 hover:bg-white mt-4 transition-all active:translate-y-1 uppercase border-b-4 border-orange-600 hover:border-gray-400">
                                <span wire:loading.remove wire:target="enviarSolicitudConduccion">ENVIAR DOCUMENTOS</span>
                                <span wire:loading wire:target="enviarSolicitudConduccion">SUBIENDO ARCHIVOS...</span>
                            </button>
                        </form>
                    </div>
                </div>
            @elseif(session('estatus_verificacion') == 'solicitado')
                <div class="bg-black border-4 border-primary-500 p-8 text-center relative">
                    <div class="absolute top-0 left-0 w-full h-2 bg-primary-500"></div>
                    <div class="w-16 h-16 bg-black text-primary-500 border-4 border-primary-500 flex items-center justify-center mx-auto mb-4 text-2xl font-black">⏳</div>
                    <h4 class="font-black text-white text-xl mb-2 uppercase">TU SOLICITUD ESTÁ EN REVISIÓN</h4>
                    <p class="text-primary-500 font-bold text-sm uppercase">El equipo de KOPI está validando tus documentos. Esto puede demorar un par de horas.</p>
                </div>
            @elseif(session('estatus_verificacion') == 'rechazado')
                <div class="bg-black border-4 border-accent-500 p-8 text-center relative">
                    <div class="absolute top-0 left-0 w-full h-2 bg-accent-500"></div>
                    <div class="w-16 h-16 bg-black text-accent-500 border-4 border-accent-500 flex items-center justify-center mx-auto mb-4 text-2xl font-black">❌</div>
                    <h4 class="font-black text-white text-xl mb-2 uppercase">SOLICITUD RECHAZADA</h4>
                    <p class="text-accent-500 font-bold text-sm uppercase">Tus documentos no cumplieron con los requisitos. Acude a administración escolar para aclarar tu situación.</p>
                </div>
            @else
                <div class="bg-black border-4 border-[#10B981] p-8 text-center relative">
                    <div class="absolute top-0 left-0 w-full h-2 bg-[#10B981]"></div>
                    <div class="w-16 h-16 bg-black text-[#10B981] border-4 border-[#10B981] flex items-center justify-center mx-auto mb-4 text-2xl font-black">✅</div>
                    <h4 class="font-black text-white text-xl mb-2 uppercase">ERES CONDUCTOR VERIFICADO</h4>
                    <p class="text-[#10B981] font-bold text-sm uppercase">Ya puedes registrar tu vehículo y comenzar a publicar tus rutas para llevar pasajeros.</p>
                </div>
            @endif
        </div>

        <!-- Right Column: Vehicle Form -->
        <div class="md:col-span-7">
            @if(session('es_conductor') == true || session('estatus_verificacion') == 'aprobado')
                <div class="bg-black border-4 border-gray-900 p-8 relative">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-accent-500 via-orange-500 to-primary-500"></div>
                    
                    <h5 class="text-xl font-black text-primary-500 mb-8 uppercase tracking-widest border-b-2 border-gray-800 pb-4">REGISTRO OBLIGATORIO DE VEHÍCULO</h5>
                    
                    <form wire:submit="registrarVehiculo" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">MARCA</label>
                                <select wire:model="marca" required class="w-full bg-gray-900 border-2 border-gray-800 rounded-none py-4 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase cursor-pointer">
                                    <option value="" disabled selected>SELECCIONA UNA MARCA...</option>
                                    <option value="Nissan">NISSAN</option>
                                    <option value="Chevrolet">CHEVROLET</option>
                                    <option value="Volkswagen">VOLKSWAGEN</option>
                                    <option value="Honda">HONDA</option>
                                    <option value="Toyota">TOYOTA</option>
                                    <option value="Mazda">MAZDA</option>
                                    <option value="Kia">KIA</option>
                                    <option value="Ford">FORD</option>
                                    <option value="Hyundai">HYUNDAI</option>
                                    <option value="Renault">RENAULT</option>
                                    <option value="Otro">OTRA MARCA</option>
                                </select>
                                @error('marca') <span class="text-accent-500 text-xs mt-2 block font-bold uppercase">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">MODELO / LÍNEA</label>
                                <input type="text" list="modelos-comunes" wire:model="modelo" placeholder="EJ. VERSA 2020" required class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                                <datalist id="modelos-comunes">
                                    <option value="Versa"></option>
                                    <option value="March"></option>
                                    <option value="Sentra"></option>
                                    <option value="Tsuru"></option>
                                    <option value="Aveo"></option>
                                    <option value="Beat"></option>
                                    <option value="Spark"></option>
                                    <option value="Jetta"></option>
                                    <option value="Vento"></option>
                                    <option value="Civic"></option>
                                    <option value="Mazda 3"></option>
                                    <option value="Rio"></option>
                                </datalist>
                                @error('modelo') <span class="text-accent-500 text-xs mt-2 block font-bold uppercase">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">COLOR</label>
                                <input type="text" wire:model="color" placeholder="EJ. BLANCO PERLA" required class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                                @error('color') <span class="text-accent-500 text-xs mt-2 block font-bold uppercase">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">PLACAS</label>
                                <input type="text" wire:model="placas" placeholder="EJ. UKP-892-A" required class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 uppercase transition-colors">
                                @error('placas') <span class="text-accent-500 text-xs mt-2 block font-bold uppercase">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">CAPACIDAD MÁXIMA (ASIENTOS)</label>
                            <input type="number" wire:model="asientos_totales" min="1" max="15" placeholder="TOTAL DE ASIENTOS LIBRES" required class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors uppercase">
                            @error('asientos_totales') <span class="text-accent-500 text-xs mt-2 block font-bold uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-primary-500 text-black font-black py-5 hover:bg-white transition-all active:translate-y-1 uppercase text-lg border-b-4 border-orange-600 hover:border-gray-400">
                                GUARDAR VEHÍCULO
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
