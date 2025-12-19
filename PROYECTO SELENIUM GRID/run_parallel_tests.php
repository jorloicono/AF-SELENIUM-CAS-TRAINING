<?php
/**
 * EJECUTAR SAUCEDOMO TESTS EN PARALELO
 */

// Instalar dependencias
if (!file_exists('vendor/autoload.php')) {
    echo "Instalando dependencias...\n";
    exec('composer install');
}

require_once 'vendor/autoload.php';
require_once 'selenium-config.php';
require_once 'src/ParallelRunner.php';

use App\ParallelRunner;

// Leer usuarios desde CSV
$users = [];
if (($handle = fopen('users.csv', 'r')) !== false) {
    fgetcsv($handle); // Saltar header
    while (($row = fgetcsv($handle)) !== false) {
        $users[] = [
            'username' => $row[0],
            'password' => $row[1],
            'expected_role' => $row[2],
            'expected_url' => $row[3]
        ];
    }
    fclose($handle);
}

if (empty($users)) {
    die("❌ No se encontraron usuarios en users.csv\n");
}

// Navegadores para paralelización
$browsers = ['chrome', 'firefox'];

// EJECUTAR EN PARALELO
$results = ParallelRunner::runParallel($users, $browsers);
?>