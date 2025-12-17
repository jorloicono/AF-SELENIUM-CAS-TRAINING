<?php
/**
 * Script de Automatización: Tests de Wikipedia con Selenium
 * 
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Ejecuta tests similares a JUnit
 * 3. Valida título de Wikipedia
 * 4. Cierra navegador y ChromeDriver
 */


require_once('vendor/autoload.php');
require_once('selenium-config.php');


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;


class Clase2_CrearTestConAnotacionesJUnit
{
    private $driver;
    private $chromeDriverProcess;
    private $testsPasados = 0;
    private $testsFallidos = 0;


    public function __construct()
    {
        echo "\n========== TESTS UNITARIOS - WIKIPEDIA ==========\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }


    /**
     * Inicia ChromeDriver como proceso independiente
     * [Equivalente a @BeforeClass]
     */
    private function iniciarChromeDriver()
    {
        echo "[SETUP] Iniciando ChromeDriver en puerto " . CHROMEDRIVER_PORT . "...\n";


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


        // Esperar a que ChromeDriver esté listo
        sleep(2);


        if (!is_resource($this->chromeDriverProcess)) {
            throw new Exception("No se pudo iniciar ChromeDriver");
        }


        echo "   ✓ ChromeDriver iniciado correctamente\n\n";
    }


    /**
     * Conecta WebDriver con ChromeDriver
     */
    private function conectarDriver()
    {
        echo "[SETUP] Conectando con Selenium WebDriver...\n";


        try {
            $capabilities = DesiredCapabilities::chrome();
            $this->driver = RemoteWebDriver::create(
                CHROMEDRIVER_HOST,
                $capabilities,
                5000 // timeout de 5 segundos
            );

            // Maximizar ventana (equivalente a driver.manage().window().maximize())
            $this->driver->manage()->window()->maximize();

            echo "   ✓ Conexión establecida con éxito\n\n";
        } catch (Exception $e) {
            $this->detenerChromeDriver();
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }


    /**
     * @BeforeEach - Se ejecuta antes de cada test
     */
    private function setUp()
    {
        // El driver ya está inicializado en el constructor
        // Este método se mantiene para claridad y estructura similar a JUnit
    }


    /**
     * Test 1: testWikipediaTitle
     * Verifica que el título sea exactamente "Wikipedia"
     */
    public function testWikipediaTitle()
    {
        $testName = "testWikipediaTitle";
        echo "[TEST] Ejecutando: $testName\n";


        try {
            // Navegar a Wikipedia
            $this->driver->get('https://www.wikipedia.org');


            // Esperar hasta que el título sea visible
            $wait = new WebDriverWait($this->driver, 5);
            $wait->until(
                WebDriverExpectedCondition::titleContains('Wikipedia')
            );


            // Obtener el título de la página
            $titulo = $this->driver->getTitle();


            // Verificar que el título es "Wikipedia"
            $this->assertEquals(
                "Wikipedia",
                $titulo,
                "El título de la página no es el esperado."
            );


            echo "   ✓ TEST PASADO\n\n";
            $this->testsPasados++;


        } catch (Exception $e) {
            echo "   ✗ TEST FALLIDO: " . $e->getMessage() . "\n\n";
            $this->testsFallidos++;
        }
    }


    /**
     * Test 2: testWikipediaTitleContains
     * Verifica que el título contiene "Wikipedia"
     */
    public function testWikipediaTitleContains()
    {
        $testName = "testWikipediaTitleContains";
        echo "[TEST] Ejecutando: $testName\n";


        try {
            // Navegar a Wikipedia
            $this->driver->get('https://www.wikipedia.org');


            // Esperar hasta que el título sea visible
            $wait = new WebDriverWait($this->driver, 5);
            $wait->until(
                WebDriverExpectedCondition::titleContains('Wikipedia')
            );


            // Obtener el título de la página
            $titulo = $this->driver->getTitle();


            // Verificar que el título contiene "Wikipedia"
            $this->assertTrue(
                strpos($titulo, 'Wikipedia') !== false,
                "El título de la página no es el esperado."
            );


            echo "   ✓ TEST PASADO\n\n";
            $this->testsPasados++;


        } catch (Exception $e) {
            echo "   ✗ TEST FALLIDO: " . $e->getMessage() . "\n\n";
            $this->testsFallidos++;
        }
    }


    /**
     * @AfterEach - Se ejecuta después de cada test
     */
    private function tearDown()
    {
        // En nuestro caso, mantenemos el driver activo entre tests
        // Pero podrías cerrar y reabrir aquí si lo necesitas
    }


    /**
     * Equivalente a assertEquals de JUnit
     */
    private function assertEquals($esperado, $actual, $mensaje = "")
    {
        if ($esperado !== $actual) {
            throw new Exception(
                $mensaje . " [Esperado: '$esperado', Actual: '$actual']"
            );
        }
    }


    /**
     * Equivalente a assertTrue de JUnit
     */
    private function assertTrue($condicion, $mensaje = "")
    {
        if (!$condicion) {
            throw new Exception($mensaje . " [Condición no cumplida]");
        }
    }


    /**
     * Ejecuta todos los tests
     */
    public function ejecutarTodos()
    {
        try {
            $this->testWikipediaTitle();
            $this->testWikipediaTitleContains();
        } finally {
            $this->detener();
            $this->mostrarReporte();
        }
    }


    /**
     * Muestra el reporte final de tests
     */
    private function mostrarReporte()
    {
        echo "\n========== REPORTE DE TESTS ==========\n";
        echo "   Tests Pasados:  " . $this->testsPasados . " ✓\n";
        echo "   Tests Fallidos: " . $this->testsFallidos . " ✗\n";
        echo "   Total:          " . ($this->testsPasados + $this->testsFallidos) . "\n";
        echo "=====================================\n\n";


        if ($this->testsFallidos === 0) {
            echo "   ✓ TODOS LOS TESTS PASARON EXITOSAMENTE\n\n";
        } else {
            echo "   ✗ ALGUNOS TESTS FALLARON\n\n";
        }
    }


    /**
     * Cierra navegador y ChromeDriver
     */
    private function detener()
    {
        echo "\n[TEARDOWN] Finalizando...\n";


        if ($this->driver !== null) {
            try {
                $this->driver->quit();
                echo "   ✓ Navegador cerrado\n";
            } catch (Exception $e) {
                echo "   ⚠ Error al cerrar navegador: " . $e->getMessage() . "\n";
            }
        }


        $this->detenerChromeDriver();
    }


    /**
     * Detiene el proceso de ChromeDriver
     */
    private function detenerChromeDriver()
    {
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
            echo "   ✓ ChromeDriver detenido\n";
        }
    }
}


// Ejecutar los tests
try {
    $testSuite = new Clase2_CrearTestConAnotacionesJUnit();
    $testSuite->ejecutarTodos();
} catch (Exception $e) {
    echo "\n✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
?>