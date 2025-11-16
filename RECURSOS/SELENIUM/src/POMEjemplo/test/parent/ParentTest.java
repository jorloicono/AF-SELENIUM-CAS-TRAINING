package POMEjemplo.test.parent;

import POMEjemplo.main.LoginPage;
import POMEjemplo.main.ProductsPage;
import org.junit.jupiter.api.BeforeAll;
import org.junit.jupiter.api.AfterAll;
import org.junit.jupiter.api.Assertions;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;

import java.time.Duration;

public class ParentTest {

    WebDriver webDriver;
    protected LoginPage loginPage;
    protected ProductsPage productsPage;

    @BeforeAll
    public void setUp() {
        try{
            System.setProperty("web-driver.chrome.driver","./drivers/chromedriver.exe");
            webDriver = new ChromeDriver();

            webDriver.manage().window().maximize();
            webDriver.manage().timeouts().implicitlyWait(Duration.ofSeconds(20));

            loginPage = new LoginPage(webDriver);
            productsPage = new ProductsPage(webDriver);
        }catch (Exception e){
            Assertions.fail("Can not create driver session");
        }
    }

    @AfterAll
    public void tearDown() {
        webDriver.quit();
    }

}
