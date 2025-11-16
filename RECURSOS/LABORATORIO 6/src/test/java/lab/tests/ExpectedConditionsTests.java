package lab.tests;

import lab.tests.CustomExpectedCondition;
import org.openqa.selenium.*;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.ui.*;
import org.testng.annotations.Test;
import java.time.Duration;
import static org.testng.Assert.*;
public class ExpectedConditionsTests extends BaseTest {
    @Test(groups = {"prueba"})
    public void testElementToBeSelected() {
        driver.get(url("checkbox-demo.html"));
        WebElement checkAllBtn = driver.findElement(By.id("checkAll"));
        checkAllBtn.click();
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(10));
        WebElement cb1 = driver.findElement(By.id("cb1"));
        WebElement cb2 = driver.findElement(By.id("cb2"));
        assertTrue(wait.until(ExpectedConditions.elementToBeSelected(cb1)));
        assertTrue(wait.until(ExpectedConditions.elementToBeSelected(cb2)));
    }
    @Test
    public void testElementToBeClickable() {
        driver.get(url("simple-form-demo.html"));
        WebElement input = driver.findElement(By.id("user-message"));
        input.sendKeys("hola mundo");
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(10));
        wait.until(ExpectedConditions.elementToBeClickable(By.id("showInput"))).click();
        String text = driver.findElement(By.id("message")).getText();
        assertEquals(text, "");
    }
    @Test
    public void testVisibilityOfElementLocated() {
        driver.get(url("drag-drop-demo.html"));
        WebElement draggable = driver.findElement(By.id("draggable"));
        WebElement dropzone = driver.findElement(By.id("dropzone"));
        Actions actions = new Actions(driver);
        actions.dragAndDrop(draggable, dropzone).perform();
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(10));
        WebElement droppedItem =
                wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("dropped-item")));
        assertEquals(droppedItem.getText(), "Draggable 1");
    }
    @Test
    public void testTextToBePresentInElementLocated() {
        driver.get(url("dynamic-data-loading-demo.html"));
        driver.findElement(By.id("getRandom")).click();
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(10));

        assertTrue(wait.until(ExpectedConditions.textToBePresentInElementLocated(By.id("loading"), "First Name")));
    }
    @Test
    public void testFrameToBeAvailableAndSwitchToIt() {
        driver.get(url("iframe-demo.html"));
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(10));
        WebElement iframe = driver.findElement(By.id("editorFrame"));
        wait.until(ExpectedConditions.frameToBeAvailableAndSwitchToIt(iframe));
        WebElement body = driver.findElement(By.id("editorBody"));
        body.clear();
        body.sendKeys("switched to iframe");
        assertEquals(body.getText(), "switched to iframe");
        // volver al top-level
        driver.switchTo().defaultContent();
    }
    @Test
    public void testAlertIsPresent() {
        driver.get(url("alert-demo.html"));
        driver.findElement(By.id("showAlert")).click();
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
        Alert alert = wait.until(ExpectedConditions.alertIsPresent());
        assertEquals(alert.getText(), "Soy una alerta!");
        alert.accept();
    }
    @Test
    public void testPresenceOfElementLocatedAndTitleAndUrl() {
        driver.get(url("simple-form-demo.html"));
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
        WebElement input =
                wait.until(ExpectedConditions.presenceOfElementLocated(By.id("user-message")));
        assertTrue(input.isDisplayed());
        assertTrue(wait.until(ExpectedConditions.titleContains("Simple Form")));
        assertTrue(wait.until(ExpectedConditions.urlToBe(url("simple-form-demo.html"))));
    }
    @Test
    public void testCustomExpectedCondition() {
        driver.get(url("table-search-demo.html"));
        WebElement search = driver.findElement(By.id("searchBox"));
        search.sendKeys("Bennet");
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(10));
        // Usamos nuestra condición personalizada:
        boolean found = wait.until(new CustomExpectedCondition(By.cssSelector("#table tbody tr:nth-child(1) td.name"), "Bennet"));
        assertTrue(found, "No se encontró 'Bennet' en la tabla dentro del timeout");
    }
}

