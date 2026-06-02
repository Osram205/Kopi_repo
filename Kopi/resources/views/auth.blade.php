<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso | Kopi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="auth-shell" data-auth-mode="{{ $mode ?? 'login' }}">
        <section class="auth-visual">
            <a class="brand auth-brand" href="/app" aria-label="Kopi inicio">
                <span class="brand-mark">K</span>
                <span>Kopi</span>
            </a>
            <div class="auth-copy">
                <p class="eyebrow">Carpool universitario</p>
                <h1>Acceso Kopi</h1>
                <p>Entra con tu correo institucional para reservar viajes, registrar tu auto o publicar rutas.</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-tabs" role="tablist" aria-label="Acceso Kopi">
                <button class="auth-tab active" data-auth-view="login" type="button">Iniciar sesion</button>
                <button class="auth-tab" data-auth-view="registro" type="button">Registro</button>
            </div>

            <section class="auth-view active" id="auth-login">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">Acceso</p>
                        <h2>Iniciar sesion</h2>
                    </div>
                </div>
                <form id="loginForm" class="form-grid auth-form">
                    <label class="full-span">Correo institucional <input name="correo_institucional" type="email" placeholder="usuario@upq.edu.mx" required></label>
                    <label class="full-span">Contrasena <input name="contrasena" type="password" required></label>
                    <button class="primary-button" type="submit">Entrar</button>
                </form>
            </section>

            <section class="auth-view" id="auth-registro">
                <div class="panel-heading">
                    <div>
                        <p class="eyebrow">Alta</p>
                        <h2>Crear cuenta</h2>
                    </div>
                </div>
                <form id="registerForm" class="form-grid auth-form">
                    <label>Nombre <input name="nombre" required></label>
                    <label>Matricula <input name="matricula" required></label>
                    <label class="full-span">Correo institucional <input name="correo_institucional" type="email" placeholder="usuario@upq.edu.mx" required></label>
                    <label>Telefono <input name="telefono" required></label>
                    <label>Contrasena <input name="contrasena" type="password" minlength="8" required></label>
                    <label class="check-row"><input name="es_conductor" type="checkbox"> Tambien quiero publicar viajes</label>
                    <button class="primary-button" type="submit">Registrarme</button>
                </form>
            </section>

            <section class="status-line" id="statusLine" role="status" aria-live="polite"></section>
            <a class="ghost-link auth-backlink" href="/app">Volver a viajes</a>
        </section>
    </main>
</body>
</html>
