package POMEjemplo.main;

import org.junit.jupiter.api.Assertions;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

public class ProductsPage {

    WebDriver webDriver;

    /**
     * create page constructor
     */
    public ProductsPage(WebDriver webDriver) {
        this.webDriver = webDriver;
    }

    /**
     * define web elements for Products page
     */
    By productsTitle = By.cssSelector("div.header_secondary_container span.title");

    public void checkProductsPageOpened() {
        String title = webDriver.findElement(productsTitle).getText();
        Assertions.assertTrue(title.contains("Products"), "Products page was not opened");
    }

}
