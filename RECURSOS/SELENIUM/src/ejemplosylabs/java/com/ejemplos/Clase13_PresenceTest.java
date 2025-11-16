package ejemplosylabs.java.com.ejemplos;

import org.junit.jupiter.api.*;
import org.openqa.selenium.*;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.*;

import java.time.Duration;

public class Clase13_PresenceTest {

    WebDriver driver;
    WebDriverWait wait;

    @BeforeEach
    void setup() {
        driver = new ChromeDriver();
        wait = new WebDriverWait(driver, Duration.ofSeconds(10));
    }

    @Test
    void testPresenceOfElement() {
        driver.get("https://the-internet.herokuapp.com/dynamic_loading/2");
        WebElement elemento = wait.until(
                ExpectedConditions.presenceOfElementLocated(By.id("finish"))
        );

        System.out.println("Elemento presente en DOM");
    }

    @AfterEach
    void teardown() {
        driver.quit();
    }
}
