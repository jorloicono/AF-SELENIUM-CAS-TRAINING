package ejemplosylabs.java.com.labs;

import org.openqa.selenium.*;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.*;

import java.time.Duration;
import java.util.Arrays;
import java.util.NoSuchElementException;

public class Ejercicio2 {

    private WebDriver driver;

    // --- Setup ---
    public void setup() {

        driver = new ChromeDriver();
        driver.manage().window().maximize();
        // Espera implícita global (solo se aplica a los tests que la usan)
        driver.manage().timeouts().implicitlyWait(Duration.ofSeconds(10));
    }

    // --- TearDown ---
    public void teardown() {
        if (driver != null) {
            driver.quit();
        }
    }

    // ==========================
    // Parte 1 – Espera Implícita
    // ==========================
    public long testImplicitWait() {
        System.out.println("=== Parte 1: Espera Implícita ===");
        long start = System.currentTimeMillis();

        driver.get("https://the-internet.herokuapp.com/dynamic_controls");

        // Caso 1 - Eliminar elemento
        driver.findElement(By.xpath("//button[text()='Remove']")).click();
        WebElement mensaje = driver.findElement(By.id("message"));
        System.out.println("Texto mostrado: " + mensaje.getText());

        // Caso 2 - Agregar elemento
        driver.findElement(By.xpath("//button[text()='Add']")).click();
        WebElement checkbox = driver.findElement(By.cssSelector("#checkbox input"));
        if (checkbox.isDisplayed()) {
            checkbox.click();
            System.out.println("Checkbox marcado nuevamente.");
        }

        // Caso 3 - Campo de texto dinámico
        WebElement enableButton = driver.findElement(By.xpath("//button[text()='Enable']"));
        enableButton.click();

        WebElement input = driver.findElement(By.xpath("//input[@type='text']"));
        input.sendKeys("Prueba con espera implícita");
        System.out.println("Texto escrito en campo habilitado.");

        driver.findElement(By.xpath("//button[text()='Disable']")).click();
        System.out.println("Campo deshabilitado nuevamente.");

        long end = System.currentTimeMillis();
        long duration = (end - start);
        System.out.println("Tiempo total (implícita): " + duration + " ms\n");
        return duration;
    }

    // ==========================
    // 🟡 Parte 2 – Espera Explícita
    // ==========================
    public long testExplicitWait() {
        System.out.println("=== Parte 2: Espera Explícita ===");
        long start = System.currentTimeMillis();

        driver.get("https://the-internet.herokuapp.com/dynamic_loading/1");

        driver.findElement(By.cssSelector("#start button")).click();

        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(10));
        WebElement helloText = wait.until(ExpectedConditions.visibilityOfElementLocated(By.id("finish")));

        String texto = helloText.getText();
        if ("Hello World!".equals(texto)) {
            System.out.println("Texto encontrado correctamente: " + texto);
        } else {
            System.out.println("Texto inesperado: " + texto);
        }

        long end = System.currentTimeMillis();
        long duration = (end - start);
        System.out.println("Tiempo total (explícita): " + duration + " ms\n");
        return duration;
    }

    // ==========================
    // Parte 3 – Espera Fluida (Fluent Wait)
    // ==========================
    public long testFluentWait() {
        System.out.println("=== Parte 3: Espera Fluida ===");
        long start = System.currentTimeMillis();

        driver.get("https://the-internet.herokuapp.com/dynamic_loading/2");

        driver.findElement(By.cssSelector("#start button")).click();

        Wait<WebDriver> fluentWait = new FluentWait<>(driver)
                .withTimeout(Duration.ofSeconds(15))
                .pollingEvery(Duration.ofMillis(500))
                .ignoring(NoSuchElementException.class)
                .ignoring(StaleElementReferenceException.class);

        WebElement helloText = fluentWait.until(driver ->
                driver.findElement(By.id("finish"))
        );

        System.out.println("Texto encontrado (fluent wait): " + helloText.getText());

        long end = System.currentTimeMillis();
        long duration = (end - start);
        System.out.println("Tiempo total (fluida): " + duration + " ms\n");
        return duration;
    }

    // ==========================
    // ⚙️ Método reutilizable
    // ==========================
    public boolean waitForText(By locator, String text, int timeoutSeconds, String mode) {
        try {
            switch (mode.toLowerCase()) {
                case "implicit":
                    driver.findElement(locator);
                    return driver.findElement(locator).getText().equals(text);

                case "explicit":
                    WebDriverWait explicit = new WebDriverWait(driver, Duration.ofSeconds(timeoutSeconds));
                    return explicit.until(ExpectedConditions.textToBe(locator, text));

                case "fluent":
                    Wait<WebDriver> fluent = new FluentWait<>(driver)
                            .withTimeout(Duration.ofSeconds(timeoutSeconds))
                            .pollingEvery(Duration.ofMillis(500))
                            .ignoring(NoSuchElementException.class);
                    return fluent.until(d -> d.findElement(locator).getText().equals(text));

                default:
                    throw new IllegalArgumentException("Modo de espera no válido.");
            }
        } catch (TimeoutException e) {
            System.out.println("⚠️ Texto no encontrado en modo " + mode);
            return false;
        }
    }

    // ==========================
    // 🧩 MAIN
    // ==========================
    public static void main(String[] args) {
        Ejercicio2 test = new Ejercicio2();
        test.setup();

        long t1 = test.testImplicitWait();
        long t2 = test.testExplicitWait();
        long t3 = test.testFluentWait();

        test.teardown();

        System.out.println("====== COMPARATIVO DE TIEMPOS ======");
        System.out.println("Implícita: " + t1 + " ms");
        System.out.println("Explícita: " + t2 + " ms");
        System.out.println("Fluida:    " + t3 + " ms");

        long min = Math.min(t1, Math.min(t2, t3));
        if (min == t1) System.out.println("🏆 La espera implícita fue la más rápida.");
        else if (min == t2) System.out.println("🏆 La espera explícita fue la más rápida.");
        else System.out.println("🏆 La espera fluida fue la más rápida.");
    }
}

