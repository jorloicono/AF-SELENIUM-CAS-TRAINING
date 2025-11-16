package POMEjemplo.main;

import org.junit.jupiter.api.Assertions;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

public class LoginPage {

    WebDriver webDriver;

    /**
     * create page constructor
     */
    public LoginPage(WebDriver webDriver) {
        this.webDriver = webDriver;
    }

    /**
     * define web elements for Login page
     */
    By userNameField = By.id("user-name");
    By passWordField = By.id("password");
    By loginBtn = By.id("login-button");


    // method for entering username
    public void enterUserName(String userName) {
        webDriver.findElement(userNameField).clear();
        webDriver.findElement(userNameField).sendKeys(userName);
    }

    // method for entering password
    public void enterPassWord(String passWord) {
        webDriver.findElement(passWordField).clear();
        webDriver.findElement(passWordField).sendKeys(passWord);
    }

    // method for clicking on Login button
    public void clickOnLogin() {
        webDriver.findElement(loginBtn).click();
    }

    // method for opening login page
    public void openLoginPage() {
        try{
            webDriver.get("https://www.saucedemo.com/");
        }catch (Exception e){
            Assertions.fail("Impossible to open Login page");
        }
    }

}
