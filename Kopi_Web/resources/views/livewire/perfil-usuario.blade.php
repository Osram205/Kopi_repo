<div class="max-w-3xl mx-auto px-6 mt-12 pb-12">
    <div class="mb-8 border-b-4 border-gray-900 pb-4">
        <h3 class="text-3xl font-black text-white uppercase">MI PERFIL</h3>
        <p class="text-gray-500 mt-2 font-bold uppercase">Administra tu información personal y datos de contacto.</p>
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
        
        <div class="flex flex-col sm:flex-row items-center gap-6 mb-8 border-b-4 border-gray-900 pb-8 text-center sm:text-left">
            <div class="relative w-32 h-32 bg-gray-900 flex items-center justify-center text-5xl border-4 border-gray-700 overflow-hidden group cursor-pointer hover:border-primary-500 transition-colors">
                @if ($nueva_foto)
                    <img src="{{ $nueva_foto->temporaryUrl() }}" alt="Perfil" class="w-full h-full object-cover">
                @elseif ($foto_perfil)
                    <img src="{{ config('services.fastapi.public_url') }}/static/uploads/{{ $foto_perfil }}?t={{ time() }}" alt="Perfil" class="w-full h-full object-cover">
                @else
                    <span class="font-black text-gray-500">P</span>
                @endif
                <div class="absolute inset-0 bg-black/80 hidden group-hover:flex items-center justify-center transition-all">
                    <label class="cursor-pointer text-sm font-black text-white text-center w-full h-full flex items-center justify-center uppercase">
                        <input type="file" wire:model.live="nueva_foto" class="hidden" accept="image/*">
                        CAMBIAR
                    </label>
                </div>
            </div>
            <div>
                <h4 class="text-3xl font-black text-white uppercase">{{ $nombre }} {{ $apellidos }}</h4>
                <p class="text-primary-500 font-black text-sm uppercase mt-1 tracking-widest">{{ $correo_institucional }}</p>
                <span class="inline-block mt-3 px-3 py-1 bg-black border-2 border-gray-800 text-xs font-black text-gray-500 uppercase tracking-widest">
                    MATRÍCULA: {{ $matricula }}
                </span>
            </div>
        </div>

        <form wire:submit="guardar" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">NOMBRE COMPLETO <span class="text-[10px] text-gray-600">(NO MODIFICABLE)</span></label>
                    <input type="text" value="{{ $nombre }} {{ $apellidos }}" disabled class="w-full bg-gray-900 border-2 border-gray-800 rounded-none py-4 px-4 text-gray-500 cursor-not-allowed uppercase font-bold">
                </div>
                
                <div>
                    <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">CARRERA / FACULTAD <span class="text-[10px] text-gray-600">(NO MODIFICABLE)</span></label>
                    <input type="text" value="{{ $carrera }}" disabled class="w-full bg-gray-900 border-2 border-gray-800 rounded-none py-4 px-4 text-gray-500 cursor-not-allowed uppercase font-bold">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-black text-white mb-2 uppercase tracking-wider">TELÉFONO / WHATSAPP</label>
                    <input type="tel" wire:model="telefono" maxlength="15" required class="w-full bg-black border-4 border-gray-800 rounded-none py-4 px-4 text-white font-black focus:outline-none focus:border-primary-500 transition-colors uppercase">
                    @error('telefono') <span class="text-accent-500 text-xs font-bold uppercase mt-2 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" wire:loading.attr="disabled" class="w-full bg-primary-500 text-black font-black py-5 hover:bg-white transition-all active:translate-y-1 uppercase text-lg border-b-4 border-orange-600 hover:border-gray-400">
                    <span wire:loading.remove wire:target="guardar">GUARDAR CAMBIOS</span>
                    <span wire:loading wire:target="guardar">GUARDANDO...</span>
                </button>
            </div>
        </form>
    </div>
</div>
