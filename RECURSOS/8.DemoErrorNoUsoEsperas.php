<?php
/**
 * Script de Automatización: Demo Error por No Usar Esperas
 * Adaptación de Clase8_DemoErrorNoUsoEsperas (Java) a PHP
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Abre un HTML local (index_completo.html)
 * 3. Busca el botón con id "implicitWaitButton"
 * 4. Hace clic en el botón
 * 5. Espera 5 segundos (solo para verlo, NO para esperar elementos)
 * 6. Cierra el navegador
 *
 * Nota: Igual que en el ejemplo Java, NO se usan esperas explícitas/implícitas
 * para el segundo elemento que debería aparecer tras el clic, por lo que
 * un acceso inmediato a ese elemento provocaría error.
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Clase8_DemoErrorNoUsoEsperas
{
    private $driver;
    private $chromeDriverProcess;

    public function __construct()
    {
        echo "\n===== DEMO ERROR POR NO USAR ESPERAS (PHP) =====\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
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

        echo "   ✓ ChromeDriver iniciado correctamente\n\n";
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
            echo "   ✓ Conexión establecida\n\n";
        } catch (\Exception $e) {
            $this->detenerChromeDriver();
            throw new \Exception("Error de conexión: " . $e->getMessage());
        }
    }

    private function navegarPaginaLocal()
    {
        echo "[3/4] Navegando a la página local index_completo.html...\n";

        // Ajusta la ruta a tu entorno. Ejemplo usando file://:
        // Ruta equivalente al ejemplo Java:
        // C:\Users\Jorge\Desktop\AF-TESTING-AUTOMATIZADO-SELENIUM-CUCUMBER\RECURSOS\src\ejemplosylabs\resources\index_completo.html
        $rutaLocalWindows = 'C:\Users\Jorge\Desktop\AF-SELENIUM-CAS-TRAINING\RECURSOS\index_completo.html';
        $url = 'file:///' . str_replace('\\', '/', $rutaLocalWindows);

        echo "   URL: $url\n";
        $this->driver->get($url);
        echo "   ✓ Página cargada\n\n";
    }

    private function esperar($milisegundos)
    {
        $segundos = $milisegundos / 1000;
        echo "[ESPERA] Esperando $segundos segundos...\n";
        sleep($segundos);
    }

    private function demoErrorPorNoUsarEsperas()
    {
        echo "[4/4] Buscando y haciendo clic en el botón 'implicitWaitButton'...\n";

        try {
            // Buscamos el botón por id (como en el ejemplo Java)
            $buttonImplicitWait = $this->driver->findElement(WebDriverBy::id('implicitWaitButton'));
            echo "   ✓ Botón 'implicitWaitButton' encontrado\n";

            // Hacemos clic
            echo "   Haciendo clic en el botón...\n";
            $buttonImplicitWait->click();
            echo "   ✓ Clic realizado\n\n";

            // Pausa visual (NO es un mecanismo de espera para Selenium)
            $this->esperar(5000);

            // Aquí podrías intentar buscar un elemento que aparece tras el clic
            // sin usar esperas explícitas ni implícitas para forzar el error,
            // imitanto el comentario del código Java:
            //
            // $otroElemento = $this->driver->findElement(WebDriverBy::id('otroElementoQueTarda'));
            //
            // Pero lo dejamos comentado para que el script no falle siempre.

        } catch (\Exception $e) {
            // Mostramos el error tal y como hace el ejemplo Java con e.printStackTrace()
            echo "✗ EXCEPCIÓN DURANTE LA DEMO:\n";
            echo $e->getMessage() . "\n\n";
        }
    }

    public function ejecutar()
    {
        try {
            $this->navegarPaginaLocal();
            $this->demoErrorPorNoUsarEsperas();
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
    $script = new Clase8_DemoErrorNoUsoEsperas();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
