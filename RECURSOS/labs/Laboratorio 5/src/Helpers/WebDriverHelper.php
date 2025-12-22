<?php
namespace App\Helpers;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class WebDriverHelper
{
    private static $driver;
    private static $chromeProcess;

    public static function start($timeout = 10)
    {
        global $chromeProcess;
        self::startChromeDriver();
        self::$driver = RemoteWebDriver::create(CHROMEDRIVER_HOST, DesiredCapabilities::chrome(), 5000);
        self::$driver->manage()->timeouts()->implicitlyWait($timeout * 1000);
        self::$driver->manage()->window()->maximize();
        return self::$driver;
    }

    private static function startChromeDriver()
    {
        echo "[SERVER] Iniciando ChromeDriver...\n";
        $descriptorspec = [0 => ["pipe", "r"], 1 => ["pipe", "w"], 2 => ["pipe", "w"]];
        self::$chromeProcess = proc_open(CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT, $descriptorspec, $pipes);
        sleep(3);
    }

    public static function stop()
    {
        if (self::$driver)
            self::$driver->quit();
        if (self::$chromeProcess) {
            proc_terminate(self::$chromeProcess);
            proc_close(self::$chromeProcess);
        }
    }

    public static function waitFor($driver, $condition, $timeout = 10)
    {
        $wait = new WebDriverWait($driver, $timeout);
        return $wait->until($condition);
    }

    public static function elementSelected($driver, WebDriverBy $by)
    {
        return self::waitFor($driver, WebDriverExpectedCondition::elementToBeSelected($by));
    }

    public static function elementClickable($driver, WebDriverBy $by)
    {
        return self::waitFor($driver, WebDriverExpectedCondition::elementToBeClickable($by));
    }

    public static function elementVisible($driver, WebDriverBy $by)
    {
        return self::waitFor($driver, WebDriverExpectedCondition::visibilityOfElementLocated($by));
    }

    public static function textPresent($driver, WebDriverBy $by, $text)
    {
        return self::waitFor($driver, WebDriverExpectedCondition::textToBePresentInElementLocated($by, $text));
    }
}
?>