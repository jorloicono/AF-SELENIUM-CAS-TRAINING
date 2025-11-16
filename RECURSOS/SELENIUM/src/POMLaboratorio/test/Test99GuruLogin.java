package POMLaboratorio.test;

import java.time.Duration;
import java.util.concurrent.TimeUnit;

import org.openqa.selenium.WebDriver;

import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;

import org.testng.Assert;

import org.testng.annotations.BeforeTest;

import org.testng.annotations.Test;

import POMLaboratorio.pages.Guru99HomePage;

import POMLaboratorio.pages.Guru99Login;

public class Test99GuruLogin {
    WebDriver driver;

    Guru99Login objLogin;

    Guru99HomePage objHomePage;

    @BeforeTest

    public void setup(){

        System.setProperty("webdriver.chrome.driver", "./drivers/chromedriver.exe");

        // Inicializar el navegador
        driver = new ChromeDriver();
        driver.manage().window().maximize();

        driver.manage().timeouts().implicitlyWait(10, TimeUnit.SECONDS);

        driver.get("https://demo.guru99.com/V4/");

    }

    /**

     * This test case will login in https://demo.guru99.com/V4/

     * Verify login page title as guru99 bank

     * Login to application

     * Verify the home page using Dashboard message

     */

    @Test(priority=0)

    public void test_Home_Page_Appear_Correct(){

        //Create Login Page object

        objLogin = new Guru99Login(driver);

        //Verify login page title

        String loginPageTitle = objLogin.getLoginTitle();

        Assert.assertTrue(loginPageTitle.toLowerCase().contains("guru99 bank"));

        //login to application

        objLogin.loginToGuru99("mngr643642", "bynatyn");

        // go the next page

        objHomePage = new Guru99HomePage(driver);

        //Verify home page

        Assert.assertTrue(objHomePage.getHomePageDashboardUserName().toLowerCase().contains("manger id : mngr643642"));

    }
}
