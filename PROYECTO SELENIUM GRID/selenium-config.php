<?php
/**
 * Configuración de Selenium Grid
 */

// URL del Hub de Selenium Grid
define('SELENIUM_HUB_URL', 'http://localhost:4444/');

// Timeout de conexión (en milisegundos)
define('CONNECTION_TIMEOUT', 10000);

// Navegadores disponibles
define('AVAILABLE_BROWSERS', ['chrome', 'firefox']);

// Configuración de timeouts
define('IMPLICIT_WAIT', 10); // segundos
define('PAGE_LOAD_TIMEOUT', 30); // segundos

// Configuración de pantalla
define('SCREEN_WIDTH', 1920);
define('SCREEN_HEIGHT', 1080);
?>