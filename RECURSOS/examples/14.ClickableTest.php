<?php
/**
 * Script de Automatización: Expected Conditions - Clickable (SIN PHPUnit)
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Navega a https://the-internet.herokuapp.com/dynamic_loading/1
 * 3. Configura WebDriverWait con timeout de 10 segundos
 * 4. Espera a que el elemento sea CLICKEABLE
 * 5. Hace clic en el botón
 * 6. Imprime el resultado
 * 7. Cierra el navegador
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Support\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Clase14_Clickable
{
    private $driver;
    private $wait;
    private $chromeDriverProcess;

    private const TIMEOUT_SECONDS = 10;
    private const URL = "https://the-internet.herokuapp.com/dynamic_loading/1";

    public function ejecutar()
    {
        echo "\n===== INICIO DEL SCRIPT =====\n";

        try {
            $this->iniciarChromeDriver();
            $this->conectarDriver();

            $this->wait = new WebDriverWait($this->driver, self::TIMEOUT_SECONDS);

            echo "\n[3/4] Navegando a la página...\n";
            $this->driver->get(self::URL);
            echo "✓ Página cargada\n\n";

            echo "Esperando a que el botón '#start button' sea CLICKEABLE...\n";
            echo "Condiciones verificadas:\n";
            echo "- Presente en el DOM\n";
            echo "- Visible\n";
            echo "- Habilitado\n";
            echo "- No superpuesto\n\n";

            $boton = $this->wait->until(
                WebDriverExpectedCondition::elementToBeClickable(
                    WebDriverBy::cssSelector("#start button")
                )
            );

            if ($boton !== null) {
                echo "✓ Botón clickeable\n";
                echo "Haciendo clic...\n";
                $boton->click();
                echo "✓ Clic ejecutado\n\n";

                echo "═════════════════════════════════════\n";
                echo "✓ RESULTADO: Botón clickeable y clicado\n";
                echo "═════════════════════════════════════\n\n";
            }

        } catch (\Exception $e) {
            echo "✗ ERROR EN EL SCRIPT\n";
            echo $e->getMessage() . "\n\n";
        } finally {
            $this->detener();
            echo "===== FIN DEL SCRIPT =====\n\n";
        }
    }

    private function iniciarChromeDriver()
    {
        echo "[1/4] Iniciando ChromeDriver...\n";

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

        echo "✓ ChromeDriver iniciado\n";
    }

    private function conectarDriver()
    {
        echo "[2/4] Conectando con WebDriver...\n";

        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create(
            CHROMEDRIVER_HOST,
            $capabilities,
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
$script = new Clase14_Clickable();
$script->ejecutar();
