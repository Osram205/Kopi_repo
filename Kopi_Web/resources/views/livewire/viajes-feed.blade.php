<div class="max-w-5xl mx-auto px-6 mt-12 grid grid-cols-1 lg:grid-cols-12 gap-12">
    
    @if (session()->has('success'))
        <div class="lg:col-span-12 p-4 bg-[#10B981]/10 border border-[#10B981] text-[#10B981] rounded-xl flex justify-between items-center">
            <span class="font-semibold">{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.style.display='none'" class="text-[#10B981] hover:text-white">&times;</button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="lg:col-span-12 p-4 bg-accent-500/10 border border-accent-500 text-accent-500 rounded-xl flex justify-between items-center">
            <span class="font-semibold">{{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.style.display='none'" class="text-accent-500 hover:text-white">&times;</button>
        </div>
    @endif

    <!-- Left Column: Search -->
    <div class="lg:col-span-5 space-y-8">
        <div class="space-y-4">
            <h1 class="text-5xl md:text-6xl font-black tracking-tighter uppercase text-white leading-none">
                Encuentra tu <br><span class="bg-primary-500 text-black px-2 inline-block mt-2 transform -skew-x-6">Viaje Ideal</span>
            </h1>
            <p class="text-gray-400 text-lg font-bold">Busca la ruta perfecta a tu destino dentro de nuestra red universitaria.</p>
        </div>
        
        <div class="bg-black p-6 border-4 border-gray-900 relative space-y-4">
            <div class="absolute -top-1 -right-1 w-8 h-8 bg-primary-500"></div>

            <div class="relative group">
                <input type="text" wire:model.live="search" placeholder="BUSCAR DESTINO O RUTA (EJ. UPQ, CENTRO...)" 
                       class="w-full bg-gray-900 border-2 border-gray-800 rounded-none py-4 px-4 text-white font-bold uppercase placeholder-gray-500 focus:outline-none focus:border-primary-500 transition-colors shadow-[4px_4px_0px_rgba(251,191,36,0.3)] focus:shadow-[4px_4px_0px_rgba(251,191,36,1)]">
            </div>

            <!-- Mapa de Exploración -->
            <div wire:ignore class="mt-6 border-2 border-gray-800 p-1 bg-gray-900">
                <p class="text-xs font-black text-primary-500 uppercase tracking-widest mb-3 flex items-center gap-2 px-2">
                    <span class="w-2 h-2 bg-accent-500 animate-pulse"></span>
                    Salidas Activas
                </p>
                <div id="mapa-explorar" class="w-full h-80 bg-black z-0 relative"></div>
            </div>
        </div>
    </div>

    <!-- Right Column: Results -->
    <div class="lg:col-span-7 space-y-6 mt-8 lg:mt-0">
        <h2 class="text-3xl font-black mb-6 flex items-center justify-between border-b-4 border-gray-900 pb-4 uppercase">
            <span class="text-white">Rutas Activas</span>
            <span class="text-sm px-3 py-1 bg-primary-500 text-black font-black transform skew-x-[-10deg]">{{ count($viajes) }} RUTAS</span>
        </h2>
        
        <div class="space-y-6">
            @forelse($viajes as $viaje)
                <!-- Trip Card -->
                <div wire:key="viaje-{{ $viaje['id'] }}" class="bg-black border-2 border-gray-800 p-6 hover:border-primary-500 transition-colors duration-200 group relative">
                    
                    <div class="absolute top-0 right-0 w-2 h-full bg-gray-900 group-hover:bg-primary-500 transition-colors"></div>

                    <div class="flex justify-between items-start mb-6">
                        <div class="flex flex-col relative pl-6 space-y-6">
                            <div class="absolute left-0 top-3 bottom-3 w-1 bg-gray-800"></div>
                            
                            <div class="relative">
                                <div class="absolute -left-[27px] top-1.5 w-3 h-3 bg-primary-500 outline outline-4 outline-black"></div>
                                <p class="text-gray-500 font-black text-xs uppercase tracking-widest mb-1">Origen</p>
                                <p class="text-2xl font-black text-white leading-none uppercase">{{ $viaje['origen'] }}</p>
                            </div>
                            
                            <!-- Waypoints (Paradas intermedias) -->
                            @if(isset($viaje['paradas']) && count($viaje['paradas']) > 1)
                                <div class="relative py-1">
                                    <div class="absolute -left-[25px] top-1/2 -translate-y-1/2 w-2 h-2 bg-gray-500 outline outline-4 outline-black"></div>
                                    <p class="text-xs text-gray-400 font-bold bg-gray-900 inline-block px-2 py-1 uppercase">{{ count($viaje['paradas']) - 1 }} PARADAS</p>
                                </div>
                            @endif
                            
                            <div class="relative">
                                <div class="absolute -left-[27px] top-1.5 w-3 h-3 bg-accent-500 outline outline-4 outline-black"></div>
                                <p class="text-gray-500 font-black text-xs uppercase tracking-widest mb-1">Destino</p>
                                <p class="text-2xl font-black text-white leading-none uppercase">{{ $viaje['destino'] }}</p>
                            </div>
                        </div>
                        
                        <div class="text-right flex flex-col items-end pr-4">
                            <p class="text-4xl font-black text-white">${{ number_format($viaje['costo_por_asiento'], 2) }}</p>
                            <span class="mt-2 text-xs font-black px-3 py-1 uppercase {{ $viaje['asientos_disponibles'] > 0 ? 'bg-primary-500 text-black' : 'bg-accent-500 text-white' }}">
                                {{ $viaje['asientos_disponibles'] }} LIBRES
                            </span>
                        </div>
                    </div>
                    
                    <div class="border-t-2 border-gray-900 pt-5 mt-4 flex justify-between items-center pr-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-900 border-2 border-gray-700 flex items-center justify-center">
                                @if(isset($viaje['conductor']['foto_credencial']) && $viaje['conductor']['foto_credencial'])
                                    <img src="{{ config('services.fastapi.public_url') }}/static/uploads/{{ $viaje['conductor']['foto_credencial'] }}" alt="Conductor" class="w-full h-full object-cover">
                                @else
                                    <span class="font-black text-gray-500 text-xl">{{ isset($viaje['conductor']['nombre']) ? substr($viaje['conductor']['nombre'], 0, 1) : 'C' }}</span>
                                @endif
                            </div>
                            <div>
                                <p class="font-black text-white flex items-center gap-2 uppercase">{{ $viaje['conductor']['nombre'] ?? 'CONDUCTOR' }} <span class="text-xs bg-[#10B981] text-black px-1 py-0.5">VERIFICADO</span></p>
                                <div class="flex items-center gap-3 mt-1">
                                    <p class="text-xs text-primary-500 font-black uppercase">SALIDA: {{ \Carbon\Carbon::parse($viaje['hora_salida'])->format('h:i A') }}</p>
                                    @if(isset($viaje['vehiculo']))
                                        <p class="text-xs text-gray-400 font-bold uppercase">
                                            / {{ $viaje['vehiculo']['marca'] }} {{ $viaje['vehiculo']['modelo'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <button wire:click="solicitarAsiento({{ $viaje['id'] }})" 
                                wire:loading.attr="disabled"
                                class="text-sm font-black px-6 py-3 uppercase transition-transform active:translate-y-1 {{ $viaje['asientos_disponibles'] == 0 ? 'bg-gray-800 text-gray-600 cursor-not-allowed border-b-4 border-gray-900' : 'bg-primary-500 text-black hover:bg-white border-b-4 border-orange-600 hover:border-gray-400' }}"
                                {{ $viaje['asientos_disponibles'] == 0 ? 'disabled' : '' }}>
                            <span wire:loading.remove wire:target="solicitarAsiento({{ $viaje['id'] }})">Reservar Asiento</span>
                            <span wire:loading wire:target="solicitarAsiento({{ $viaje['id'] }})">PROCESANDO...</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-black border-4 border-gray-900 p-10 text-center relative">
                    <div class="absolute top-0 left-0 w-full h-1 bg-accent-500"></div>
                    <div class="w-16 h-16 bg-gray-900 flex items-center justify-center mx-auto mb-4 border-2 border-gray-800">
                        <span class="text-2xl font-black text-gray-500">?</span>
                    </div>
                    <h4 class="text-xl font-black text-white mb-2 uppercase">No hay rutas activas</h4>
                    <p class="text-gray-500 font-bold uppercase">Prueba con otra búsqueda o vuelve a revisar.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Coordenadas centro de Querétaro
        const initLat = 20.5881;
        const initLng = -100.3899;

        // Estilo de mapa oscuro premium (CartoDB Dark Matter)
        const tileUrl = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
        const tileAttrib = '&copy; <a href="https://carto.com/">CARTO</a>';

        const map = L.map('mapa-explorar').setView([initLat, initLng], 12);
        L.tileLayer(tileUrl, { attribution: tileAttrib }).addTo(map);

        // Ícono personalizado premium
        const carIcon = L.divIcon({
            html: `<div style="background-color: var(--color-primary-500); width: 24px; height: 24px; border-radius: 50%; border: 3px solid var(--color-brand-800); box-shadow: 0 0 10px rgba(251,191,36,0.5);"></div>`,
            className: 'custom-car-icon',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        // Cargar los viajes en el mapa
        const trips = @json($todosLosViajes);
        
        trips.forEach(trip => {
            if(trip.paradas && trip.paradas.length > 0) {
                // Tomamos la coordenada de la parada 0 (Origen)
                const coordsStr = trip.paradas[0].coordenadas;
                if(coordsStr && coordsStr.includes(',')) {
                    const [lat, lng] = coordsStr.split(',');
                    if(lat && lng) {
                        const marker = L.marker([parseFloat(lat), parseFloat(lng)], { icon: carIcon }).addTo(map);
                        
                        // Popup interactivo
                        marker.bindPopup(`
                            <div class="text-black p-1">
                                <p class="font-extrabold text-lg mb-1">${trip.origen}</p>
                                <p class="text-sm">Hacia: <b>${trip.destino}</b></p>
                                <p class="text-sm">Hora: <b>${trip.hora_salida}</b></p>
                                <p class="text-sm text-primary-600 font-bold mt-1">$${trip.costo_por_asiento} MXN</p>
                            </div>
                        `);
                    }
                }
            }
        });

        // Forzar render correcto
        setTimeout(() => map.invalidateSize(), 500);
    });
</script>
