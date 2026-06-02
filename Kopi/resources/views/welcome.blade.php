<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kopi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app" class="app-shell">
        <header class="topbar">
            <a class="brand" href="#buscar" aria-label="Kopi inicio">
                <span class="brand-mark">K</span>
                <span>Kopi</span>
            </a>
            <nav class="nav-tabs" aria-label="Navegacion principal">
                <button class="nav-tab active" data-view="buscar" type="button">Buscar</button>
                <button class="nav-tab" data-view="reservas" type="button">Reservas</button>
                <button class="nav-tab" data-view="perfil" type="button">Perfil</button>
            </nav>
            <div class="session-actions">
                <div class="session-chip" id="sessionChip">Invitado</div>
                <a class="ghost-link" id="loginLink" href="/login">Entrar</a>
                <button class="danger-button compact-button" id="logoutButton" type="button">Salir</button>
            </div>
        </header>

        <main>
            <section class="hero-band">
                <div class="hero-copy">
                    <p class="eyebrow">Carpool universitario</p>
                    <h1>Kopi</h1>
                    <p>Busca, publica y administra viajes entre campus, casa y puntos cercanos con usuarios UPQ verificados.</p>
                </div>
                <form id="searchForm" class="search-panel">
                    <label>
                        Origen
                        <input name="origen" placeholder="Campus UPQ">
                    </label>
                    <label>
                        Destino
                        <input name="destino" placeholder="El Marques">
                    </label>
                    <label>
                        Fecha
                        <input name="fecha_salida" type="date">
                    </label>
                    <button class="primary-button" type="submit">Buscar viajes</button>
                </form>
            </section>

            <section class="status-line" id="statusLine" role="status" aria-live="polite"></section>

            <section class="view active" id="view-buscar">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Viajes disponibles</p>
                        <h2>Opciones programadas</h2>
                    </div>
                    <button class="ghost-button" id="refreshTrips" type="button">Actualizar</button>
                </div>
                <div id="tripsList" class="cards-grid"></div>
            </section>

            <section class="view" id="view-reservas">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Actividad</p>
                        <h2>Reservaciones</h2>
                    </div>
                    <button class="ghost-button auth-only" id="refreshReservations" type="button">Actualizar</button>
                </div>
                <div id="reservationsList" class="stack"></div>
            </section>

            <section class="view" id="view-perfil">
                <div id="profilePanel" class="profile-grid"></div>
            </section>
        </main>
    </div>
</body>
</html>
