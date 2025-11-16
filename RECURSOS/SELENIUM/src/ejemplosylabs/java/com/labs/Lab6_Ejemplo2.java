package ejemplosylabs.java.com.labs;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.By;
import org.openqa.selenium.chrome.ChromeOptions;

import java.util.HashMap;
import java.util.Map;

public class Lab6_Ejemplo2 {
    public static void main(String[] args) {

        System.setProperty("web-driver.chrome.driver","./drivers/chromedriver.exe");

        ChromeOptions options = new ChromeOptions();

        // Evitar que aparezcan banners de cookies o pop-ups de privacidad
        options.addArguments("--disable-popup-blocking");       // bloquea pop-ups en general
        options.addArguments("--disable-notifications");        // bloquea notificaciones
        options.addArguments("--incognito");                    // modo incógnito evita cookies guardadas
        options.addArguments("--disable-extensions");           // deshabilita extensiones que puedan generar banners
        options.addArguments("--remote-allow-origins=*");       // necesario en Chrome 111+
        Map<String, Object> prefs = new HashMap<>();
        prefs.put("profile.default_content_setting_values.cookies", 2); // 2 = bloquear cookies
        prefs.put("profile.default_content_setting_values.notifications", 2);
        prefs.put("profile.default_content_setting_values.popups", 2);        // 2 = bloquear popups
        options.setExperimentalOption("prefs", prefs);

        // Iniciar Chrome con estas opciones
        WebDriver driver = new ChromeDriver(options);

        try{
            driver.get("http://facebook.com"); //URL in the browser

            driver.findElement(By.id("email")).sendKeys("micorreo@autentia.com");
            driver.findElement(By.id("pass")).sendKeys("mipassword");

            driver.findElement(By.name("login")).click();


        }catch (Exception e){
            e.printStackTrace();
        } finally {
            driver.quit();
        }

    }
}
