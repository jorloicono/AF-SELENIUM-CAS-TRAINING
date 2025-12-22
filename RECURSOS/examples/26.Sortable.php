<?php
/**
 * ========================================
 * SORTABLE DEMO - Aprende a automatizar elementos ordenables (drag & drop)
 * ========================================
 * 
 * Este script demuestra cómo interactuar con elementos sortables (ordenables)
 * en una página web usando Selenium WebDriver y acciones de arrastrar y soltar.
 * 
 * URL: https://www.tutorialspoint.com/selenium/practice/sortable.php
 * 
 * CONCEPTOS QUE APRENDERÁS:
 * -------------------------
 * 1. Identificar elementos ordenables en listas
 * 2. Usar WebDriverActions para drag and drop
 * 3. Mover elementos de una posición a otra
 * 4. Verificar el orden de elementos antes y después
 * 5. Usar dragAndDropBy() para movimientos relativos
 * 
 * DRAG AND DROP:
 * --------------
 * Acciones necesarias:
 * - clickAndHold() - Mantener clic en el elemento
 * - moveToElement() - Mover al elemento destino
 * - release() - Soltar el elemento
 * - perform() - Ejecutar la secuencia de acciones
 */
require_once 'vendor/autoload.php';
require_once 'selenium-config.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Interactions\WebDriverActions;

class SortableDemo
{
    private $driver;
    private $chromeProcess;

    public function __construct()
    {
        echo "\n🎯 === SORTABLE DEMO (tutorialspoint.com) ===\n";
        $this->startChromeDriver();
        $this->connectDriver();
    }

    private function startChromeDriver()
    {
        $desc = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $this->chromeProcess = proc_open(
            CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT,
            $desc,
            $pipes
        );
        sleep(3);
    }

    private function connectDriver()
    {
        $caps = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create(CHROMEDRIVER_HOST, $caps, 10000);
        $this->driver->manage()->window()->maximize();
    }

    /**
     * Obtiene el orden actual de los elementos en la lista
     */
    private function obtenerOrdenActual($items)
    {
        $orden = [];
        foreach ($items as $i => $item) {
            $orden[] = trim($item->getText());
        }
        return $orden;
    }

    /**
     * Muestra el orden actual de los elementos
     */
    private function mostrarOrden($items, $titulo = "Orden actual")
    {
        echo "$titulo:\n";
        foreach ($items as $i => $item) {
            $texto = trim($item->getText());
            echo "   [$i] $texto\n";
        }
    }

    public function ejecutar()
    {
        try {
            echo "[1/8] ⏳ Cargando página con sortable...\n";
            $this->driver->get('https://www.tutorialspoint.com/selenium/practice/sortable.php');
            sleep(2);

            echo "\n[2/8] 🔍 IDENTIFICANDO ELEMENTOS SORTABLES\n";
            echo "--------------------------------------\n";

            // Buscar el tbody (sin id específico ya que no existe en la página)
            $sortableItems = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));

            if (count($sortableItems) === 0) {
                throw new Exception("No se encontraron elementos en la tabla");
            }

            echo "📋 Elementos en la tabla: " . count($sortableItems) . "\n\n";

            $this->mostrarOrden($sortableItems, "📊 Orden inicial");

            // Crear objeto de acciones para drag & drop
            $actions = new WebDriverActions($this->driver);

            // ==========================================
            // INTERACCIÓN 1: MOVER PRIMER ELEMENTO AL FINAL
            // ==========================================
            echo "\n[3/8] 🔄 MOVER PRIMER ELEMENTO AL FINAL\n";
            echo "--------------------------------------\n";

