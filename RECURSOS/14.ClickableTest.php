<?php
/**
 * Script de Automatización: Expected Conditions - Clickable
 * Adaptación de Clase14_ClickableTest (Java/JUnit) a PHP/PHPUnit
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Navega a https://the-internet.herokuapp.com/dynamic_loading/1
 * 3. Configura WebDriverWait con timeout de 10 segundos
 * 4. Espera a que el elemento sea CLICKEABLE (visible, enabled, e interactuable)
 * 5. Hace clic en el botón
 * 6. Imprime que el botón fue clickeable y clicado
 * 7. Cierra el navegador
 *
 * Concepto: elementToBeClickable() es una condición compuesta que verifica:
 * - Que el elemento esté presente en el DOM
 * - Que sea visible
 * - Que esté habilitado
 * - Que no haya otros elementos superpuestos
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Support\WebDriverWait;
use Facebook\WebDriver\ExpectedCondition;
use PHPUnit\Framework\TestCase;

class Clase14_ClickableTest extends TestCase
{
    private $driver;
    private $wait;
    private $chromeDriverProcess;
    private const TIMEOUT_SECONDS = 10;
    private const URL = "https://the-internet.herokuapp.com/dynamic_loading/1";

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
        echo "[1/4] Iniciando ChromeDriver en puerto " . CHROMEDRIVER_PORT . "...\n";

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
        echo "[2/4] Conectando con Selenium WebDriver...\n";

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
     * Test: Element To Be Clickable
     * 
     * Verifica que el elemento sea CLICKEABLE usando elementToBeClickable()
     * de ExpectedConditions. Esta es una condición compuesta que verifica
     * múltiples estados del elemento antes de permitir la interacción.
     */
    public function testElementToBeClickable()
    {
        echo "[3/4] Ejecutando test: testElementToBeClickable()\n\n";

        try {
            echo "   Navegando a " . self::URL . "...\n";
            $this->driver->get(self::URL);
            echo "   ✓ Página cargada\n\n";

            echo "   Esperando a que el elemento '#start button' sea CLICKEABLE...\n";
            echo "   Condiciones verificadas:\n";
            echo "   - Elemento presente en el DOM\n";
            echo "   - Elemento visible en pantalla\n";
            echo "   - Elemento habilitado (no deshabilitado)\n";
            echo "   - No hay otros elementos superpuestos\n\n";

            $boton = $this->wait->until(
                ExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector("#start button"))
            );

            echo "   ✓ Elemento es clickeable\n\n";

            // Verificación de PHPUnit
            $this->assertNotNull($boton, "El botón debería estar clickeable");

            echo "   Haciendo clic en el botón...\n";
            $boton->click();
            echo "   ✓ Clic ejecutado\n\n";

            echo "   ═════════════════════════════════════════\n";
            echo "   RESULTADO DEL TEST:\n";
            echo "   ═════════════════════════════════════════\n";
            echo "   ✓ TEST PASADO: Botón clickeable y clicado\n";
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
