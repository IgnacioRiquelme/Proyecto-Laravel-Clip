<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Probando CierreDiaController...\n";

try {
    $controller = new App\Http\Controllers\CierreDiaController();
    echo "✓ Controller instanciado\n";
    
    $request = new Illuminate\Http\Request();
    echo "Llamando a generarCierre()...\n";
    
    $result = $controller->generarCierre($request);
    echo "✓ Ejecutado: " . get_class($result) . "\n";
    
} catch (\Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
