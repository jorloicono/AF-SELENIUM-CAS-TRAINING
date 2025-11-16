package ejemplosylabs.java.com.ejemplos;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import java.time.Duration;

public class Clase19_EjemploDobleClick {
    public static void main(String[] args) {
        // Set the path to ChromeDriver (make sure to update this with the correct path)
        System.setProperty("webdriver.chrome.driver", "./drivers/chromedriver.exe");

        // Set Chrome options
        ChromeOptions options = new ChromeOptions();
        options.addArguments("--no-sandbox", "--disable-dev-shm-usage");

        // Create a new ChromeDriver instance
        WebDriver driver = new ChromeDriver(options);

        try {
            // Navigate to Saucedemo and login
            driver.get("https://www.saucedemo.com/");
            driver.manage().window().maximize();

            // Use WebDriverWait to wait for the elements to be visible
            WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(10));

            // Wait for the login fields and perform login
            WebElement usernameField = wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("user-name")));
            usernameField.sendKeys("standard_user");

            WebElement passwordField = driver.findElement(By.id("password"));
            passwordField.sendKeys("secret_sauce");

            WebElement loginButton = driver.findElement(By.id("login-button"));
            loginButton.click();

            // Wait for the product name to be visible and interactable
            Actions actions = new Actions(driver);
            WebDriverWait wait2 = new WebDriverWait(driver, Duration.ofSeconds(10));
            WebElement elem = wait2.until(ExpectedConditions.elementToBeClickable(By.className("inventory_item_name")));
            actions.doubleClick(elem);


        } catch (Exception e) {
            System.err.println("Error: " + e.getMessage());
        } finally {
            // Always quit the driver after the test is finished
            driver.quit();
        }
    }
}
