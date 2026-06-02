const state = {
    token: localStorage.getItem('kopi_token') || '',
    user: JSON.parse(localStorage.getItem('kopi_user') || 'null'),
    trips: [],
    vehicles: [],
    reservations: [],
};

const els = {
    status: document.querySelector('#statusLine'),
    sessionChip: document.querySelector('#sessionChip'),
    loginLink: document.querySelector('#loginLink'),
    logoutButton: document.querySelector('#logoutButton'),
    tripsList: document.querySelector('#tripsList'),
    reservationsList: document.querySelector('#reservationsList'),
    vehiclesList: document.querySelector('#vehiclesList'),
    vehicleSelect: document.querySelector('#vehicleSelect'),
    profilePanel: document.querySelector('#profilePanel'),
};

const isAuthPage = Boolean(document.querySelector('.auth-shell'));
const isAppPage = Boolean(document.querySelector('#tripsList'));
const isDriverPage = Boolean(document.querySelector('.driver-shell'));
const isVehiclePage = Boolean(document.querySelector('.vehicle-shell'));

const money = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

function setStatus(message = '', type = '') {
    if (!els.status) return;
    els.status.textContent = message;
    els.status.className = `status-line ${type}`.trim();
}

function authHeaders() {
    return state.token ? { Authorization: `Bearer ${state.token}` } : {};
}

async function api(path, options = {}) {
    const response = await fetch(`/api${path}`, {
        ...options,
        headers: {
            Accept: 'application/json',
            ...(options.body ? { 'Content-Type': 'application/json' } : {}),
            ...authHeaders(),
            ...(options.headers || {}),
        },
    });

    const text = await response.text();
    const data = text ? JSON.parse(text) : {};

    if (!response.ok) {
        const details = data.errors
            ? Object.values(data.errors).flat().join(' ')
            : data.message || 'No se pudo completar la operacion.';
        throw new Error(details);
    }

    return data;
}

function persistSession(token, user) {
    state.token = token || '';
    state.user = user || null;

    if (state.token) {
        localStorage.setItem('kopi_token', state.token);
        localStorage.setItem('kopi_user', JSON.stringify(state.user));
    } else {
        localStorage.removeItem('kopi_token');
        localStorage.removeItem('kopi_user');
    }

    renderSession();
}

