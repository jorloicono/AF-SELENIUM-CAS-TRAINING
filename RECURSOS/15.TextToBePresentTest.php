<?php
/**
 * Script de Automatización: Expected Conditions - Text To Be Present (SIN PHPUnit)
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Navega a https://the-internet.herokuapp.com/dynamic_loading/1
 * 3. Configura WebDriverWait con timeout de 10 segundos
 * 4. Hace clic en el botón "Start"
 * 5. Espera a que el elemento con id "finish" contenga el texto "Hello World!"
 * 6. Imprime el resultado
 * 7. Cierra el navegador
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Clase15_TextToBePresent
{
    private $driver;
    private $wait;
    private $chromeDriverProcess;

    private const TIMEOUT_SECONDS = 10;
    private const URL = "https://the-internet.herokuapp.com/dynamic_loading/1";
    private const TEXTO_ESPERADO = "Hello World!";

    public function ejecutar()
    {
        echo "\n===== INICIO DEL SCRIPT =====\n";

        try {
            $this->iniciarChromeDriver();
            $this->conectarDriver();

            $this->wait = new WebDriverWait($this->driver, self::TIMEOUT_SECONDS);

            echo "\n[3/5] Navegando a la página...\n";
            $this->driver->get(self::URL);
            echo "✓ Página cargada\n\n";

            echo "[4/5] Haciendo clic en el botón 'Start'...\n";
            $botonStart = $this->driver->findElement(
                WebDriverBy::cssSelector("#start button")
            );
            echo "✓ Botón encontrado\n";
            $botonStart->click();
            echo "✓ Clic realizado\n\n";

            echo "[5/5] Esperando a que el texto esté presente...\n";
            echo "Texto esperado: \"" . self::TEXTO_ESPERADO . "\"\n";
            echo "Elemento: id=\"finish\"\n\n";

            $textoPresente = $this->wait->until(
                WebDriverExpectedCondition::textToBePresentInElementLocated(
                    WebDriverBy::id("finish"),
                    self::TEXTO_ESPERADO
                )
            );

            if ($textoPresente) {
                echo "═════════════════════════════════════\n";
                echo "✓ RESULTADO: Texto encontrado correctamente\n";
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
        echo "[1/5] Iniciando ChromeDriver...\n";

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
        echo "[2/5] Conectando con WebDriver...\n";

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
$script = new Clase15_TextToBePresent();
$script->ejecutar();
