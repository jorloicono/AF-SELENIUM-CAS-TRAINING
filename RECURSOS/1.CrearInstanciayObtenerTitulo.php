<?php
/**
 * Script de Automatización: Obtener título de Wikipedia
 * 
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Abre Wikipedia en Chrome
 * 3. Extrae el título de la página
 * 4. Cierra navegador y ChromeDriver
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class WikipediaBot
{
    private $driver;
    private $chromeDriverProcess;

    public function __construct()
    {
        echo "\n========== AUTOMATIZACIÓN WIKIPEDIA ==========\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }

    /**
     * Inicia ChromeDriver como proceso independiente
     */
    private function iniciarChromeDriver()
    {
        echo "[1/4] Iniciando ChromeDriver en puerto " . CHROMEDRIVER_PORT . "...\n";

        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $this->chromeDriverProcess = proc_open(
            CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT,
            $descriptorspec,
            $pipes
        );

        // Esperar a que ChromeDriver esté listo
        sleep(2);

        if (!is_resource($this->chromeDriverProcess)) {
            throw new Exception("No se pudo iniciar ChromeDriver");
        }

        echo "   ✓ ChromeDriver iniciado correctamente\n\n";
    }

    /**
     * Conecta WebDriver con ChromeDriver
     */
    private function conectarDriver()
    {
        echo "[2/4] Conectando con Selenium WebDriver...\n";

        try {
            $capabilities = DesiredCapabilities::chrome();
            $this->driver = RemoteWebDriver::create(
                CHROMEDRIVER_HOST,
                $capabilities,
                5000 // timeout de 5 segundos
            );
            echo "   ✓ Conexión establecida con éxito\n\n";
        } catch (Exception $e) {
            $this->detenerChromeDriver();
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * Abre Wikipedia y obtiene información
     */
    public function obtenerInfoWikipedia()
    {
        echo "[3/4] Navegando a Wikipedia...\n";

        try {
            // Navegar a Wikipedia
            $this->driver->get('https://es.wikipedia.org/wiki/Wikipedia:Portada');

            // Esperar a que la página cargue
            sleep(2);

            // Obtener título de la página
            $titulo = $this->driver->getTitle();

            echo "   ✓ Página cargada correctamente\n\n";

            echo "[4/4] Información obtenida:\n";
            echo "   ═══════════════════════════════════════\n";
            echo "   TÍTULO DE LA PÁGINA:\n";
            echo "   \"" . htmlspecialchars($titulo) . "\"\n";
            echo "   ═══════════════════════════════════════\n\n";

            // Información adicional
            $url = $this->driver->getCurrentURL();
            echo "   URL actual: " . htmlspecialchars($url) . "\n";

            // Obtener el H1 principal de Wikipedia
            try {
                $h1 = $this->driver->findElement(WebDriverBy::cssSelector('h1.firstHeading'));
                $titulo_contenido = $h1->getText();
                echo "   Encabezado H1: " . htmlspecialchars($titulo_contenido) . "\n";
            } catch (Exception $e) {
                echo "   [No se encontró encabezado H1]\n";
            }

            echo "\n";

        } catch (Exception $e) {
            echo "   ✗ Error durante la navegación: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }

    /**
     * Cierra navegador y ChromeDriver
     */
    public function detener()
    {
        echo "Finalizando...\n";

        if ($this->driver !== null) {
            try {
                $this->driver->quit();
                echo "   ✓ Navegador cerrado\n";
            } catch (Exception $e) {
                echo "   ⚠ Error al cerrar navegador: " . $e->getMessage() . "\n";
            }
        }

        $this->detenerChromeDriver();
    }

    /**
     * Detiene el proceso de ChromeDriver
     */
    private function detenerChromeDriver()
    {
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
            echo "   ✓ ChromeDriver detenido\n";
        }
    }

    /**
     * Ejecutor principal
     */
    public function ejecutar()
    {
        try {
            $this->obtenerInfoWikipedia();
        } finally {
            $this->detener();
            echo "\n========== PROCESO COMPLETADO ==========\n\n";
        }
    }
}

// Ejecutar el bot
try {
    $bot = new WikipediaBot();
    $bot->ejecutar();
} catch (Exception $e) {
    echo "\n✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
?>