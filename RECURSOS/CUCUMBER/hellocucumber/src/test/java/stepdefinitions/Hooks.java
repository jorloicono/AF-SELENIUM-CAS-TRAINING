package stepdefinitions;

import io.cucumber.java.Before;
import io.cucumber.java.After;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;

public class Hooks {

    // WebDriver instance shared across all step definitions
    public WebDriver driver;

    // Initialize WebDriver before each scenario
    @Before("@LoginTest")
    public void setup() {
        System.setProperty("webdriver.chrome.driver", "C:\\Users\\Jorge\\Desktop\\AF-TESTING-AUTOMATIZADO-SELENIUM-CUCUMBER\\RECURSOS\\CUCUMBER\\hellocucumber\\drivers\\chromedriver.exe");
        driver = new ChromeDriver();
        System.out.println("Browser initialized for Login Test");
    }

    // Clean up WebDriver after each scenario
    @After("@LoginTest")
    public void teardown() {
        if (driver != null) {
            driver.quit();
            System.out.println("Browser closed after Login Test");
        }
    }
}
