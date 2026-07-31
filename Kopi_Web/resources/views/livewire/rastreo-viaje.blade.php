<div class="max-w-4xl mx-auto px-6 mt-12 pb-12">
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-accent-500 border-4 border-black text-white font-black uppercase flex justify-between items-center">
            {{ session('error') }}
            <button type="button" onclick="this.parentElement.style.display='none'" class="hover:text-black">&times;</button>
        </div>
    @endif
    
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-primary-500 border-4 border-black text-black font-black uppercase flex justify-between items-center">
            {{ session('success') }}
            <button type="button" onclick="this.parentElement.style.display='none'" class="hover:text-white">&times;</button>
        </div>
    @endif

    <div class="bg-black border-4 border-gray-900 relative">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-accent-500 via-orange-500 to-primary-500"></div>

        <div class="bg-gray-900 p-6 sm:p-8 flex flex-col sm:flex-row justify-between items-start sm:items-center border-b-4 border-gray-800 gap-4">
            <div>
                <span class="text-xs font-black text-gray-500 uppercase tracking-widest mb-1 block">SEGUIMIENTO DE RUTA</span>
                <h3 class="text-3xl font-black text-white uppercase">📍 HACIA {{ $viaje['destino'] ?? 'DESTINO' }}</h3>
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <button onclick="alert('🚨 Enviando alerta de pánico y compartiendo ubicación GPS en tiempo real a tus contactos de emergencia...')" class="flex-1 sm:flex-none bg-accent-500 text-white font-black px-5 py-3 uppercase border-b-4 border-red-900 hover:bg-red-500 hover:border-red-800 transition-all active:translate-y-1">
                    <span>🚨 SOS ALERTA</span>
                </button>
                <button wire:click="$refresh" class="w-12 h-12 bg-gray-800 border-b-4 border-gray-900 text-white flex items-center justify-center hover:bg-gray-700 transition-all active:translate-y-1">
                    🔄
                </button>
            </div>
        </div>

        <div class="p-6 sm:p-10">
            @if($viaje)
                <!-- Timeline status -->
                <div class="flex items-center justify-between mb-12 relative max-w-2xl mx-auto px-4">
                    <div class="absolute left-10 right-10 top-1/2 -translate-y-1/2 h-2 bg-gray-900 z-0 border-y border-gray-800">
                        <div class="h-full bg-primary-500 z-0 transition-all duration-700 ease-out" 
                             style="width: {{ $viaje['estatus'] == 'en_curso' ? '50%' : ($viaje['estatus'] == 'completado' ? '100%' : '0%') }};"></div>
                    </div>
                    
                    <div class="relative z-10 flex flex-col items-center group">
                        <div class="w-12 h-12 flex items-center justify-center text-xl font-black transition-all duration-300 border-4 {{ $viaje['estatus'] == 'programado' || $viaje['estatus'] == 'en_curso' || $viaje['estatus'] == 'completado' ? 'bg-primary-500 text-black border-black' : 'bg-black text-gray-600 border-gray-800' }}">
                            1
                        </div>
                        <span class="mt-3 text-xs font-black {{ $viaje['estatus'] == 'programado' || $viaje['estatus'] == 'en_curso' || $viaje['estatus'] == 'completado' ? 'text-primary-500' : 'text-gray-600' }} uppercase tracking-widest">PROGRAMADO</span>
                    </div>

                    <div class="relative z-10 flex flex-col items-center group">
                        <div class="w-12 h-12 flex items-center justify-center text-xl font-black transition-all duration-300 border-4 {{ $viaje['estatus'] == 'en_curso' || $viaje['estatus'] == 'completado' ? 'bg-primary-500 text-black border-black' : 'bg-black text-gray-600 border-gray-800' }}">
                            2
                        </div>
                        <span class="mt-3 text-xs font-black {{ $viaje['estatus'] == 'en_curso' || $viaje['estatus'] == 'completado' ? 'text-primary-500' : 'text-gray-600' }} uppercase tracking-widest">EN CURSO</span>
                    </div>

                    <div class="relative z-10 flex flex-col items-center group">
                        <div class="w-12 h-12 flex items-center justify-center text-xl font-black transition-all duration-300 border-4 {{ $viaje['estatus'] == 'completado' ? 'bg-primary-500 text-black border-black' : 'bg-black text-gray-600 border-gray-800' }}">
                            3
                        </div>
                        <span class="mt-3 text-xs font-black {{ $viaje['estatus'] == 'completado' ? 'text-primary-500' : 'text-gray-600' }} uppercase tracking-widest">COMPLETADO</span>
                    </div>
                </div>

                <div class="text-center mb-10 bg-gray-900 border-2 border-gray-800 p-8 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-32 h-32 opacity-10">
                        <!-- Estética racing bg pattern -->
                        <div class="w-full h-full" style="background-image: repeating-linear-gradient(45deg, #fff 0, #fff 10px, transparent 10px, transparent 20px);"></div>
                    </div>
                    
                    @if($viaje['estatus'] == 'programado')
                        <span class="relative z-10 inline-block bg-black text-primary-500 border-4 border-primary-500 px-8 py-4 text-xl font-black uppercase">⏳ ESPERANDO AL CONDUCTOR</span>
                        <p class="text-gray-400 mt-4 text-sm font-bold uppercase relative z-10">El conductor aún no ha iniciado el viaje desde la app.</p>
                    @elseif($viaje['estatus'] == 'en_curso')
                        <span class="relative z-10 inline-block bg-[#10B981] text-black border-4 border-black px-8 py-4 text-xl font-black uppercase shadow-[8px_8px_0px_rgba(16,185,129,0.5)]">🚗💨 ¡VIAJE EN PROGRESO!</span>
                        <p class="text-gray-300 mt-6 text-sm font-bold uppercase relative z-10">Dirígete al punto de encuentro y viaja con seguridad.</p>
                    @elseif($viaje['estatus'] == 'completado')
                        <span class="relative z-10 inline-block bg-white text-black border-4 border-black px-8 py-4 text-xl font-black uppercase">🏁 VIAJE COMPLETADO</span>
                        <p class="text-gray-400 mt-5 text-sm font-bold uppercase relative z-10">Esperamos que hayas tenido un excelente trayecto.</p>
                        
                        @if(!$es_conductor)
                            <a href="{{ route('calificar.viaje', $viaje_id) }}" class="inline-block mt-6 bg-primary-500 text-black font-black py-4 px-10 border-b-4 border-orange-600 hover:bg-white hover:border-gray-400 transition-all active:translate-y-1 uppercase relative z-10">
                                ⭐ CALIFICAR EXPERIENCIA
                            </a>
                        @endif
                    @endif
                </div>

                <!-- Mapa de Rastreo en Vivo -->
                <div class="mb-10" wire:ignore>
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3 bg-gray-900 border-2 border-gray-800 p-3">
                        <h6 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
                            <span class="w-3 h-3 bg-primary-500 animate-pulse"></span>
                            RASTREO GPS EN VIVO
                        </h6>
                        <span id="ws-status" class="text-xs font-black px-3 py-1.5 bg-black text-gray-500 border-2 border-gray-800 flex items-center gap-2 uppercase">
                            <span class="w-2.5 h-2.5 bg-gray-600" id="ws-indicator"></span>
                            <span id="ws-text">CONECTANDO...</span>
                        </span>
                    </div>
                    <div class="p-2 bg-gray-900 border-2 border-gray-800">
                        <div id="mapa-rastreo" class="w-full h-96 bg-black z-0 relative"></div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="bg-black border-2 border-gray-800 p-5 flex items-center gap-5 hover:border-primary-500 transition-colors">
                        <div class="w-14 h-14 bg-gray-900 flex items-center justify-center text-2xl overflow-hidden border-2 border-gray-700">
                            @if(isset($viaje['conductor']['foto_credencial']) && $viaje['conductor']['foto_credencial'])
                                <img src="{{ config('services.fastapi.public_url') }}/static/uploads/{{ $viaje['conductor']['foto_credencial'] }}" alt="Foto" class="w-full h-full object-cover">
                            @else
                                <span class="font-black text-gray-500">C</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-primary-500 font-black uppercase tracking-widest mb-1">TU CONDUCTOR</p>
                            <p class="text-white font-black text-lg flex items-center gap-2 uppercase">{{ $viaje['conductor']['nombre'] ?? 'CONDUCTOR ASIGNADO' }} <span class="text-[10px] bg-[#10B981] text-black px-1 font-bold tracking-tight">VERIFICADO</span></p>
                        </div>
                    </div>
                    <div class="bg-black border-2 border-gray-800 p-5 flex items-center gap-5 hover:border-primary-500 transition-colors">
                        <div class="w-14 h-14 bg-gray-900 flex items-center justify-center text-2xl border-2 border-gray-700">🚗</div>
                        <div>
                            <p class="text-xs text-primary-500 font-black uppercase tracking-widest mb-1">VEHÍCULO</p>
                            <p class="text-white font-black text-lg uppercase">
                                @if(isset($viaje['vehiculo']))
                                    {{ $viaje['vehiculo']['marca'] }} {{ $viaje['vehiculo']['modelo'] }} <span class="text-gray-500">({{ $viaje['vehiculo']['color'] }})</span>
                                @else
                                    AUTO ASIGNADO
                                @endif
                            </p>
                            @if(isset($viaje['vehiculo']['placas']))
                                <p class="text-xs font-black text-black bg-white inline-block px-2 py-0.5 mt-1 border-b-2 border-gray-400">
                                    {{ substr($viaje['vehiculo']['placas'], 0, 3) }}-***
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                @if($es_conductor && isset($viaje['reservaciones']) && count($viaje['reservaciones']) > 0)
                    <div class="mt-10 bg-gray-900 border-2 border-gray-800 p-6">
                        <h6 class="text-sm font-black text-white uppercase tracking-widest mb-5 border-b-2 border-gray-800 pb-3 flex items-center gap-2">
                            <div class="w-2 h-6 bg-primary-500"></div>
                            PASAJEROS A BORDO ({{ count(array_filter($viaje['reservaciones'], fn($r) => $r['estatus_reserva'] == 'aceptado')) }})
                        </h6>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($viaje['reservaciones'] as $reserva)
                                @if($reserva['estatus_reserva'] == 'aceptado')
                                    <div class="bg-black border-2 border-gray-800 p-4 flex items-center justify-between hover:border-primary-500 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gray-900 overflow-hidden flex items-center justify-center border-2 border-gray-800">
                                                @if(isset($reserva['pasajero']['foto_credencial']) && $reserva['pasajero']['foto_credencial'])
                                                    <img src="{{ config('services.fastapi.public_url') }}/static/uploads/{{ $reserva['pasajero']['foto_credencial'] }}" alt="Pasajero" class="w-full h-full object-cover">
                                                @else
                                                    <span class="font-black text-gray-500">P</span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-white font-black text-base uppercase">{{ $reserva['pasajero']['nombre'] }}</p>
                                                <p class="text-[10px] font-black text-primary-500 bg-gray-900 inline-block px-2 py-0.5 border border-gray-800 mt-1 uppercase">{{ $reserva['asientos_solicitados'] }} ASIENTO(S)</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($es_conductor && $viaje['estatus'] != 'completado')
                    <div class="my-10 relative">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t-2 border-gray-800"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-4 bg-black text-sm font-black text-gray-500 uppercase tracking-widest">CONTROLES DE CONDUCTOR</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-5">
                        @if($viaje['estatus'] == 'programado')
                            <button wire:click="iniciarViaje" class="bg-[#10B981] text-black font-black py-5 uppercase text-xl hover:bg-white border-b-4 border-green-800 hover:border-gray-400 transition-all active:translate-y-1 flex items-center justify-center gap-3 w-full">
                                <span class="text-2xl">▶️</span> INICIAR RUTA AHORA
                            </button>
                        @endif

                        @if($viaje['estatus'] == 'en_curso')
                            <button wire:click="finalizarViaje" class="bg-white text-black font-black py-5 uppercase text-xl hover:bg-primary-500 border-b-4 border-gray-400 hover:border-orange-600 transition-all active:translate-y-1 flex items-center justify-center gap-3 w-full">
                                <span class="text-2xl">🏁</span> FINALIZAR TRAYECTO
                            </button>
                        @endif
                    </div>
                @endif
                
            @else
                <div class="text-center py-12 bg-gray-900 border-4 border-gray-800">
                    <div class="w-16 h-16 bg-black flex items-center justify-center mx-auto mb-4 text-2xl border-2 border-gray-800">⚠️</div>
                    <h4 class="text-xl font-black text-white mb-2 uppercase">VIAJE NO ENCONTRADO</h4>
                    <p class="text-gray-500 font-bold uppercase">El viaje especificado no existe o no tienes acceso.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // 1. Configuración Inicial del Mapa
        const initLat = 20.5881;
        const initLng = -100.3899;
        const tileUrl = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
        
        const map = L.map('mapa-rastreo').setView([initLat, initLng], 14);
        L.tileLayer(tileUrl, { attribution: '&copy; CARTO' }).addTo(map);

        // Ícono de auto para el conductor
        const carIcon = L.divIcon({
            html: `<div style="background-color: var(--color-primary-500); width: 30px; height: 30px; border-radius: 50%; border: 4px solid var(--color-brand-800); box-shadow: 0 0 15px var(--color-primary-500); display: flex; align-items: center; justify-content: center; font-size: 14px;">🚘</div>`,
            className: 'custom-car-icon',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        let marker = L.marker([initLat, initLng], { icon: carIcon }).addTo(map);

        // 2. Conexión WebSocket a FastAPI
        const baseUrl = "{{ config('services.fastapi.public_url') }}";
        const wsBaseUrl = baseUrl.replace('http://', 'ws://').replace('https://', 'wss://');
        const token = "{{ Session::get('jwt_token') }}";
        const viajeId = "{{ $viaje_id }}";
        
        const wsUrl = `${wsBaseUrl}/ws/gps/${viajeId}?token=${token}`;
        let ws = new WebSocket(wsUrl);
        
        const statusText = document.getElementById('ws-text');
        const statusIndicator = document.getElementById('ws-indicator');

        ws.onopen = () => {
            statusText.innerText = 'GPS Conectado';
            statusText.className = 'text-[#10B981]';
            statusIndicator.className = 'w-2 h-2 rounded-full bg-[#10B981] animate-pulse';
            
            // Si es conductor y el viaje está en curso, empezar a emitir ubicación
            const esConductor = @json($es_conductor);
            const estatusViaje = @json($viaje['estatus'] ?? '');
            
            if(esConductor && estatusViaje === 'en_curso') {
                if ("geolocation" in navigator) {
                    navigator.geolocation.watchPosition((position) => {
                        const { latitude, longitude } = position.coords;
                        
                        // Enviar ubicación real al servidor WebSocket
                        ws.send(JSON.stringify({
                            latitud: latitude,
                            longitud: longitude,
                            timestamp: Date.now()
                        }));

                        // Actualizar mi propio mapa
                        marker.setLatLng([latitude, longitude]);
                        map.panTo([latitude, longitude]);
                        
                    }, (error) => {
                        console.error("Error GPS:", error);
                    }, { enableHighAccuracy: true });
                }
            }
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            
            if(data.tipo === 'actualizacion_gps') {
                const lat = data.latitud;
                const lng = data.longitud;
                
                // Animar el movimiento del marcador
                marker.setLatLng([lat, lng]);
                map.panTo([lat, lng], { animate: true, duration: 1 });
            }
            else if(data.tipo === 'alerta_conexion') {
                alert(data.mensaje);
            }
        };

        ws.onclose = () => {
            statusText.innerText = 'Desconectado';
            statusText.className = 'text-accent-500';
            statusIndicator.className = 'w-2 h-2 rounded-full bg-accent-500';
        };

        setTimeout(() => map.invalidateSize(), 500);
    });
</script>