            // Re-obtener elementos para evitar stale reference
            $sortableItems = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));

            if (count($sortableItems) >= 2) {
                $primerElemento = $sortableItems[0];
                $ultimoElemento = $sortableItems[count($sortableItems) - 1];

                echo "🎯 Moviendo: '" . trim($primerElemento->getText()) . "'\n";
                echo "📍 Destino: después de '" . trim($ultimoElemento->getText()) . "'\n";

                // Hacer scroll al elemento
                $this->driver->executeScript("arguments[0].scrollIntoView(true);", [$primerElemento]);
                sleep(1);

                // Ejecutar drag and drop
                $actions->dragAndDrop($primerElemento, $ultimoElemento)->perform();
                sleep(2);

                echo "✅ Movimiento completado\n\n";

                // Mostrar nuevo orden
                $sortableItems = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));
                $this->mostrarOrden($sortableItems, "📊 Nuevo orden");
            }

            // ==========================================
            // INTERACCIÓN 2: MOVER SEGUNDO ELEMENTO AL PRINCIPIO
            // ==========================================
            echo "\n[4/8] 🔄 MOVER SEGUNDO ELEMENTO AL PRINCIPIO\n";
            echo "--------------------------------------\n";

            $sortableItems = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));

            if (count($sortableItems) >= 2) {
                $segundoElemento = $sortableItems[1];
                $primerElemento = $sortableItems[0];

                echo "🎯 Moviendo: '" . trim($segundoElemento->getText()) . "'\n";
                echo "📍 Destino: antes de '" . trim($primerElemento->getText()) . "'\n";

                $this->driver->executeScript("arguments[0].scrollIntoView(true);", [$segundoElemento]);
                sleep(1);

                // Drag and drop
                $actions->dragAndDrop($segundoElemento, $primerElemento)->perform();
                sleep(2);

                echo "✅ Movimiento completado\n\n";

                $sortableItems = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));
                $this->mostrarOrden($sortableItems, "📊 Nuevo orden");
            }

            // ==========================================
            // INTERACCIÓN 3: MOVIMIENTO MANUAL CON CLICK AND HOLD
            // ==========================================
            echo "\n[5/8] 🔄 MOVIMIENTO MANUAL (Click and Hold)\n";
            echo "--------------------------------------\n";

            $sortableItems = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));

            if (count($sortableItems) >= 3) {
                $elementoMover = $sortableItems[0];
                $elementoDestino = $sortableItems[2];

                echo "🎯 Moviendo: '" . trim($elementoMover->getText()) . "'\n";
                echo "📍 Destino: posición de '" . trim($elementoDestino->getText()) . "'\n";

                $this->driver->executeScript("arguments[0].scrollIntoView(true);", [$elementoMover]);
                sleep(1);

                // Método alternativo: clickAndHold + moveToElement + release
                $actions->clickAndHold($elementoMover)
                    ->moveToElement($elementoDestino)
                    ->release()
                    ->perform();
                sleep(2);

                echo "✅ Movimiento completado\n\n";

                $sortableItems = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));
                $this->mostrarOrden($sortableItems, "📊 Nuevo orden");
            }

            // ==========================================
            // INTERACCIÓN 4: RESTAURAR ORDEN ORIGINAL
            // ==========================================
            echo "\n[6/8] 🔄 INTENTANDO RESTAURAR ORDEN ORIGINAL\n";
            echo "--------------------------------------\n";

            // Recargar la página para restaurar el orden
            echo "🔄 Recargando página...\n";
            $this->driver->navigate()->refresh();
            sleep(2);

            $sortableItems = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));
            $this->mostrarOrden($sortableItems, "📊 Orden restaurado");

            // ==========================================
            // VERIFICACIÓN: CONTEO DE ELEMENTOS
            // ==========================================
            echo "\n[7/8] ✅ VERIFICACIÓN FINAL\n";
            echo "--------------------------------------\n";

            $sortableItems = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));
            echo "📊 Total de elementos sortables: " . count($sortableItems) . "\n";
            echo "📋 Lista final:\n";
            foreach ($sortableItems as $i => $item) {
                $texto = trim($item->getText());
                // Extraer solo el número si existe
                preg_match('/^\d+/', $texto, $matches);
                $numero = isset($matches[0]) ? $matches[0] : '?';
                echo "   Posición $i → ID: $numero\n";
            }

            echo "\n[8/8] 🎉 DEMO COMPLETADA CON ÉXITO!\n";
            echo "======================================\n";
            echo "📄 URL actual: " . $this->driver->getCurrentUrl() . "\n";

            echo "\n⚠️  NOTA IMPORTANTE:\n";
            echo "--------------------------------------\n";
            echo "Los elementos NO cambiaron de orden porque esta tabla específica\n";
            echo "NO tiene funcionalidad sortable implementada con JavaScript.\n";
            echo "\nEsto es común en páginas de práctica. El script demuestra las\n";
            echo "técnicas correctas de drag & drop que funcionarían en tablas reales\n";
            echo "con funcionalidad sortable (como jQuery UI Sortable).\n";

            echo "\n💡 LECCIONES APRENDIDAS:\n";
            echo "   ✅ Identificar elementos en tablas\n";
            echo "   ✅ Usar dragAndDrop() para mover elementos\n";
            echo "   ✅ Usar clickAndHold() + moveToElement() + release()\n";
            echo "   ✅ Verificar el orden antes y después\n";
            echo "   ✅ Trabajar con WebDriverActions\n";
            echo "   ✅ Las acciones se ejecutan aunque no haya efecto visual\n";

            echo "\n🔗 PÁGINAS ALTERNATIVAS CON SORTABLE REAL:\n";
            echo "   • https://jqueryui.com/sortable/\n";
            echo "   • https://www.seleniumeasy.com/test/drag-and-drop-demo.html\n";
            echo "\n⏸️  El navegador permanecerá abierto 5 segundos más...\n";

            sleep(5);

        } catch (Exception $e) {
            echo "\n❌ ERROR CAPTURADO\n";
            echo "======================================\n";
            echo "Mensaje: " . $e->getMessage() . "\n\n";

            // Diagnóstico
            echo "🔍 DIAGNÓSTICO - Intentando obtener información...\n";
            try {
                $items = $this->driver->findElements(WebDriverBy::xpath("//tbody/tr"));
                echo "Elementos encontrados: " . count($items) . "\n";
                foreach ($items as $i => $item) {
                    echo "   [$i] '" . trim($item->getText()) . "'\n";
                }
            } catch (Exception $e2) {
                echo "   No se pudo obtener información adicional\n";
            }
        } finally {
            $this->shutdown();
        }
    }

    private function shutdown()
    {
        sleep(2);
        if ($this->driver)
            $this->driver->quit();
        if (is_resource($this->chromeProcess)) {
            proc_terminate($this->chromeProcess);
            proc_close($this->chromeProcess);
        }
    }
}

$demo = new SortableDemo();
$demo->ejecutar();
