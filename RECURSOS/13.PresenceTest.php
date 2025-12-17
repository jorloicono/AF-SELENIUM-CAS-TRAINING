<?php
/**
 * Script de Automatización: Expected Conditions - Presence
 * Adaptación de Clase13_PresenceTest (Java/JUnit) a PHP/PHPUnit
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Navega a https://the-internet.herokuapp.com/dynamic_loading/2
 * 3. Configura WebDriverWait con timeout de 10 segundos
 * 4. Espera a que el elemento con id "finish" esté PRESENTE en el DOM
 * 5. Imprime que el elemento está presente
 * 6. Cierra el navegador
 *
 * Concepto: presenceOfElementLocated() espera a que el elemento exista
 * en el DOM, pero NO necesariamente que sea visible en la pantalla.
 * Esto es diferente de visibilityOfElementLocated().
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Support\WebDriverWait;
use Facebook\WebDriver\ExpectedCondition;
use PHPUnit\Framework\TestCase;

class Clase13_PresenceTest extends TestCase
{
    private $driver;
    private $wait;
    private $chromeDriverProcess;
    private const TIMEOUT_SECONDS = 10;
    private const URL = "https://the-internet.herokuapp.com/dynamic_loading/2";

    protected function setUp(): void
    {
        echo "\n===== SETUP: Inicializando WebDriver =====\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
        $this->wait = new WebDriverWait($this->driver, self::TIMEOUT_SECONDS);
        echo "===== SETUP COMPLETADO =====\n\n";
    }

    protected function tearDown(): void
    {
        echo "\n===== TEARDOWN: Limpiando recursos =====\n";
        $this->detener();
        echo "===== TEARDOWN COMPLETADO =====\n\n";
    }

    private function iniciarChromeDriver()
    {
        echo "[1/3] Iniciando ChromeDriver en puerto " . CHROMEDRIVER_PORT . "...\n";

        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"],
        ];

        $this->chromeDriverProcess = proc_open(
            CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT,
            $descriptorspec,
            $pipes
        );

        sleep(2);

        if (!is_resource($this->chromeDriverProcess)) {
            throw new \Exception("No se pudo iniciar ChromeDriver");
        }

        echo "   ✓ ChromeDriver iniciado correctamente\n";
    }

    private function conectarDriver()
    {
        echo "[2/3] Conectando con Selenium WebDriver...\n";

        try {
            $capabilities = DesiredCapabilities::chrome();
            $this->driver = RemoteWebDriver::create(
                CHROMEDRIVER_HOST,
                $capabilities,
                5000
            );
            echo "   ✓ Conexión establecida\n";
        } catch (\Exception $e) {
            $this->detenerChromeDriver();
            throw new \Exception("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * Test: Presence of Element
     * 
     * Verifica que el elemento con id "finish" esté PRESENTE en el DOM
     * usando presenceOfElementLocated() de ExpectedConditions.
     */
    public function testPresenceOfElement()
    {
        echo "[3/3] Ejecutando test: testPresenceOfElement()\n\n";

        try {
            echo "   Navegando a " . self::URL . "...\n";
            $this->driver->get(self::URL);
            echo "   ✓ Página cargada\n\n";

            echo "   Esperando a que el elemento 'finish' esté PRESENTE en el DOM...\n";
            echo "   (Nota: presenceOfElementLocated NO requiere que sea visible)\n\n";

            $elemento = $this->wait->until(
                ExpectedCondition::presenceOfElementLocated(WebDriverBy::id("finish"))
            );

            echo "   ✓ Elemento presente en DOM\n\n";

            // Verificación de PHPUnit
            $this->assertNotNull($elemento, "El elemento 'finish' debería estar presente");

            echo "   ═════════════════════════════════════════\n";
            echo "   RESULTADO DEL TEST:\n";
            echo "   ═════════════════════════════════════════\n";
            echo "   ✓ TEST PASADO: Elemento presente en DOM\n";
            echo "   ═════════════════════════════════════════\n\n";

        } catch (\Exception $e) {
            echo "✗ EXCEPCIÓN EN TEST:\n";
            echo $e->getMessage() . "\n\n";
            $this->fail("Test falló: " . $e->getMessage());
        }
    }

    private function detener()
    {
        echo "   Cerrando navegador...\n";
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
