<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMCIsImV4cCI6MTc4NTI5MzgxNH0.deAKoWK-Xhetzl3MZe3Epe2qp3rH_WbfVjTQubKwmL0";
Illuminate\Support\Facades\Session::put("jwt_token", $token);
$request = Illuminate\Http\Request::create("/perfil", "GET");
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";

