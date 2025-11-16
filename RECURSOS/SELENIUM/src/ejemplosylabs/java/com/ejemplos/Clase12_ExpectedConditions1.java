package ejemplosylabs.java.com.ejemplos;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import java.time.Duration;

public class Clase12_ExpectedConditions1 {
    public static void main(String[] args) {
        WebDriver driver = new ChromeDriver();

        driver.get("https://the-internet.herokuapp.com/dynamic_loading/1");

        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(10));

        driver.findElement(By.cssSelector("#start button")).click();

        WebElement mensaje = wait.until(
                ExpectedConditions.visibilityOfElementLocated(By.id("finish"))
        );

        System.out.println("Texto mostrado: " + mensaje.getText());

        driver.quit();
    }
}
