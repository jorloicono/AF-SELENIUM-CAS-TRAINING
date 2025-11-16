package POMEjemplo.test.login;

import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.AfterAll;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.Assertions;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;

import java.time.Duration;

public class LoginWithOutPOMTest {
    static WebDriver webDriver;

    @BeforeAll
    public static void setUp() {
        try{
            System.setProperty("web-driver.chrome.driver","./drivers/chromedriver.exe");
            webDriver = new ChromeDriver();

            webDriver.manage().window().maximize();
            webDriver.manage().timeouts().implicitlyWait(Duration.ofSeconds(20));

        }catch (Exception e){
            Assertions.fail("Can not create driver session");
        }
    }

    @Test
    public void validLogin() {
        webDriver.get("https://www.saucedemo.com");

        webDriver.findElement(By.id("user-name")).clear();
        webDriver.findElement(By.id("user-name")).sendKeys("standard_user");

        webDriver.findElement(By.id("password")).clear();
        webDriver.findElement(By.id("password")).sendKeys("secret_sauce");

        webDriver.findElement(By.id("login-button")).click();

        String text = webDriver.findElement(
                By.cssSelector("div.header_secondary_container span.title")).getText();
        Assertions.assertTrue(text.contains("Products"), "Login was not successful");
    }

    @AfterAll
    public static void tearDown() {
        webDriver.quit();
    }

}
