<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopi - Feed de Viajes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/viajes">KOPI</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarKopi" aria-controls="navbarKopi" aria-expanded="false" aria-label="Navegación">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarKopi">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('viajes') ? 'active fw-bold' : '' }}" href="/viajes">
                            Buscar Viajes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('conductor/panel') ? 'active fw-bold' : '' }}" href="/conductor/panel">
                            Zona Conductor
                        </a>
                    </li>
                </ul>

                <div class="d-flex mt-2 mt-lg-0">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm fw-bold w-100">Cerrar Sesión</button>
                    </form>
                </div>
                
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>