<?php
namespace POMExample\Pages;

use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Exception;

class ProductsPage
{
    private WebDriver $driver;
    private WebDriverBy $productsTitle;

    public function __construct(WebDriver $driver)
    {
        $this->driver = $driver;
        $this->productsTitle = WebDriverBy::cssSelector('div.header_secondary_container span.title');
    }

    public function checkProductsPageOpened(): void
    {
        $title = $this->driver->findElement($this->productsTitle)->getText();

        if (strpos($title, 'Products') === false) {
            throw new Exception('Products page was not opened. Title was: ' . $title);
        }
    }
}
