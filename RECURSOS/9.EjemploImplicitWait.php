<?php
/**
 * Script de Automatización: Ejemplo ImplicitWait
 * Adaptación de Clase9_EjemploImplicitWait (Java) a PHP
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Configura una espera implícita de 10 segundos
 * 3. Abre un HTML local (index_completo.html)
 * 4. Busca el botón con id "implicitWaitButton"
 * 5. Hace clic en el botón
 * 6. Espera 5 segundos (solo para verlo de forma detenida)
 * 7. Cierra el navegador
 *
 * Nota: Con la espera implícita configurada, Selenium esperará hasta
 * 10 segundos para encontrar elementos que no aparezcan de inmediato.
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;

class Clase9_EjemploImplicitWait
{
    private $driver;
    private $chromeDriverProcess;
    private const IMPLICIT_WAIT_SECONDS = 10;
    private const VISUAL_WAIT_MILLISECONDS = 5000;

    public function __construct()
    {
        echo "\n===== EJEMPLO IMPLICIT WAIT (PHP) =====\n\n";
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

    private function configurarImplicitWait()
    {
        echo "[3/5] Configurando espera implícita de " . self::IMPLICIT_WAIT_SECONDS . " segundos...\n";

        try {
            // En php-webdriver, la espera implícita se configura así:
            $this->driver->manage()->timeouts()->implicitlyWait(self::IMPLICIT_WAIT_SECONDS);

            echo "   ✓ Espera implícita configurada\n";
            echo "   Selenium esperará hasta " . self::IMPLICIT_WAIT_SECONDS . " segundos para encontrar elementos\n\n";
        } catch (\Exception $e) {
            throw new \Exception("Error configurando espera implícita: " . $e->getMessage());
        }
    }

    private function navegarPaginaLocal()
    {
        echo "[4/5] Navegando a la página local index_completo.html...\n";

        // Ajusta la ruta a tu entorno
        $rutaLocalWindows = 'C:\\Users\\Jorge\\Downloads\\jorloicono AF-TESTING-AUTOMATIZADO-SELENIUM-CUCUMBER master RECURSOS\\SELENIUM\\src\\ejemplosylabs\\resources\\index_completo.html';
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

    private function buscarYHacerClicEnBoton()
    {
        echo "[5/5] Buscando botón 'implicitWaitButton' y haciendo clic...\n";

        try {
            // Buscamos el botón por id
            echo "   Buscando elemento con id 'implicitWaitButton'...\n";
            $buttonImplicitWait = $this->driver->findElement(WebDriverBy::id('implicitWaitButton'));
            echo "   ✓ Botón 'implicitWaitButton' encontrado\n";

            // Hacemos clic
            echo "   Haciendo clic en el botón...\n";
            $buttonImplicitWait->click();
            echo "   ✓ Clic realizado\n\n";

            // Pausa visual (NO es un mecanismo de espera para Selenium)
            $this->esperar(self::VISUAL_WAIT_MILLISECONDS);

            echo "   ✓ Elemento se ha procesado correctamente\n";
            echo "   Puedes ver en el navegador que el mensaje se ha publicado correctamente\n\n";

        } catch (\Exception $e) {
            echo "✗ EXCEPCIÓN DURANTE LA BÚSQUEDA:\n";
            echo $e->getMessage() . "\n\n";
            throw $e;
        }
    }

    public function ejecutar()
    {
        try {
            $this->configurarImplicitWait();
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
    $script = new Clase9_EjemploImplicitWait();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
