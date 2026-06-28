<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopi - Crear Cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            min-height: 100vh;
        }
        .card-login {
            border-radius: 1rem;
            backdrop-filter: blur(10px);
            background-color: rgba(25, 25, 25, 0.03);
        }
    </style>
</head>
<body class="d-flex align-items-center py-4 bg-light">
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-10 col-md-8 col-lg-6">
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card border-0 shadow-lg card-login bg-white">
                    <div class="card-body p-4 p-sm-5">
                        
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary mb-1">Únete a KOPI</h2>
                            <p class="text-muted small">Registra tus datos institucionales</p>
                        </div>

                        <form action="{{ route('registro.post') }}" method="POST">
                            @csrf
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6 form-floating">
                                    <input type="text" name="nombre" class="form-control" id="floatingNombre" placeholder="Tu nombre" value="{{ old('nombre') }}" required>
                                    <label for="floatingNombre" class="ms-2">Nombre Completo</label>
                                </div>
                                <div class="col-md-6 form-floating">
                                    <input type="text" name="matricula" class="form-control" id="floatingMatricula" placeholder="123456" value="{{ old('matricula') }}" required>
                                    <label for="floatingMatricula" class="ms-2">Matrícula</label>
                                </div>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" name="correo_institucional" class="form-control" id="floatingEmail" placeholder="alumno@upq.edu.mx" value="{{ old('correo_institucional') }}" required>
                                <label for="floatingEmail">Correo Institucional (@upq.edu.mx)</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="tel" name="telefono" class="form-control" id="floatingTelefono" placeholder="442..." value="{{ old('telefono') }}" required>
                                <label for="floatingTelefono">Teléfono celular (WhatsApp)</label>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="password" name="contrasena" class="form-control" id="floatingPassword" placeholder="Contraseña" required>
                                <label for="floatingPassword">Crear Contraseña (Mín. 6 caracteres)</label>
                            </div>

                            <button class="btn btn-primary btn-lg w-100 fw-bold shadow-sm mb-3" type="submit">
                                Registrarme
                            </button>

                            <div class="text-center mt-3">
                                <span class="text-muted">¿Ya tienes cuenta?</span>
                                <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Inicia Sesión</a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>