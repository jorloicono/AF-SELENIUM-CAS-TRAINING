package stepdefinitions;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import io.cucumber.java.en.Given;
import io.cucumber.java.en.When;
import io.cucumber.java.en.Then;

public class LoginSteps {

    private WebDriver driver;

    // Constructor to inject WebDriver from Hooks class
    public LoginSteps(Hooks hooks) {
        this.driver = hooks.driver;
    }

    @Given("the user is on the login page")
    public void userOnLoginPage() {
        driver.get("https://www.saucedemo.com/");
    }

    @When("the user enters {string} and {string}")
    public void userEntersCredentials(String username, String password) {
        WebElement usernameField = driver.findElement(By.id("user-name"));
        WebElement passwordField = driver.findElement(By.id("password"));
        WebElement loginButton = driver.findElement(By.id("login-button"));

        usernameField.sendKeys(username);
        passwordField.sendKeys(password);
        loginButton.click();
    }

    @Then("the user should be redirected to the homepage")
    public void userRedirectedToHomepage() {
        String currentUrl = driver.getCurrentUrl();
        assert currentUrl.contains("inventory.html");
    }
}