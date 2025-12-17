<?php
/**
 * Script de Automatización: Buscar elemento por TagName
 * Puerto de Java a PHP
 * 
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Navega a Wikipedia en español
 * 3. Busca un elemento por su TagName ("title")
 * 4. Obtiene el atributo "innerText" del elemento
 * 5. Imprime el título de la página
 * 6. Cierra el navegador
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Clase5_BuscarElementoPorTagName
{
    private $driver;
    private $chromeDriverProcess;

    /**
     * Constructor - Inicia ChromeDriver
     */
    public function __construct()
    {
        echo "\n========== BUSCAR ELEMENTO POR TAGNAME ==========\n\n";
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
        echo "[3/4] Navegando a https://www.wikipedia.es...\n";

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
        usleep($milisegundos * 1000);
    }

    /**
     * Busca un elemento por su TagName y obtiene un atributo
     * Equivalente a By.tagName() en Java
     */
    private function buscarElementoPorTagName($tagName, $atributo)
    {
        echo "[4/4] Buscando elemento con TagName: '$tagName'...\n";

        try {
            // Buscar el elemento por TagName
            // Nota: En Selenium PHP, tagName se busca con cssSelector
            $elemento = $this->driver->findElement(WebDriverBy::tagName($tagName));
            echo "   ✓ Elemento encontrado\n\n";

            // Obtener el atributo del elemento
            echo "   Obteniendo atributo '$atributo'...\n";
            $valorAtributo = $elemento->getAttribute($atributo);

            // Si el atributo no existe, intentar obtener el texto del elemento
            if (empty($valorAtributo)) {
                $valorAtributo = $elemento->getText();
                echo "   (Usando getText() en lugar de getAttribute)\n";
            }

            echo "   ✓ Atributo obtenido\n\n";

            return $valorAtributo;

        } catch (Exception $e) {
            throw new Exception("Error al buscar elemento o atributo: " . $e->getMessage());
        }
    }

    /**
     * Obtiene y imprime el título de la página
     */
    private function mostrarTitulo($titulo)
    {
        echo "   ═══════════════════════════════════════\n";
        echo "   TÍTULO DE LA PÁGINA:\n";
        echo "   \"" . htmlspecialchars($titulo) . "\"\n";
        echo "   ═══════════════════════════════════════\n\n";
    }

    /**
     * Ejecuta el script principal
     */
    public function ejecutar()
    {
        try {
            // Navegar a Wikipedia
            $this->navegarWikipedia();

            // Esperar 1 segundo
            $this->esperar(1000);

            // Buscar el elemento con tagName "title" y obtener su atributo "innerText"
            $titulo = $this->buscarElementoPorTagName('title', 'innerText');

            // Mostrar el título
            $this->mostrarTitulo($titulo);

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
    $script = new Clase5_BuscarElementoPorTagName();
    $script->ejecutar();
    echo "\n========== PROCESO COMPLETADO ==========\n\n";
} catch (Exception $e) {
    echo "\n✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
?>