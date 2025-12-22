<?php
namespace POMExample\Pages;

use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Exception;

class LoginPage
{
    private WebDriver $driver;

    public function __construct(WebDriver $driver)
    {
        $this->driver = $driver;
    }

    // localizadores
    private WebDriverBy $userNameField;
    private WebDriverBy $passWordField;
    private WebDriverBy $loginBtn;

    private function initLocators(): void
    {
        $this->userNameField = WebDriverBy::id('user-name');
        $this->passWordField = WebDriverBy::id('password');
        $this->loginBtn = WebDriverBy::id('login-button');
    }

    public function openLoginPage(): void
    {
        try {
            $this->driver->get('https://www.saucedemo.com/');
            $this->initLocators();
        } catch (Exception $e) {
            throw new Exception('Impossible to open Login page: ' . $e->getMessage());
        }
    }

    public function enterUserName(string $userName): void
    {
        $el = $this->driver->findElement($this->userNameField);
        $el->clear();
        $el->sendKeys($userName);
    }

    public function enterPassWord(string $passWord): void
    {
        $el = $this->driver->findElement($this->passWordField);
        $el->clear();
        $el->sendKeys($passWord);
    }

    public function clickOnLogin(): void
    {
        $this->driver->findElement($this->loginBtn)->click();
    }
}
