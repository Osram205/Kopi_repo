<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Generate a valid token for Ale Briones (ID 9) or Uriel Perez (ID 10)
$payload = ["sub" => "10"];
$token = "fake"; // Wait, I cant generate JWT easily here without PyJWT.

