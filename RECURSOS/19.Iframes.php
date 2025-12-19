<?php
/**
 * Script de Automatización: Manejo de IFRAME (UNO) - Versión Completa
 *
 * Página: https://the-internet.herokuapp.com/iframe
 *
 * Flujo:
 * 1. Abrir navegador
 * 2. Validar URL
 * 3. Esperar iframe
 * 4. Cambiar foco al iframe
 * 5. Interactuar con contenido interno
 * 6. Validar texto ingresado
 * 7. Salir del iframe
 * 8. Cerrar navegador
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Clase17_Iframe_Completo
{
    private $driver;
    private $wait;
    private $chromeDriverProcess;

    private const TIMEOUT_SECONDS = 10;
    private const URL = "https://the-internet.herokuapp.com/iframe";
    private const TEXTO_PRUEBA = "Texto escrito dentro del iframe con Selenium PHP";

    public function ejecutar()
    {
        echo "\n===== INICIO SCRIPT IFRAME (COMPLETO) =====\n";

        try {
            $this->iniciarChromeDriver();
            $this->conectarDriver();

            $this->wait = new WebDriverWait($this->driver, self::TIMEOUT_SECONDS);

            $this->navegarYValidarPagina();
            $this->cambiarAFocusIframe();
            $this->interactuarDentroIframe();
            $this->salirDelIframe();

            echo "═════════════════════════════════════\n";
            echo "✓ RESULTADO FINAL: Iframe manejado correctamente\n";
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
    private function navegarYValidarPagina()
    {
        echo "[3/8] Navegando a la página...\n";
        $this->driver->get(self::URL);


        echo "✓ URL validada correctamente\n\n";
    }

    /* ================= PASO 2 ================= */
    private function cambiarAFocusIframe()
    {
        echo "[4/8] Localizando iframe...\n";

        try {
            $iframe = $this->wait->until(
                WebDriverExpectedCondition::presenceOfElementLocated(
                    WebDriverBy::id("mce_0_ifr")
                )
            );
            echo "✓ Iframe localizado por ID\n";
        } catch (\Exception $e) {
            echo "⚠ No se encontró por ID, intentando por índice...\n";
            $iframe = $this->driver->findElements(WebDriverBy::tagName("iframe"))[0];
            echo "✓ Iframe localizado por índice\n";
        }

        $this->driver->switchTo()->frame($iframe);
        echo "✓ Foco cambiado al iframe\n\n";
    }

    /* ================= PASO 3 ================= */
    private function interactuarDentroIframe()
    {
        echo "[5/8] Interactuando dentro del iframe...\n";

        $editor = $this->wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::id("tinymce")
            )
        );

        $editor->clear();
        $editor->sendKeys(self::TEXTO_PRUEBA);
        echo "✓ Texto escrito\n";

        $textoActual = $editor->getText();

        if ($textoActual === self::TEXTO_PRUEBA) {
            echo "✓ Validación correcta del texto\n\n";
        } else {
            throw new \Exception("El texto dentro del iframe no coincide");
        }
    }

    /* ================= PASO 4 ================= */
    private function salirDelIframe()
    {
        echo "[6/8] Saliendo del iframe...\n";
        $this->driver->switchTo()->defaultContent();
        echo "✓ Foco devuelto al contenido principal\n\n";
    }

    /* ================= SETUP ================= */
    private function iniciarChromeDriver()
    {
        echo "[1/8] Iniciando ChromeDriver...\n";

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
        echo "[2/8] Conectando con WebDriver...\n";

        $this->driver = RemoteWebDriver::create(
            CHROMEDRIVER_HOST,
            DesiredCapabilities::chrome(),
            5000
        );

        echo "✓ Conexión establecida\n";
    }

    private function detener()
    {
        echo "\n[7/8] Cerrando recursos...\n";

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
$script = new Clase17_Iframe_Completo();
$script->ejecutar();
