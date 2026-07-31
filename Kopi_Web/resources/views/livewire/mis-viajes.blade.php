<div class="max-w-5xl mx-auto px-6 mt-12">
    <div class="mb-8 border-b-4 border-gray-900 pb-4">
        <h3 class="text-3xl font-black text-white uppercase">Mis Viajes Solicitados</h3>
        <p class="text-gray-500 mt-2 font-bold uppercase">Da seguimiento a tus peticiones de viaje y realiza tus pagos.</p>
    </div>

    @if(session()->has('success'))
        <div class="mb-6 p-4 bg-primary-500 border-4 border-black text-black font-black uppercase">
            {{ session('success') }}
            <button type="button" onclick="this.parentElement.style.display='none'" class="float-right hover:text-white">&times;</button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mb-6 p-4 bg-accent-500 border-4 border-black text-white font-black uppercase">
            {{ session('error') }}
            <button type="button" onclick="this.parentElement.style.display='none'" class="float-right hover:text-black">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($misReservas as $reserva)
            <div wire:key="misreserva-{{ $reserva['id'] }}" class="bg-black border-2 border-gray-800 p-6 flex flex-col h-full hover:border-primary-500 transition-colors duration-200 relative group">
                
                <div class="absolute top-0 left-0 h-full w-2 bg-gray-900 group-hover:bg-primary-500 transition-colors"></div>

                <div class="flex justify-between items-center mb-6 pl-4">
                    <span class="text-xs font-black text-gray-500 uppercase tracking-widest">TICKET #{{ $reserva['id'] }}</span>
                    
                    @if($reserva['estatus_reserva'] == 'solicitado')
                        <span class="px-3 py-1 text-xs font-black bg-black text-primary-500 border-2 border-primary-500 uppercase">⏳ PENDIENTE</span>
                    @elseif($reserva['estatus_reserva'] == 'aceptado')
                        <span class="px-3 py-1 text-xs font-black bg-[#10B981] text-black border-2 border-black uppercase">✅ APROBADO</span>
                    @else
                        <span class="px-3 py-1 text-xs font-black bg-accent-500 text-white border-2 border-black uppercase">❌ RECHAZADO</span>
                    @endif
                </div>

                <h5 class="text-xl font-black text-white mb-2 pl-4 flex items-center gap-2 uppercase">
                    @if(isset($reserva['viaje']))
                        📍 {{ $reserva['viaje']['destino'] ?? 'Destino' }}
                    @else
                        Ruta Asociada: Viaje #{{ $reserva['viaje_id'] }}
                    @endif
                </h5>
                <p class="text-gray-400 text-sm mb-6 pl-4 flex-grow uppercase font-bold">
                    HAS SOLICITADO <strong class="text-primary-500">{{ $reserva['asientos_solicitados'] }}</strong> ASIENTO(S)
                    @if(isset($reserva['viaje']['costo_por_asiento']))
                        POR UN TOTAL DE <strong class="text-white font-black">${{ number_format($reserva['viaje']['costo_por_asiento'] * $reserva['asientos_solicitados'], 2) }} MXN</strong>.
                    @else
                        .
                    @endif
                </p>

                <div class="pl-4">
                    @if($reserva['estatus_reserva'] == 'aceptado')
                        <div class="space-y-3">
                            <button wire:click="abrirModalPago({{ $reserva['id'] }})" class="w-full bg-[#10B981] text-black font-black py-4 uppercase hover:bg-white border-b-4 border-green-800 hover:border-gray-400 transition-all active:translate-y-1">
                                💳 PROCEDER AL PAGO
                            </button>
                            <a href="{{ route('viaje.rastreo', $reserva['viaje_id']) }}" class="w-full block text-center border-2 border-primary-500 text-primary-500 font-black py-4 hover:bg-primary-500 hover:text-black transition-colors uppercase">
                                📍 VER RASTREO GPS
                            </a>
                        </div>
                    @elseif($reserva['estatus_reserva'] == 'solicitado')
                        <button class="w-full bg-gray-900 border-2 border-gray-800 text-gray-500 font-black py-4 cursor-not-allowed uppercase">
                            PAGO BLOQUEADO (PENDIENTE)
                        </button>
                    @else
                        <button class="w-full border-2 border-accent-500 text-accent-500 font-black py-4 cursor-not-allowed uppercase bg-black">
                            SOLICITUD CANCELADA
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-black border-4 border-gray-900 p-12 text-center relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-accent-500"></div>
                <div class="w-16 h-16 bg-gray-900 flex items-center justify-center mx-auto mb-4 border-2 border-gray-800">
                    <span class="text-2xl font-black text-gray-500">🎟️</span>
                </div>
                <h4 class="text-xl font-black text-white mb-2 uppercase">NO HAS SOLICITADO VIAJES</h4>
                <p class="text-gray-500 font-bold uppercase">Ve al buscador principal para encontrar tu próxima ruta.</p>
            </div>
        @endforelse
    </div>

    @if($modalPagoAbierto)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90">
            <div class="bg-black border-4 border-gray-900 w-full max-w-md relative">
                
                <div class="absolute top-0 left-0 w-full h-2 bg-primary-500"></div>

                <div class="flex justify-between items-center p-6 border-b-4 border-gray-900">
                    <h5 class="font-black text-xl text-white uppercase">CONFIRMAR CUOTA</h5>
                    <button wire:click="cerrarModal" class="text-gray-500 hover:text-white transition-colors font-black text-xl">&times;</button>
                </div>
                
                <div class="p-6">
                    <form wire:submit="procesarPago">
                        <div class="text-center mb-6 bg-gray-900 p-4 border-2 border-gray-800">
                            <span class="text-gray-500 text-xs uppercase font-black tracking-widest">TOTAL A CONFIRMAR</span>
                            <h2 class="text-3xl font-black text-[#10B981] mt-2 mb-2 uppercase">CALCULADO POR KOPI</h2>
                            <p class="text-gray-400 text-xs mb-3 font-bold uppercase">El importe final se toma del costo real del viaje y los asientos aprobados.</p>
                            <span class="inline-block px-3 py-1 bg-black border-2 border-gray-800 text-xs text-primary-500 font-bold uppercase tracking-widest">TICKET: #{{ $reservacionSeleccionadaId }}</span>
                        </div>

                        <label class="block text-sm font-black text-white mb-3 uppercase tracking-wider">SELECCIONA TU MÉTODO DE PAGO</label>
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <label class="relative cursor-pointer">
                                <input type="radio" wire:model.live="metodo_pago" value="tarjeta" class="peer sr-only">
                                <div class="w-full py-4 text-center border-4 border-gray-800 peer-checked:border-primary-500 bg-black peer-checked:bg-gray-900 text-gray-500 peer-checked:text-primary-500 font-black transition-all uppercase">
                                    💳 TARJETA
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" wire:model.live="metodo_pago" value="transferencia" class="peer sr-only">
                                <div class="w-full py-4 text-center border-4 border-gray-800 peer-checked:border-primary-500 bg-black peer-checked:bg-gray-900 text-gray-500 peer-checked:text-primary-500 font-black transition-all uppercase">
                                    📲 SPEI
                                </div>
                            </label>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" wire:click="cerrarModal" class="w-1/2 px-4 py-4 border-2 border-gray-700 text-gray-500 font-black uppercase hover:bg-gray-900 hover:text-white transition-colors">CANCELAR</button>
                            <button type="submit" wire:loading.attr="disabled" class="w-1/2 bg-primary-500 text-black font-black py-4 uppercase hover:bg-white transition-all active:translate-y-1 border-b-4 border-orange-600 hover:border-gray-400">
                                <span wire:loading.remove wire:target="procesarPago">LIQUIDAR</span>
                                <span wire:loading wire:target="procesarPago">PROCESANDO...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
