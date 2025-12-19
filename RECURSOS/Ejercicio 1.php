<?php
/**
 * Script de Automatización: Ejercicio Completo de Formulario
 * Adaptación de Ejercicio1 (Java) a PHP
 *
 * Este script automatiza completamente un formulario HTML complejo:
 * 1. Inputs de texto y password
 * 2. Textarea
 * 3. Select/dropdown
 * 4. Datalist con confirmación
 * 5. Input file con verificación
 * 6. Checkboxes (desmarcar/marcar)
 * 7. Radio buttons con verificación de exclusividad
 * 8. Input date (formato YYYY-MM-DD)
 * 9. Submit y validación de redirección
 * 10. Verificación de datos en página de resultados
 * 11. Navegación de regreso
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;

class Ejercicio1_CompletoFormulario
{
    private $driver;
    private $chromeDriverProcess;

    // Datos para el test
    private $testName = "Usuario Automático";
    private $testPassword = "PasswordS3cur3#";
    private $datalistSelection = "Seattle";
    private $fileName = "C:\Users\Jorge\Desktop\AF-SELENIUM-CAS-TRAINING\RECURSOS\index.html";
    private $testDate = "2024-12-31"; // Formato 'yyyy-MM-dd' para input[type=date]

    public function __construct()
    {
        echo "\n===== EJERCICIO 1: FORMULARIO COMPLETO (PHP) =====\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }

    private function iniciarChromeDriver()
    {
        echo "[1/10] Iniciando ChromeDriver en puerto " . CHROMEDRIVER_PORT . "...\n";

        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"],
        ];

        $this->chromeDriverProcess = proc_open(
            CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT,
            $descriptorspec,
            $pipes
        );

        sleep(2);

        if (!is_resource($this->chromeDriverProcess)) {
            throw new \Exception("No se pudo iniciar ChromeDriver");
        }

        echo "   ✓ ChromeDriver iniciado correctamente\n\n";
    }

    private function conectarDriver()
    {
        echo "[2/10] Conectando con Selenium WebDriver...\n";

        try {
            $capabilities = DesiredCapabilities::chrome();
            $this->driver = RemoteWebDriver::create(
                CHROMEDRIVER_HOST,
                $capabilities,
                5000
            );
            echo "   ✓ Conexión establecida\n\n";
        } catch (\Exception $e) {
            $this->detenerChromeDriver();
            throw new \Exception("Error de conexión: " . $e->getMessage());
        }
    }

    private function navegarPagina()
    {
        echo "[3/10] Navegando a index.html...\n";

        // Ajusta la ruta a tu entorno
        $rutaLocalWindows = 'C:\Users\Jorge\Desktop\AF-SELENIUM-CAS-TRAINING\RECURSOS\index.html';
        $url = 'file:///' . str_replace('\\', '/', $rutaLocalWindows);

        echo "   URL: $url\n";
        $this->driver->get($url);
        echo "   ✓ Página cargada\n\n";
    }

    public function ejecutar()
    {
        try {
            $this->navegarPagina();

            // 1. Input texto
            echo "[4/10] Rellenando input texto (name)...\n";
            $textInput = $this->driver->findElement(WebDriverBy::id("name"));
            $textInput->sendKeys($this->testName);
            echo "   ✓ Nombre: '$this->testName'\n";

            // 2. Input password
            echo "[5/10] Rellenando input password...\n";
            $passwordInput = $this->driver->findElement(WebDriverBy::id("pwd"));
            $passwordInput->sendKeys($this->testPassword);
            echo "   ✓ Password establecido\n";

            // 3. Textarea
            echo "[6/10] Rellenando textarea...\n";
            $textArea = $this->driver->findElement(WebDriverBy::id("msg"));
            $textArea->sendKeys("Mensaje automatizado desde PHP.");
            echo "   ✓ Mensaje establecido\n";

            // 4. Select/Dropdown
            echo "[7/10] Seleccionando opción en dropdown...\n";
            $dropdown = $this->driver->findElement(WebDriverBy::id("sel"));
            $select = new \Facebook\WebDriver\WebDriverSelect($dropdown);
            $select->selectByIndex(1); // Selecciona "Opción 2"
            echo "   ✓ Opción 2 seleccionada\n";

            // 5. Datalist
            echo "[8/10] Seleccionando ciudad en datalist...\n";
            $datalistInput = $this->driver->findElement(WebDriverBy::id("city"));
            $datalistInput->sendKeys($this->datalistSelection);
            $datalistInput->sendKeys(WebDriverKeys::ENTER);
            echo "   ✓ Ciudad '$this->datalistSelection' seleccionada\n";

            // 6. Input file
            echo "[9/10] Subiendo archivo...\n";
            $absoluteFilePath = (new \SplFileInfo($this->fileName))->getRealPath();
            if (!file_exists($absoluteFilePath)) {
                echo "   ⚠ ADVERTENCIA: Archivo '$absoluteFilePath' no existe\n";
                echo "   Crea un archivo 'test-file.txt' en el directorio actual\n\n";
            }
            $fileInput = $this->driver->findElement(WebDriverBy::id("file"));
            $fileInput->sendKeys($absoluteFilePath);
            echo "   ✓ Ruta enviada: $absoluteFilePath\n";

            // Verificar estado del archivo
            $fileStatus = $this->driver->findElement(WebDriverBy::id("fileStatus"));
            if (!strpos($fileStatus->getText(), $this->fileName) !== false) {
                throw new \Exception("ERROR: No se verificó la carga de archivo.");
            }
            echo "   ✓ Archivo verificado en UI\n\n";

            // 7. Checkboxes
            echo "[10/10] Interactuando con checkboxes...\n";
            $cb1 = $this->driver->findElement(WebDriverBy::id("cb1"));
            $cb2 = $this->driver->findElement(WebDriverBy::id("cb2"));

            if ($cb1->isSelected()) {
                $cb1->click();
                echo "   ✓ cb1 desmarcado\n";
            }
            if (!$cb2->isSelected()) {
                $cb2->click();
                echo "   ✓ cb2 marcado\n";
            }

            // 8. Radio buttons
            $radioR2 = $this->driver->findElement(WebDriverBy::cssSelector("input[type='radio'][value='r2']"));
            $radioR2->click();
            echo "   ✓ Radio r2 seleccionado\n";

            // Verificar exclusividad
            $radioR1 = $this->driver->findElement(WebDriverBy::cssSelector("input[type='radio'][value='r1']"));
            $radioR1->click();
            if ($radioR2->isSelected()) {
                throw new \Exception("ERROR: El radio button r2 sigue marcado (falló exclusividad).");
            }
            echo "   ✓ Checkboxes y Radio Buttons verificados: OK\n\n";

            // 9. Input date
            echo "[11/11] Estableciendo fecha...\n";
            $dateInput = $this->driver->findElement(WebDriverBy::id("date"));
            $dateInput->sendKeys($this->testDate);

            if ($dateInput->getAttribute("value") !== $this->testDate) {
                throw new \Exception("ERROR: Fecha no establecida correctamente.");
            }
            echo "   ✓ Fecha '$this->testDate' ingresada: OK\n\n";

            // 10. Submit
            echo "[12/12] Enviando formulario...\n";
            $submitButton = $this->driver->findElement(WebDriverBy::id("submitBtn"));
            $submitButton->click();
            echo "   ✓ Formulario enviado\n\n";

            // 11. Verificar redirección
            $currentUrl = $this->driver->getCurrentUrl();
            if (!str_ends_with($currentUrl, "submitted-form.html")) {
                throw new \Exception("ERROR: Falló la redirección a submitted-form.html. URL actual: $currentUrl");
            }
            echo "   ✓ Redirección a 'submitted-form.html' verificada: OK\n\n";

            // 12. Verificar datos en página de resultados
            $dataTableText = $this->driver->findElement(WebDriverBy::id("dataTable"))->getText();
            if (
                !str_contains($dataTableText, $this->testName) ||
                !str_contains($dataTableText, $this->testDate) ||
                !str_contains($dataTableText, "r2")
            ) {
                throw new \Exception("ERROR: Los datos enviados no se verificaron correctamente.");
            }
            echo "   ✓ Datos en 'submitted-form.html' verificados: OK\n\n";

            // 13. Regreso a index
            echo "[13/13] Regresando a index.html...\n";
            $this->driver->findElement(WebDriverBy::linkText("Return to index"))->click();
            if (!str_ends_with($this->driver->getCurrentUrl(), "index.html")) {
                throw new \Exception("ERROR: Falló el regreso a index.html.");
            }
            echo "   ✓ Regreso a index.html exitoso: OK\n\n";

            echo "🎉 ===== TODOS LOS PASOS COMPLETADOS EXITOSAMENTE ===== 🎉\n\n";

        } catch (\Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n\n";
        } finally {
            $this->detener();
        }
    }

    private function detener()
    {
        echo "Finalizando...\n";
        if ($this->driver !== null) {
            $this->driver->quit();
            echo "   ✓ Navegador cerrado\n";
        }
        $this->detenerChromeDriver();
    }

    private function detenerChromeDriver()
    {
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
            echo "   ✓ ChromeDriver detenido\n";
        }
    }
}

try {
    $script = new Ejercicio1_CompletoFormulario();
    $script->ejecutar();
    echo "========== PROCESO COMPLETADO ==========\n\n";
} catch (\Exception $e) {
    echo "✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
