<?php
/**
 * Selenium PHP – Tabs & Accordion Challenge
 * Optimizado para emojis y caracteres especiales
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class TabsAccordionChallenge
{
    private $driver;
    private $chromeDriverProcess;
    private $baseUrl;

    public function __construct($htmlPath)
    {
        echo "\n===== Tabs & Accordion Challenge =====\n";
        $this->baseUrl = "file://" . realpath($htmlPath);
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }

    /* ------------------- Setup ------------------- */
    private function iniciarChromeDriver()
    {
        echo "[SETUP] Iniciando ChromeDriver...\n";
        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];

        $this->chromeDriverProcess = proc_open(
            CHROMEDRIVER_PATH . " --port=" . CHROMEDRIVER_PORT,
            $descriptorspec,
            $pipes
        );

        sleep(2);

        if (!is_resource($this->chromeDriverProcess)) {
            throw new Exception("No se pudo iniciar ChromeDriver");
        }
        echo "   ✓ ChromeDriver iniciado\n";
    }

    private function conectarDriver()
    {
        $this->driver = RemoteWebDriver::create(
            CHROMEDRIVER_HOST,
            DesiredCapabilities::chrome(),
            5000
        );
        echo "   ✓ WebDriver conectado\n\n";
    }

    private function esperar($milisegundos)
    {
        usleep($milisegundos * 1000);
    }

    /* ------------------- Screenshots ------------------- */
    private function screenshot($filename)
    {
        $this->driver->takeScreenshot($filename);
        echo "   ✓ Screenshot guardado: $filename\n";
    }

    /* ------------------- Tabs ------------------- */
    public function testTabs()
    {
        echo "[TABS] Navegando HTML local...\n";
        $this->driver->get($this->baseUrl);
        $wait = new WebDriverWait($this->driver, 5);

        // Tab Products
        // Activar tab Products
        $productsTab = $this->driver->findElement(WebDriverBy::xpath("//button[contains(text(),'Products')]"));
        $productsTab->click();
        $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("products")));

        // Validar texto del accordion
        $accordionText = $this->driver->findElement(
            WebDriverBy::xpath("//div[@id='products']//p[contains(text(),'Interactúa con el')]")
        )->getText();
        echo "   ✓ Texto Products validado: $accordionText\n";


        // Tab Home
        $homeTab = $this->driver->findElement(WebDriverBy::xpath("//button[contains(text(),'Home')]"));
        $homeTab->click();
        $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("home")));
        $homeText = $this->driver->findElement(WebDriverBy::xpath("//div[@id='home']//h2"))->getText();
        if ($homeText !== "Bienvenido a la práctica")
            throw new Exception("Home text inválido");
        echo "   ✓ Tab Home validada\n";

        // Tab Contact
        $contactTab = $this->driver->findElement(WebDriverBy::xpath("//button[contains(text(),'Contact')]"));
        $contactTab->click();
        $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("contact")));
        $email = $this->driver->findElement(WebDriverBy::xpath("//div[@id='contact']//strong[contains(text(),'info@testing.com')]"))->getText();
        if ($email !== "info@testing.com")
            throw new Exception("Email Contact inválido");
        echo "   ✓ Tab Contact validada\n\n";
    }

    /* ------------------- Accordion ------------------- */
    public function testAccordion()
    {
        echo "[ACCORDION] Expandir panels en Products...\n";

        $productsTab = $this->driver->findElement(WebDriverBy::xpath("//button[contains(text(),'Products')]"));
        $productsTab->click();
        $wait = new WebDriverWait($this->driver, 5);
        $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("products")));

        // Expand Smartphone Pro
        $smartphoneHeader = $this->driver->findElement(WebDriverBy::xpath("//div[contains(@class,'accordion-header') and contains(text(),'Smartphone Pro')]"));
        $smartphoneHeader->click();
        $this->esperar(500);
        $smartphoneBody = $this->driver->findElement(WebDriverBy::id("panel1"));
        if (!str_contains($smartphoneBody->getAttribute("class"), "active"))
            throw new Exception("Panel Smartphone Pro no expandido");
        echo "   ✓ Smartphone Pro expandido\n";

        // Expand Laptop Ultra
        $laptopHeader = $this->driver->findElement(WebDriverBy::xpath("//div[contains(@class,'accordion-header') and contains(text(),'Laptop Ultra')]"));
        $laptopHeader->click();
        $this->esperar(500);
        $laptopBody = $this->driver->findElement(WebDriverBy::id("panel2"));
        if (!str_contains($laptopBody->getAttribute("class"), "active"))
            throw new Exception("Panel Laptop Ultra no expandido");
        echo "   ✓ Laptop Ultra expandido\n";

        // No expandir Wireless Pro
        $wirelessBody = $this->driver->findElement(WebDriverBy::id("panel3"));
        if (str_contains($wirelessBody->getAttribute("class"), "active"))
            throw new Exception("Panel Wireless Pro está expandido (no debería)");

        // Validar status panels
        $statusText = $this->driver->findElement(WebDriverBy::id("accordion-status"))->getText();
        if ($statusText !== "Panels expandidos: 2/3")
            throw new Exception("Status panels inválido: $statusText");
        echo "   ✓ Status panels validado: $statusText\n";

        $this->screenshot("products_two_panels.png");

        // Reset
        $resetBtn = $this->driver->findElement(WebDriverBy::xpath("//button[contains(text(),'Reset Todo')]"));
        $resetBtn->click();
        $this->esperar(500);
        $statusText2 = $this->driver->findElement(WebDriverBy::id("accordion-status"))->getText();
        if ($statusText2 !== "Panels expandidos: 0/3")
            throw new Exception("Reset panels falló");
        echo "   ✓ Reset realizado\n";

        $this->screenshot("reset_final.png");
    }

    /* ------------------- Preguntas ------------------- */
    public function resolverPreguntas()
    {
        echo "[PREGUNTAS] Respuestas:\n";

        echo "Pregunta 1: XPath exacto header 'Laptop Ultra' => //div[@onclick='toggleAccordion(2)']\n";
        echo "Pregunta 2: \$wait->until(WebDriverExpectedCondition::textToBe(WebDriverBy::id('accordion-status'),'2/3'));\n";
        echo "Pregunta 3: \$activeHeaders = \$driver->findElements(WebDriverBy::cssSelector('.accordion-header.active'));\n";
        echo "            assert(count(\$activeHeaders) === 2);\n";
    }

    /* ------------------- Cierre ------------------- */
    public function cerrar()
    {
        if ($this->driver)
            $this->driver->quit();
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
        }
        echo "\n===== FIN CHALLENGE =====\n";
    }
}

/* ------------------- EJECUTAR ------------------- */
try {
    $challenge = new TabsAccordionChallenge("C:\\Users\\Jorge\\Desktop\\AF-SELENIUM-CAS-TRAINING\\RECURSOS\\html\\tabs-accordion-test.html");
    $challenge->testTabs();
    $challenge->testAccordion();
    $challenge->resolverPreguntas();
    $challenge->cerrar();
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}
?>