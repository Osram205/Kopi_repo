<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perfil conductor | Kopi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell driver-shell">
        <header class="topbar">
            <a class="brand" href="/app" aria-label="Kopi inicio">
                <span class="brand-mark">K</span>
                <span>Kopi</span>
            </a>
            <nav class="nav-tabs" aria-label="Navegacion conductor">
                <a class="ghost-link" href="/app">Viajes</a>
                <a class="ghost-link" href="/vehiculos/nuevo">Registrar auto</a>
            </nav>
            <div class="session-actions">
                <div class="session-chip" id="sessionChip">Invitado</div>
                <a class="ghost-link" id="loginLink" href="/login">Entrar</a>
                <button class="danger-button compact-button" id="logoutButton" type="button">Salir</button>
            </div>
        </header>

        <main>
            <section class="driver-hero">
                <div>
                    <p class="eyebrow">Perfil conductor</p>
                    <h1>Gestiona tus rutas</h1>
                    <p>Publica viajes, revisa tus autos registrados y atiende solicitudes desde el flujo de conductor.</p>
                </div>
                <a class="primary-link" href="/vehiculos/nuevo">Agregar auto</a>
            </section>

            <section class="status-line" id="statusLine" role="status" aria-live="polite"></section>

            <section class="two-column">
                <article class="panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Publicacion</p>
                            <h2>Nuevo viaje</h2>
                        </div>
                    </div>
                    <form id="tripForm" class="form-grid">
                        <label>Vehiculo <select name="vehiculo_id" id="vehicleSelect" required></select></label>
                        <label>Origen <input name="origen" required></label>
                        <label>Destino <input name="destino" required></label>
                        <label>Fecha <input name="fecha_salida" type="date" required></label>
                        <label>Hora <input name="hora_salida" type="time" required></label>
                        <label>Asientos <input name="asientos_disponibles" type="number" min="1" value="3" required></label>
                        <label>Costo <input name="costo_por_asiento" type="number" min="0" step="0.01" value="35" required></label>
                        <label class="full-span">Paradas
                            <textarea name="paradas_text" rows="3" placeholder="Biblioteca | 20.587,-100.389&#10;Acceso principal | 20.590,-100.392"></textarea>
                        </label>
                        <button class="primary-button" type="submit">Publicar viaje</button>
                    </form>
                </article>

                <article class="panel">
                    <div class="panel-heading">
                        <div>
                            <p class="eyebrow">Mis autos</p>
                            <h2>Vehiculos registrados</h2>
                        </div>
                    </div>
                    <div id="vehiclesList" class="cards-grid compact-grid"></div>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
