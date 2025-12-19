<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Interactions\WebDriverActions;

class SeleniumAllActionsChrome
{
    private $driver;
    private $chromeDriverProcess;
    private const IMPLICIT_WAIT_SECONDS = 5;

    public function __construct()
    {
        echo "===== INICIANDO CHROMEDRIVER Y SELENIUM =====\n";
        $this->startChromeDriver();
        $this->connectDriver();
    }

    private function startChromeDriver()
    {
        echo "Iniciando ChromeDriver en puerto " . CHROMEDRIVER_PORT . "...\n";

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

        echo "ChromeDriver iniciado correctamente\n\n";
    }

    private function connectDriver()
    {
        echo "Conectando con Selenium WebDriver...\n";

        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create(
            CHROMEDRIVER_HOST,
            $capabilities,
            5000
        );

        $this->driver->manage()->timeouts()->implicitlyWait(self::IMPLICIT_WAIT_SECONDS);
        echo "Conexión establecida y espera implícita configurada\n\n";
    }

    public function run()
    {
        try {
            $url = "https://www.selenium.dev/selenium/web/web-form.html";
            echo "Abriendo página: $url\n";
            $this->driver->get($url);

            // --- Texto ---
            $textInput = $this->driver->findElement(WebDriverBy::name('my-text'));
            $textInput->sendKeys("Hola Selenium");
            $textInput->clear();
            $textInput->sendKeys("Nuevo Texto");

            // --- Select / Dropdown ---
            $select = new WebDriverSelect($this->driver->findElement(WebDriverBy::name('my-select')));
            $select->selectByVisibleText("Two");

            // --- Checkbox / Radio ---
            $checkbox = $this->driver->findElement(WebDriverBy::id('my-check-1'));
            if (!$checkbox->isSelected())
                $checkbox->click();

            $radio = $this->driver->findElement(WebDriverBy::cssSelector("body > main:nth-child(1) > div:nth-child(1) > form:nth-child(2) > div:nth-child(1) > div:nth-child(2) > div:nth-child(5) > label:nth-child(1)"));
            $radio->click();

            // --- File upload ---
            $fileInput = $this->driver->findElement(WebDriverBy::name('my-file'));
            $fileInput->sendKeys(__DIR__ . "/Ejercicio 1.php");

            // --- Color, Date, Range ---
            $this->driver->findElement(WebDriverBy::name('my-color'))->sendKeys("#00ff00");
            $this->driver->findElement(WebDriverBy::name('my-date'))->sendKeys("2025-12-31");
            $this->driver->findElement(WebDriverBy::name('my-range'))->sendKeys("50");

            // --- Acciones avanzadas ---
            $submitBtn = $this->driver->findElement(WebDriverBy::cssSelector("button[type='submit']"));
            $actions = new WebDriverActions($this->driver);
            $actions->moveToElement($submitBtn)->click()->perform();

            // --- JavaScript Execution ---
            $msg = $this->driver->executeScript("return document.getElementById('message').innerText;");
            echo "Mensaje: $msg\n";

            // --- Screenshot ---
            $this->driver->takeScreenshot(__DIR__ . "/screenshot.png");
            echo "Screenshot guardado\n";

            // --- Ventanas / Tabs ---
            $mainWindow = $this->driver->getWindowHandle();
            foreach ($this->driver->getWindowHandles() as $handle) {
                $this->driver->switchTo()->window($handle);
            }
            $this->driver->switchTo()->window($mainWindow);

            // --- Espera explícita ---
            $this->driver->wait()->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('message'))
            );

        } catch (\Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        } finally {
            $this->stop();
        }
    }

    private function stop()
    {
        echo "Finalizando...\n";
        if ($this->driver) {
            $this->driver->quit();
            echo "Navegador cerrado\n";
        }
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
            echo "ChromeDriver detenido\n";
        }
    }
}

// EJECUCIÓN
$script = new SeleniumAllActionsChrome();
$script->run();
