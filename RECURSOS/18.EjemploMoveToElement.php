<?php
/**
 * Script de Automatización: Actions - Move To Element (Hover)
 * Adaptación de Clase18_EjemploMoveToElement (Java) a PHP
 *
 * Este script:
 * 1. Inicia ChromeDriver con opciones optimizadas
 * 2. Navega a https://www.saucedemo.com/ y hace login
 * 3. Maximiza la ventana y configura espera implícita
 * 4. Usa Actions para hacer HOVER sobre el dropdown de ordenación
 * 5. Verifica que la opción "Z to A" sea visible tras el hover
 * 6. Pausa para observar el resultado
 * 7. Cierra el navegador
 *
 * Concepto: moveToElement() simula el movimiento del mouse hacia un elemento
 * (hover), activando menús desplegables, tooltips, etc.
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Interactions\WebDriverActions;

class Clase18_EjemploMoveToElement
{
    private $driver;
    private $chromeDriverProcess;

    public function __construct()
    {
        echo "\n===== EJEMPLO ACTIONS - MOVE TO ELEMENT (HOVER) (PHP) =====\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }

    private function iniciarChromeDriver()
    {
        echo "[1/6] Iniciando ChromeDriver en puerto " . CHROMEDRIVER_PORT . "...\n";

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
        echo "[2/6] Conectando con Selenium WebDriver...\n";

        try {
            // Configurar ChromeOptions como en el ejemplo Java
            $options = new ChromeOptions();
            $options->addArguments(["--no-sandbox", "--disable-dev-shm-usage"]);

            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

            $this->driver = RemoteWebDriver::create(
                CHROMEDRIVER_HOST,
                $capabilities,
                5000
            );
            echo "   ✓ Conexión establecida con ChromeOptions\n\n";
        } catch (\Exception $e) {
            $this->detenerChromeDriver();
            throw new \Exception("Error de conexión: " . $e->getMessage());
        }
    }

    private function loginSaucedemo()
    {
        echo "[3/6] Navegando a Saucedemo y haciendo login...\n";

        $this->driver->get("https://www.saucedemo.com/");

        // Maximizar ventana
        $this->driver->manage()->window()->maximize();
        echo "   ✓ Ventana maximizada\n";

        // Espera implícita de 5 segundos
        $this->driver->manage()->timeouts()->implicitlyWait(5);
        echo "   ✓ Espera implícita configurada (5s)\n";

        // Login
        $usernameInput = $this->driver->findElement(WebDriverBy::id("user-name"));
        $usernameInput->sendKeys("standard_user");
        echo "   ✓ Usuario ingresado\n";

        $passwordInput = $this->driver->findElement(WebDriverBy::id("password"));
        $passwordInput->sendKeys("secret_sauce");
        echo "   ✓ Password ingresado\n";

        $loginButton = $this->driver->findElement(WebDriverBy::id("login-button"));
        $loginButton->click();
        echo "   ✓ Login realizado\n\n";
    }

    private function hoverSortDropdown()
    {
        echo "[4/6] Haciendo HOVER sobre el dropdown de ordenación...\n";

        // Buscar dropdown de ordenación
        $sortDropdown = $this->driver->findElement(WebDriverBy::className("product_sort_container"));
        echo "   ✓ Dropdown de ordenación encontrado\n";

        // Crear Actions y hacer hover (moveToElement)
        $actions = new WebDriverActions($this->driver);
        $actions->moveToElement($sortDropdown)->perform();
        echo "   ✓ Hover realizado con Actions API\n\n";
        echo "   Mensaje: Hovered over sort dropdown.\n\n";
    }

    private function verificarOpcionVisible()
    {
        echo "[5/6] Verificando que la opción 'Z to A' sea visible...\n";

        try {
            $optionZa = $this->driver->findElement(WebDriverBy::cssSelector("option[value='za']"));
            $isVisible = $optionZa->isDisplayed();

            echo "   ═════════════════════════════════════════\n";
            echo "   VERIFICACIÓN:\n";
            echo "   ═════════════════════════════════════════\n";
            echo "   ¿Es visible la opción 'Z to A'? $isVisible\n";
            echo "   ═════════════════════════════════════════\n\n";

        } catch (\Exception $e) {
            echo "   ✗ Opción 'Z to A' no encontrada o no visible\n";
            echo "   Mensaje: " . $e->getMessage() . "\n\n";
        }
    }

    private function pausaVisual()
    {
        echo "[6/6] Pausa visual para observar resultado (5 segundos)...\n";
        sleep(5);
        echo "   ✓ Pausa completada\n\n";
    }

    public function ejecutar()
    {
        try {
            $this->loginSaucedemo();
            $this->hoverSortDropdown();
            $this->verificarOpcionVisible();
            $this->pausaVisual();
        } catch (\Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n\n";
        } finally {
            $this->detener();
        }

        echo "===== PROCESO COMPLETADO =====\n\n";
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
    $script = new Clase18_EjemploMoveToElement();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
