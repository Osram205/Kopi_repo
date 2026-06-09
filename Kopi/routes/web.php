<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViajeWebController;

// ==========================================
// RUTAS DE LA APLICACIÓN WEB (CLIENTE)
// ==========================================

// Pantalla principal que carga los viajes desde FastAPI
Route::get('/', [ViajeWebController::class, 'index'])->name('home');

// Pantallas de autenticación (las conectaremos después a FastAPI)
Route::get('/login', function () { return view('auth', ['mode' => 'login']); })->name('login');
Route::get('/registro', function () { return view('auth', ['mode' => 'registro']); })->name('registro');

// Pantallas del conductor
Route::get('/conductor', [ViajeWebController::class, 'create'])->name('conductor.create');
Route::post('/conductor/viajes', [ViajeWebController::class, 'store'])->name('conductor.store');

Route::get('/vehiculos/nuevo', function () { return view('vehicle'); })->name('vehiculos.create');