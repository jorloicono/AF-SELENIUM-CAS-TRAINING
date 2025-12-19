<?php
/**
 * Script de Automatización: Actions - Click
 * Adaptación de Clase17_EjemploAccionClick (Java) a PHP
 *
 * Este script:
 * 1. Inicia ChromeDriver con opciones optimizadas
 * 2. Navega a https://www.saucedemo.com/ y hace login
 * 3. Maximiza la ventana y configura espera implícita
 * 4. Usa Actions para hacer clic en "Add to Cart" del Sauce Labs Backpack
 * 5. Espera explícita al badge del carrito
 * 6. Imprime el número de items en el carrito
 * 7. Cierra el navegador
 *
 * Concepto: Actions API permite realizar interacciones complejas como
 * clics avanzados, hover, drag & drop, etc.
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Support\WebDriverWait;
use Facebook\WebDriver\ExpectedCondition;

class Clase17_EjemploAccionClick
{
    private $driver;
    private $chromeDriverProcess;

    public function __construct()
    {
        echo "\n===== EJEMPLO ACTIONS - CLICK (PHP) =====\n\n";
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

    private function addToCartConActions()
    {
        echo "[4/6] Usando Actions para hacer clic en 'Add to Cart'...\n";

        // Buscar botón Add to Cart del Sauce Labs Backpack
        $addToCartButton = $this->driver->findElement(
            WebDriverBy::id("add-to-cart-sauce-labs-backpack")
        );
        echo "   ✓ Botón 'Add to Cart' encontrado\n";

        // Crear Actions y hacer clic
        $actions = new WebDriverActions($this->driver);
        $actions->click($addToCartButton)->perform();
        echo "   ✓ Clic realizado con Actions API\n\n";
        echo "   Mensaje: Clicked 'Add to Cart' button.\n\n";
    }

    private function esperarYVerificarCarrito()
    {
        echo "[5/6] Esperando badge del carrito con Explicit Wait...\n";

        $wait = new WebDriverWait($this->driver, 10);

        // Espera explícita al badge del carrito
        $cartBadge = $wait->until(
            ExpectedCondition::visibilityOfElementLocated(WebDriverBy::className("shopping_cart_badge"))
        );

        $itemsCount = $cartBadge->getText();
        echo "   ✓ Badge del carrito visible\n";
        echo "   ═════════════════════════════════════════\n";
        echo "   ITEMS EN CARRITO:\n";
        echo "   \"" . htmlspecialchars($itemsCount) . "\"\n";
        echo "   ═════════════════════════════════════════\n\n";
    }

    private function pausaVisual()
    {
        echo "[6/6] Pausa visual para observar resultado (2 segundos)...\n";
        sleep(2);
        echo "   ✓ Pausa completada\n\n";
    }

    public function ejecutar()
    {
        try {
            $this->loginSaucedemo();
            $this->addToCartConActions();
            $this->esperarYVerificarCarrito();
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
    $script = new Clase17_EjemploAccionClick();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
