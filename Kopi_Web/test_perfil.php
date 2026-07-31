<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$login = Illuminate\Support\Facades\Http::asForm()->post("http://127.0.0.1:8000/auth/login", [
    "username" => "a210001@upq.edu.mx",
    "password" => "Kopi1234!"
]);
$token = $login->json()["access_token"] ?? null;
if (!$token) {
    echo "No token: " . $login->body() . "\n";
    exit;
}
$perfil = Illuminate\Support\Facades\Http::withToken($token)->get("http://127.0.0.1:8000/usuarios/perfil");
echo "Perfil: " . $perfil->body() . "\n";

