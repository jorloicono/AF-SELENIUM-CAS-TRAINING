<?php
/**
 * Configuración de Selenium + PHP
 * 
 * Este archivo contiene la configuración centralizada
 * para las pruebas de automatización
 */

define('CHROMEDRIVER_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'chromedriver.exe');
define('CHROMEDRIVER_PORT', 9515);
define('CHROMEDRIVER_HOST', 'http://127.0.0.1:' . CHROMEDRIVER_PORT);

// Verificar que ChromeDriver existe
if (!file_exists(CHROMEDRIVER_PATH)) {
    die('ERROR: chromedriver.exe no encontrado en: ' . CHROMEDRIVER_PATH . "\n");
}

echo "✓ ChromeDriver encontrado en: " . CHROMEDRIVER_PATH . "\n";
?>