<?php
// project-root/src/POMExample/Tests/Login/LoginWithOutPOMTest.php
namespace POMExample\Tests\Login;

require_once 'vendor/autoload.php';
require_once 'selenium-config.php';

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Exception;

class LoginWithOutPOMTest
{
    private RemoteWebDriver $driver;
    private $chromeDriverProcess;

    public function setUp(): void
    {
        echo "\n========== LoginWithOutPOMTest - setUp ==========\n";

        // Lanzar chromedriver
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $this->chromeDriverProcess = proc_open(
            CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT,
            $descriptorspec,
            $pipes
        );
        sleep(2);

        if (!is_resource($this->chromeDriverProcess)) {
            throw new Exception('Can not create driver session');
        }

        // Crear WebDriver
        $caps = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create(
            CHROMEDRIVER_HOST,
            $caps,
            5000
        );

        $this->driver->manage()->window()->maximize();
        echo "   ✓ Driver creado\n\n";
    }

    public function tearDown(): void
    {
        echo "\n========== LoginWithOutPOMTest - tearDown ==========\n";
        if (isset($this->driver)) {
            $this->driver->quit();
            echo "   ✓ Navegador cerrado\n";
        }
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
            echo "   ✓ ChromeDriver detenido\n";
        }
    }

    public function validLogin(): void
    {
        echo ">>> Ejecutando validLogin SIN POM...\n";

        try {
            // Ir a la página
            $this->driver->get('https://www.saucedemo.com');

            // user-name
            $this->driver->findElement(WebDriverBy::id('user-name'))->clear();
            $this->driver->findElement(WebDriverBy::id('user-name'))
                ->sendKeys('standard_user');

            // password
            $this->driver->findElement(WebDriverBy::id('password'))->clear();
            $this->driver->findElement(WebDriverBy::id('password'))
                ->sendKeys('secret_sauce');

            // login-button
            $this->driver->findElement(WebDriverBy::id('login-button'))->click();

            // comprobar texto Products
            $text = $this->driver
                ->findElement(WebDriverBy::cssSelector('div.header_secondary_container span.title'))
                ->getText();

            if (strpos($text, 'Products') === false) {
                throw new Exception('Login was not successful. Title: ' . $text);
            }

            echo "   ✓ validLogin SIN POM PASADO\n";
        } catch (Exception $e) {
            echo "   ✗ validLogin SIN POM FALLÓ: " . $e->getMessage() . "\n";
        }
    }
}

// “Runner” manual del test
$test = new LoginWithOutPOMTest();
try {
    $test->setUp();
    $test->validLogin();
} finally {
    $test->tearDown();
}
