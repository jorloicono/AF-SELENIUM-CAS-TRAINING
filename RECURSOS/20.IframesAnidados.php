<?php
/**
 * Script de Automatización: IFRAME ANIDADO (SIN PHPUnit)
 *
 * Página:
 * https://the-internet.herokuapp.com/nested_frames
 *
 * Estructura:
 * ┌ frame-top
 * │   ├ frame-left
 * │   ├ frame-middle
 * │   └ frame-right
 * └ frame-bottom
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Clase18_Iframe_Anidado
{
    private $driver;
    private $wait;
    private $chromeDriverProcess;

    private const TIMEOUT_SECONDS = 10;
    private const URL = "https://the-internet.herokuapp.com/nested_frames";

    public function ejecutar()
    {
        echo "\n===== INICIO SCRIPT IFRAME ANIDADO =====\n";

        try {
            $this->iniciarChromeDriver();
            $this->conectarDriver();

            $this->wait = new WebDriverWait($this->driver, self::TIMEOUT_SECONDS);

            $this->navegarPagina();
            $this->frameTop();
            $this->frameMiddle();
            $this->salirTotal();

            echo "═════════════════════════════════════\n";
            echo "✓ RESULTADO FINAL: Iframe anidado manejado correctamente\n";
            echo "═════════════════════════════════════\n\n";

        } catch (\Exception $e) {
            echo "✗ ERROR DETECTADO\n";
            echo "Mensaje: " . $e->getMessage() . "\n\n";
        } finally {
            $this->detener();
            echo "===== FIN DEL SCRIPT =====\n\n";
        }
    }

    /* ================= PASO 1 ================= */
    private function navegarPagina()
    {
        echo "[3/7] Navegando a la página...\n";
        $this->driver->get(self::URL);

        echo "✓ Página cargada y validada\n\n";
    }

    /* ================= PASO 2 ================= */
    private function frameTop()
    {
        echo "[4/7] Entrando al iframe PADRE (frame-top)...\n";

        $this->driver->switchTo()->frame("frame-top");
        echo "✓ Dentro de frame-top\n\n";
    }

    /* ================= PASO 3 ================= */
    private function frameMiddle()
    {
        echo "[5/7] Entrando al iframe HIJO (frame-middle)...\n";

        $this->driver->switchTo()->frame("frame-middle");
        echo "✓ Dentro de frame-middle\n";

        $contenido = $this->wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::id("content")
            )
        );

        $texto = $contenido->getText();
        echo "Texto encontrado: \"$texto\"\n";

        if ($texto === "MIDDLE") {
            echo "✓ Validación correcta del contenido\n\n";
        } else {
            throw new \Exception("Texto inesperado dentro del iframe anidado");
        }
    }

    /* ================= PASO 4 ================= */
    private function salirTotal()
    {
        echo "[6/7] Saliendo al documento principal...\n";
        $this->driver->switchTo()->defaultContent();
        echo "✓ Foco restaurado correctamente\n\n";
    }

    /* ================= SETUP ================= */
    private function iniciarChromeDriver()
    {
        echo "[1/7] Iniciando ChromeDriver...\n";

        $this->chromeDriverProcess = proc_open(
            CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT,
            [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["pipe", "w"],
            ],
            $pipes
        );

        sleep(2);

        if (!is_resource($this->chromeDriverProcess)) {
            throw new \Exception("No se pudo iniciar ChromeDriver");
        }

        echo "✓ ChromeDriver iniciado\n";
    }

    private function conectarDriver()
    {
        echo "[2/7] Conectando con WebDriver...\n";

        $this->driver = RemoteWebDriver::create(
            CHROMEDRIVER_HOST,
            DesiredCapabilities::chrome(),
            5000
        );

        echo "✓ Conexión establecida\n";
    }

    private function detener()
    {
        echo "\n[7/7] Cerrando recursos...\n";

        if ($this->driver !== null) {
            $this->driver->quit();
            echo "✓ Navegador cerrado\n";
        }

        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
            echo "✓ ChromeDriver detenido\n";
        }
    }
}

/* ===== EJECUCIÓN ===== */
$script = new Clase18_Iframe_Anidado();
$script->ejecutar();
