<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. We need a valid token. Let us register a brand new user via API directly.
$regData = [
    ["name" => "nombre", "contents" => "TestUser"],
    ["name" => "apellidos", "contents" => "TestUser"],
    ["name" => "matricula", "contents" => "12345678"],
    ["name" => "carrera", "contents" => "ISI"],
    ["name" => "telefono", "contents" => "1112223333"],
    ["name" => "correo_institucional", "contents" => "test_profile@upq.edu.mx"],
    ["name" => "contrasena", "contents" => "Kopi1234!"]
];

$testImage = __DIR__ . "/public/favicon.ico";
if (!file_exists($testImage)) file_put_contents($testImage, "test");
$regData[] = ["name" => "foto_credencial", "contents" => file_get_contents($testImage), "filename" => "test.png"];

$regReq = Illuminate\Support\Facades\Http::asMultipart()->post("http://127.0.0.1:8000/auth/registro", $regData);

$login = Illuminate\Support\Facades\Http::asForm()->post("http://127.0.0.1:8000/auth/login", [
    "username" => "test_profile@upq.edu.mx",
    "password" => "Kopi1234!"
]);
$token = $login->json()["access_token"] ?? null;
if (!$token) { echo "Login failed: " . $login->body() . "\n"; exit; }

// Now let us test updating profile
$multipartData = [
    [
        "name" => "telefono",
        "contents" => "9998887777"
    ]
];
$response = Illuminate\Support\Facades\Http::withToken($token)->asMultipart()->post("http://127.0.0.1:8000/usuarios/perfil", $multipartData);
echo "Update Status: " . $response->status() . "\n";
echo "Update Body: " . $response->body() . "\n";

$perfil = Illuminate\Support\Facades\Http::withToken($token)->get("http://127.0.0.1:8000/usuarios/perfil");
echo "Perfil After Update: " . $perfil->body() . "\n";


