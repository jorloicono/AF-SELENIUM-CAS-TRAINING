package ejemplosylabs.java.com.labs;

import org.openqa.selenium.Dimension;
import org.openqa.selenium.Point;
import org.openqa.selenium.WebDriver;
import org.testng.Assert;
import org.testng.annotations.Test;

public class WindowTest extends BaseTest {

    @Test
    public void testWindow() {
        // Navigate to the test page
        driver.get("https://bonigarcia.dev/selenium-webdriver-java/");

        // Access the Window interface via WebDriver.Options
        WebDriver.Window window = driver.manage().window();

        // Get initial position and size of the browser window
        Point initialPosition = window.getPosition();
        Dimension initialSize = window.getSize();
        System.out.println("Initial Position: " + initialPosition);
        System.out.println("Initial Size: " + initialSize);

        // Maximize the browser window
        window.maximize();

        // Get position and size after maximizing
        Point maximizedPosition = window.getPosition();
        Dimension maximizedSize = window.getSize();
        System.out.println("Maximized Position: " + maximizedPosition);
        System.out.println("Maximized Size: " + maximizedSize);

        // Assert that the position has changed (e.g., likely moved to (0,0))
        Assert.assertNotEquals(initialPosition, maximizedPosition,
                "Position should change after maximizing the window");

        // Assert that the size has changed (e.g., increased to screen size)
        Assert.assertNotEquals(initialSize, maximizedSize,
                "Size should change after maximizing the window");

        // Optional: Verify the maximized size is close to the screen size
        // Note: Screen size depends on the system; this is a basic check
        Dimension screenSize = driver.manage().window().getSize();
        Assert.assertTrue(maximizedSize.getWidth() >= initialSize.getWidth(),
                "Maximized width should be greater than or equal to initial width");
        Assert.assertTrue(maximizedSize.getHeight() >= initialSize.getHeight(),
                "Maximized height should be greater than or equal to initial height");
    }
}
