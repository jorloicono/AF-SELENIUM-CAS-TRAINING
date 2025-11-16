package ejemplosylabs.java.com.ejemplos;

import org.junit.jupiter.api.AfterEach;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertTrue;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.openqa.selenium.support.ui.ExpectedConditions;

import java.time.Duration;

public class Clase2_CrearTestConAnotacionesJUnit {

    private WebDriver driver;
    //BeforeEach es una anotacion de JUnit que te permite definir que el metodo sobre el que se añade sea ejecutado
    // antes de cada test, en este caso concreto garantiza que para cada metodo de test se va a crear una nueva ventana deñ navegador
    @BeforeEach
    void setUp() {
        // Configurar el driver de Chrome (Asegúrate de tener el driver en la ruta correcta)
        System.setProperty("webdriver.chrome.driver", "./drivers/chromedriver.exe");

        // Inicializar el navegador
        driver = new ChromeDriver();
        driver.manage().window().maximize();
    }

    @Test
    void testWikipediaTitle() {
        // Navegar a Wikipedia
        driver.get("https://www.wikipedia.org");

        // Esperar hasta que el título sea visible
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
        wait.until(ExpectedConditions.titleContains("Wikipedia"));

        // Obtener el título de la página
        String titulo = driver.getTitle();

        // Verificar que el título es "Wikipedia"
        assertEquals("Wikipedia", titulo, "El título de la página no es el esperado.");
    }

    @Test
    void testWikipediaTitleContains() {
        // Navegar a Wikipedia
        driver.get("https://www.wikipedia.org");

        // Esperar hasta que el título sea visible
        WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
        wait.until(ExpectedConditions.titleContains("Wikipedia"));

        // Obtener el título de la página
        String titulo = driver.getTitle();

        // Verificar que el título es "Wikipedia"
        assertTrue(titulo.contains("Wikipedia"), "El título de la página no es el esperado.");
    }

    // AfterEach es una anotacion de JUnit que te permite definir que el metodo sobre el que se añade sea ejecutado
    // despues de cada test, en este caso concreto garantiza que al finalizar cada test se cierra la ventana en la que se ha ejecutado
    @AfterEach
    void tearDown() {
        if (driver != null) {
            driver.quit();
        }
    }
}

