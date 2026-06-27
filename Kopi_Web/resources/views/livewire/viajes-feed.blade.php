<div class="container pb-5">
    
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-5">
            <h3 class="fw-bold text-dark m-0">Viajes Disponibles</h3>
        </div>
        <div class="col-md-7">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">🔍</span>
                <input type="text" wire:model.live="search" class="form-control border-start-0 py-2" placeholder="Buscar destino (Ej. Alameda, UPQ...)">
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse($viajes as $viaje)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-uppercase text-muted small fw-bold mb-1">Ruta</p>
                                <h5 class="fw-bold text-primary mb-0">{{ $viaje['origen'] }}</h5>
                                <div class="text-muted small my-1">⬇️ hacia</div>
                                <h5 class="fw-bold text-dark">{{ $viaje['destino'] }}</h5>
                            </div>
                            <h4 class="fw-bold text-success m-0">${{ number_format($viaje['costo_por_asiento'], 2) }}</h4>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3 text-muted">
                            <span class="fw-bold me-2">🕒 Salida:</span> 
                            {{ \Carbon\Carbon::parse($viaje['hora_salida'])->format('h:i A') }}
                        </div>
                        
                        <div class="d-flex align-items-center mb-4">
                            <span class="badge {{ $viaje['asientos_disponibles'] > 0 ? 'bg-primary' : 'bg-danger' }} rounded-pill px-3 py-2">
                                {{ $viaje['asientos_disponibles'] }} Asientos libres
                            </span>
                        </div>
                        
                        <button wire:click="solicitarAsiento({{ $viaje['id'] }})" 
                                wire:loading.attr="disabled"
                                class="btn btn-dark w-100 fw-bold py-2 rounded-3 shadow-sm" 
                                {{ $viaje['asientos_disponibles'] == 0 ? 'disabled' : '' }}>
                            
                            <span wire:loading.remove wire:target="solicitarAsiento({{ $viaje['id'] }})">
                                Solicitar Asiento
                            </span>
                            
                            <span wire:loading wire:target="solicitarAsiento({{ $viaje['id'] }})">
                                ⏳ Procesando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 mt-4">
                <div class="card border-0 shadow-sm rounded-4 p-5 bg-white">
                    <h4 class="text-muted fw-bold">No hay rutas activas</h4>
                    <p class="text-secondary">Prueba con otra búsqueda o vuelve a revisar en unos minutos.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>