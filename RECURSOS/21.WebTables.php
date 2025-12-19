<?php
/**
 * Script de Automatización: WEBTABLES - Manejo Completo (SIN PHPUnit)
 *
 * Página:
 * https://the-internet.herokuapp.com/tables
 *
 * Acciones:
 * 1. Localizar tabla
 * 2. Contar filas y columnas
 * 3. Leer celdas específicas
 * 4. Buscar un valor dentro de la tabla
 * 5. Validar datos
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Clase19_WebTables
{
    private $driver;
    private $wait;
    private $chromeDriverProcess;

    private const TIMEOUT_SECONDS = 10;
    private const URL = "https://the-internet.herokuapp.com/tables";
    private const VALOR_BUSCADO = "Doe";

    public function ejecutar()
    {
        echo "\n===== INICIO SCRIPT WEBTABLES =====\n";

        try {
            $this->iniciarChromeDriver();
            $this->conectarDriver();

            $this->wait = new WebDriverWait($this->driver, self::TIMEOUT_SECONDS);

            $this->navegarPagina();
            $this->procesarTabla();

            echo "═════════════════════════════════════\n";
            echo "✓ RESULTADO FINAL: WebTable procesada correctamente\n";
            echo "═════════════════════════════════════\n\n";

        } catch (\Exception $e) {
            echo "✗ ERROR DETECTADO\n";
            echo "Mensaje: " . $e->getMessage() . "\n\n";
        } finally {
            $this->detener();
            echo "===== FIN DEL SCRIPT =====\n\n";
        }
    }

    /* ================= PASO 1 ================= */
    private function navegarPagina()
    {
        echo "[3/6] Navegando a la página...\n";
        $this->driver->get(self::URL);

        echo "✓ Página cargada y validada\n\n";
    }

    /* ================= PASO 2 ================= */
    private function procesarTabla()
    {
        echo "[4/6] Localizando tabla...\n";

        $tabla = $this->wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::id("table1")
            )
        );

        echo "✓ Tabla localizada (table1)\n\n";

        $this->contarFilasColumnas($tabla);
        $this->leerCeldaEspecifica($tabla);
        $this->buscarValorEnTabla($tabla);
    }

    /* ================= FILAS / COLUMNAS ================= */
    private function contarFilasColumnas($tabla)
    {
        echo "[5/6] Contando filas y columnas...\n";

        $filas = $tabla->findElements(WebDriverBy::xpath(".//tbody/tr"));
        $columnas = $tabla->findElements(WebDriverBy::xpath(".//thead/tr/th"));

        echo "Total filas: " . count($filas) . "\n";
        echo "Total columnas: " . count($columnas) . "\n";

        if (count($filas) > 0 && count($columnas) > 0) {
            echo "✓ Tabla válida\n\n";
        } else {
            throw new \Exception("La tabla no contiene datos");
        }
    }

    /* ================= LEER CELDA ================= */
    private function leerCeldaEspecifica($tabla)
    {
        echo "[6/6] Leyendo celda específica (Fila 1, Columna 2)...\n";

        $celda = $tabla->findElement(
            WebDriverBy::xpath(".//tbody/tr[1]/td[2]")
        );

        echo "Valor encontrado: \"" . $celda->getText() . "\"\n\n";
    }

    /* ================= BUSCAR VALOR ================= */
    private function buscarValorEnTabla($tabla)
    {
        echo "Buscando valor \"" . self::VALOR_BUSCADO . "\" en la tabla...\n";

        $filas = $tabla->findElements(WebDriverBy::xpath(".//tbody/tr"));
        $encontrado = false;

        foreach ($filas as $index => $fila) {
            $celdas = $fila->findElements(WebDriverBy::tagName("td"));

            foreach ($celdas as $celda) {
                if (strpos($celda->getText(), self::VALOR_BUSCADO) !== false) {
                    echo "✓ Valor encontrado en fila " . ($index + 1) . "\n";
                    $encontrado = true;
                    break 2;
                }
            }
        }

        if (!$encontrado) {
            throw new \Exception("Valor no encontrado en la tabla");
        }

        echo "\n";
    }

    /* ================= SETUP ================= */
    private function iniciarChromeDriver()
    {
        echo "[1/6] Iniciando ChromeDriver...\n";

        $this->chromeDriverProcess = proc_open(
            CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT,
            [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["pipe", "w"],
            ],
            $pipes
        );

        sleep(2);

        if (!is_resource($this->chromeDriverProcess)) {
            throw new \Exception("No se pudo iniciar ChromeDriver");
        }

        echo "✓ ChromeDriver iniciado\n";
    }

    private function conectarDriver()
    {
        echo "[2/6] Conectando con WebDriver...\n";

        $this->driver = RemoteWebDriver::create(
            CHROMEDRIVER_HOST,
            DesiredCapabilities::chrome(),
            5000
        );

        echo "✓ Conexión establecida\n";
    }

    private function detener()
    {
        echo "\nCerrando recursos...\n";

        if ($this->driver !== null) {
            $this->driver->quit();
            echo "✓ Navegador cerrado\n";
        }

        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
            echo "✓ ChromeDriver detenido\n";
        }
    }
}

/* ===== EJECUCIÓN ===== */
$script = new Clase19_WebTables();
$script->ejecutar();
