package stepdefinitions;

import io.cucumber.java.en.*;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import static org.junit.Assert.assertTrue;

public class FailedLoginSteps {

    WebDriver driver;

    @Given("the user goes to the login page")
    public void the_user_is_on_the_login_page() {
        System.setProperty("webdriver.chrome.driver", "C:\\Users\\Jorge\\Desktop\\AF-TESTING-AUTOMATIZADO-SELENIUM-CUCUMBER\\RECURSOS\\CUCUMBER\\hellocucumber\\drivers\\chromedriver.exe");
        driver = new ChromeDriver();
        driver.get("https://www.saucedemo.com/"); // Cambia la URL a tu página real
    }

    @When("the user enters {string} and {string}")
    public void the_user_enters_username_and_password(String username, String password) {
        driver.findElement(By.id("user-name")).sendKeys(username);
        driver.findElement(By.id("password")).sendKeys(password);
        driver.findElement(By.id("login-button")).click();
    }

    @Then("the user should see an error message indicating login failure")
    public void the_user_should_see_an_error_message_indicating_login_failure() {
        String errorMessage = driver.findElement(By.cssSelector(".error-message-container")).getText();
        assertTrue("No se mostró el mensaje de error",
                errorMessage.contains("locked") || errorMessage.contains("Error"));
        driver.quit();
    }
}

