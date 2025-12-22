<?php
namespace App\Tests;

use App\Helpers\WebDriverHelper;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Interactions\Actions;

class ExpectedConditionsTests
{
    private $driver;
    private $server;

    public function __construct()
    {
        $this->driver = WebDriverHelper::start();
        $this->server = new \App\Server\StaticFileServer(__DIR__ . '/../../pages');
        $this->server->start();
    }

    public function runAllTests()
    {
        echo "\n=== LABORATORIO 6 - EXPECTED CONDITIONS ===\n\n";

        $this->testCheckbox();
        $this->testClickable();
        $this->testDragDrop();
        $this->testDynamicData();
        $this->testIframe();
        $this->testAlert();
        $this->testTableSearch();

        echo "\n🎉 ¡TODOS LOS TESTS PASARON!\n";
    }

    private function testCheckbox()
    {
        echo "[1/7] testElementToBeSelected - Checkbox\n";
        $this->driver->get(BASE_URL . '/checkbox-demo.html');
        $btn = $this->driver->findElement(WebDriverBy::id('checkAll'));
        $btn->click();
        WebDriverHelper::elementSelected($this->driver, WebDriverBy::id('cb1'));
        WebDriverHelper::elementSelected($this->driver, WebDriverBy::id('cb2'));
        echo "   ✓ PASS\n\n";
    }

    private function testClickable()
    {
        echo "[2/7] testElementToBeClickable - Form\n";
        $this->driver->get(BASE_URL . '/simple-form-demo.html');
        $input = $this->driver->findElement(WebDriverBy::id('user-message'));
        $input->sendKeys('Hola Mundo');
        $btn = WebDriverHelper::elementClickable($this->driver, WebDriverBy::id('showInput'));
        $btn->click();
        $msg = $this->driver->findElement(WebDriverBy::id('message'))->getText();
        echo "   ✓ PASS: '$msg'\n\n";
    }

    private function testDragDrop()
    {
        echo "[3/7] testVisibilityOfElementLocated - DragDrop\n";
        $this->driver->get(BASE_URL . '/drag-drop-demo.html');
        $drag = $this->driver->findElement(WebDriverBy::id('draggable'));
        $drop = $this->driver->findElement(WebDriverBy::id('dropzone'));
        $actions = new Actions($this->driver);
        $actions->dragAndDrop($drag, $drop)->perform();
        $item = WebDriverHelper::elementVisible($this->driver, WebDriverBy::id('dropped-item'));
        echo "   ✓ PASS: '{$item->getText()}'\n\n";
    }

    private function testDynamicData()
    {
        echo "[4/7] testTextToBePresent - Dynamic Data\n";
        $this->driver->get(BASE_URL . '/dynamic-data-loading-demo.html');
        $btn = $this->driver->findElement(WebDriverBy::id('getRandom'));
        $btn->click();
        WebDriverHelper::textPresent($this->driver, WebDriverBy::id('loading'), 'John');
        echo "   ✓ PASS\n\n";
    }

    private function testIframe()
    {
        echo "[5/7] testFrameToBeAvailable - Iframe\n";
        $this->driver->get(BASE_URL . '/iframe-demo.html');
        $iframe = $this->driver->findElement(WebDriverBy::id('editorFrame'));
        $wait = new \Facebook\WebDriver\WebDriverWait($this->driver, 10);
        $wait->until(\Facebook\WebDriver\WebDriverExpectedCondition::frameToBeAvailableAndSwitchToIt($iframe));
        $body = $this->driver->findElement(WebDriverBy::id('editorBody'));
        $body->clear()->sendKeys('iframe test');
        $this->driver->switchTo()->defaultContent();
        echo "   ✓ PASS\n\n";
    }

    private function testAlert()
    {
        echo "[6/7] testAlertIsPresent\n";
        $this->driver->get(BASE_URL . '/alert-demo.html');
        $btn = $this->driver->findElement(WebDriverBy::id('showAlert'));
        $btn->click();
        $wait = new \Facebook\WebDriver\WebDriverWait($this->driver, 5);
        $alert = $wait->until(\Facebook\WebDriver\WebDriverExpectedCondition::alertIsPresent());
        echo "   ✓ PASS: '{$alert->getText()}'\n\n";
        $alert->accept();
    }

    private function testTableSearch()
    {
        echo "[7/7] testCustomCondition - Table Search\n";
        $this->driver->get(BASE_URL . '/table-search-demo.html');
        $search = $this->driver->findElement(WebDriverBy::id('searchBox'));
        $search->sendKeys('Bennet');
        $wait = new \Facebook\WebDriver\WebDriverWait($this->driver, 10);
        $row = $wait->until(function ($driver) {
            $rows = $driver->findElements(WebDriverBy::cssSelector('table tbody tr'));
            foreach ($rows as $row) {
                if (stripos($row->getText(), 'Bennet') !== false)
                    return $row;
            }
            return false;
        });
        echo "   ✓ PASS: Fila Bennet encontrada\n\n";
    }

    public function __destruct()
    {
        WebDriverHelper::stop();
    }
}
?>