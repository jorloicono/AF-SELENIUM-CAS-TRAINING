<?php

/**
 * ═══════════════════════════════════════════════════════════════
 * 🎯 DEMO: SELECTABLE - Elementos Seleccionables
 * ═══════════════════════════════════════════════════════════════
 *
 * 📋 DESCRIPCIÓN:
 * Este script demuestra cómo trabajar con listas de elementos
 * seleccionables usando Selenium WebDriver en PHP.
 *
 * 🎓 OBJETIVOS DE APRENDIZAJE:
 * • Ver qué elementos están marcados inicialmente
 * • Seleccionar múltiples elementos individuales
 * • Deseleccionar elementos clickeando de nuevo
 * • Seleccionar todos los elementos
 * • Limpiar toda la selección
 * • Detectar el estado de selección mediante clases CSS
 * • Contar cuántos elementos están seleccionados
 *
 * 🔗 URL DE PRÁCTICA:
 * https://www.tutorialspoint.com/selenium/practice/selectable.php
 *
 * ═══════════════════════════════════════════════════════════════
 */

require_once './vendor/autoload.php';
require_once './selenium-config.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;

class SelectableDemo
{
    private $driver;
    private $chromeProcess;

    public function __construct()
    {
        echo "\n🎯 === SELECTABLE DEMO (tutorialspoint.com) ===\n";
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
     * Obtener elementos seleccionables
     */
    private function obtenerElementos()
    {
        // Los elementos seleccionables tienen clase 'list-group-li' (no list-group-item)
        return $this->driver->findElements(WebDriverBy::xpath("//li[@class='list-group-li']"));
    }

    /**
     * Muestra el estado de selección de los elementos
     */
    private function mostrarEstados($items, $titulo = "Estado actual")
    {
        echo "$titulo:\n";
        foreach ($items as $i => $item) {
            $texto = substr(trim($item->getText()), 0, 50);
            $clase = $item->getAttribute('class');
            $seleccionado = (strpos($clase, 'active') !== false);
            $icono = $seleccionado ? "✅" : "⬜";
            echo "   $icono [$i] $texto...\n";
        }
        echo "\n";
    }

    /**
     * Contar elementos seleccionados
     */
    private function contarSeleccionados($items)
    {
        $contador = 0;
        $seleccionados = [];

        foreach ($items as $i => $item) {
            $clase = $item->getAttribute('class');
            if (strpos($clase, 'active') !== false) {
                $contador++;
                $texto = substr(trim($item->getText()), 0, 40);
                $seleccionados[] = "[$i] $texto...";
            }
        }

        return ['total' => $contador, 'items' => $seleccionados];
    }

    public function runDemo()
    {
        try {
            // ==========================================
            // PASO 1: ABRIR LA PÁGINA
            // ==========================================
            echo "[1/11] 🌐 ABRIENDO LA PÁGINA\n";
            echo "======================================\n";

            $url = "https://www.tutorialspoint.com/selenium/practice/selectable.php";
            echo "📍 URL: $url\n";

            $this->driver->get($url);
            sleep(3);

            echo "✅ Página cargada\n";
            echo "⏳ Esperando elementos...\n";
            sleep(2);
            echo "\n";

            // ==========================================
            // PASO 2: ENCONTRAR ELEMENTOS Y VER ESTADO INICIAL
            // ==========================================
            echo "[2/11] 🔍 ENCONTRANDO ELEMENTOS Y VERIFICANDO ESTADO INICIAL\n";
            echo "======================================\n";

            $items = $this->obtenerElementos();

            if (count($items) === 0) {
                throw new Exception("No se encontraron elementos seleccionables");
            }

            echo "📄 Elementos encontrados: " . count($items) . "\n\n";

            // Mostrar estado inicial
            $this->mostrarEstados($items, "📊 ESTADO INICIAL");

            // Contar cuántos están inicialmente seleccionados
            $resultado = $this->contarSeleccionados($items);
            echo "ℹ️  Elementos marcados inicialmente: {$resultado['total']}\n";
            if ($resultado['total'] > 0) {
                foreach ($resultado['items'] as $sel) {
                    echo "   • $sel\n";
                }
            }
            echo "\n";

            // ==========================================
            // PASO 3: SELECCIONAR PRIMER ELEMENTO
            // ==========================================
            echo "[3/11] 👆 SELECCIONAR PRIMER ELEMENTO\n";
            echo "======================================\n";

            $items = $this->obtenerElementos();
            if (count($items) > 0) {
                $texto = substr(trim($items[0]->getText()), 0, 50);
                echo "🎯 Seleccionando elemento [0]: '$texto...'\n";

                $items[0]->click();
                sleep(1);

                echo "✅ Elemento seleccionado\n\n";

                $items = $this->obtenerElementos();
                $this->mostrarEstados($items, "📊 Estado después de seleccionar");
            }

            // ==========================================
            // PASO 4: SELECCIONAR TERCER ELEMENTO
            // ==========================================
            echo "[4/11] 👆 SELECCIONAR TERCER ELEMENTO\n";
            echo "======================================\n";

            $items = $this->obtenerElementos();
            if (count($items) >= 3) {
                $texto = substr(trim($items[2]->getText()), 0, 50);
                echo "🎯 Seleccionando elemento [2]: '$texto...'\n";

                $items[2]->click();
                sleep(1);

                echo "✅ Elemento seleccionado\n\n";

                $items = $this->obtenerElementos();
                $this->mostrarEstados($items, "📊 Estado después de seleccionar");
            }

            // ==========================================
            // PASO 5: SELECCIONAR QUINTO ELEMENTO
            // ==========================================
            echo "[5/11] 👆 SELECCIONAR QUINTO ELEMENTO\n";
            echo "======================================\n";

            $items = $this->obtenerElementos();
            if (count($items) >= 5) {
                $texto = substr(trim($items[4]->getText()), 0, 50);
                echo "🎯 Seleccionando elemento [4]: '$texto...'\n";

                $items[4]->click();
                sleep(1);

                echo "✅ Elemento seleccionado\n\n";

                $items = $this->obtenerElementos();
                $this->mostrarEstados($items, "📊 Estado después de seleccionar");
            }

            // ==========================================
            // PASO 6: DESELECCIONAR PRIMER ELEMENTO
            // ==========================================
            echo "[6/11] 🔄 DESELECCIONAR PRIMER ELEMENTO\n";
            echo "======================================\n";

            $items = $this->obtenerElementos();
            if (count($items) > 0) {
                $texto = substr(trim($items[0]->getText()), 0, 50);
                echo "🎯 Deseleccionando elemento [0]: '$texto...'\n";
                echo "ℹ️  (Clickeando de nuevo para deseleccionar)\n";

                $items[0]->click();
                sleep(1);

                echo "✅ Elemento deseleccionado\n\n";

                $items = $this->obtenerElementos();
                $this->mostrarEstados($items, "📊 Estado después de deseleccionar");
            }

            // ==========================================
            // PASO 7: SELECCIONAR SEGUNDO ELEMENTO
            // ==========================================
            echo "[7/11] 👆 SELECCIONAR SEGUNDO ELEMENTO\n";
            echo "======================================\n";

            $items = $this->obtenerElementos();
            if (count($items) >= 2) {
                $texto = substr(trim($items[1]->getText()), 0, 50);
                echo "🎯 Seleccionando elemento [1]: '$texto...'\n";

                $items[1]->click();
                sleep(1);

                echo "✅ Elemento seleccionado\n\n";

                $items = $this->obtenerElementos();
                $this->mostrarEstados($items, "📊 Estado después de seleccionar");
            }

            // ==========================================
            // PASO 8: CONTAR ELEMENTOS SELECCIONADOS
            // ==========================================
            echo "[8/11] 📊 CONTAR ELEMENTOS SELECCIONADOS\n";
            echo "======================================\n";

            $items = $this->obtenerElementos();
            $resultado = $this->contarSeleccionados($items);

            echo "✅ Total de elementos seleccionados: {$resultado['total']}\n\n";

            if ($resultado['total'] > 0) {
                echo "📋 Elementos seleccionados:\n";
                foreach ($resultado['items'] as $sel) {
                    echo "   • $sel\n";
                }
                echo "\n";
            } else {
                echo "ℹ️  No hay elementos seleccionados actualmente\n\n";
            }

            // ==========================================
            // PASO 9: SELECCIONAR TODOS LOS ELEMENTOS
            // ==========================================
            echo "[9/11] ⭐ SELECCIONAR TODOS LOS ELEMENTOS\n";
            echo "======================================\n";

            $items = $this->obtenerElementos();
            echo "🎯 Seleccionando todos los elementos...\n";

            foreach ($items as $i => $item) {
                $clase = $item->getAttribute('class');
                $yaSeleccionado = strpos($clase, 'active') !== false;

                if (!$yaSeleccionado) {
                    $texto = substr(trim($item->getText()), 0, 40);
                    echo "   • Seleccionando [$i]: $texto...\n";
                    $item->click();
                    sleep(0.5);
                }
            }

            echo "\n✅ Todos los elementos seleccionados\n\n";

            $items = $this->obtenerElementos();
            $this->mostrarEstados($items, "📊 Estado después de seleccionar todos");

            // ==========================================
            // PASO 10: DESELECCIONAR ALGUNOS ELEMENTOS
            // ==========================================
            echo "[10/11] 🔄 DESELECCIONAR ALGUNOS ELEMENTOS\n";
            echo "======================================\n";

            $items = $this->obtenerElementos();
            if (count($items) >= 4) {
                echo "🎯 Deseleccionando elementos [1] y [3]...\n";

                // Deseleccionar elemento 2 (índice 1)
                $texto1 = substr(trim($items[1]->getText()), 0, 40);
                echo "   • Deseleccionando [1]: $texto1...\n";
                $items[1]->click();
                sleep(0.5);

                // Deseleccionar elemento 4 (índice 3)
                $texto3 = substr(trim($items[3]->getText()), 0, 40);
                echo "   • Deseleccionando [3]: $texto3...\n";
                $items[3]->click();
                sleep(0.5);

                echo "\n✅ Elementos deseleccionados\n\n";

                $items = $this->obtenerElementos();
                $this->mostrarEstados($items, "📊 Estado después de deseleccionar");
            }

            // ==========================================
            // PASO 11: LIMPIAR TODA LA SELECCIÓN
            // ==========================================
            echo "[11/11] 🧹 LIMPIAR TODA LA SELECCIÓN\n";
            echo "======================================\n";

            $items = $this->obtenerElementos();
            echo "🎯 Deseleccionando todos los elementos...\n";

            foreach ($items as $i => $item) {
                $clase = $item->getAttribute('class');
                $estaSeleccionado = strpos($clase, 'active') !== false;

                if ($estaSeleccionado) {
                    $texto = substr(trim($item->getText()), 0, 40);
                    echo "   • Deseleccionando [$i]: $texto...\n";
                    $item->click();
                    sleep(0.5);
                }
            }

            echo "\n✅ Toda la selección limpiada\n\n";

            $items = $this->obtenerElementos();
            $this->mostrarEstados($items, "📊 Estado final (sin selección)");

            // ==========================================
            // VERIFICACIÓN FINAL
            // ==========================================
            $resultado = $this->contarSeleccionados($items);

            echo "\n🎉 DEMO COMPLETADA CON ÉXITO!\n";
            echo "======================================\n";
            echo "📄 URL actual: " . $this->driver->getCurrentURL() . "\n";
            echo "📊 Total de elementos: " . count($items) . "\n";
            echo "📊 Elementos seleccionados: {$resultado['total']}\n";

            echo "\n💡 LECCIONES APRENDIDAS:\n";
            echo "   ✅ Verificar estado inicial de elementos\n";
            echo "   ✅ Seleccionar elementos individuales\n";
            echo "   ✅ Deseleccionar clickeando de nuevo\n";
            echo "   ✅ Seleccionar todos los elementos en bucle\n";
            echo "   ✅ Deseleccionar elementos específicos\n";
            echo "   ✅ Limpiar toda la selección\n";
            echo "   ✅ Detectar selección mediante clase CSS 'active'\n";
            echo "   ✅ Contar elementos seleccionados\n";
            echo "   ✅ Verificar estados antes y después de cada acción\n";

            echo "\n⏸️  El navegador permanecerá abierto 5 segundos más...\n";

            sleep(5);

        } catch (Exception $e) {
            echo "\n❌ ERROR CAPTURADO\n";
            echo "======================================\n";
            echo "Mensaje: " . $e->getMessage() . "\n\n";

            // Diagnóstico
            echo "🔍 DIAGNÓSTICO - Intentando obtener información...\n";
            try {
                $items = $this->driver->findElements(
                    WebDriverBy::xpath("//ul[@class='list-group']/li")
                );
                echo "Elementos <li> con clase list-group: " . count($items) . "\n";

                if (count($items) > 0) {
                    echo "\nPrimeros 5 elementos:\n";
                    for ($i = 0; $i < min(5, count($items)); $i++) {
                        $texto = substr(trim($items[$i]->getText()), 0, 60);
                        echo "   [$i] $texto\n";
                    }
                }
            } catch (Exception $e2) {
                echo "Error en diagnóstico: " . $e2->getMessage() . "\n";
            }

            echo "\n⏸️  El navegador permanecerá abierto para inspección...\n";
            sleep(10);
        }
    }

    public function __destruct()
    {
        if ($this->driver) {
            $this->driver->quit();
        }
        if ($this->chromeProcess) {
            proc_terminate($this->chromeProcess);
        }
    }
}

// Ejecutar demo
$demo = new SelectableDemo();
$demo->runDemo();

echo "\n✨ Script finalizado\n\n";
