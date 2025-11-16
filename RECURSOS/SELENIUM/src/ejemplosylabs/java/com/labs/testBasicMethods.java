package ejemplosylabs.java.com.labs;

import org.openqa.selenium.remote.RemoteWebDriver;
import org.openqa.selenium.remote.SessionId;
import org.testng.Assert;
import org.testng.annotations.Test;

public class testBasicMethods extends BaseTest {
    String appUrl = "https://bonigarcia.dev/selenium-webdriver-java/";

    @Test
    public void testBasicMethod() {
        driver.get(appUrl);
        Assert.assertEquals(driver.getTitle(), "Hands-On Selenium WebDriver with Java");
        Assert.assertEquals(driver.getCurrentUrl(), appUrl);
    }

    @Test
    public void TestSessionId() {
        driver.get(appUrl);
        SessionId sessionId = ((RemoteWebDriver) driver).getSessionId();
        System.out.println(sessionId);
    }
}
