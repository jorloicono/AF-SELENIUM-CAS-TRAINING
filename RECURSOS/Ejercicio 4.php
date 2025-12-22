<?php
/**
 * Automatización Selenium PHP – Tablas HTML
 * 
 * Incluye:
 *  EJERCICIO 1 → Lectura básica
 *  EJERCICIO 2 → Búsqueda por cliente
 *  EJERCICIO 3 → Conteo de filas y estados
 *  EJERCICIO 4 → Filtro por producto
 *  EJERCICIO 5 → Cálculos (suma, promedio, max, min)
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Automatizacion_TablaVentas
{
    private $driver;
    private $chromeDriverProcess;
    private $baseUrl = "C:\Users\Jorge\Desktop\AF-SELENIUM-CAS-TRAINING\RECURSOS\html";

    public function __construct()
    {
        echo "\n========== AUTOMATIZACIÓN TABLA DE VENTAS ==========\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }

    /* =====================================================
       SETUP
       ===================================================== */

    private function iniciarChromeDriver()
    {
        echo "[SETUP] Iniciando ChromeDriver...\n";

        $this->chromeDriverProcess = proc_open(
            CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT,
            [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["pipe", "w"]
            ],
            $pipes
        );

        sleep(2);

        if (!is_resource($this->chromeDriverProcess)) {
            throw new Exception("No se pudo iniciar ChromeDriver");
        }

        echo "   ✓ ChromeDriver iniciado\n\n";
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

    private function navegar()
    {
        $this->driver->get($this->baseUrl . "/sales-table.html");
        (new WebDriverWait($this->driver, 10))
            ->until(WebDriverExpectedCondition::visibilityOfElementLocated(
                WebDriverBy::id("salesTable")
            ));
    }

    private function obtenerFilas()
    {
        return $this->driver->findElements(
            WebDriverBy::cssSelector("table tbody tr")
        );
    }

    /* =====================================================
       EJERCICIO 1 – LECTURA BÁSICA
       ===================================================== */

    private function ejercicio1()
    {
        echo "===== EJERCICIO 1: Lectura Básica =====\n";

        $rows = $this->obtenerFilas();
        $cells = $rows[0]->findElements(WebDriverBy::cssSelector("td"));

        $headers = ["ID", "Cliente", "Producto", "Cantidad", "Precio", "Total", "Fecha", "Estado"];

        foreach ($cells as $i => $cell) {
            $value = trim($cell->getText());
            if (empty($value)) {
                throw new Exception("Campo vacío en columna {$headers[$i]}");
            }
            echo "{$headers[$i]}: $value\n";
        }
        echo "✓ Lectura correcta\n\n";
    }

    /* =====================================================
       EJERCICIO 2 – BÚSQUEDA
       ===================================================== */

    private function ejercicio2()
    {
        echo "===== EJERCICIO 2: Búsqueda por Cliente =====\n";

        $clienteBuscado = "María López";
        $rows = $this->obtenerFilas();
        $encontrado = false;

        foreach ($rows as $row) {
            $cells = $row->findElements(WebDriverBy::cssSelector("td"));
            if ($cells[1]->getText() === $clienteBuscado) {
                echo "Cliente encontrado:\n";
                foreach ($cells as $cell) {
                    echo "- " . $cell->getText() . "\n";
                }
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            throw new Exception("Cliente no encontrado");
        }

        echo "✓ Cliente verificado\n\n";
    }

    /* =====================================================
       EJERCICIO 3 – CONTEO
       ===================================================== */

    private function ejercicio3()
    {
        echo "===== EJERCICIO 3: Conteo =====\n";

        $rows = $this->obtenerFilas();
        if (count($rows) !== 10) {
            throw new Exception("Cantidad incorrecta de filas");
        }

        $contador = [
            "Completado" => 0,
            "Pendiente" => 0,
            "Cancelado" => 0
        ];

        foreach ($rows as $row) {
            $estado = trim(
                $row->findElements(WebDriverBy::cssSelector("td"))[7]->getText()
            );
            $contador[$estado]++;
        }

        foreach ($contador as $estado => $total) {
            echo "$estado: $total\n";
        }

        echo "✓ Conteo validado\n\n";
    }

    /* =====================================================
       EJERCICIO 4 – FILTRO
       ===================================================== */

    private function ejercicio4()
    {
        echo "===== EJERCICIO 4: Filtrado =====\n";

        $antes = count($this->obtenerFilas());

        $input = $this->driver->findElement(WebDriverBy::id("searchInput"));
        $input->clear();
        $input->sendKeys("Laptop");
        sleep(2);

        $filtradas = $this->obtenerFilas();
        $despues = count($filtradas);

        if ($despues >= $antes) {
            throw new Exception("Filtro no aplicado");
        }

        foreach ($filtradas as $row) {
            $producto = $row->findElements(WebDriverBy::cssSelector("td"))[2]->getText();
            if (strpos($producto, "Laptop") === false) {
                throw new Exception("Producto incorrecto tras filtro");
            }
        }

        echo "Filas antes: $antes\n";
        echo "Filas después: $despues\n";
        echo "✓ Filtro correcto\n\n";
    }

    /* =====================================================
       EJERCICIO 5 – CÁLCULOS
       ===================================================== */

    private function ejercicio5()
    {
        echo "===== EJERCICIO 5: Cálculos =====\n";

        $rows = $this->obtenerFilas();
        $totales = [];

        foreach ($rows as $row) {
            $texto = $row->findElements(WebDriverBy::cssSelector("td"))[5]->getText();
            $totales[] = (float) str_replace([',', '$'], '', $texto);
        }

        $suma = array_sum($totales);
        $promedio = $suma / count($totales);

        echo "Suma total: $" . number_format($suma, 2) . "\n";
        echo "Promedio: $" . number_format($promedio, 2) . "\n";
        echo "Máximo: $" . number_format(max($totales), 2) . "\n";
        echo "Mínimo: $" . number_format(min($totales), 2) . "\n";

        echo "✓ Cálculos correctos\n\n";
    }

    /* =====================================================
       EJECUCIÓN GENERAL
       ===================================================== */

    public function ejecutar()
    {
        try {
            $this->navegar();
            $this->ejercicio1();
            $this->ejercicio2();
            $this->ejercicio3();
            $this->ejercicio4();
            $this->ejercicio5();

            echo "🎉 TODOS LOS EJERCICIOS COMPLETADOS\n\n";

        } catch (Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n";
        } finally {
            $this->cerrar();
        }
    }

    private function cerrar()
    {
        if ($this->driver) {
            $this->driver->quit();
            echo "Navegador cerrado\n";
        }
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
            echo "ChromeDriver detenido\n";
        }
        echo "========== FIN ==========\n";
    }
}

/* =====================================================
   LANZADOR
   ===================================================== */

try {
    $script = new Automatizacion_TablaVentas();
    $script->ejecutar();
} catch (Exception $e) {
    echo "ERROR FATAL: " . $e->getMessage();
}
