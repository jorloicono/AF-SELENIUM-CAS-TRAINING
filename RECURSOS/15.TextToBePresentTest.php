<?php
/**
 * Script de Automatización: Expected Conditions - Text To Be Present
 * Adaptación de Clase15_TextToBePresentTest (Java/JUnit) a PHP/PHPUnit
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Navega a https://the-internet.herokuapp.com/dynamic_loading/1
 * 3. Configura WebDriverWait con timeout de 10 segundos
 * 4. Hace clic en el botón "Start"
 * 5. Espera a que el elemento con id "finish" contenga el texto "Hello World!"
 * 6. Imprime que el texto está presente
 * 7. Cierra el navegador
 *
 * Concepto: textToBePresentInElementLocated() espera a que un elemento
 * específico contenga un texto determinado. Esto es útil para validar
 * que el contenido dinámico se ha cargado correctamente.
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Support\WebDriverWait;
use Facebook\WebDriver\ExpectedCondition;
use PHPUnit\Framework\TestCase;

class Clase15_TextToBePresentTest extends TestCase
{
    private $driver;
    private $wait;
    private $chromeDriverProcess;
    private const TIMEOUT_SECONDS = 10;
    private const URL = "https://the-internet.herokuapp.com/dynamic_loading/1";
    private const TEXTO_ESPERADO = "Hello World!";

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
        echo "[1/5] Iniciando ChromeDriver en puerto " . CHROMEDRIVER_PORT . "...\n";

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
        echo "[2/5] Conectando con Selenium WebDriver...\n";

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
     * Test: Text To Be Present In Element
     * 
     * Verifica que el elemento contenga un texto específico usando
     * textToBePresentInElementLocated() de ExpectedConditions.
     * Esta condición espera tanto a la presencia del elemento como
     * a que contenga el texto esperado.
     */
    public function testTextToBePresentInElement()
    {
        echo "[3/5] Ejecutando test: testTextToBePresentInElement()\n\n";

        try {
            echo "   Navegando a " . self::URL . "...\n";
            $this->driver->get(self::URL);
            echo "   ✓ Página cargada\n\n";

            echo "[4/5] Haciendo clic en el botón 'Start'...\n";
            $botonStart = $this->driver->findElement(WebDriverBy::cssSelector("#start button"));
            echo "   ✓ Botón encontrado\n";
            $botonStart->click();
            echo "   ✓ Clic realizado\n\n";

            echo "[5/5] Esperando a que el elemento 'finish' contenga el texto...\n";
            echo "   Buscando: \"" . self::TEXTO_ESPERADO . "\"\n";
            echo "   En elemento con id: \"finish\"\n\n";

            // Esperamos a que el texto esté presente en el elemento
            $textoEsta = $this->wait->until(
                ExpectedCondition::textToBePresentInElementLocated(
                    WebDriverBy::id("finish"),
                    self::TEXTO_ESPERADO
                )
            );

            echo "   ✓ Texto encontrado en el elemento\n\n";

            // Verificación de PHPUnit
            $this->assertTrue($textoEsta, "El texto '" . self::TEXTO_ESPERADO . "' debería estar presente");

            echo "   ═════════════════════════════════════════\n";
            echo "   RESULTADO DEL TEST:\n";
            echo "   ═════════════════════════════════════════\n";
            echo "   ✓ TEST PASADO: Texto '" . self::TEXTO_ESPERADO . "' presente\n";
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
