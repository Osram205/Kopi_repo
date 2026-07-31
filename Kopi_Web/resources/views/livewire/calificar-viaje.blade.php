<div class="flex items-center justify-center min-h-[calc(100vh-100px)] py-12">
    <div class="w-full max-w-md px-6">
        
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

        <div class="bg-black border-4 border-gray-900 relative">
            <div class="absolute top-0 left-0 w-full h-2 bg-primary-500"></div>
            
            <div class="bg-gray-900 p-6 border-b-4 border-black text-center">
                <div class="w-16 h-16 bg-primary-500 flex items-center justify-center mx-auto mb-4 text-3xl">
                    ⭐
                </div>
                <h3 class="text-2xl font-black text-white mb-1 uppercase tracking-widest">CALIFICA TU VIAJE</h3>
                <p class="text-gray-500 font-bold uppercase text-sm tracking-wider">VIAJE #{{ $viaje_id }}</p>
            </div>

            <div class="p-8">
                <form wire:submit="enviarCalificacion" class="space-y-8 text-center">
                    
                    <div class="bg-black border-4 border-gray-800 p-4 inline-block mx-auto">
                        <span class="text-xs text-primary-500 uppercase font-black tracking-widest block mb-1">EVALUANDO A:</span>
                        <h5 class="font-black text-white text-xl uppercase">{{ $nombre_evaluado }}</h5>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-white mb-4 uppercase tracking-wider">¿CÓMO CALIFICARÍAS EL TRAYECTO?</label>
                        
                        <div class="flex justify-center gap-4">
                            @foreach([1, 2, 3, 4, 5] as $val)
                                <label class="cursor-pointer relative">
                                    <input type="radio" wire:model="puntuacion" value="{{ $val }}" class="peer sr-only">
                                    <div class="w-14 h-14 flex items-center justify-center border-4 border-gray-800 bg-gray-900 text-gray-500 peer-checked:border-primary-500 peer-checked:bg-primary-500 peer-checked:text-black font-black text-2xl transition-all hover:border-gray-500">
                                        {{ $val }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('puntuacion') <span class="text-accent-500 text-xs mt-2 block font-bold uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-black text-white mb-2 text-left uppercase tracking-wider">COMENTARIOS (OPCIONAL)</label>
                        <textarea wire:model="comentarios" rows="3" placeholder="¿QUÉ TAL ESTUVO LA PLÁTICA? ¿MANEJÓ BIEN?" 
                                  class="w-full bg-black border-4 border-gray-800 rounded-none py-3 px-4 text-white font-bold focus:outline-none focus:border-primary-500 transition-colors resize-none uppercase"></textarea>
                        @error('comentarios') <span class="text-accent-500 text-xs mt-2 block text-left font-bold uppercase">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" wire:loading.attr="disabled" class="w-full bg-primary-500 text-black font-black text-lg py-5 hover:bg-white transition-all active:translate-y-1 uppercase border-b-4 border-orange-600 hover:border-gray-400">
                            <span wire:loading.remove wire:target="enviarCalificacion">ENVIAR CALIFICACIÓN</span>
                            <span wire:loading wire:target="enviarCalificacion">ENVIANDO...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
