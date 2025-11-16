package ejemplosylabs.java.com.labs;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.testng.Assert;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Test;
public class LoginTest {
    WebDriver driver;
    @BeforeMethod
    public void setUp() {
        // Initialize the WebDriver (ensure chromedriver path is correct)
        System.setProperty("webdriver.chrome.driver", "./drivers/chromedriver.exe");
        driver = new ChromeDriver();
        driver.get("https://www.saucedemo.com/"); // URL of the login page
    }
    @Test(dataProvider = "loginData", dataProviderClass = DataProviderExample.class)
    public void testLogin(String username, String password) {
        // Find elements for username, password, and login button
        WebElement usernameField = driver.findElement(By.id("user-name"));
        WebElement passwordField = driver.findElement(By.id("password"));
        WebElement loginButton = driver.findElement(By.id("login-button"));
        // Clear any existing text from fields
        usernameField.clear();
        passwordField.clear();
        // Input login credentials from DataProvider
        usernameField.sendKeys(username);
        passwordField.sendKeys(password);
        loginButton.click();
        System.out.println("The DataDriven test executed successfully");
        // Validate the login behavior by checking the title of the page
        Assert.assertTrue(driver.getTitle().contains("Swag Labs"));
    }
    @AfterMethod
    public void tearDown() {
        driver.quit(); // Close the browser after each test
    }
}
