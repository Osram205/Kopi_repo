<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Support\Facades\Http::attach(
    "foto_perfil", "fake content", "test.jpg"
);

echo "Testing attach()...\n";
try {
    $res = $request->post("http://127.0.0.1:8000/usuarios/perfil", ["telefono" => "5551234567"]);
    echo "Status: " . $res->status() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

