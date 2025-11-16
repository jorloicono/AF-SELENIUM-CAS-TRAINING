package ejemplosylabs.java.com.ejemplos;

import org.junit.jupiter.api.*;
import org.openqa.selenium.*;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.*;

import java.time.Duration;

public class Clase15_TextToBePresentTest {

    WebDriver driver;
    WebDriverWait wait;

    @BeforeEach
    void setup() {
        driver = new ChromeDriver();
        wait = new WebDriverWait(driver, Duration.ofSeconds(10));
    }

    @Test
    void testTextToBePresentInElement() {
        driver.get("https://the-internet.herokuapp.com/dynamic_loading/1");
        driver.findElement(By.cssSelector("#start button")).click();

        wait.until(ExpectedConditions.textToBePresentInElementLocated(By.id("finish"), "Hello World!"));
        System.out.println("Texto 'Hello World!' presente");
    }

    @AfterEach
    void teardown() {
        driver.quit();
    }
}
