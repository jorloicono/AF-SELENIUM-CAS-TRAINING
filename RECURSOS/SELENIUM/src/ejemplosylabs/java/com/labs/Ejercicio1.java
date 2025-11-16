package ejemplosylabs.java.com.labs;

import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.support.ui.Select;

import java.io.File;


public class Ejercicio1 {
    public static void main(String[] args){

        System.setProperty("web-driver.chrome.driver","./drivers/chromedriver.exe");

        // Creo la instancia del navegador

        WebDriver driver = new ChromeDriver();

        // Navegamos a la web

        driver.get("C:\\Users\\Jorge\\Desktop\\AF-TESTING-AUTOMATIZADO-SELENIUM-CUCUMBER\\RECURSOS\\src\\test\\resources\\index.html");

        // Datos para el test
        String testName = "Usuario Automático";
        String testPassword = "PasswordS3cur3#";
        String datalistSelection = "Seattle";
        String fileName = "test-file.txt";
        String testDate = "2024-12-31"; // Formato 'yyyy-MM-dd' para input[type=date]

        try{

            WebElement textInput = driver.findElement(By.id("name"));
            textInput.sendKeys(testName);

            WebElement passwordInput = driver.findElement(By.id("pwd"));
            passwordInput.sendKeys(testPassword);

            WebElement textArea = driver.findElement(By.id("msg"));
            textArea.sendKeys("Mensaje automatizado desde Java.");

            WebElement dropdown = driver.findElement(By.id("sel"));
            Select select = new Select(dropdown);
            select.selectByIndex(1); // Selecciona "Opción 2"

            WebElement datalistInput = driver.findElement(By.id("city"));
            datalistInput.sendKeys(datalistSelection);
            datalistInput.sendKeys(Keys.ENTER); // Confirma la selección de la datalist

            String absoluteFilePath = new File(fileName).getAbsolutePath();
            WebElement fileInput = driver.findElement(By.id("file"));
            fileInput.sendKeys(absoluteFilePath);

            WebElement fileStatus = driver.findElement(By.id("fileStatus"));
            if (!fileStatus.getText().contains(fileName)) {
                throw new Exception("ERROR: No se verificó la carga de archivo.");
            }

            WebElement cb1 = driver.findElement(By.id("cb1"));
            WebElement cb2 = driver.findElement(By.id("cb2"));

            // Desmarca cb1 (si está marcado) y marca cb2
            if (cb1.isSelected()) cb1.click();
            if (!cb2.isSelected()) cb2.click();

            // Selecciona el segundo radio button (r2)
            WebElement radioR2 = driver.findElement(By.cssSelector("input[type='radio'][value='r2']"));
            radioR2.click();

            // Verificación: r1 debe estar deseleccionado
            WebElement radioR1 = driver.findElement(By.cssSelector("input[type='radio'][value='r1']"));
            if (radioR1.isSelected()) {
                throw new Exception("ERROR: El radio button r1 sigue marcado (falló exclusividad).");
            }
            System.out.println("5. Checkboxes y Radio Buttons interactuados y verificados: OK");

            WebElement dateInput = driver.findElement(By.id("date"));
            // Se envía el texto directamente en el formato YYYY-MM-DD
            dateInput.sendKeys(testDate);

            if (!dateInput.getAttribute("value").equals(testDate)) {
                throw new Exception("ERROR: Fecha no establecida correctamente.");
            }
            System.out.println("6. Fecha '" + testDate + "' ingresada: OK");

            WebElement submitButton = driver.findElement(By.id("submitBtn"));
            submitButton.click();

            // Valida la URL de destino
            String currentUrl = driver.getCurrentUrl();
            if (!currentUrl.endsWith("submitted-form.html")) {
                throw new Exception("ERROR: Falló la redirección a submitted-form.html. URL actual: " + currentUrl);
            }
            System.out.println("7a. Formulario enviado y redirección a 'submitted-form.html' verificada: OK");

            // Verifica los datos enviados en la nueva página
            String dataTableText = driver.findElement(By.id("dataTable")).getText();
            if (!dataTableText.contains(testName) || !dataTableText.contains(testDate) || !dataTableText.contains("r2")) {
                throw new Exception("ERROR: Los datos enviados no se verificaron correctamente.");
            }
            System.out.println("7b. Datos en 'submitted-form.html' verificados: OK");

            // Navega de regreso
            driver.findElement(By.linkText("Return to index")).click();
            if (!driver.getCurrentUrl().endsWith("index.html")) {
                throw new Exception("ERROR: Falló el regreso a index.html.");
            }
            System.out.println("7c. Regreso a index.html exitoso: OK");

        }catch (Exception e){
            e.printStackTrace();
        } finally {
            driver.quit();
        }

    }
}