function renderSession() {
    const isLogged = Boolean(state.token && state.user);
    if (els.sessionChip) {
        els.sessionChip.textContent = isLogged ? state.user.nombre : 'Invitado';
    }
    if (els.loginLink) {
        els.loginLink.hidden = isLogged;
    }
    if (els.logoutButton) {
        els.logoutButton.hidden = !isLogged;
    }

    document.querySelectorAll('.auth-only').forEach((button) => {
        button.disabled = !isLogged;
    });

    renderProfile();
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function formatDate(date) {
    if (!date) return 'Sin fecha';
    const [year, month, day] = String(date).slice(0, 10).split('-').map(Number);
    return new Date(year, month - 1, day).toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function normalizeCollection(payload) {
    return Array.isArray(payload) ? payload : payload.data || [];
}

async function loadTrips(params = new URLSearchParams()) {
    setStatus('Cargando viajes...');
    const query = params.toString();
    const payload = await api(`/viajes/buscar${query ? `?${query}` : ''}`);
    state.trips = normalizeCollection(payload);
    renderTrips();
    setStatus(`${state.trips.length} viaje(s) disponible(s).`, 'success');
}

function renderTrips() {
    if (!els.tripsList) return;
    if (!state.trips.length) {
        els.tripsList.innerHTML = '<div class="empty-state">No hay viajes que coincidan con la busqueda.</div>';
        return;
    }

    els.tripsList.innerHTML = state.trips.map((trip) => {
        const paradas = trip.paradas || [];
        const firstStop = paradas[0];
        const canReserve = state.token && firstStop && trip.asientos_disponibles > 0;

        return `
            <article class="card">
                <div>
                    <div class="route-line">
                        <span>${escapeHtml(trip.origen)}</span>
                        <strong>-></strong>
                        <span>${escapeHtml(trip.destino)}</span>
                    </div>
                    <div class="trip-meta">
                        <span class="pill">${formatDate(trip.fecha_salida)}</span>
                        <span class="pill">${escapeHtml(trip.hora_salida || 'Hora pendiente')}</span>
                        <span class="pill">${trip.asientos_disponibles} asiento(s)</span>
                        <span class="pill">${money.format(Number(trip.costo_por_asiento || 0))}</span>
                    </div>
                </div>
                <div class="mini-meta">
                    <span>Conductor: ${escapeHtml(trip.conductor?.nombre || 'Sin dato')}</span>
                    <span>Auto: ${escapeHtml([trip.vehiculo?.marca, trip.vehiculo?.modelo, trip.vehiculo?.color].filter(Boolean).join(' ') || 'Sin auto')}</span>
                    <span>Paradas: ${paradas.length || 0}</span>
                </div>
                <div class="card-actions">
                    <button class="small-button reserve-trip" data-trip="${trip.id}" data-stop="${firstStop?.id || ''}" ${canReserve ? '' : 'disabled'} type="button">Reservar</button>
                    ${!state.token ? '<span class="pill">Inicia sesion para reservar</span>' : ''}
                    ${state.token && !firstStop ? '<span class="pill">Sin paradas para abordar</span>' : ''}
                </div>
            </article>
        `;
    }).join('');
}

async function loadVehicles() {
    if (!state.token) {
        state.vehicles = [];
        renderVehicles();
        return;
    }

    const payload = await api('/vehiculos');
    state.vehicles = normalizeCollection(payload);
    renderVehicles();
}

function renderVehicles() {
    if (els.vehicleSelect) {
        els.vehicleSelect.innerHTML = state.vehicles.length
            ? state.vehicles.map((vehicle) => `<option value="${vehicle.id}">${escapeHtml(vehicle.placas)} | ${escapeHtml(vehicle.marca)} ${escapeHtml(vehicle.modelo)}</option>`).join('')
            : '<option value="">Registra un vehiculo primero</option>';
    }

    if (!els.vehiclesList) {
        renderProfile();
        return;
    }

    if (!state.vehicles.length) {
        els.vehiclesList.innerHTML = '<div class="empty-state">Aun no tienes vehiculos registrados. Registra un auto antes de publicar rutas.</div>';
        renderProfile();
        return;
    }

    els.vehiclesList.innerHTML = state.vehicles.map((vehicle) => `
        <article class="card">
            <div class="route-line"><span>${escapeHtml(vehicle.placas)}</span></div>
            <div class="mini-meta">
                <span>${escapeHtml(vehicle.marca)} ${escapeHtml(vehicle.modelo)}</span>
                <span>${escapeHtml(vehicle.color)}</span>
                <span>${vehicle.asientos_totales} asiento(s)</span>
            </div>
        </article>
    `).join('');
    renderProfile();
}

function userIsDriver() {
    return Boolean(state.user?.es_conductor || state.vehicles.length);
}

function renderProfile() {
    if (!els.profilePanel) return;

    if (!state.token || !state.user) {
        els.profilePanel.innerHTML = `
            <article class="panel profile-card passenger-profile">
                <p class="eyebrow">Perfil pasajero</p>
                <h2>Sesion requerida</h2>
                <p>Inicia sesion para ver tus reservaciones y convertirte en conductor.</p>
                <a class="primary-link" href="/login">Iniciar sesion</a>
            </article>
        `;
        return;
    }

    if (userIsDriver()) {
        els.profilePanel.innerHTML = `
            <article class="panel profile-card driver-profile">
                <p class="eyebrow">Perfil conductor</p>
                <h2>${escapeHtml(state.user.nombre)}</h2>
                <p>Tu cuenta puede publicar viajes y administrar autos registrados.</p>
                <div class="profile-stats">
                    <span><strong>${state.vehicles.length}</strong> auto(s)</span>
                    <span><strong>${state.reservations.length}</strong> reservacion(es)</span>
                </div>
                <div class="card-actions">
                    <a class="primary-link" href="/conductor">Abrir panel conductor</a>
                    <a class="ghost-link" href="/vehiculos/nuevo">Registrar auto</a>
                </div>
            </article>
        `;
        return;
    }

    els.profilePanel.innerHTML = `
        <article class="panel profile-card passenger-profile">
            <p class="eyebrow">Perfil pasajero</p>
            <h2>${escapeHtml(state.user.nombre)}</h2>
            <p>Tu cuenta esta en modo pasajero. Puedes buscar viajes, reservar asientos y revisar tus reservaciones.</p>
            <div class="profile-stats">
                <span><strong>${state.reservations.length}</strong> reservacion(es)</span>
                <span><strong>0</strong> autos</span>
            </div>
            <div class="card-actions">
                <a class="primary-link" href="/vehiculos/nuevo">Convertirme en conductor</a>
                <button class="ghost-button" data-view="reservas" type="button">Ver reservas</button>
            </div>
        </article>
    `;
}

async function loadReservations() {
    if (!els.reservationsList) return;
    if (!state.token) {
        els.reservationsList.innerHTML = '<div class="empty-state">Inicia sesion para ver tus reservaciones.</div>';
        return;
    }

    setStatus('Cargando reservaciones...');
    const payload = await api('/reservaciones');
    state.reservations = normalizeCollection(payload);
    renderReservations();
    setStatus(`${state.reservations.length} reservacion(es) encontrada(s).`, 'success');
}

function renderReservations() {
    if (!els.reservationsList) return;
    if (!state.reservations.length) {
        els.reservationsList.innerHTML = '<div class="empty-state">No tienes reservaciones activas.</div>';
        return;
    }

    els.reservationsList.innerHTML = state.reservations.map((reservation) => {
        const trip = reservation.viaje || {};
        const isDriver = trip.conductor_id === state.user?.id;
        const isPassenger = reservation.pasajero_id === state.user?.id;
        const accepted = reservation.estatus_reserva === 'aceptado';

        return `
            <article class="reservation-row">
                <div>
                    <div class="route-line">
                        <span>${escapeHtml(trip.origen || 'Origen')}</span>
                        <strong>-></strong>
                        <span>${escapeHtml(trip.destino || 'Destino')}</span>
                    </div>
                    <div class="trip-meta">
                        <span class="pill">${escapeHtml(reservation.estatus_reserva)}</span>
                        <span class="pill">${reservation.asientos_solicitados} asiento(s)</span>
                        <span class="pill">${formatDate(trip.fecha_salida)}</span>
                        <span class="pill">${escapeHtml(reservation.parada_subida?.nombre_parada || 'Parada')}</span>
                    </div>
                </div>
                <div class="card-actions">
                    ${isDriver && reservation.estatus_reserva === 'solicitado' ? `<button class="small-button reservation-status" data-id="${reservation.id}" data-status="aceptado" type="button">Aceptar</button><button class="small-button reservation-status" data-id="${reservation.id}" data-status="rechazado" type="button">Rechazar</button>` : ''}
                    ${isPassenger && accepted ? `<button class="small-button pay-reservation" data-id="${reservation.id}" type="button">Registrar pago</button>` : ''}
                    ${isPassenger && ['solicitado', 'aceptado'].includes(reservation.estatus_reserva) ? `<button class="small-button reservation-status" data-id="${reservation.id}" data-status="cancelado" type="button">Cancelar</button>` : ''}
                </div>
            </article>
        `;
    }).join('');
}

function switchView(viewName) {
    document.querySelectorAll('.view').forEach((view) => view.classList.remove('active'));
    document.querySelector(`#view-${viewName}`)?.classList.add('active');
    document.querySelectorAll('.nav-tab').forEach((tab) => {
        tab.classList.toggle('active', tab.dataset.view === viewName);
    });

    if (viewName === 'reservas') loadReservations().catch(showError);
    if (viewName === 'perfil') {
        Promise.all([loadVehicles(), loadReservations()]).catch(showError);
        renderProfile();
    }
}

function formData(form) {
    return Object.fromEntries(new FormData(form).entries());
}

function parseStops(text) {
    return text
        .split('\n')
        .map((line) => line.trim())
        .filter(Boolean)
        .map((line, index) => {
            const [name, coords] = line.split('|').map((part) => part?.trim());
            return {
                nombre_parada: name || `Parada ${index + 1}`,
                coordenadas: coords || '0,0',
                orden: index + 1,
            };
        });
}

function requireAuth() {
    if (!state.token) {
        window.location.href = '/login';
        throw new Error('Inicia sesion para continuar.');
    }
}

function showError(error) {
    setStatus(error.message, 'error');
}

document.querySelectorAll('.nav-tab').forEach((tab) => {
    tab.addEventListener('click', () => switchView(tab.dataset.view));
});

document.querySelector('#searchForm')?.addEventListener('submit', (event) => {
    event.preventDefault();
    const params = new URLSearchParams();
    Object.entries(formData(event.currentTarget)).forEach(([key, value]) => {
        if (value) params.set(key, value);
    });
    loadTrips(params).catch(showError);
});

document.querySelector('#refreshTrips')?.addEventListener('click', () => loadTrips().catch(showError));
document.querySelector('#refreshReservations')?.addEventListener('click', () => loadReservations().catch(showError));

document.querySelector('#loginForm')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        const payload = await api('/login', {
            method: 'POST',
            body: JSON.stringify({ ...formData(event.currentTarget), device_name: 'web' }),
        });
        persistSession(payload.access_token, payload.usuario);
        event.currentTarget.reset();
        setStatus('Sesion iniciada.', 'success');
        window.location.href = '/app';
    } catch (error) {
        showError(error);
    }
});

