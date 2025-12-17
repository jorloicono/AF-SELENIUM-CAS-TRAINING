<?php
/**
 * Script de Automatización: Buscar elemento por XPath
 * Puerto de Java a PHP
 * 
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Navega a Wikipedia en español
 * 3. Busca elemento por XPath "//*[@id=\"main-tfa\"]//*[text() = ' Leer ']"
 * 4. Hace clic en el botón "Leer"
 * 5. Imprime el título de la nueva página
 * 6. Cierra el navegador
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Clase6_BuscarElementoPorXPath
{
    private $driver;
    private $chromeDriverProcess;

    public function __construct()
    {
        echo "\n========== BUSCAR ELEMENTO POR XPATH ==========\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }

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

        sleep(2);
        if (!is_resource($this->chromeDriverProcess)) {
            throw new Exception("No se pudo iniciar ChromeDriver");
        }
        echo "   ✓ ChromeDriver iniciado correctamente\n\n";
    }

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
            echo "   ✓ Conexión establecida\n\n";
        } catch (Exception $e) {
            $this->detenerChromeDriver();
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    private function navegarWikipedia()
    {
        echo "[3/5] Navegando a https://www.wikipedia.es...\n";
        $this->driver->get('https://www.wikipedia.es');
        echo "   ✓ Página cargada\n\n";
    }

    private function esperar($milisegundos)
    {
        $segundos = $milisegundos / 1000;
        echo "[ESPERA] Esperando $segundos segundos...\n";
        sleep($segundos);
    }

    private function buscarYHacerClicPorXPath()
    {
        echo "[4/5] Buscando elemento por XPath: //*[@id=\"main-tfa\"]//*[text() = ' Leer ']...\n";
        try {
            $xpath = "//*[@id=\"main-tfa\"]//*[text() = ' Leer ']";
            $leerButton = $this->driver->findElement(WebDriverBy::xpath($xpath));
            echo "   ✓ Elemento 'Leer' encontrado\n";

            echo "   Haciendo clic en 'Leer'...\n";
            $leerButton->click();
            echo "   ✓ Clic realizado\n\n";
        } catch (Exception $e) {
            throw new Exception("Error con XPath: " . $e->getMessage());
        }
    }

    private function mostrarTitulo()
    {
        echo "[5/5] Obteniendo título de la página...\n";
        $titulo = $this->driver->getTitle();
        echo "   ═══════════════════════════════════════\n";
        echo "   TÍTULO:\n";
        echo "   \"" . htmlspecialchars($titulo) . "\"\n";
        echo "   ═══════════════════════════════════════\n\n";
    }

    public function ejecutar()
    {
        try {
            $this->navegarWikipedia();
            $this->esperar(2000);
            $this->buscarYHacerClicPorXPath();
            $this->esperar(2000);
            $this->mostrarTitulo();
        } catch (Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n\n";
        } finally {
            $this->detener();
        }
    }

    private function detener()
    {
        echo "Finalizando...\n";
        if ($this->driver !== null) {
            $this->driver->quit();
            echo "   ✓ Navegador cerrado\n";
        }
        $this->detenerChromeDriver();
    }

    private function detenerChromeDriver()
    {
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
            echo "   ✓ ChromeDriver detenido\n";
        }
    }
}

try {
    $script = new Clase6_BuscarElementoPorXPath();
    $script->ejecutar();
    echo "========== PROCESO COMPLETADO ==========\n\n";
} catch (Exception $e) {
    echo "✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
?>