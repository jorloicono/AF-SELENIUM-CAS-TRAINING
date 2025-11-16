package ejemplosylabs.java.com.labs;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;


public class Lab6_Ejemplo1 {
    public static void main(String[] args) {

        System.setProperty("web-driver.chrome.driver","./drivers/chromedriver.exe");

        WebDriver driver = new ChromeDriver();

        try{

            driver.get("http://google.com");

            System.out.println(driver.getTitle());

            Thread.sleep(5000);


        }catch (Exception e){
            e.printStackTrace();
        } finally {
            driver.quit();
        }

    }
}
