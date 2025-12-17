<?php
/**
 * Script de Automatización: Ejemplo Implicit Wait Ineficiente (No Click)
 * Adaptación de Clase10_EjemploImplicitIneficienteNonClick (Java) a PHP
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Configura una espera implícita de 10 segundos
 * 3. Abre un HTML local (index_completo.html)
 * 4. Busca el botón con id "explicitWaitButton"
 * 5. Hace clic en el botón (pero el botón NO estaba habilitado)
 * 6. Espera 5 segundos (solo para verlo de forma detenida)
 * 7. Cierra el navegador
 *
 * Problema demostrado: La espera implícita busca el elemento, pero NO espera
 * a que el elemento esté en un estado habilitado para interactuar.
 * Por eso el clic no produce efecto, aunque el elemento exista.
 * Esto se resuelve con Explicit Wait en la siguiente clase.
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Clase10_EjemploImplicitIneficienteNonClick
{
    private $driver;
    private $chromeDriverProcess;
    private const IMPLICIT_WAIT_SECONDS = 10;
    private const VISUAL_WAIT_MILLISECONDS = 5000;

    public function __construct()
    {
        echo "\n===== EJEMPLO IMPLICIT WAIT INEFICIENTE (No Click) (PHP) =====\n\n";
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
            $this->driver->manage()->timeouts()->implicitlyWait(self::IMPLICIT_WAIT_SECONDS);

            echo "   ✓ Espera implícita configurada\n";
            echo "   ⚠ NOTA: La espera implícita solo espera a que el elemento EXISTA,\n";
            echo "           no a que esté HABILITADO para interactuar\n\n";
        } catch (\Exception $e) {
            throw new \Exception("Error configurando espera implícita: " . $e->getMessage());
        }
    }

    private function navegarPaginaLocal()
    {
        echo "[4/5] Navegando a la página local index_completo.html...\n";

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
        echo "[5/5] Buscando botón 'explicitWaitButton' y haciendo clic...\n";

        try {
            echo "   Buscando elemento con id 'explicitWaitButton'...\n";
            $buttonExplicitWait = $this->driver->findElement(WebDriverBy::id('explicitWaitButton'));
            echo "   ✓ Botón 'explicitWaitButton' encontrado\n";

            echo "   Haciendo clic en el botón...\n";
            $buttonExplicitWait->click();
            echo "   ✓ Clic realizado\n\n";

            $this->esperar(self::VISUAL_WAIT_MILLISECONDS);

            echo "   ⚠ PROBLEMA IDENTIFICADO:\n";
            echo "   ═════════════════════════════════════════\n";
            echo "   ¡No aparece nada en el navegador!\n";
            echo "   ¿Por qué? Se ha clicado en el botón, pero...\n";
            echo "   ¡¡¡ EL BOTÓN TODAVÍA NO ESTABA HABILITADO !!!\n";
            echo "   \n";
            echo "   La espera implícita solo busca que el elemento EXISTA,\n";
            echo "   pero NO espera a que esté en estado HABILITADO.\n";
            echo "   \n";
            echo "   Solución: Usar Explicit Wait (próxima clase)\n";
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
    $script = new Clase10_EjemploImplicitIneficienteNonClick();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
