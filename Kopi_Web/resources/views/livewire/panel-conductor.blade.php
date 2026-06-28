<div class="container pb-5">
    
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-md-8">

            @if(empty($estatusVerificacion) || $estatusVerificacion == 'pendiente' || $estatusVerificacion == 'rechazado')
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-primary text-white p-4 border-0">
                        <h4 class="mb-0 fw-bold">Postulación para Conductor UPQ</h4>
                        <small class="text-white-50">Sube tu documentación oficial para revisión de seguridad</small>
                    </div>
                    
                    <div class="card-body p-4 p-sm-5">
                        @if($estatusVerificacion == 'rechazado')
                            <div class="alert alert-warning border-0 shadow-sm mb-4">
                                <strong>⚠️ Postulación Anterior Rechazada:</strong> Tus documentos previos no cumplieron con las normas o eran ilegibles. Por favor, vuelve a subir tus archivos actualizados.
                            </div>
                        @endif

                        <form wire:submit.prevent="enviarSolicitudConduccion">
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">1. Credencial Universitaria (Frente)</label>
                                    <input type="file" wire:model="foto_credencial" class="form-control py-2 @error('foto_credencial') is-invalid @enderror" accept="image/*" required>
                                    @error('foto_credencial') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">2. Licencia de Conducir Vigente (Frente)</label>
                                    <input type="file" wire:model="foto_licencia" class="form-control py-2 @error('foto_licencia') is-invalid @enderror" accept="image/*" required>
                                    @error('foto_licencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">3. Tarjeta de Circulación del Vehículo</label>
                                    <input type="file" wire:model="tarjeta_circulacion" class="form-control py-2 @error('tarjeta_circulacion') is-invalid @enderror" accept="image/*" required>
                                    @error('tarjeta_circulacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div wire:loading wire:target="foto_credencial, foto_licencia, tarjeta_circulacion" class="text-primary small mt-3 w-100 text-center">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                Procesando e indexando archivos temporalmente...
                            </div>

                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary btn-lg w-100 fw-bold mt-4 rounded-3 shadow-sm">
                                <span wire:loading.remove wire:target="enviarSolicitudConduccion">Enviar Expediente Digital</span>
                                <span wire:loading wire:target="enviarSolicitudConduccion">⏳ Subiendo documentos al servidor central...</span>
                            </button>
                        </form>
                    </div>
                </div>

            @elseif($estatusVerificacion == 'solicitado')
                <div class="card border-0 shadow-sm rounded-4 text-center p-4 p-sm-5 bg-white border-start border-warning border-4">
                    <div class="mb-3 fs-1">⏳</div>
                    <h4 class="fw-bold text-dark">Tu solicitud está en revisión</h4>
                    <p class="text-muted mx-auto mb-0" style="max-width: 480px;">
                        Hemos enviado tus documentos al Panel de Control de la universidad. Un coordinador validará tus credenciales en breve. Te notificaremos aquí mismo.
                    </p>
                    <div class="spinner-border text-warning mt-4 shadow-sm" role="status"></div>
                </div>

            @elseif($estatusVerificacion == 'aprobado')
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-dark text-white p-4 border-0">
                        <div class="d-flex align-items-center">
                            <div class="fs-3 me-3">✅</div>
                            <div>
                                <h4 class="mb-0 fw-bold text-info">Conductor Autorizado</h4>
                                <small class="text-white-50">Comunidad de Movilidad Segura UPQ</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 p-sm-5">
                        <h5 class="fw-bold text-secondary mb-4">Registro Obligatorio de Vehículo</h5>
                        
                        <form wire:submit.prevent="registrarVehiculo">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small fw-bold">Marca</label>
                                    <input type="text" wire:model="marca" class="form-control py-2" placeholder="Ej. Nissan, Chevrolet" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small fw-bold">Modelo / Año</label>
                                    <input type="text" wire:model="modelo" class="form-control py-2" placeholder="Ej. Versa 2022, Aveo" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small fw-bold">Placas de Circulación</label>
                                    <input type="text" wire:model="placas" class="form-control py-2" placeholder="Ej. UKN-90-21" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label text-muted small fw-bold">Color del Auto</label>
                                    <input type="text" wire:model="color" class="form-control py-2" placeholder="Ej. Rojo metálico, Gris" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">Asientos Totales Disponibles</label>
                                    <select wire:model="asientos_totales" class="form-select py-2" required>
                                        <option value="">Selecciona la capacidad máxima de tu auto...</option>
                                        <option value="1">1 Asiento libre</option>
                                        <option value="2">2 Asientos libres</option>
                                        <option value="3">3 Asientos libres</option>
                                        <option value="4">4 Asientos libres</option>
                                        <option value="5">5 Asientos libres</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold mt-4 rounded-3 shadow-sm">
                                Guardar Vehículo y Continuar
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>