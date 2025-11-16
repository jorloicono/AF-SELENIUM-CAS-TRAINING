package ejemplosylabs.java.com.ejemplos;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.interactions.Actions;
import java.time.Duration;

public class Clase18_EjemploMoveToElement {
    public static void main(String[] args) {
        // Set the path to the ChromeDriver executable (make sure to replace with your actual path)
        System.setProperty("webdriver.chrome.driver", "./drivers/chromedriver.exe");

        ChromeOptions options = new ChromeOptions();
        options.addArguments("--no-sandbox", "--disable-dev-shm-usage");
        WebDriver driver = new ChromeDriver(options);

        try {
            // Navigate to Saucedemo and login
            driver.get("https://www.saucedemo.com/");
            driver.manage().window().maximize();
            driver.manage().timeouts().implicitlyWait(Duration.ofSeconds(5));
            driver.findElement(By.id("user-name")).sendKeys("standard_user");
            driver.findElement(By.id("password")).sendKeys("secret_sauce");
            driver.findElement(By.id("login-button")).click();

            // Perform hover action on sort dropdown
            WebElement sortDropdown = driver.findElement(By.className("product_sort_container"));
            Actions actions = new Actions(driver);
            actions.moveToElement(sortDropdown).build().perform();
            System.out.println("Hovered over sort dropdown.");

            // Verify dropdown is active (check if options are visible)
            WebElement option = driver.findElement(By.cssSelector("option[value='za']"));
            System.out.println("Is 'Z to A' option visible? " + option.isDisplayed());

            Thread.sleep(5000); // Pause to observe
        } catch (Exception e) {
            System.err.println("Error: " + e.getMessage());
        } finally {
            driver.quit();
        }
    }
}
