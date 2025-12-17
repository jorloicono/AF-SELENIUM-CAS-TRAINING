<?php
/**
 * Script de Automatización: Expected Conditions - Alert Is Present
 * Adaptación de Clase16_AlertTest (Java/JUnit) a PHP/PHPUnit
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Navega a https://the-internet.herokuapp.com/javascript_alerts
 * 3. Configura WebDriverWait con timeout de 10 segundos
 * 4. Hace clic en el botón "Click for JS Alert"
 * 5. Espera a que aparezca una alerta usando alertIsPresent()
 * 6. Cambia el foco al alerta (switchTo().alert())
 * 7. Imprime el texto de la alerta
 * 8. Acepta la alerta (alert.accept())
 * 9. Cierra el navegador
 *
 * Concepto: alertIsPresent() espera a que una alerta JavaScript aparezca.
 * Una vez presente, se puede interactuar con ella usando switchTo().alert().
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Support\WebDriverWait;
use Facebook\WebDriver\ExpectedCondition;
use PHPUnit\Framework\TestCase;

class Clase16_AlertTest extends TestCase
{
    private $driver;
    private $wait;
    private $chromeDriverProcess;
    private const TIMEOUT_SECONDS = 10;
    private const URL = "https://the-internet.herokuapp.com/javascript_alerts";

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
     * Test: Alert Is Present
     * 
     * Verifica que una alerta JavaScript aparezca usando alertIsPresent()
     * de ExpectedConditions. Una vez presente, se interactúa con la alerta.
     */
    public function testAlertIsPresent()
    {
        echo "[3/5] Ejecutando test: testAlertIsPresent()\n\n";

        try {
            echo "   Navegando a " . self::URL . "...\n";
            $this->driver->get(self::URL);
            echo "   ✓ Página cargada\n\n";

            echo "[4/5] Haciendo clic en el botón 'Click for JS Alert'...\n";
            $botonAlert = $this->driver->findElement(
                WebDriverBy::xpath("//button[text()='Click for JS Alert']")
            );
            echo "   ✓ Botón encontrado\n";
            $botonAlert->click();
            echo "   ✓ Clic realizado\n\n";

            echo "[5/5] Esperando a que aparezca la alerta...\n";
            $this->wait->until(ExpectedCondition::alertIsPresent());
            echo "   ✓ Alerta presente\n\n";

            // Cambiamos el foco al alerta
            echo "   Cambiando foco a la alerta...\n";
            $alert = $this->driver->switchTo()->alert();
            echo "   ✓ Foco en alerta\n\n";

            // Obtenemos el texto de la alerta
            $textoAlerta = $alert->getText();
            echo "   ═════════════════════════════════════════\n";
            echo "   CONTENIDO DE LA ALERTA:\n";
            echo "   ═════════════════════════════════════════\n";
            echo "   Texto alerta: \"" . htmlspecialchars($textoAlerta) . "\"\n";
            echo "   ═════════════════════════════════════════\n\n";

            // Verificación de PHPUnit
            $this->assertNotEmpty($textoAlerta, "La alerta debería contener texto");

            // Aceptamos la alerta
            echo "   Aceptando la alerta (alert.accept())...\n";
            $alert->accept();
            echo "   ✓ Alerta aceptada\n\n";

            echo "   ═════════════════════════════════════════\n";
            echo "   RESULTADO DEL TEST:\n";
            echo "   ═════════════════════════════════════════\n";
            echo "   ✓ TEST PASADO: Alerta detectada e interactuada\n";
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
