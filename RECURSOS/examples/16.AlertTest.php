<?php
/**
 * Script de Automatización: Manejo de los 3 tipos de Alertas JS (SIN PHPUnit)
 *
 * Tipos:
 * 1. JS Alert       → aceptar
 * 2. JS Confirm     → aceptar y cancelar
 * 3. JS Prompt      → enviar texto y aceptar
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Clase16_Alert
{
    private $driver;
    private $wait;
    private $chromeDriverProcess;

    private const TIMEOUT_SECONDS = 10;
    private const URL = "https://the-internet.herokuapp.com/javascript_alerts";

    public function ejecutar()
    {
        echo "\n===== INICIO DEL SCRIPT =====\n";

        try {
            $this->iniciarChromeDriver();
            $this->conectarDriver();

            $this->wait = new WebDriverWait($this->driver, self::TIMEOUT_SECONDS);

            echo "\n[3/6] Navegando a la página...\n";
            $this->driver->get(self::URL);
            echo "✓ Página cargada\n\n";

            $this->manejarJsAlert();
            $this->manejarJsConfirm();
            $this->manejarJsPrompt();

            echo "═════════════════════════════════════\n";
            echo "✓ RESULTADO FINAL: Todas las alertas manejadas\n";
            echo "═════════════════════════════════════\n\n";

        } catch (\Exception $e) {
            echo "✗ ERROR EN EL SCRIPT\n";
            echo $e->getMessage() . "\n\n";
        } finally {
            $this->detener();
            echo "===== FIN DEL SCRIPT =====\n\n";
        }
    }

    /* ================= ALERTA SIMPLE ================= */
    private function manejarJsAlert()
    {
        echo "[4/6] JS ALERT (Aceptar)\n";

        $this->driver->findElement(
            WebDriverBy::xpath("//button[text()='Click for JS Alert']")
        )->click();

        $this->wait->until(WebDriverExpectedCondition::alertIsPresent());
        $alert = $this->driver->switchTo()->alert();

        echo "Texto: " . $alert->getText() . "\n";
        $alert->accept();

        echo "✓ JS Alert aceptada\n\n";
    }

    /* ================= CONFIRM ================= */
    private function manejarJsConfirm()
    {
        echo "[5/6] JS CONFIRM (Aceptar y Cancelar)\n";

        $boton = $this->driver->findElement(
            WebDriverBy::xpath("//button[text()='Click for JS Confirm']")
        );

        // ACEPTAR
        $boton->click();
        $this->wait->until(WebDriverExpectedCondition::alertIsPresent());
        $alert = $this->driver->switchTo()->alert();
        echo "Texto: " . $alert->getText() . "\n";
        $alert->accept();
        echo "✓ Confirm aceptado\n";

        // CANCELAR
        $boton->click();
        $this->wait->until(WebDriverExpectedCondition::alertIsPresent());
        $alert = $this->driver->switchTo()->alert();
        $alert->dismiss();
        echo "✓ Confirm cancelado\n\n";
    }

    /* ================= PROMPT ================= */
    private function manejarJsPrompt()
    {
        echo "[6/6] JS PROMPT (Enviar texto)\n";

        $this->driver->findElement(
            WebDriverBy::xpath("//button[text()='Click for JS Prompt']")
        )->click();

        $this->wait->until(WebDriverExpectedCondition::alertIsPresent());
        $alert = $this->driver->switchTo()->alert();

        echo "Texto: " . $alert->getText() . "\n";

        $texto = "Hola Selenium PHP";
        $alert->sendKeys($texto);
        $alert->accept();

        echo "✓ Texto enviado: \"$texto\"\n\n";
    }

    /* ================= SETUP ================= */
    private function iniciarChromeDriver()
    {
        echo "[1/6] Iniciando ChromeDriver...\n";

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
        echo "[2/6] Conectando con WebDriver...\n";

        $this->driver = RemoteWebDriver::create(
            CHROMEDRIVER_HOST,
            DesiredCapabilities::chrome(),
            5000
        );

        echo "✓ Conexión establecida\n";
    }

    private function detener()
    {
        echo "\nCerrando recursos...\n";

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
$script = new Clase16_Alert();
$script->ejecutar();
