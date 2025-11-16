package ejemplosylabs.java.com.labs;

import org.openqa.selenium.Alert;
import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.Test;

public class AlertTest extends BaseTest {
    String URl1 = "https://bonigarcia.dev/selenium-webdriver-java/dialog-boxes.html";

    @Test
    void testAlert() {
        driver.get(URl1);
//        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
        driver.findElement(By.id("my-alert")).click();
//        wait.until(ExpectedConditions.alertIsPresent());
        Alert alert = driver.switchTo().alert();
        Assert.assertEquals(alert.getText(), "Hello world!");
        System.out.println("Launch Alert Executed Successfully");
        alert.accept();
    }

    @Test
    void testConfirm() {
        driver.get(
                "https://bonigarcia.dev/selenium-webdriver-java/dialog-boxes.html");
//        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));

        driver.findElement(By.id("my-confirm")).click();
//        wait.until(ExpectedConditions.alertIsPresent());
        Alert confirm = driver.switchTo().alert();
        Assert.assertEquals(confirm.getText(), "Is this correct?");
        System.out.println("Launch Confirm Alert Executed Successfully");
        confirm.dismiss();
    }

    @Test
    public void testPromptAlert() {
        driver.get(URl1);
        driver.findElement(By.id("my-prompt")).click();
        Alert prompt = driver.switchTo().alert();
        prompt.sendKeys("Vaibhav");
        Assert.assertEquals(prompt.getText(), "Please enter your name");
        prompt.accept();
        System.out.println("testPromptAlert executed Successfully");
    }
}
