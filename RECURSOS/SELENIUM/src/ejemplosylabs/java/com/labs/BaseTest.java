package ejemplosylabs.java.com.labs;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.testng.annotations.AfterMethod;
import org.testng.annotations.BeforeMethod;

public class BaseTest {

    protected WebDriver driver;

    // Set up the ChromeDriver
    @BeforeMethod
    public void setup() {
        // Set the path to your chromedriver executable
        System.setProperty("web-driver.chrome.driver","./drivers/chromedriver.exe");

        // Initialize the ChromeDriver
        driver = new ChromeDriver();
    }

    // Close the browser after each test
    @AfterMethod
    public void teardown() {
        if (driver != null) {
            driver.quit();
        }
    }
}