<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$viajes = Illuminate\Support\Facades\DB::table("viajes")->get();
foreach($viajes as $v) {
    echo "ID: $v->id | Conductor: $v->conductor_id | Estatus: $v->estatus | Asientos: $v->asientos_disponibles | Fecha: $v->fecha_salida | Borrado: $v->deleted_at\n";
}

