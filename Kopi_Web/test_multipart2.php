<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Support\Facades\Http::asMultipart();
$data = ["telefono" => "5551234567"];
try {
    $res = $request->post("http://127.0.0.1:8000/usuarios/perfil", $data);
    echo "Res: " . $res->body() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

