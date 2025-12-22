<?php
/**
 * Script de Automatización: Buscar elemento por LinkText
 * Puerto de Java a PHP
 * 
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Navega a Wikipedia en español
 * 3. Busca un elemento por su LinkText ("Ver historial")
 * 4. Hace clic en el elemento encontrado
 * 5. Imprime el título de la nueva página
 * 6. Cierra el navegador
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Clase4_BuscandoElementoPorLinktext
{
    private $driver;
    private $chromeDriverProcess;

    /**
     * Constructor - Inicia ChromeDriver
     */
    public function __construct()
    {
        echo "\n========== BUSCAR ELEMENTO POR LINKTEXT ==========\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }

    /**
     * Inicia ChromeDriver como proceso independiente
     */
    private function iniciarChromeDriver()
    {
        echo "[1/5] Iniciando ChromeDriver en puerto " . CHROMEDRIVER_PORT . "...\n";

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
        echo "[2/5] Conectando con Selenium WebDriver...\n";

        try {
            $capabilities = DesiredCapabilities::chrome();
            $this->driver = RemoteWebDriver::create(
                CHROMEDRIVER_HOST,
                $capabilities,
                5000
            );

            echo "   ✓ Conexión establecida con éxito\n\n";
        } catch (Exception $e) {
            $this->detenerChromeDriver();
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * Navega a Wikipedia en español
     */
    private function navegarWikipedia()
    {
        echo "[3/5] Navegando a https://www.wikipedia.es...\n";

        try {
            $this->driver->get('https://www.wikipedia.es');
            echo "   ✓ Página cargada\n\n";
        } catch (Exception $e) {
            throw new Exception("Error al navegar: " . $e->getMessage());
        }
    }

    /**
     * Espera un tiempo determinado
     * Equivalente a Thread.sleep() en Java
     */
    private function esperar($milisegundos)
    {
        $segundos = $milisegundos / 1000;
        echo "[ESPERA] Esperando $segundos segundos...\n";
        sleep($segundos);
    }

    /**
     * Busca un elemento por su LinkText y hace clic
     * Equivalente a By.linkText() en Java
     */
    private function buscarYHacerClicPorLinkText($linkText)
    {
        echo "[4/5] Buscando elemento con LinkText: '$linkText'...\n";

        try {
            // Buscar el elemento por LinkText (búsqueda exacta)
            $elemento = $this->driver->findElement(WebDriverBy::linkText($linkText));
            echo "   ✓ Elemento encontrado\n";

            // Hacer clic en el elemento
            echo "   Haciendo clic en el elemento...\n";
            $elemento->click();
            echo "   ✓ Clic realizado\n\n";

        } catch (Exception $e) {
            throw new Exception("Error al buscar/hacer clic en elemento: " . $e->getMessage());
        }
    }

    /**
     * Obtiene y imprime el título de la página
     */
    private function obtenerYMostrarTitulo()
    {
        echo "[5/5] Obteniendo información de la página...\n";

        try {
            $titulo = $this->driver->getTitle();

            echo "   ═══════════════════════════════════════\n";
            echo "   TÍTULO DE LA PÁGINA:\n";
            echo "   \"" . htmlspecialchars($titulo) . "\"\n";
            echo "   ═══════════════════════════════════════\n\n";

        } catch (Exception $e) {
            throw new Exception("Error al obtener título: " . $e->getMessage());
        }
    }

    /**
     * Ejecuta el script principal
     */
    public function ejecutar()
    {
        try {
            // Navegar a Wikipedia
            $this->navegarWikipedia();

            // Esperar 2 segundos
            $this->esperar(5000);

            // Buscar el elemento con LinkText "Ver historial" y hacer clic
            $this->buscarYHacerClicPorLinkText('Ver historial');

            // Esperar 2 segundos
            $this->esperar(2000);

            // Obtener y mostrar el título
            $this->obtenerYMostrarTitulo();

        } catch (Exception $e) {
            echo "\n✗ ERROR: " . $e->getMessage() . "\n\n";
        } finally {
            $this->detener();
        }
    }

    /**
     * Cierra navegador y ChromeDriver
     */
    private function detener()
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
}

// Ejecutar el script
try {
    $script = new Clase4_BuscandoElementoPorLinktext();
    $script->ejecutar();
    echo "\n========== PROCESO COMPLETADO ==========\n\n";
} catch (Exception $e) {
    echo "\n✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
?>