<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/app', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth');
});

Route::get('/registro', function () {
    return view('auth', ['mode' => 'registro']);
});

Route::get('/vehiculos/nuevo', function () {
    return view('vehicle');
});

Route::get('/conductor', function () {
    return view('driver');
});
