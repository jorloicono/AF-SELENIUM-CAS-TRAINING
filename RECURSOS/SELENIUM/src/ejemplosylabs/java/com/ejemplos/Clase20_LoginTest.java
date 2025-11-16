package ejemplosylabs.java.com.ejemplos;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;
import org.testng.annotations.Test;

public class Clase20_LoginTest {

    WebDriver driver;

    @BeforeMethod
    public void setUp() {
        // Initialize the WebDriver (make sure the path to chromedriver is correct)
        System.setProperty("webdriver.chrome.driver", "./drivers/chromedriver.exe");
        driver = new ChromeDriver();
        driver.get("https://www.saucedemo.com/"); // URL of the login page
    }

    @Test(dataProvider = "loginData", dataProviderClass = Clase19_DataProvider.class)
    public void testLogin(String username, String password) {
        // Perform login test
        WebElement usernameField = driver.findElement(By.id("user-name"));
        WebElement passwordField = driver.findElement(By.id("password"));
        WebElement loginButton = driver.findElement(By.id("login-button"));

        usernameField.clear(); // Clear any existing text
        passwordField.clear(); // Clear any existing text

        usernameField.sendKeys(username);
        passwordField.sendKeys(password);
        loginButton.click();

        System.out.println("The Datadriven test executed successfully");
    }

    @AfterMethod
    public void tearDown() {
        driver.quit();
    }
}