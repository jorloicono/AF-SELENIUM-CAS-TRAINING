package POMEjemplo.test.login;

import POMEjemplo.main.LoginPage;
import POMEjemplo.main.ProductsPage;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.AfterAll;
import org.junit.jupiter.api.Assertions;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;

import java.time.Duration;

public class ParentTest {

        static WebDriver webDriver;
        static LoginPage loginPage;
        static ProductsPage productsPage;

        @BeforeAll
        public static void setUp() {
            System.setProperty("web-driver.chrome.driver","./drivers/chromedriver.exe");
            webDriver = new ChromeDriver();

            webDriver.manage().window().maximize();
            webDriver.manage().timeouts().implicitlyWait(Duration.ofSeconds(20));

            loginPage = new LoginPage(webDriver);
            productsPage = new ProductsPage(webDriver);
        }

        @AfterAll
    public static void tearDown() {
        webDriver.quit();
    }

}
