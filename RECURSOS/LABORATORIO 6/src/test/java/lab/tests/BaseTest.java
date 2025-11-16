package lab.tests;

import lab.server.StaticFileServer;
import io.github.bonigarcia.wdm.WebDriverManager;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.testng.annotations.*;
import java.nio.file.Path;
import java.time.Duration;
public class BaseTest {
    protected WebDriver driver;
    protected static StaticFileServer fileServer;
    protected static final int SERVER_PORT = 8000;
    @BeforeSuite(alwaysRun = true)
    public void startServer() throws Exception {
        Path pages = Path.of("src", "test", "resources", "pages");
        fileServer = new StaticFileServer(SERVER_PORT, pages);
        fileServer.start();
    }
    @AfterSuite(alwaysRun = true)
    public void stopServer() {
        if (fileServer != null) fileServer.stop();
    }
    @BeforeClass
    public void setup() {
        WebDriverManager.chromedriver().setup(); // se encarga de descargar la versión exacta
        driver = new ChromeDriver();
        driver.manage().window().maximize();
    }
    @AfterClass
    public void tearDown() {
        if (driver != null) driver.quit();
    }
    protected String url(String page) {
        return "http://localhost:" + SERVER_PORT + "/" + page;
    }
}
