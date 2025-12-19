<?php
/**
 * Script de Automatización: Ejemplo Explicit Wait con FluentWait
 * Adaptación de Clase11_ExplicitWait (Java) a PHP
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Abre un HTML local (index_completo.html)
 * 3. Configura un FluentWait genérico con:
 *    - Timeout: 10 segundos
 *    - Polling: cada 2 segundos
 *    - Ignora excepciones
 * 4. Busca el botón con id "explicitWaitButton" usando FluentWait
 * 5. Hace clic en el botón (ahora sí funciona porque el botón está habilitado)
 * 6. Espera 5 segundos (solo para verlo de forma detenida)
 * 7. Cierra el navegador
 *
 * Ventaja: FluentWait es más flexible que WebDriverWait y permite
 * definir lógica personalizada de espera con Function/Callable.
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Support\Events\EventFiringWebDriver;
use Facebook\WebDriver\WebDriverWait;

class Clase11_ExplicitWait
{
    private $driver;
    private $chromeDriverProcess;
    private const TIMEOUT_SECONDS = 10;
    private const POLLING_SECONDS = 2;
    private const VISUAL_WAIT_MILLISECONDS = 5000;

    public function __construct()
    {
        echo "\n===== EJEMPLO EXPLICIT WAIT CON FLUENT WAIT (PHP) =====\n\n";
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

    private function navegarPaginaLocal()
    {
        echo "[3/5] Navegando a la página local index_completo.html...\n";

        $rutaLocalWindows = 'C:\Users\Jorge\Desktop\AF-SELENIUM-CAS-TRAINING\RECURSOS\index_completo.html';
        $url = 'file:///' . str_replace('\\', '/', $rutaLocalWindows);

        echo "   URL: $url\n";
        $this->driver->get($url);
        echo "   ✓ Página cargada\n\n";
    }

    private function esperar($milisegundos)
    {
        $segundos = $milisegundos / 1000;
        echo "[ESPERA VISUAL] Esperando $segundos segundos para ver el resultado detenidamente...\n";
        sleep($segundos);
    }

    private function crearFluentWait()
    {
        echo "[4/5] Configurando FluentWait genérico...\n";
        echo "   Timeout: " . self::TIMEOUT_SECONDS . " segundos\n";
        echo "   Polling: cada " . self::POLLING_SECONDS . " segundos\n";
        echo "   Ignorando excepciones\n\n";

        // En php-webdriver, usamos WebDriverWait que es similar a FluentWait
        // WebDriverWait es esencialmente un FluentWait configurado para Selenium
        $wait = new WebDriverWait(
            $this->driver,
            self::TIMEOUT_SECONDS,
            self::POLLING_SECONDS * 1000  // polling en milisegundos
        );

        return $wait;
    }

    private function buscarYHacerClicEnBoton()
    {
        echo "[5/5] Buscando botón 'explicitWaitButton' usando FluentWait y haciendo clic...\n\n";

        try {
            $wait = $this->crearFluentWait();

            echo "   Esperando a encontrar el elemento con id 'explicitWaitButton'...\n";

            // En php-webdriver, usamos una callable/Function similar a Java
            $buttonExplicitWait = $wait->until(
                function ($driver) {
                    echo "   [Polling] Intentando encontrar el elemento...\n";
                    try {
                        $element = $driver->findElement(WebDriverBy::id('explicitWaitButton'));
                        echo "   [Polling] ✓ Elemento encontrado y accesible\n";
                        return $element;
                    } catch (\Exception $e) {
                        echo "   [Polling] ✗ Elemento no disponible aún, reintentando...\n";
                        return null;
                    }
                }
            );

            echo "   ✓ Botón 'explicitWaitButton' encontrado exitosamente\n\n";

            echo "   Haciendo clic en el botón...\n";
            $buttonExplicitWait->click();
            echo "   ✓ Clic realizado\n\n";

            $this->esperar(self::VISUAL_WAIT_MILLISECONDS);

            echo "   ✓ ÉXITO:\n";
            echo "   ═════════════════════════════════════════\n";
            echo "   ¡Ahora SÍ aparece el resultado en el navegador!\n";
            echo "   El mensaje se ha publicado correctamente.\n";
            echo "   \n";
            echo "   La diferencia con Implicit Wait:\n";
            echo "   - Implicit: Solo espera presencia del elemento\n";
            echo "   - Explicit: Espera condiciones específicas (con polling)\n";
            echo "   ═════════════════════════════════════════\n\n";

        } catch (\Exception $e) {
            echo "✗ EXCEPCIÓN DURANTE LA BÚSQUEDA:\n";
            echo $e->getMessage() . "\n\n";
            throw $e;
        }
    }

    public function ejecutar()
    {
        try {
            $this->navegarPaginaLocal();
            $this->buscarYHacerClicEnBoton();
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
    $script = new Clase11_ExplicitWait();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
