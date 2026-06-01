<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ViajeController;
use App\Http\Controllers\Api\ReservacionController;
use App\Http\Controllers\Api\VehiculoController;
use App\Http\Controllers\Api\PagoController;
use App\Http\Controllers\Api\CalificacionController;

// ==========================================
// RUTAS PÚBLICAS (No requieren token)
// ==========================================
Route::post('/registro', [AuthController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);

// Opcional: Permitir que los invitados busquen viajes, pero pedir login para reservar
Route::get('/viajes/buscar', [ViajeController::class, 'index']); 


// ==========================================
// RUTAS PROTEGIDAS (Requieren inicio de sesión)
// ==========================================
Route::middleware('auth.api')->group(function () {
    
    // Perfil del usuario
    Route::get('/perfil', [AuthController::class, 'perfil']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Gestión de Viajes (Conductor)
    Route::apiResource('/vehiculos', VehiculoController::class)->except(['show']);
    Route::post('/viajes', [ViajeController::class, 'store']); // Publicar viaje
    Route::get('/viajes/{id}', [ViajeController::class, 'show']); // Detalle viaje
    Route::put('/viajes/{id}', [ViajeController::class, 'update']); // Editar viaje
    Route::delete('/viajes/{id}', [ViajeController::class, 'destroy']); // Cancelar viaje

    // Gestión de Reservaciones
    Route::get('/reservaciones', [ReservacionController::class, 'index']);
    Route::post('/reservaciones', [ReservacionController::class, 'store']); // Solicitar asiento
    Route::put('/reservaciones/{id}/estatus', [ReservacionController::class, 'actualizarEstatus']); // Conductor acepta/rechaza
    Route::post('/pagos', [PagoController::class, 'store']);
    Route::put('/pagos/{id}/estatus', [PagoController::class, 'actualizarEstatus']);
    Route::post('/calificaciones', [CalificacionController::class, 'store']);

});
