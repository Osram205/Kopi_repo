<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-5">
            
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-lg bg-white rounded-4 overflow-hidden">
                <div class="card-body p-4 p-sm-5">
                    
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 60px; height: 60px;">
                            <span class="fs-3">{{ $paso == 1 ? '🪪' : '🔐' }}</span>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Recuperación de Acceso</h3>
                        <p class="text-muted small">
                            {{ $paso == 1 ? 'Paso 1: Confirma tus credenciales institucionales.' : 'Paso 2: Genera una nueva contraseña segura.' }}
                        </p>
                    </div>

                    @if($paso == 1)
                        <form wire:submit.prevent="verificarIdentidad">
                            <div class="form-floating mb-3">
                                <input type="email" wire:model="correo_institucional" class="form-control @error('correo_institucional') is-invalid @enderror" id="floatEmail" placeholder="alumno@upq.edu.mx" required>
                                <label for="floatEmail">Correo Institucional</label>
                                @error('correo_institucional') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-floating mb-4">
                                <input type="text" wire:model="matricula" class="form-control @error('matricula') is-invalid @enderror" id="floatMatricula" placeholder="123456" required>
                                <label for="floatMatricula">Matrícula Escolar</label>
                                @error('matricula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm mb-3">
                                <span wire:loading.remove wire:target="verificarIdentidad">Validar Datos</span>
                                <span wire:loading wire:target="verificarIdentidad">⏳ Comprobando expediente...</span>
                            </button>
                        </form>
                    
                    @else
                        <div class="alert alert-success border-0 small shadow-sm mb-4">
                            <strong>✅ Identidad Confirmada:</strong> Se ha desbloqueado la actualización para la matrícula <strong>{{ $matricula }}</strong>.
                        </div>

                        <form wire:submit.prevent="cambiarContrasena">
                            <div class="form-floating mb-4">
                                <input type="password" wire:model="nueva_contrasena" class="form-control @error('nueva_contrasena') is-invalid @enderror" id="floatPassword" placeholder="Nueva Contraseña" required minlength="6">
                                <label for="floatPassword">Ingresa tu Nueva Contraseña</label>
                                @error('nueva_contrasena') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <button type="submit" wire:loading.attr="disabled" class="btn btn-dark btn-lg w-100 fw-bold shadow-sm mb-3">
                                <span wire:loading.remove wire:target="cambiarContrasena">Guardar Nueva Contraseña</span>
                                <span wire:loading wire:target="cambiarContrasena">Guardando cambios...</span>
                            </button>
                        </form>
                    @endif

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-muted small">← Cancelar y volver al inicio</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>