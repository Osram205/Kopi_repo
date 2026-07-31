<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$testImage = __DIR__ . "/public/favicon.ico"; // Just to have a file
if (!file_exists($testImage)) file_put_contents($testImage, "test");

$response = Illuminate\Support\Facades\Http::attach(
    "foto_credencial", file_get_contents($testImage), "test.png"
)->post("http://127.0.0.1:8000/auth/registro", [
    "nombre" => "Test",
    "apellidos" => "User",
    "matricula" => "12345678",
    "carrera" => "ISI",
    "telefono" => "1234567890",
    "correo_institucional" => "test12345@upq.edu.mx",
    "contrasena" => "Kopi1234!"
]);
echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";

