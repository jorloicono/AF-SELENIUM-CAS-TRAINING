package ejemplosylabs.java.com.ejemplos;


import org.junit.jupiter.api.*;
import org.openqa.selenium.*;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.*;

import java.time.Duration;

public class Clase14_ClickableTest {

    WebDriver driver;
    WebDriverWait wait;

    @BeforeEach
    void setup() {
        driver = new ChromeDriver();
        wait = new WebDriverWait(driver, Duration.ofSeconds(10));
    }

    @Test
    void testElementToBeClickable() {
        driver.get("https://the-internet.herokuapp.com/dynamic_loading/1");

        WebElement boton = wait.until(
                ExpectedConditions.elementToBeClickable(By.cssSelector("#start button"))
        );

        boton.click();
        System.out.println("Botón clickeable y clicado");
    }

    @AfterEach
    void teardown() {
        driver.quit();
    }
}

