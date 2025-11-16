package ejemplosylabs.java.com.ejemplos;

import org.junit.jupiter.api.*;
import org.openqa.selenium.*;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.*;

import java.time.Duration;

public class Clase16_AlertTest {

    WebDriver driver;
    WebDriverWait wait;

    @BeforeEach
    void setup() {
        driver = new ChromeDriver();
        wait = new WebDriverWait(driver, Duration.ofSeconds(10));
    }

    @Test
    void testAlertIsPresent() {
        driver.get("https://the-internet.herokuapp.com/javascript_alerts");

        driver.findElement(By.xpath("//button[text()='Click for JS Alert']")).click();
        wait.until(ExpectedConditions.alertIsPresent());

        Alert alert = driver.switchTo().alert();
        System.out.println("Texto alerta: " + alert.getText());
        alert.accept();
    }

    @AfterEach
    void teardown() {
        driver.quit();
    }
}

