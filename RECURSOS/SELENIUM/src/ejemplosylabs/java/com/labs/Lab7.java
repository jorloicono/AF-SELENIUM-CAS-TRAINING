package ejemplosylabs.java.com.labs;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.sikuli.script.FindFailed;
import org.sikuli.script.Pattern;
import org.sikuli.script.Screen;
import org.openqa.selenium.chrome.ChromeDriver;

public class Lab7 {
        public static void main(String[] args) throws FindFailed {
            System.setProperty("webdriver.chrome.driver", "./drivers/chromedriver.exe");

            String filepath = "C:\\Users\\Jorge\\Desktop\\AF-TESTING-AUTOMATIZADO-SELENIUM-CUCUMBER\\RECURSOS\\src\\test\\resources";
            String inputFilePath = "C:\\Users\\Jorge\\Desktop\\AF-TESTING-AUTOMATIZADO-SELENIUM-CUCUMBER\\RECURSOS\\src\\test\\resources";
            Screen s = new Screen();
            Pattern fileInputTextBox = new Pattern(filepath + "barrabusqueda.png");
            Pattern openButton = new Pattern(filepath + "botonabrir.png");
            WebDriver driver;
            // Open Chrome browser
            driver = new ChromeDriver();
            driver.get("https://demo.guru99.com/test/image_upload/index.php");
            // Click on Browse button and handle windows pop up using Sikuli
            driver.findElement(By.xpath(".//*[@id='photoimg']")).click();
            s.wait(fileInputTextBox, 20);
            s.type(fileInputTextBox, inputFilePath + "ALUMNOS CLOUDFTIC.txt");
            s.click(openButton);
            // Close the browser
            driver.close();
        }
    }

