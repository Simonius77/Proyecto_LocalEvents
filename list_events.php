<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Evento;

$eventos = Evento::select('id_evento', 'nombre', 'precio')->get();
foreach($eventos as $e) {
    echo "ID: {$e->id_evento} | Name: {$e->nombre} | Price: {$e->precio}\n";
}
