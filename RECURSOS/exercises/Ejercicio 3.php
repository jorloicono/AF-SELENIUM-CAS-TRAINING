<?php
/**
 * Script de Automatización: Alertas e iFrames Combinados
 * Adaptación de ejercicios de Selenium PHP en un solo flujo de ejecución
 *
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Conecta con Selenium WebDriver
 * 3. Configura espera implícita
 * 4. Ejecuta los ejercicios:
 *    - Alert Simple
 *    - Confirm (Sí / No)
 *    - Prompt
 *    - iFrame Básico
 *    - Formulario en iFrame
 * 5. Cierra el navegador
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class EjerciciosCombinadosSelenium
{
    private $driver;
    private $chromeDriverProcess;
    private const IMPLICIT_WAIT_SECONDS = 10;
    private const VISUAL_WAIT_MILLISECONDS = 1000;

    public function __construct()
    {
        echo "\n===== EJERCICIOS COMBINADOS SELENIUM (PHP) =====\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
        $this->configurarImplicitWait();
    }

    private function iniciarChromeDriver()
    {
        echo "[1/5] Iniciando ChromeDriver...\n";
        $descriptorspec = [0 => ["pipe", "r"], 1 => ["pipe", "w"], 2 => ["pipe", "w"]];
        $this->chromeDriverProcess = proc_open(CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT, $descriptorspec, $pipes);
        sleep(2);
        if (!is_resource($this->chromeDriverProcess))
            throw new \Exception("No se pudo iniciar ChromeDriver");
        echo "   ✓ ChromeDriver iniciado correctamente\n\n";
    }

    private function conectarDriver()
    {
        echo "[2/5] Conectando con Selenium WebDriver...\n";
        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create(CHROMEDRIVER_HOST, $capabilities, 5000);
        echo "   ✓ Conexión establecida\n\n";
    }

    private function configurarImplicitWait()
    {
        echo "[3/5] Configurando espera implícita de " . self::IMPLICIT_WAIT_SECONDS . " segundos...\n";
        $this->driver->manage()->timeouts()->implicitlyWait(self::IMPLICIT_WAIT_SECONDS);
        echo "   ✓ Espera implícita configurada\n\n";
    }

    private function esperar($ms)
    {
        usleep($ms * 1000);
    }

    private function navegarPaginaAlertas()
    {
        $url = 'file:///' . str_replace('\\', '/', "C:\Users\Jorge\Desktop\AF-SELENIUM-CAS-TRAINING\RECURSOS\html\alertas-page.html");
        echo "[NAVEGACIÓN] Abriendo página: $url\n";
        $this->driver->get($url);
        echo "   ✓ Página cargada\n\n";
    }

    private function ejercicioAlert()
    {
        echo "===== EJERCICIO ALERT SIMPLE =====\n";
        $this->driver->findElement(WebDriverBy::id('alertBtn'))->click();
        $wait = new WebDriverWait($this->driver, 5);
        $wait->until(WebDriverExpectedCondition::alertIsPresent());
        $alert = $this->driver->switchTo()->alert();
        echo "Texto alerta: " . $alert->getText() . "\n";
        $alert->accept();
        $status = $this->driver->findElement(WebDriverBy::id('alertStatus'))->getText();
        echo "Estado: $status\n\n";
        $this->esperar(self::VISUAL_WAIT_MILLISECONDS);
    }

    private function ejercicioConfirm()
    {
        echo "===== EJERCICIO CONFIRM =====\n";
        $wait = new WebDriverWait($this->driver, 5);

        echo "[Confirm Aceptar]\n";
        $this->driver->findElement(WebDriverBy::id('confirmYesBtn'))->click();
        $wait->until(WebDriverExpectedCondition::alertIsPresent());
        $alert = $this->driver->switchTo()->alert();
        $alert->accept();
        echo "Resultado: " . $this->driver->findElement(WebDriverBy::id('confirmStatus'))->getText() . "\n";

        echo "[Confirm Cancelar]\n";
        $this->driver->findElement(WebDriverBy::id('confirmNoBtn'))->click();
        $wait->until(WebDriverExpectedCondition::alertIsPresent());
        $alert = $this->driver->switchTo()->alert();
        $alert->dismiss();
        echo "Resultado: " . $this->driver->findElement(WebDriverBy::id('confirmStatus'))->getText() . "\n\n";

        $this->esperar(self::VISUAL_WAIT_MILLISECONDS);
    }

    private function ejercicioPrompt()
    {
        echo "===== EJERCICIO PROMPT =====\n";
        $wait = new WebDriverWait($this->driver, 5);
        $this->driver->findElement(WebDriverBy::id('promptBtn'))->click();
        $wait->until(WebDriverExpectedCondition::alertIsPresent());
        $alert = $this->driver->switchTo()->alert();
        $texto = "María González";
        $alert->sendKeys($texto);
        $alert->accept();
        $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('result')));
        echo "Resultado prompt: " . $this->driver->findElement(WebDriverBy::id('result'))->getText() . "\n\n";
        $this->esperar(self::VISUAL_WAIT_MILLISECONDS);
    }

    private function navegarPaginaIFrames()
    {
        $url = 'file:///' . str_replace('\\', '/', "C:\Users\Jorge\Desktop\AF-SELENIUM-CAS-TRAINING\RECURSOS\html\iframes-page.html");
        echo "[NAVEGACIÓN] Abriendo página: $url\n";
        $this->driver->get($url);
        echo "   ✓ Página cargada\n\n";
    }
    private function ejercicioIFrame()
    {
        echo "===== EJERCICIO IFRAME BÁSICO =====\n";
        $this->driver->switchTo()->frame('iframe1');
        $this->driver->findElement(WebDriverBy::id('iframeBtn'))->click();
        $msg = $this->driver->findElement(WebDriverBy::id('message'))->getText();
        echo "Mensaje iFrame: $msg\n";
        $this->driver->switchTo()->defaultContent();
        echo "Vuelto al contexto principal\n\n";
        $this->esperar(self::VISUAL_WAIT_MILLISECONDS);
    }

    private function ejercicioFormularioIFrame()
    {
        echo "===== EJERCICIO FORMULARIO EN IFRAME =====\n";
        $this->driver->switchTo()->frame('iframe2');
        $this->driver->findElement(WebDriverBy::id('iframeName'))->sendKeys('Pedro López');
        $this->driver->findElement(WebDriverBy::id('iframeMessage'))->sendKeys('Automatización en iFrame');
        $this->driver->findElement(WebDriverBy::tagName('button'))->click();
        $msg = $this->driver->findElement(WebDriverBy::id('formResult'))->getText();
        echo "Resultado formulario: $msg\n";
        $this->driver->switchTo()->defaultContent();
        echo "Vuelto al contexto principal\n\n";
        $this->esperar(self::VISUAL_WAIT_MILLISECONDS);
    }

    public function ejecutar()
    {
        try {
            $htmlPath = 'C:\Users\Jorge\Desktop\AF-SELENIUM-CAS-TRAINING\RECURSOS\index_completo.html';
            $this->navegarPaginaAlertas();
            $this->ejercicioAlert();
            $this->ejercicioConfirm();
            $this->ejercicioPrompt();
            $this->navegarPaginaIFrames();
            $this->ejercicioIFrame();
            $this->ejercicioFormularioIFrame();
        } catch (\Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n";
        } finally {
            $this->detener();
        }

        echo "===== TODOS LOS EJERCICIOS COMPLETADOS =====\n\n";
    }

    private function detener()
    {
        echo "Finalizando...\n";
        if ($this->driver !== null)
            $this->driver->quit();
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
        }
        echo "   ✓ Navegador y ChromeDriver cerrados\n";
    }
}

// EJECUCIÓN
try {
    $script = new EjerciciosCombinadosSelenium();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "✗ ERROR FATAL: " . $e->getMessage() . "\n";
    exit(1);
}
?>