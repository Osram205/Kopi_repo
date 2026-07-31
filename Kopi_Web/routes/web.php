<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Livewire\ViajesFeed; 
use App\Livewire\PanelConductor;
use App\Livewire\RestablecerPassword;
use App\Livewire\PublicarViaje;
use App\Livewire\GestorReservaciones;
use App\Livewire\MisViajes;
use App\Livewire\RastreoViaje;
use App\Livewire\CalificarViaje;
use App\Livewire\PerfilUsuario;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'procesarLogin'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// --- NUEVAS RUTAS DE ACCESO ---
Route::get('/registro', [AuthController::class, 'showRegistro'])->name('registro');
Route::post('/registro', [AuthController::class, 'procesarRegistro'])->name('registro.post'); // <- NUEVA
Route::get('/recuperar-password', RestablecerPassword::class)->name('password.request');

// Rutas protegidas por sesión JWT local.
Route::middleware('jwt.session')->group(function () {
    Route::get('/viajes', ViajesFeed::class)->name('viajes.index');
    Route::get('/conductor/panel', PanelConductor::class)->name('conductor.panel');
    Route::get('/publicar-viaje', PublicarViaje::class)->name('viajes.publicar');
    Route::get('/mis-solicitudes', GestorReservaciones::class)->name('reservaciones.gestionar');
    Route::get('/mis-viajes', MisViajes::class)->name('viajes.mios');
    Route::get('/viaje/{id}/rastreo', RastreoViaje::class)->name('viaje.rastreo');
    Route::get('/viajes/{viaje_id}/calificar/{evaluado_id?}', CalificarViaje::class)->name('calificar.viaje');
    Route::get('/perfil', PerfilUsuario::class)->name('perfil');
});