document.querySelector('#registerForm')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const data = formData(event.currentTarget);
    data.es_conductor = Boolean(data.es_conductor);

    try {
        await api('/registro', { method: 'POST', body: JSON.stringify(data) });
        event.currentTarget.reset();
        setStatus('Cuenta creada. Ya puedes iniciar sesion.', 'success');
        switchAuthView('login');
    } catch (error) {
        showError(error);
    }
});

document.querySelector('#vehicleForm')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        requireAuth();
        const data = formData(event.currentTarget);
        data.asientos_totales = Number(data.asientos_totales);
        await api('/vehiculos', { method: 'POST', body: JSON.stringify(data) });
        state.user = { ...state.user, es_conductor: true };
        localStorage.setItem('kopi_user', JSON.stringify(state.user));
        event.currentTarget.reset();
        setStatus('Vehiculo registrado.', 'success');
        await loadVehicles();
        window.location.href = '/conductor';
    } catch (error) {
        showError(error);
    }
});

document.querySelector('#tripForm')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
        requireAuth();
        const data = formData(event.currentTarget);
        const paradas = parseStops(data.paradas_text);
        delete data.paradas_text;
        data.vehiculo_id = Number(data.vehiculo_id);
        data.asientos_disponibles = Number(data.asientos_disponibles);
        data.costo_por_asiento = Number(data.costo_por_asiento);
        data.paradas = paradas.length ? paradas : [{ nombre_parada: data.origen, coordenadas: '0,0', orden: 1 }];

        await api('/viajes', { method: 'POST', body: JSON.stringify(data) });
        event.currentTarget.reset();
        setStatus('Viaje publicado.', 'success');
        if (isAppPage && els.tripsList) {
            await loadTrips();
            switchView('buscar');
        }
    } catch (error) {
        showError(error);
    }
});

