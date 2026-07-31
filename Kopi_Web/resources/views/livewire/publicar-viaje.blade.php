<div class="max-w-4xl mx-auto px-6 mt-12 pb-12">
    <div class="mb-8 border-b-4 border-gray-900 pb-4">
        <h3 class="text-3xl font-black text-white uppercase">PUBLICAR NUEVA RUTA</h3>
        <p class="text-gray-500 mt-2 font-bold uppercase">Crea un nuevo viaje y ayuda a otros universitarios a llegar a su destino.</p>
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

    <div class="bg-black border-4 border-gray-900 p-8 relative">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-accent-500 via-orange-500 to-primary-500"></div>
        
        <form wire:submit="publicar" class="space-y-10">
            
            <!-- Detalles de la Ruta con Mapas -->
            <div>
                <h6 class="text-sm font-black text-white bg-gray-900 px-4 py-2 inline-block border-2 border-gray-800 uppercase tracking-widest mb-6">DETALLES DE LA RUTA</h6>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Mapa Origen -->
                    <div class="flex flex-col gap-3" wire:ignore>
                        <label class="block text-sm font-black text-gray-400 uppercase">PUNTO DE ORIGEN</label>
                        <div id="mapa-origen" class="w-full h-56 bg-gray-900 border-4 border-gray-800 z-0 relative"></div>
                    </div>
                    <div class="flex flex-col gap-3 pt-7">
                        <div class="relative">
                            <input type="text" id="input-origen" wire:model.blur="origen" placeholder="EJ. ALAMEDA HIDALGO, QUERÉTARO..." autocomplete="off" maxlength="100"
                                   class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 pr-12 text-white font-bold uppercase focus:outline-none focus:border-primary-500 transition-colors">
                            <button type="button" onclick="window.buscarTextoMapa('origen')" class="absolute right-0 top-0 h-full w-12 bg-gray-900 border-l-4 border-gray-800 text-gray-500 hover:text-primary-500 hover:bg-black transition-colors flex items-center justify-center">
                                🔍
                            </button>
                            <!-- Autocomplete Dropdown -->
                            <ul id="dropdown-origen" class="hidden absolute top-full left-0 w-full bg-black border-4 border-gray-800 mt-2 shadow-2xl z-50 max-h-60 overflow-y-auto"></ul>
                        </div>
                        <span class="text-gray-600 text-xs mt-1 font-bold uppercase">💡 Si no encuentras el local exacto, busca la calle o mueve el pin manualmente.</span>
                        @error('origen') <span class="text-accent-500 text-xs font-bold uppercase mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Mapa Destino -->
                    <div class="flex flex-col gap-3" wire:ignore>
                        <label class="block text-sm font-black text-gray-400 uppercase">PUNTO DE DESTINO</label>
                        <div id="mapa-destino" class="w-full h-56 bg-gray-900 border-4 border-gray-800 z-0 relative"></div>
                    </div>
                    <div class="flex flex-col gap-3 pt-7">
                        <div class="relative">
                            <input type="text" id="input-destino" wire:model.blur="destino" placeholder="EJ. UPQ EL MARQUÉS..." autocomplete="off" maxlength="100"
                                   class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 pr-12 text-white font-bold uppercase focus:outline-none focus:border-primary-500 transition-colors">
                            <button type="button" onclick="window.buscarTextoMapa('destino')" class="absolute right-0 top-0 h-full w-12 bg-gray-900 border-l-4 border-gray-800 text-gray-500 hover:text-primary-500 hover:bg-black transition-colors flex items-center justify-center">
                                🔍
                            </button>
                            <!-- Autocomplete Dropdown -->
                            <ul id="dropdown-destino" class="hidden absolute top-full left-0 w-full bg-black border-4 border-gray-800 mt-2 shadow-2xl z-50 max-h-60 overflow-y-auto"></ul>
                        </div>
                        <span class="text-gray-600 text-xs mt-1 font-bold uppercase">💡 Si no encuentras el local exacto, busca la calle o mueve el pin manualmente.</span>
                        @error('destino') <span class="text-accent-500 text-xs font-bold uppercase mt-1 block">{{ $message }}</span> @enderror
                    </div>

                </div>
                
                <!-- Dynamic Intermediate Stops -->
                <div class="mt-8 p-6 bg-gray-900 border-2 border-gray-800 relative">
                    <div class="absolute -top-3 left-4 bg-black px-2 text-xs font-black text-primary-500 uppercase tracking-widest">
                        PARADAS INTERMEDIAS (OPCIONAL)
                    </div>
                    <div class="flex justify-end mb-4">
                        <button type="button" wire:click="addParada" class="text-xs bg-black text-gray-400 px-4 py-2 border-2 border-gray-800 font-black hover:border-primary-500 hover:text-primary-500 transition-colors uppercase">
                            + AÑADIR PARADA
                        </button>
                    </div>
                    
                    @foreach($paradas_intermedias as $index => $parada)
                        <div class="flex items-center gap-3 mb-4 relative">
                            <div class="w-3 h-3 bg-gray-500 border-2 border-black absolute -left-6 z-10"></div>
                            <input type="text" wire:model="paradas_intermedias.{{ $index }}.nombre" placeholder="EJ. OXXO DEL MIRADOR..." maxlength="50"
                                   class="w-full bg-black border-2 border-gray-800 rounded-none py-3 px-4 text-white font-bold uppercase focus:outline-none focus:border-primary-500 transition-colors">
                            <button type="button" wire:click="removeParada({{ $index }})" class="text-accent-500 hover:bg-accent-500 hover:text-white border-2 border-accent-500 font-black px-4 py-3 transition-colors uppercase">X</button>
                        </div>
                    @endforeach
                    @if(count($paradas_intermedias) == 0)
                        <p class="text-xs text-gray-600 font-bold uppercase text-center py-2">SIN PARADAS ADICIONALES. IRÁS DIRECTO DEL ORIGEN AL DESTINO.</p>
                    @endif
                </div>
            </div>

            <!-- Fecha y Rutas Recurrentes -->
            <div>
                <h6 class="text-sm font-black text-white bg-gray-900 px-4 py-2 inline-block border-2 border-gray-800 uppercase tracking-widest mb-6">PLANIFICACIÓN DE VIAJE</h6>
                
                <div class="bg-gray-900 border-2 border-gray-800 p-6 relative">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h4 class="text-lg font-black text-white uppercase">¿ES UNA RUTA FRECUENTE?</h4>
                            <p class="text-xs text-gray-500 font-bold uppercase mt-1">Programa automáticamente este viaje para las próximas semanas.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="es_recurrente" class="sr-only peer">
                            <div class="w-14 h-8 bg-black border-2 border-gray-700 peer-focus:outline-none peer-checked:border-primary-500 peer peer-checked:after:translate-x-full peer-checked:after:border-black after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-gray-500 after:border-gray-500 after:border after:h-6 after:w-6 after:transition-all peer-checked:after:bg-primary-500 peer-checked:bg-black"></div>
                        </label>
                    </div>

                    @if($es_recurrente)
                        <div class="mt-6 pt-6 border-t-2 border-gray-800">
                            <label class="block text-sm font-black text-white mb-3 uppercase tracking-wider">DÍAS DE LA SEMANA</label>
                            <div class="flex flex-wrap gap-3 mb-6">
                                @php
                                    $dias = [1 => 'LUN', 2 => 'MAR', 3 => 'MIÉ', 4 => 'JUE', 5 => 'VIE', 6 => 'SÁB'];
                                @endphp
                                @foreach($dias as $num => $nombre)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" wire:model="dias_recurrentes" value="{{ $num }}" class="hidden peer">
                                        <div class="px-5 py-3 text-sm font-black border-2 border-gray-700 bg-black text-gray-500 peer-checked:bg-primary-500 peer-checked:text-black peer-checked:border-primary-500 transition-colors uppercase">
                                            {{ $nombre }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            
                            <label class="block text-sm font-black text-white mb-3 uppercase tracking-wider">REPETIR POR CUANTAS SEMANAS:</label>
                            <input type="number" wire:model="semanas_recurrentes" min="1" max="4" class="w-full sm:w-1/2 bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold text-lg focus:outline-none focus:border-primary-500 transition-colors">
                            <span class="text-xs text-gray-600 mt-2 block font-bold uppercase">MÁXIMO 4 SEMANAS DE ANTICIPACIÓN. COMENZANDO DESDE HOY.</span>
                            <!-- Hidden input for base date validation -->
                            <input type="hidden" wire:model="fecha_salida" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-black text-white mb-3 uppercase tracking-wider">FECHA EXACTA</label>
                            <input type="date" wire:model="fecha_salida" required class="w-full sm:w-1/2 bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold text-lg focus:outline-none focus:border-primary-500 transition-colors uppercase" style="color-scheme: dark;">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Horario y Capacidad -->
            <div>
                <h6 class="text-sm font-black text-white bg-gray-900 px-4 py-2 inline-block border-2 border-gray-800 uppercase tracking-widest mb-6">HORARIO Y CAPACIDAD</h6>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-black text-gray-400 uppercase mb-3">HORA DE SALIDA</label>
                        <input type="time" wire:model="hora_salida" required 
                               class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold text-lg focus:outline-none focus:border-primary-500 transition-colors"
                               style="color-scheme: dark;">
                        @error('hora_salida') <span class="text-accent-500 text-xs font-bold uppercase mt-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-black text-gray-400 uppercase mb-3">ASIENTOS LIBRES</label>
                        <input type="number" wire:model="asientos_disponibles" min="1" max="10" placeholder="EJ. 3" required 
                               class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold text-lg focus:outline-none focus:border-primary-500 transition-colors">
                        @error('asientos_disponibles') <span class="text-accent-500 text-xs font-bold uppercase mt-2 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-black text-gray-400 uppercase mb-3">CUOTA ($MXN)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500 font-black text-lg">$</span>
                            <input type="number" wire:model="costo_por_asiento" step="1.00" min="0" placeholder="15.00" required 
                                   class="w-full bg-black border-4 border-gray-800 rounded-none py-3 pl-10 pr-4 text-white font-bold text-lg focus:outline-none focus:border-primary-500 transition-colors">
                        </div>
                        @error('costo_por_asiento') <span class="text-accent-500 text-xs font-bold uppercase mt-2 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Vehículo -->
            <div>
                <h6 class="text-sm font-black text-white bg-gray-900 px-4 py-2 inline-block border-2 border-gray-800 uppercase tracking-widest mb-6">VEHÍCULO A UTILIZAR</h6>
                <div>
                    <select wire:model="vehiculo_id" required 
                            class="w-full bg-black border-4 border-gray-800 rounded-none py-4 px-4 text-white font-bold text-lg focus:outline-none focus:border-primary-500 transition-colors cursor-pointer uppercase">
                        <option value="" disabled>-- SELECCIONA UN VEHÍCULO REGISTRADO --</option>
                        @foreach($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo['id'] }}" class="bg-gray-900 text-white font-bold uppercase">
                                {{ $vehiculo['marca'] }} {{ $vehiculo['modelo'] }} ({{ $vehiculo['placas'] }}) - {{ $vehiculo['asientos_totales'] }} LUGARES MÁX.
                            </option>
                        @endforeach
                    </select>
                    @if(count($vehiculos) == 0)
                        <div class="text-accent-500 text-xs mt-3 font-black uppercase">⚠️ NO TIENES VEHÍCULOS REGISTRADOS. VE A TU PERFIL DE CONDUCTOR PARA AGREGAR UNO.</div>
                    @endif
                    @error('vehiculo_id') <span class="text-accent-500 text-xs font-bold uppercase mt-2 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" wire:loading.attr="disabled" {{ count($vehiculos) == 0 ? 'disabled' : '' }}
                    class="w-full bg-primary-500 text-black font-black text-xl py-5 hover:bg-white transition-all active:translate-y-1 border-b-4 border-orange-600 hover:border-gray-400 mt-6 disabled:opacity-50 disabled:cursor-not-allowed disabled:border-none uppercase tracking-widest">
                <span wire:loading.remove wire:target="publicar">PUBLICAR VIAJE</span>
                <span wire:loading wire:target="publicar">PUBLICANDO...</span>
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        
        // Coordenadas iniciales (Centro de Querétaro)
        const initLat = 20.5881;
        const initLng = -100.3899;

        // Estilo de mapa oscuro premium (CartoDB Dark Matter)
        const tileUrl = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
        const tileAttrib = '&copy; <a href="https://carto.com/">CARTO</a>';

        // --- MAPA ORIGEN ---
        const mapOrigen = L.map('mapa-origen').setView([initLat, initLng], 13);
        L.tileLayer(tileUrl, { attribution: tileAttrib }).addTo(mapOrigen);
        
        let markerOrigen = L.marker([initLat, initLng], {draggable: true}).addTo(mapOrigen);
        
        // --- MAPA DESTINO ---
        const mapDestino = L.map('mapa-destino').setView([initLat, initLng], 13);
        L.tileLayer(tileUrl, { attribution: tileAttrib }).addTo(mapDestino);
        
        let markerDestino = L.marker([initLat + 0.01, initLng + 0.01], {draggable: true}).addTo(mapDestino);

        // Función de Geocoding Inverso (OpenStreetMap Nominatim)
        async function getAddressFromCoords(lat, lng, type) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await response.json();
                const addressName = data.display_name.split(',').slice(0, 2).join(', '); // Tomar solo calle y colonia
                
                // Actualizar las propiedades de Livewire
                if(type === 'origen') {
                    @this.set('origen_lat', lat);
                    @this.set('origen_lng', lng);
                    @this.set('origen', addressName);
                } else {
                    @this.set('destino_lat', lat);
                    @this.set('destino_lng', lng);
                    @this.set('destino', addressName);
                }
            } catch (error) {
                console.error("Error geocoding:", error);
            }
        }

        // Eventos al soltar el pin
        markerOrigen.on('dragend', function(event) {
            const position = markerOrigen.getLatLng();
            getAddressFromCoords(position.lat, position.lng, 'origen');
        });

        markerDestino.on('dragend', function(event) {
            const position = markerDestino.getLatLng();
            getAddressFromCoords(position.lat, position.lng, 'destino');
        });

        // Eventos al hacer click en cualquier parte del mapa
        mapOrigen.on('click', function(e) {
            markerOrigen.setLatLng(e.latlng);
            getAddressFromCoords(e.latlng.lat, e.latlng.lng, 'origen');
        });

        mapDestino.on('click', function(e) {
            markerDestino.setLatLng(e.latlng);
            getAddressFromCoords(e.latlng.lat, e.latlng.lng, 'destino');
        });

        // --- BÚSQUEDA POR TEXTO (FORWARD GEOCODING) ---
        window.buscarTextoMapa = async function(type) {
            const inputEl = document.getElementById(type === 'origen' ? 'input-origen' : 'input-destino');
            const query = inputEl.value;
            if(!query) return;

            try {
                // Buscamos en Querétaro para mejorar precisión
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Querétaro')}&limit=1`);
                const data = await response.json();
                
                if(data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);

                    if(type === 'origen') {
                        mapOrigen.setView([lat, lng], 15);
                        markerOrigen.setLatLng([lat, lng]);
                        @this.set('origen_lat', lat);
                        @this.set('origen_lng', lng);
                    } else {
                        mapDestino.setView([lat, lng], 15);
                        markerDestino.setLatLng([lat, lng]);
                        @this.set('destino_lat', lat);
                        @this.set('destino_lng', lng);
                    }
                } else {
                    alert('No se encontró el lugar. Intenta ser más específico.');
                }
            } catch (error) {
                console.error("Error en búsqueda:", error);
            }
        };

        // Escuchar Enter en los inputs para buscar
        document.getElementById('input-origen').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                window.buscarTextoMapa('origen');
            }
        });
        document.getElementById('input-destino').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                window.buscarTextoMapa('destino');
            }
        });

        // --- AUTOCOMPLETADO MIENTRAS ESCRIBES ---
        const setupAutocomplete = (type) => {
            const inputEl = document.getElementById(`input-${type}`);
            const dropdownEl = document.getElementById(`dropdown-${type}`);
            let timeout = null;

            inputEl.addEventListener('input', function(e) {
                clearTimeout(timeout);
                const query = e.target.value;
                if (query.length < 3) {
                    dropdownEl.classList.add('hidden');
                    return;
                }

                timeout = setTimeout(async () => {
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Querétaro')}&limit=5`);
                        const data = await response.json();
                        
                        dropdownEl.innerHTML = '';
                        if (data && data.length > 0) {
                            data.forEach(place => {
                                const li = document.createElement('li');
                                li.className = 'px-4 py-3 hover:bg-gray-800 cursor-pointer border-b border-gray-800 last:border-b-0 text-sm text-gray-300 transition-colors flex items-center gap-3';
                                li.innerHTML = `<span class="text-primary-500 text-lg">📍</span> ${place.display_name}`;
                                
                                li.addEventListener('click', () => {
                                    const lat = parseFloat(place.lat);
                                    const lng = parseFloat(place.lon);
                                    
                                    inputEl.value = place.display_name;
                                    dropdownEl.classList.add('hidden');

                                    if(type === 'origen') {
                                        mapOrigen.setView([lat, lng], 15);
                                        markerOrigen.setLatLng([lat, lng]);
                                        @this.set('origen_lat', lat);
                                        @this.set('origen_lng', lng);
                                        @this.set('origen', place.display_name);
                                    } else {
                                        mapDestino.setView([lat, lng], 15);
                                        markerDestino.setLatLng([lat, lng]);
                                        @this.set('destino_lat', lat);
                                        @this.set('destino_lng', lng);
                                        @this.set('destino', place.display_name);
                                    }
                                });
                                dropdownEl.appendChild(li);
                            });
                            dropdownEl.classList.remove('hidden');
                        } else {
                            dropdownEl.classList.add('hidden');
                        }
                    } catch(err) {
                        console.error(err);
                    }
                }, 400); // 400ms debounce
            });

            // Cerrar al clickear fuera
            document.addEventListener('click', (e) => {
                if(!inputEl.contains(e.target) && !dropdownEl.contains(e.target)) {
                    dropdownEl.classList.add('hidden');
                }
            });
        };

        setupAutocomplete('origen');
        setupAutocomplete('destino');

        // Forzar resize (reparación de un bug común en Leaflet cuando está oculto/cargando)
        setTimeout(() => {
            mapOrigen.invalidateSize();
            mapDestino.invalidateSize();
        }, 500);
    });
</script>
