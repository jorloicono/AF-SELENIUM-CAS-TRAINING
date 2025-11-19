package ejemplosylabs.java.com.labs;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.FluentWait;
import org.openqa.selenium.support.ui.Wait;
import org.openqa.selenium.support.ui.WebDriverWait;

import java.time.Duration;
import java.util.NoSuchElementException;

public class Ejercicio2 {

    public static void main(String[] args) {

        // 1️⃣ Configuración inicial
        WebDriver driver = new ChromeDriver();
        driver.manage().window().maximize();

        // Abre tu archivo local o URL del HTML
        driver.get("C:\\Users\\Jorge\\Desktop\\AF-SELENIUM-CAS-TRAINING\\RECURSOS\\SELENIUM\\src\\ejemplosylabs\\resources\\EJERCICIO2.html"); // <-- cambia la ruta

        // Espera explícita
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(10));

        // ------------------------------
        // 2️⃣ Botón que se habilita dinámicamente
        WebElement enableBtn = driver.findElement(By.id("enable-button"));
        wait.until(ExpectedConditions.elementToBeClickable(enableBtn));
        enableBtn.click();
        System.out.println("Botón habilitado y clickeado.");

        // ------------------------------
        // 3️⃣ Elemento AJAX dinámico
        WebElement addBtn = driver.findElement(By.id("adder"));
        addBtn.click();

        WebElement redBox = wait.until(
                ExpectedConditions.visibilityOfElementLocated(By.id("redbox"))
        );
        System.out.println("Elemento AJAX visible: " + redBox.getAttribute("id"));

        // ------------------------------
        // 4️⃣ Crear y cambiar a iframe dinámico
        WebElement frameBtn = driver.findElement(By.id("frame-button"));
        frameBtn.click();

        wait.until(ExpectedConditions.frameToBeAvailableAndSwitchToIt(By.id("my-frame")));
        WebElement frameText = wait.until(
                ExpectedConditions.visibilityOfElementLocated(By.id("inside-frame"))
        );
        System.out.println("Texto dentro del iframe: " + frameText.getText());

        // Volver al DOM principal
        driver.switchTo().defaultContent();

        // ------------------------------
        // 5️⃣ Fluent Wait para cambio de color
        WebElement colorBox = driver.findElement(By.id("colorbox"));

        Wait<WebDriver> fluentWait = new FluentWait<>(driver)
                .withTimeout(Duration.ofSeconds(20))
                .pollingEvery(Duration.ofSeconds(1))
                .ignoring(NoSuchElementException.class);

        fluentWait.until(d -> {
            String color = colorBox.getCssValue("background-color");
            System.out.println("Esperando cambio de color... actual: " + color);
            return color.contains("0, 128, 0"); // verde en RGB
        });

        System.out.println("El color cambió a verde.");

        // ------------------------------
        // 6️⃣ Validar texto final
        WebElement finishText = wait.until(
                ExpectedConditions.visibilityOfElementLocated(By.id("finish"))
        );
        System.out.println("Texto final: " + finishText.getText());

        // ------------------------------
        // Cerrar navegador
        driver.quit();
    }
}
