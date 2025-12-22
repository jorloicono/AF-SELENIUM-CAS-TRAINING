<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class SeleniumWindowsExample
{
    private $driver;
    private $chromeDriverProcess;
    private const IMPLICIT_WAIT_SECONDS = 5;

    public function __construct()
    {
        $this->startChromeDriver();
        $this->connectDriver();
    }

    private function startChromeDriver()
    {
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

        sleep(2); // esperar que arranque
        if (!is_resource($this->chromeDriverProcess)) {
            throw new \Exception("No se pudo iniciar ChromeDriver");
        }
    }

    private function connectDriver()
    {
        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create(
            CHROMEDRIVER_HOST,
            $capabilities,
            5000
        );

        $this->driver->manage()->timeouts()->implicitlyWait(self::IMPLICIT_WAIT_SECONDS);
    }

    public function run()
    {
        try {
            // --- Abrir página de prueba ---
            $this->driver->get("https://www.selenium.dev/selenium/web/window_switching_tests/page_with_frame.html");

            // --- Ventana principal ---
            $mainWindow = $this->driver->getWindowHandle();
            echo "Ventana principal: $mainWindow\n";

            // --- Abrir nueva ventana mediante link ---
            $this->driver->findElement(WebDriverBy::linkText("Open new window"))->click();

            // Cambiar a la nueva ventana
            $handles = $this->driver->getWindowHandles();
            foreach ($handles as $handle) {
                if ($handle !== $mainWindow) {
                    $this->driver->switchTo()->window($handle);
                    break;
                }
            }

            // Verificar título de la nueva ventana
            $title = $this->driver->getTitle();
            echo "Título nueva ventana: $title\n";

            // Cerrar la nueva ventana
            $this->driver->close();

            // Volver a ventana principal
            $this->driver->switchTo()->window($mainWindow);

            // --- Abrir nueva pestaña con JavaScript ---
            $this->driver->executeScript("window.open('about:blank','_blank');");
            $handles = $this->driver->getWindowHandles();
            $newTab = end($handles); // obtener última ventana abierta
            $this->driver->switchTo()->window($newTab);
            echo "Nueva pestaña abierta, título: " . $this->driver->getTitle() . "\n";

            // --- Abrir nueva ventana con JavaScript ---
            $this->driver->executeScript("window.open('about:blank','_blank','width=600,height=400');");
            $handles = $this->driver->getWindowHandles();
            $newWindow = end($handles);
            $this->driver->switchTo()->window($newWindow);
            echo "Nueva ventana abierta, título: " . $this->driver->getTitle() . "\n";

        } catch (\Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        } finally {
            $this->stop();
        }
    }

    private function stop()
    {
        if ($this->driver) {
            $this->driver->quit();
        }
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
        }
        echo "ChromeDriver y navegador cerrados\n";
    }
}

// --- EJECUCIÓN ---
$script = new SeleniumWindowsExample();
$script->run();
