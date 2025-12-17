<?php
namespace POMExample\Tests;

require_once 'vendor/autoload.php';
require_once 'selenium-config.php';

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use POMExample\Pages\LoginPage;
use POMExample\Pages\ProductsPage;

abstract class ParentTest
{
    protected RemoteWebDriver $driver;
    protected LoginPage $loginPage;
    protected ProductsPage $productsPage;
    private $chromeDriverProcess;

    public function setUp(): void
    {
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

        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create(
            CHROMEDRIVER_HOST,
            $capabilities,
            5000
        );

        $this->driver->manage()->window()->maximize();
        $this->loginPage = new LoginPage($this->driver);
        $this->productsPage = new ProductsPage($this->driver);
    }

    public function tearDown(): void
    {
        if (isset($this->driver)) {
            $this->driver->quit();
        }
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
        }
    }
}
