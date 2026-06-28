<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Livewire\ViajesFeed; 
use App\Livewire\PanelConductor;
use App\Livewire\RestablecerPassword;

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
Route::get('/recuperar-password', [AuthController::class, 'showRecuperar'])->name('password.request');
Route::get('/recuperar-password', RestablecerPassword::class)->name('password.request');

// Ruta protegida apuntando directamente al componente de Livewire
Route::get('/viajes', ViajesFeed::class)->name('viajes.index');
Route::get('/conductor/panel', PanelConductor::class)->name('conductor.panel');