<div class="max-w-5xl mx-auto px-6 mt-12 pb-12">
    <div class="mb-8 border-b-4 border-gray-900 pb-4">
        <h3 class="text-3xl font-black text-white uppercase">GESTIÓN DE SOLICITUDES</h3>
        <p class="text-gray-500 mt-2 font-bold uppercase">Revisa y aprueba a los pasajeros interesados en tus rutas.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-primary-500 border-4 border-black text-black font-black uppercase flex justify-between items-center">
            {{ session('success') }}
            <button type="button" onclick="this.parentElement.style.display='none'" class="hover:text-white">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($reservaciones as $reserva)
            <div wire:key="reserva-{{ $reserva['id'] }}" class="bg-black border-2 border-gray-800 p-6 flex flex-col h-full hover:border-primary-500 transition-colors duration-200 relative group">
                
                <div class="absolute top-0 left-0 h-full w-2 bg-gray-900 group-hover:bg-primary-500 transition-colors"></div>

                <div class="flex justify-between items-start mb-6 pl-4">
                    <div>
                        <span class="text-xs font-black text-gray-500 uppercase tracking-widest block mb-1">RESERVA #{{ $reserva['id'] }}</span>
                        <div class="text-sm font-black text-white flex items-center gap-1.5 uppercase">
                            @if(isset($reserva['viaje']))
                                📍 {{ $reserva['viaje']['destino'] ?? 'DESTINO' }}
                            @else
                                RUTA: VIAJE #{{ $reserva['viaje_id'] }}
                            @endif
                        </div>
                    </div>
                    
                    @if($reserva['estatus_reserva'] == 'solicitado')
                        <span class="px-3 py-1 text-xs font-black bg-black text-primary-500 border-2 border-primary-500 uppercase">⏳ PENDIENTE</span>
                    @elseif($reserva['estatus_reserva'] == 'aceptado')
                        <span class="px-3 py-1 text-xs font-black bg-[#10B981] text-black border-2 border-black uppercase">✅ APROBADO</span>
                    @else
                        <span class="px-3 py-1 text-xs font-black bg-accent-500 text-white border-2 border-black uppercase">❌ RECHAZADA</span>
                    @endif
                </div>
                
                <div class="bg-gray-900 p-5 mb-6 flex-grow border-2 border-gray-800 relative z-10 ml-4">
                    <h6 class="font-black text-white mb-3 flex items-center gap-4 uppercase">
                        <div class="w-12 h-12 bg-black border-2 border-gray-700 flex items-center justify-center text-xl overflow-hidden">
                            @if(isset($reserva['pasajero']['foto_credencial']) && $reserva['pasajero']['foto_credencial'])
                                <img src="{{ config('services.fastapi.public_url') }}/static/uploads/{{ $reserva['pasajero']['foto_credencial'] }}" alt="Foto" class="w-full h-full object-cover">
                            @else
                                <span class="text-gray-500">P</span>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="text-lg leading-tight">{{ $reserva['pasajero']['nombre'] ?? 'PASAJERO ASIGNADO' }}</span>
                            @if(isset($reserva['pasajero']['correo_institucional']))
                                <span class="text-[10px] bg-[#10B981] text-black px-1 mt-1 font-bold tracking-tight inline-block w-max">
                                    ALUMNO VERIFICADO
                                </span>
                            @endif
                        </div>
                    </h6>
                    <p class="text-sm text-gray-400 font-bold bg-black p-3 border-2 border-gray-800 uppercase">
                        SOLICITA <strong class="text-primary-500 font-black text-base">{{ $reserva['asientos_solicitados'] }}</strong> ASIENTO(S) PARA ESTE VIAJE.
                    </p>
                </div>
                
                <div class="pl-4">
                    @if($reserva['estatus_reserva'] == 'solicitado')
                        <div class="flex gap-3">
                            <button wire:click="responderSolicitud({{ $reserva['id'] }}, 'rechazado')" 
                                    class="w-1/2 py-4 border-2 border-accent-500 text-accent-500 font-black hover:bg-accent-500 hover:text-white transition-all active:translate-y-1 text-sm uppercase">
                                DENEGAR
                            </button>
                            <button wire:click="responderSolicitud({{ $reserva['id'] }}, 'aceptado')" 
                                    class="w-1/2 bg-primary-500 text-black font-black py-4 hover:bg-white border-b-4 border-orange-600 hover:border-gray-400 transition-all active:translate-y-1 text-sm uppercase">
                                APROBAR
                            </button>
                        </div>
                    @else
                        <div class="space-y-3">
                            <button class="w-full bg-gray-900 border-2 border-gray-800 text-gray-500 font-black py-4 cursor-not-allowed text-sm uppercase">
                                SOLICITUD YA PROCESADA
                            </button>
                            @if($reserva['estatus_reserva'] == 'aceptado')
                                <a href="{{ route('viaje.rastreo', $reserva['viaje_id']) }}" class="w-full block text-center border-2 border-primary-500 text-primary-500 font-black py-4 hover:bg-primary-500 hover:text-black transition-colors text-sm uppercase">
                                    📍 EMPEZAR / VER VIAJE
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-black border-4 border-gray-900 p-12 text-center relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-accent-500"></div>
                <div class="w-16 h-16 bg-gray-900 flex items-center justify-center mx-auto mb-4 border-2 border-gray-800">
                    <span class="text-2xl font-black text-gray-500">📥</span>
                </div>
                <h4 class="text-xl font-black text-white mb-2 uppercase">BANDEJA LIMPIA</h4>
                <p class="text-gray-500 font-bold uppercase">Aún no tienes solicitudes de viaje pendientes de revisar.</p>
            </div>
        @endforelse
    </div>
</div>
