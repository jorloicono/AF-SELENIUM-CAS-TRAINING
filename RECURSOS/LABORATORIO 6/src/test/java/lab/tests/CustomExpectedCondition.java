package lab.tests;

import org.openqa.selenium.*;
import org.openqa.selenium.support.ui.ExpectedCondition;
/**
 * Devuelve true cuando el texto esperado aparece en el elemento localizado.
 * Usamos esta clase para combinar lógica en un solo ExpectedCondition.
 */
public class CustomExpectedCondition implements ExpectedCondition<Boolean> {
    private final By locator;
    private final String expectedText;
    public CustomExpectedCondition(By locator, String expectedText) {
        this.locator = locator;
        this.expectedText = expectedText;
    }
    @Override
    public Boolean apply(WebDriver driver) {
        try {
            WebElement el = driver.findElement(locator);
            return el.isDisplayed() && el.getText().contains(expectedText);
        } catch (NoSuchElementException | StaleElementReferenceException e) {
            return false;
        }
    }
    @Override
    public String toString() {
        return String.format("texto '%s' presente en %s", expectedText, locator);
    }
}
