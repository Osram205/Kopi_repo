<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMCIsImV4cCI6MTc4NTI5MzgxNH0.deAKoWK-Xhetzl3MZe3Epe2qp3rH_WbfVjTQubKwmL0";
$multipartData = [];
try {
    $response = Illuminate\Support\Facades\Http::withToken($token)
        ->asMultipart()
        ->post("http://127.0.0.1:8000/usuarios/perfil", $multipartData);
    echo "Status: " . $response->status() . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

