package stepdefinitions;

import io.cucumber.java.en.*;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import static org.junit.Assert.assertTrue;

public class SuccessfulLoginSteps {

    WebDriver driver;

    @Given("the user is on the login page")
    public void the_user_is_on_the_login_page() {
        System.setProperty("webdriver.chrome.driver", "C:\\Users\\Jorge\\Desktop\\AF-TESTING-AUTOMATIZADO-SELENIUM-CUCUMBER\\RECURSOS\\CUCUMBER\\hellocucumber\\drivers\\chromedriver.exe");
        driver = new ChromeDriver();
        driver.get("https://www.saucedemo.com/"); // Cambia la URL a tu página real
    }

    @When("the user provides {string} and {string}")
    public void the_user_enters_username_and_password(String username, String password) {
        driver.findElement(By.id("user-name")).sendKeys(username);
        driver.findElement(By.id("password")).sendKeys(password);
        driver.findElement(By.id("login-button")).click();
    }

    @Then("the user is redirected to the homepage")
    public void the_user_should_be_redirected_to_the_homepage() {
        String currentUrl = driver.getCurrentUrl();
        assertTrue("No se redirigió a la homepage", currentUrl.contains("homepage"));
        driver.quit();
    }
}

