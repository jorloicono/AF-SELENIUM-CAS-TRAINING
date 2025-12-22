<?php
/**
 * Script de Automatización: Rellenar Formulario Completo
 * Puerto de Java a PHP
 * 
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Navega al formulario de Selenium
 * 3. Rellena text input, password, dropdown, datalist
 * 4. Sube archivo y envía formulario
 * 5. Cierra navegador
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Clase7_RellenarUnFormulario
{
    private $driver;
    private $chromeDriverProcess;

    public function __construct()
    {
        echo "\n========== RELLENAR FORMULARIO COMPLETO ==========\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }

    private function iniciarChromeDriver()
    {
        echo "[1/8] Iniciando ChromeDriver...\n";
        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );
        $this->chromeDriverProcess = proc_open(
            CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT,
            $descriptorspec,
            $pipes
        );
        sleep(2);
        echo "   ✓ ChromeDriver listo\n\n";
    }

    private function conectarDriver()
    {
        echo "[2/8] Conectando WebDriver...\n";
        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create(CHROMEDRIVER_HOST, $capabilities, 5000);
        echo "   ✓ Driver conectado\n\n";
    }

    private function navegarFormulario()
    {
        echo "[3/8] Navegando a formulario Selenium...\n";
        $this->driver->get('https://www.selenium.dev/selenium/web/web-form.html');
        echo "   ✓ Formulario cargado\n\n";
    }

    private function esperar($milisegundos)
    {
        echo "[ESPERA] $milisegundos ms...\n";
        usleep($milisegundos * 1000);
    }

    private function rellenarTextInput()
    {
        echo "[4/8] Rellenando text input...\n";
        $textInput = $this->driver->findElement(WebDriverBy::id('my-text-id'));
        $textInput->sendKeys('Texto de prueba');
        echo "   ✓ 'Texto de prueba' ingresado\n";
        $this->esperar(5000);
    }

    private function rellenarPassword()
    {
        echo "[5/8] Rellenando password...\n";
        $passwordInput = $this->driver->findElement(WebDriverBy::name('my-password'));
        $passwordInput->sendKeys('MiPassword');
        echo "   ✓ Password ingresado\n";
        $this->esperar(5000);
    }

    private function seleccionarDropdown()
    {
        echo "[6/8] Seleccionando dropdown...\n";
        $dropdown = $this->driver->findElement(WebDriverBy::name('my-select'));

        // Nota: PHP WebDriver no tiene clase Select directa como Java
        // Usamos click + selección por value via JavaScript
        $dropdown->click();
        $this->driver->executeScript("arguments[0].value = '2';", [$dropdown]);
        echo "   ✓ Opción '2' seleccionada\n";
        $this->esperar(5000);
    }

    private function rellenarDatalist()
    {
        echo "[7/8] Rellenando datalist...\n";
        $datalist = $this->driver->findElement(WebDriverBy::name('my-datalist'));
        $datalist->sendKeys('Seattle');
        echo "   ✓ 'Seattle' ingresado\n";
        $this->esperar(5000);
    }

    private function subirArchivo()
    {
        echo "[8/8] Subiendo archivo...\n";
        $fileInput = $this->driver->findElement(WebDriverBy::name('my-file'));
        // Ruta absoluta al chromedriver (ajusta según tu sistema)
        $filePath = realpath('./drivers/chromedriver.exe') ?: './drivers/chromedriver.exe';
        $fileInput->sendKeys($filePath);
        echo "   ✓ Archivo subido\n";
        $this->esperar(5000);
    }

    private function enviarFormulario()
    {
        echo "[9/8] Enviando formulario...\n";
        $submitButton = $this->driver->findElement(WebDriverBy::cssSelector("button[type='submit']"));
        $submitButton->click();
        echo "   ✓ Formulario enviado\n";
        $this->esperar(5000);
    }

    public function ejecutar()
    {
        try {
            $this->navegarFormulario();
            $this->rellenarTextInput();
            $this->rellenarPassword();
            $this->seleccionarDropdown();
            $this->rellenarDatalist();
            $this->subirArchivo();
            $this->enviarFormulario();
        } catch (Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n\n";
        } finally {
            $this->detener();
        }
    }

    private function detener()
    {
        echo "Finalizando...\n";
        if ($this->driver)
            $this->driver->quit();
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
        }
        echo "   ✓ Todo cerrado\n\n";
    }
}

try {
    $form = new Clase7_RellenarUnFormulario();
    $form->ejecutar();
    echo "========== FORMULARIO COMPLETADO ==========\n\n";
} catch (Exception $e) {
    echo "✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
?>