document.querySelector('#logoutButton')?.addEventListener('click', async () => {
    try {
        if (state.token) await api('/logout', { method: 'POST' });
    } catch {
        // Local logout should still proceed if the token is already invalid.
    }
    persistSession('', null);
    state.reservations = [];
    state.vehicles = [];
            renderReservations();
            renderVehicles();
            renderTrips();
            renderProfile();
            setStatus('Sesion cerrada.', 'success');
});

function switchAuthView(viewName) {
    document.querySelectorAll('.auth-view').forEach((view) => view.classList.remove('active'));
    document.querySelector(`#auth-${viewName}`)?.classList.add('active');
    document.querySelectorAll('.auth-tab').forEach((tab) => {
        tab.classList.toggle('active', tab.dataset.authView === viewName);
    });
    if (viewName === 'registro') {
        history.replaceState(null, '', '/registro');
    } else {
        history.replaceState(null, '', '/login');
    }
}

document.querySelectorAll('.auth-tab').forEach((tab) => {
    tab.addEventListener('click', () => switchAuthView(tab.dataset.authView));
});

document.addEventListener('click', async (event) => {
    const reserveButton = event.target.closest('.reserve-trip');
    const statusButton = event.target.closest('.reservation-status');
    const payButton = event.target.closest('.pay-reservation');
    const viewButton = event.target.closest('[data-view]');

    if (viewButton && viewButton.tagName === 'BUTTON') {
        switchView(viewButton.dataset.view);
    }

    try {
        if (reserveButton) {
            requireAuth();
            await api('/reservaciones', {
                method: 'POST',
                body: JSON.stringify({
                    viaje_id: Number(reserveButton.dataset.trip),
                    parada_subida_id: Number(reserveButton.dataset.stop),
                    asientos_solicitados: 1,
                }),
            });
            setStatus('Reservacion solicitada.', 'success');
            await Promise.all([loadTrips(), loadReservations()]);
        }

        if (statusButton) {
            requireAuth();
            await api(`/reservaciones/${statusButton.dataset.id}/estatus`, {
                method: 'PUT',
                body: JSON.stringify({ estatus_reserva: statusButton.dataset.status }),
            });
            setStatus('Reservacion actualizada.', 'success');
            await Promise.all([loadTrips(), loadReservations()]);
        }

        if (payButton) {
            requireAuth();
            await api('/pagos', {
                method: 'POST',
                body: JSON.stringify({
                    reservacion_id: Number(payButton.dataset.id),
                    metodo_pago: 'transferencia',
                }),
            });
            setStatus('Pago registrado como pendiente.', 'success');
            await loadReservations();
        }
    } catch (error) {
        showError(error);
    }
});

renderSession();

if (isAuthPage) {
    const authMode = document.querySelector('.auth-shell')?.dataset.authMode || 'login';
    switchAuthView(authMode);
}

if (isAppPage) {
    renderReservations();
    renderVehicles();
    loadTrips().catch(showError);
}

if ((isAppPage || isDriverPage) && state.token) {
    Promise.all([loadVehicles(), loadReservations()]).catch(showError);
}

if ((isDriverPage || isVehiclePage) && !state.token) {
    window.location.href = '/login';
}
