<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrar auto | Kopi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell vehicle-shell">
        <header class="topbar">
            <a class="brand" href="/app" aria-label="Kopi inicio">
                <span class="brand-mark">K</span>
                <span>Kopi</span>
            </a>
            <nav class="nav-tabs" aria-label="Navegacion secundaria">
                <a class="ghost-link" href="/app">Viajes</a>
                <a class="ghost-link" href="/conductor">Perfil conductor</a>
            </nav>
            <div class="session-actions">
                <div class="session-chip" id="sessionChip">Invitado</div>
                <a class="ghost-link" id="loginLink" href="/login">Entrar</a>
                <button class="danger-button compact-button" id="logoutButton" type="button">Salir</button>
            </div>
        </header>

        <main class="narrow-main">
            <section class="status-line" id="statusLine" role="status" aria-live="polite"></section>
            <article class="panel standalone-panel">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">Vehiculo</p>
                        <h1>Registrar auto</h1>
                    </div>
                </div>
                <form id="vehicleForm" class="form-grid">
                    <label>Placas <input name="placas" maxlength="10" required></label>
                    <label>Marca <input name="marca" required></label>
                    <label>Modelo <input name="modelo" required></label>
                    <label>Color <input name="color" required></label>
                    <label>Asientos <input name="asientos_totales" type="number" min="1" max="12" value="4" required></label>
                    <button class="primary-button" type="submit">Guardar vehiculo</button>
                </form>
            </article>
        </main>
    </div>
</body>
</html>
