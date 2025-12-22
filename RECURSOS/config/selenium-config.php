<?php
/**
 * Configuración de Selenium + PHP
 * 
 * Este archivo contiene la configuración centralizada
 * para las pruebas de automatización
 */

// Rutas del proyecto
define('PROJECT_ROOT', dirname(__DIR__));
define('CHROMEDRIVER_PATH', PROJECT_ROOT . DIRECTORY_SEPARATOR . 'drivers' . DIRECTORY_SEPARATOR . 'chromedriver.exe');
define('TEST_PAGES_PATH', PROJECT_ROOT . DIRECTORY_SEPARATOR . 'test-pages');

// Configuración de ChromeDriver
define('CHROMEDRIVER_PORT', 9515);
define('CHROMEDRIVER_HOST', 'http://127.0.0.1:' . CHROMEDRIVER_PORT);

// Verificar que ChromeDriver existe
if (!file_exists(CHROMEDRIVER_PATH)) {
    die('ERROR: chromedriver.exe no encontrado en: ' . CHROMEDRIVER_PATH . "\n");
}

echo "✓ ChromeDriver encontrado en: " . CHROMEDRIVER_PATH . "\n";
?>