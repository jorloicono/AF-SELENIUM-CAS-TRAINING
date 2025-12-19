<?php
namespace App;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class BaseTest
{
    protected $driver;
    protected $chromeProcess;

    public function setup()
    {
        $this->startChromeDriver();
        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create(CHROMEDRIVER_HOST, $capabilities, 5000);
        $this->driver->manage()->window()->maximize();
        $this->driver->manage()->timeouts()->implicitlyWait(5000);
    }

    private function startChromeDriver()
    {
        $descriptorspec = [0 => ["pipe", "r"], 1 => ["pipe", "w"], 2 => ["pipe", "w"]];
        $this->chromeProcess = proc_open(CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT, $descriptorspec, $pipes);
        sleep(3);
    }

    public function teardown()
    {
        if ($this->driver)
            $this->driver->quit();
        if ($this->chromeProcess) {
            proc_terminate($this->chromeProcess);
            proc_close($this->chromeProcess);
        }
    }
}
?>