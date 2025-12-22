<?php
/**
 * Selenium PHP – Resizable, Sortable & Selectable Challenge
 * Automatiza:
 *  - Resizable
 *  - Sortable
 *  - Selectable
 *  - Validaciones, screenshots y status
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Interactions\WebDriverKeyboard;
use Facebook\WebDriver\Interactions\WebDriverMouse;


class JQueryUIChallenge
{
    private $driver;
    private $chromeDriverProcess;

    public function __construct()
    {
        echo "\n===== JQuery UI Challenge =====\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }

    /* ------------------- Setup ------------------- */
    private function iniciarChromeDriver()
    {
        echo "[SETUP] Iniciando ChromeDriver...\n";
        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];

        $this->chromeDriverProcess = proc_open(
            CHROMEDRIVER_PATH . " --port=" . CHROMEDRIVER_PORT,
            $descriptorspec,
            $pipes
        );

        sleep(2);
        if (!is_resource($this->chromeDriverProcess)) {
            throw new Exception("No se pudo iniciar ChromeDriver");
        }
        echo "   ✓ ChromeDriver iniciado\n";
    }

    private function conectarDriver()
    {
        $this->driver = RemoteWebDriver::create(
            CHROMEDRIVER_HOST,
            DesiredCapabilities::chrome(),
            5000
        );
        echo "   ✓ WebDriver conectado\n\n";
    }

    private function esperar($milisegundos)
    {
        usleep($milisegundos * 1000);
    }

    private function screenshot($filename)
    {
        $this->driver->takeScreenshot($filename);
        echo "   ✓ Screenshot guardado: $filename\n";
    }

    /* ------------------- RESIZABLE ------------------- */
    public function testResizable($htmlPath)
    {
        echo "[RESIZABLE] Abrir HTML...\n";
        $this->driver->get("file://" . realpath($htmlPath));
        $wait = new WebDriverWait($this->driver, 5);

        $resizable = $this->driver->findElement(WebDriverBy::id('resizable'));
        $handle = $resizable->findElement(WebDriverBy::className('ui-resizable-se'));

        $actions = new WebDriverActions($this->driver);
        $actions->dragAndDropBy($handle, 250, 250)->perform();
        $this->esperar(500);

        // Validar tamaño final
        $sizeText = $this->driver->findElement(WebDriverBy::id('size-status'))->getText();
        if (strpos($sizeText, '450px') === false) {
            throw new Exception("Resizable no alcanzó 450px: $sizeText");
        }
        echo "   ✓ Resizable validado: $sizeText\n";

        $this->screenshot("resizable_final.png");
    }

    /* ------------------- SORTABLE ------------------- */
    public function testSortable($htmlPath)
    {
        echo "[SORTABLE] Abrir HTML...\n";
        $this->driver->get("file://" . realpath($htmlPath));
        $wait = new WebDriverWait($this->driver, 5);

        $items = $this->driver->findElements(WebDriverBy::xpath('//ul[@id="sortable"]/li'));
        if (count($items) !== 5)
            throw new Exception("No se encontraron 5 items iniciales");
        $this->screenshot("sortable_inicial.png");

        $actions = new WebDriverActions($this->driver);

        // Item 4 -> posición 1
        $source = $items[3]; // Item 4
        $target = $items[0]; // Item 1
        $actions->dragAndDrop($source, $target)->perform();
        $this->esperar(500);

        // Item 1 -> posición 3
        $items = $this->driver->findElements(WebDriverBy::xpath('//ul[@id="sortable"]/li'));
        $source = $items[1]; // Item 1 ahora
        $target = $items[2]; // posición 3
        $actions->dragAndDrop($source, $target)->perform();
        $this->esperar(500);

        $status = $this->driver->findElement(WebDriverBy::id('order-status'))->getText();
        if ($status !== 'Item 4, Item 2, Item 1, Item 3, Item 5') {
            throw new Exception("Orden intermedio inválido: $status");
        }
        echo "   ✓ Orden intermedio validado: $status\n";
        $this->screenshot("sortable_intermedio.png");

        // Item 5 -> posición 2
        $items = $this->driver->findElements(WebDriverBy::xpath('//ul[@id="sortable"]/li'));
        $actions->dragAndDrop($items[4], $items[1])->perform();
        $this->esperar(500);

        $statusFinal = $this->driver->findElement(WebDriverBy::id('order-status'))->getText();
        if ($statusFinal !== 'Item 4, Item 5, Item 2, Item 1, Item 3') {
            throw new Exception("Orden final inválida: $statusFinal");
        }
        echo "   ✓ Orden final validado: $statusFinal\n";

        // Reset
        $this->driver->findElement(WebDriverBy::className('reset-btn'))->click();
        $this->esperar(1000);
        $statusReset = $this->driver->findElement(WebDriverBy::id('order-status'))->getText();
        if ($statusReset !== 'Item 1, Item 2, Item 3, Item 4, Item 5') {
            throw new Exception("Reset orden falló: $statusReset");
        }
        echo "   ✓ Reset orden validado: $statusReset\n";
        $this->screenshot("sortable_final.png");
    }

    /* ------------------- SELECTABLE ------------------- */
    public function testSelectable($htmlPath)
    {
        echo "[SELECTABLE] Abrir HTML...\n";
        $this->driver->get("file://" . realpath($htmlPath));
        $wait = new WebDriverWait($this->driver, 5);

        $items = $this->driver->findElements(WebDriverBy::xpath('//ul[@id="selectable"]/li'));
        if (count($items) !== 6)
            throw new Exception("No se encontraron 6 items");

        $actions = new WebDriverActions($this->driver);

        // Ctrl+Click Item 2, 4, 6
        $actions->keyDown(WebDriverKeys::CONTROL)
            ->click($items[1])
            ->click($items[3])
            ->click($items[5])
            ->keyUp(WebDriverKeys::CONTROL)
            ->perform();
        $this->esperar(500);

        $selected = $this->driver->findElements(WebDriverBy::xpath('//li[contains(@class,"ui-selected")]'));
        if (count($selected) !== 3)
            throw new Exception("Selección Ctrl+Click inválida");
        echo "   ✓ Ctrl+Click validado: 3 items seleccionados\n";
        $this->screenshot("selectable_ctrlclick.png");

        // SHIFT+Click Item 5 (rango 4→5)
        $actions->keyDown(WebDriverKeys::SHIFT)
            ->click($items[4])
            ->keyUp(WebDriverKeys::SHIFT)
            ->perform();
        $this->esperar(500);

        $selected = $this->driver->findElements(WebDriverBy::xpath('//li[contains(@class,"ui-selected")]'));
        if (count($selected) !== 3)
            throw new Exception("Selección Shift+Click inválida");
        echo "   ✓ Shift+Click validado: Items 2,4,5 seleccionados\n";
        $this->screenshot("selectable_shiftclick.png");

        // Click en área vacía para limpiar
        $this->driver->findElement(WebDriverBy::id('selectable'))->click();
        $this->esperar(500);
        $selected = $this->driver->findElements(WebDriverBy::xpath('//li[contains(@class,"ui-selected")]'));
        if (count($selected) !== 0)
            throw new Exception("Reset selección falló");
        echo "   ✓ Reset selección validado: 0 items\n";
        $this->screenshot("selectable_reset.png");
    }

    /* ------------------- Cierre ------------------- */
    public function cerrar()
    {
        if ($this->driver)
            $this->driver->quit();
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
        }
        echo "\n===== FIN CHALLENGE =====\n";
    }
}

/* ------------------- EJECUTAR ------------------- */
try {
    $challenge = new JQueryUIChallenge();

    $challenge->testResizable("C:\\Users\\Jorge\\Desktop\\AF-SELENIUM-CAS-TRAINING\\RECURSOS\\html\\resizable-test.html");
    $challenge->testSortable("C:\\Users\\Jorge\\Desktop\\AF-SELENIUM-CAS-TRAINING\\RECURSOS\\html\\sortable-test.html");
    $challenge->testSelectable("C:\\Users\\Jorge\\Desktop\\AF-SELENIUM-CAS-TRAINING\\RECURSOS\\html\\selectable-test.html");

    $challenge->cerrar();
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}
?>