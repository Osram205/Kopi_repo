<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopi - Iniciar Sesión</title>
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
            <div class="col-11 col-sm-8 col-md-6 col-lg-5">
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card border-0 shadow-lg card-login bg-white">
                    <div class="card-body p-4 p-sm-5">
                        
                        <div class="text-center mb-4">
                            <h1 class="fw-bold text-primary mb-1">KOPI</h1>
                            <p class="text-muted small text-uppercase tracking-wider">Sistema de Carpooling Universitario</p>
                        </div>

                        <form action="{{ route('login.post') }}" method="POST">
                            @csrf
                            
                            <div class="form-floating mb-3">
                                <input type="email" 
                                       name="correo_institucional" 
                                       class="form-control @error('correo_institucional') is-invalid @enderror" 
                                       id="floatingEmail" 
                                       placeholder="alumno@upq.edu.mx"
                                       value="{{ old('correo_institucional') }}"
                                       required>
                                <label for="floatingEmail">Correo Institucional</label>
                                @error('correo_institucional')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating mb-2">
                                <input type="password" 
                                       name="contrasena" 
                                       class="form-control @error('contrasena') is-invalid @enderror" 
                                       id="floatingPassword" 
                                       placeholder="Contraseña"
                                       required>
                                <label for="floatingPassword">Contraseña</label>
                                @error('contrasena')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end mb-4">
                                <a href="{{ route('password.request') }}" class="text-decoration-none small fw-bold text-primary">¿Olvidaste tu contraseña?</a>
                            </div>

                            <button class="btn btn-primary btn-lg w-100 fw-bold shadow-sm mb-3" type="submit">
                                Ingresar al Sistema
                            </button>

                            <div class="d-flex align-items-center my-4">
                                <hr class="flex-grow-1 text-muted" style="opacity: 0.2;">
                                <span class="px-3 text-muted small text-uppercase tracking-wider">o</span>
                                <hr class="flex-grow-1 text-muted" style="opacity: 0.2;">
                            </div>

                            <a href="{{ route('registro') }}" class="btn btn-outline-dark btn-lg w-100 fw-bold shadow-sm mb-3">
                                Crear nueva cuenta
                            </a>

                            <div class="text-center mt-3">
                                <small class="text-muted">Exclusivo para la comunidad universitaria</small>
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