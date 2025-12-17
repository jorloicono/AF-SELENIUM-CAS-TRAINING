<?php
/**
 * Script de Automatización: Expected Conditions - Visibility
 * Adaptación de Clase12_ExpectedConditions1 (Java) a PHP
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Navega a https://the-internet.herokuapp.com/dynamic_loading/1
 * 3. Configura WebDriverWait con timeout de 10 segundos
 * 4. Hace clic en el botón "Start"
 * 5. Espera a que el elemento con id "finish" sea visible
 * 6. Imprime el texto del elemento
 * 7. Cierra el navegador
 *
 * Concepto: ExpectedConditions permite esperar a condiciones específicas
 * sin escribir lógica personalizada de polling.
 * visibilityOfElementLocated() espera a que el elemento sea visible en la página.
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Support\WebDriverWait;
use Facebook\WebDriver\ExpectedCondition;

class Clase12_ExpectedConditions1
{
    private $driver;
    private $chromeDriverProcess;
    private const TIMEOUT_SECONDS = 10;
    private const URL = "https://the-internet.herokuapp.com/dynamic_loading/1";

    public function __construct()
    {
        echo "\n===== EXPECTED CONDITIONS - VISIBILITY (PHP) =====\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
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
        } catch (\Exception $e) {
            $this->detenerChromeDriver();
            throw new \Exception("Error de conexión: " . $e->getMessage());
        }
    }

    private function navegarPagina()
    {
        echo "[3/5] Navegando a " . self::URL . "...\n";

        try {
            $this->driver->get(self::URL);
            echo "   ✓ Página cargada\n\n";
        } catch (\Exception $e) {
            throw new \Exception("Error navegando a la página: " . $e->getMessage());
        }
    }

    private function configurarWebDriverWait()
    {
        echo "[4/5] Configurando WebDriverWait...\n";
        echo "   Timeout: " . self::TIMEOUT_SECONDS . " segundos\n";
        echo "   Condition: visibilityOfElementLocated(By.id('finish'))\n\n";

        $wait = new WebDriverWait($this->driver, self::TIMEOUT_SECONDS);
        return $wait;
    }

    private function hacerClicYEsperarMensaje()
    {
        echo "[5/5] Haciendo clic en el botón 'Start' y esperando al elemento...\n";

        try {
            // Hacemos clic en el botón Start
            echo "   Buscando botón con selector CSS '#start button'...\n";
            $botonStart = $this->driver->findElement(WebDriverBy::cssSelector("#start button"));
            echo "   ✓ Botón encontrado\n";

            echo "   Haciendo clic...\n";
            $botonStart->click();
            echo "   ✓ Clic realizado\n\n";

            // Configuramos WebDriverWait
            $wait = $this->configurarWebDriverWait();

            // Esperamos a que el elemento con id "finish" sea visible
            echo "   Esperando a que el elemento 'finish' sea visible...\n";
            $mensaje = $wait->until(
                ExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("finish"))
            );

            echo "   ✓ Elemento visible encontrado\n\n";

            // Obtenemos el texto del elemento
            $textoMensaje = $mensaje->getText();

            echo "   ═════════════════════════════════════════\n";
            echo "   RESULTADO:\n";
            echo "   ═════════════════════════════════════════\n";
            echo "   Texto mostrado: \"" . htmlspecialchars($textoMensaje) . "\"\n";
            echo "   ═════════════════════════════════════════\n\n";

        } catch (\Exception $e) {
            echo "✗ EXCEPCIÓN:\n";
            echo $e->getMessage() . "\n\n";
            throw $e;
        }
    }

    public function ejecutar()
    {
        try {
            $this->navegarPagina();
            $this->hacerClicYEsperarMensaje();
        } catch (\Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n\n";
        } finally {
            $this->detener();
        }

        echo "===== PROCESO COMPLETADO =====\n\n";
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
    $script = new Clase12_ExpectedConditions1();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
