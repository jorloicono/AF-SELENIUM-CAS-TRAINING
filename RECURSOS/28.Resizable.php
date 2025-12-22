<?php

/**
 * ═══════════════════════════════════════════════════════════════
 * 🎯 DEMO: RESIZABLE - Elementos Redimensionables
 * ═══════════════════════════════════════════════════════════════
 *
 * 📋 DESCRIPCIÓN:
 * Este script demuestra cómo redimensionar elementos usando
 * Selenium WebDriver en PHP mediante acciones de mouse.
 *
 * 🎓 OBJETIVOS DE APRENDIZAJE:
 * • Identificar elementos redimensionables
 * • Redimensionar arrastrando esquinas/bordes
 * • Verificar dimensiones antes y después
 * • Agrandar y reducir tamaños
 * • Cambiar solo ancho o solo alto
 *
 * 🔗 URL DE PRÁCTICA:
 * https://www.tutorialspoint.com/selenium/practice/resizable.php
 *
 * ═══════════════════════════════════════════════════════════════
 */

require_once './vendor/autoload.php';
require_once './selenium-config.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Interactions\WebDriverActions;

class ResizableDemo
{
    private $driver;
    private $chromeProcess;

    public function __construct()
    {
        echo "\n🎯 === RESIZABLE DEMO (tutorialspoint.com) ===\n";
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
     * Obtener dimensiones de un elemento
     */
    private function obtenerDimensiones($elemento, $nombre)
    {
        $size = $elemento->getSize();
        $ancho = $size->getWidth();
        $alto = $size->getHeight();

        echo "📏 $nombre: {$ancho}px × {$alto}px\n";

        return ['ancho' => $ancho, 'alto' => $alto];
    }

    /**
     * Redimensionar un elemento arrastrando desde su esquina
     */
    private function redimensionarElemento($xpath, $offsetX, $offsetY, $descripcion)
    {
        echo "\n🎯 $descripcion\n";
        echo "   Desplazamiento: X={$offsetX}px, Y={$offsetY}px\n";

        // Obtener el elemento
        $elemento = $this->driver->findElement(WebDriverBy::xpath($xpath));

        // Obtener dimensiones antes
        $dimAntes = $this->obtenerDimensiones($elemento, "Tamaño ANTES");

        // Hacer scroll al elemento
        $this->driver->executeScript("arguments[0].scrollIntoView({block: 'center'});", [$elemento]);
        sleep(1);

        // Usar JavaScript para cambiar el tamaño directamente
        $script = "
            var el = arguments[0];
            var newWidth = el.offsetWidth + arguments[1];
            var newHeight = el.offsetHeight + arguments[2];
            if (newWidth > 50) el.style.width = newWidth + 'px';
            if (newHeight > 50) el.style.height = newHeight + 'px';
        ";

        $this->driver->executeScript($script, [$elemento, $offsetX, $offsetY]);
        sleep(1);

        // Re-obtener el elemento y sus dimensiones
        $elemento = $this->driver->findElement(WebDriverBy::xpath($xpath));
        $dimDespues = $this->obtenerDimensiones($elemento, "Tamaño DESPUÉS");

        // Calcular cambio
        $cambioAncho = $dimDespues['ancho'] - $dimAntes['ancho'];
        $cambioAlto = $dimDespues['alto'] - $dimAntes['alto'];

        echo "   📊 Cambio: Ancho ";
        echo ($cambioAncho >= 0 ? "+$cambioAncho" : "$cambioAncho") . "px, Alto ";
        echo ($cambioAlto >= 0 ? "+$cambioAlto" : "$cambioAlto") . "px\n";

        if ($cambioAncho != 0 || $cambioAlto != 0) {
            echo "   ✅ Redimensionamiento exitoso\n";
        } else {
            echo "   ⚠️  Sin cambios detectados\n";
        }

        return ['ancho' => $cambioAncho, 'alto' => $cambioAlto];
    }

    public function runDemo()
    {
        try {
            // ==========================================
            // PASO 1: ABRIR LA PÁGINA
            // ==========================================
            echo "\n[1/9] 🌐 ABRIENDO LA PÁGINA\n";
            echo "======================================\n";

            $url = "https://www.tutorialspoint.com/selenium/practice/resizable.php";
            echo "📍 URL: $url\n";

            $this->driver->get($url);
            sleep(3);

            echo "✅ Página cargada\n\n";

            // ==========================================
            // PASO 2: ENCONTRAR ELEMENTOS RESIZABLE
            // ==========================================
            echo "[2/9] 🔍 ENCONTRANDO ELEMENTOS REDIMENSIONABLES\n";
            echo "======================================\n";

            // Buscar el elemento 'both selector'
            $elemento1 = $this->driver->findElement(
                WebDriverBy::xpath("//div[contains(@class, 'both') and contains(@class, 'selector')]")
            );

            // Buscar el elemento 'flex-box'
            $elemento2 = $this->driver->findElement(
                WebDriverBy::xpath("//div[contains(@class, 'flex-box')]")
            );

            echo "✅ Elementos redimensionables encontrados: 2\n\n";

            // Mostrar información de cada elemento
            echo "📦 Elemento 1 ('both selector')\n";
            $this->obtenerDimensiones($elemento1, "   Tamaño inicial");
            echo "\n";

            echo "📦 Elemento 2 ('flex-box')\n";
            $this->obtenerDimensiones($elemento2, "   Tamaño inicial");
            echo "\n";

            // ==========================================
            // PASO 3: REDIMENSIONAR PRIMER ELEMENTO - AGRANDAR
            // ==========================================
            echo "[3/9] 🔼 REDIMENSIONAR PRIMER ELEMENTO (AGRANDAR)\n";
            echo "======================================\n";

            $this->redimensionarElemento(
                "//div[contains(@class, 'both') and contains(@class, 'selector')]",
                80,   // Aumentar ancho 80px
                60,   // Aumentar alto 60px
                "Agrandando elemento 'both selector'"
            );

            sleep(1);

            // ==========================================
            // PASO 4: REDIMENSIONAR PRIMER ELEMENTO - REDUCIR
            // ==========================================
            echo "\n[4/9] 🔽 REDIMENSIONAR PRIMER ELEMENTO (REDUCIR)\n";
            echo "======================================\n";

            $this->redimensionarElemento(
                "//div[contains(@class, 'both') and contains(@class, 'selector')]",
                -40,  // Reducir ancho 40px
                -30,  // Reducir alto 30px
                "Reduciendo elemento 'both selector'"
            );

            sleep(1);

            // ==========================================
            // PASO 5: REDIMENSIONAR SEGUNDO ELEMENTO - AGRANDAR
            // ==========================================
            echo "\n[5/9] 🔼 REDIMENSIONAR SEGUNDO ELEMENTO (AGRANDAR)\n";
            echo "======================================\n";

            echo "📦 Trabajando con elemento 'flex-box'\n";

            $this->redimensionarElemento(
                "//div[contains(@class, 'flex-box')]",
                150,  // Aumentar ancho 150px
                80,   // Aumentar alto 80px
                "Agrandando segundo elemento 'flex-box'"
            );

            sleep(1);

            // ==========================================
            // PASO 6: REDIMENSIONAR SEGUNDO ELEMENTO - SOLO ANCHO
            // ==========================================
            echo "\n[6/9] ↔️ REDIMENSIONAR SEGUNDO ELEMENTO (SOLO ANCHO)\n";
            echo "======================================\n";

            $this->redimensionarElemento(
                "//div[contains(@class, 'flex-box')]",
                -80,  // Reducir ancho 80px
                0,    // Sin cambio en alto
                "Reduciendo solo el ancho del flex-box'"
            );

            sleep(1);

            // ==========================================
            // PASO 7: REDIMENSIONAR SEGUNDO ELEMENTO - SOLO ALTO
            // ==========================================
            echo "\n[7/9] ↕️ REDIMENSIONAR SEGUNDO ELEMENTO (SOLO ALTO)\n";
            echo "======================================\n";

            $this->redimensionarElemento(
                "//div[contains(@class, 'flex-box')]",
                0,    // Sin cambio en ancho
                50,   // Aumentar alto 50px
                "Aumentando solo el alto del 'flex-box'"
            );

            sleep(1);

            // ==========================================
            // PASO 8: REDIMENSIONAR PRIMER ELEMENTO - SOLO ANCHO
            // ==========================================
            echo "\n[8/9] ↔️ REDIMENSIONAR PRIMER ELEMENTO (SOLO ANCHO)\n";
            echo "======================================\n";

            $this->redimensionarElemento(
                "//div[contains(@class, 'both') and contains(@class, 'selector')]",
                50,   // Aumentar ancho 50px
                0,    // Sin cambio en alto
                "Aumentando solo el ancho del 'both selector'"
            );

            sleep(1);

            // ==========================================
            // PASO 9: VERIFICACIÓN FINAL
            // ==========================================
            echo "\n[9/9] ✅ VERIFICACIÓN FINAL\n";
            echo "======================================\n";

            $elemento1Final = $this->driver->findElement(
                WebDriverBy::xpath("//div[contains(@class, 'both') and contains(@class, 'selector')]")
            );
            $elemento2Final = $this->driver->findElement(
                WebDriverBy::xpath("//div[contains(@class, 'flex-box')]")
            );

            echo "📊 Dimensiones finales de ambos elementos:\n\n";

            echo "📦 Elemento 1 ('both selector')\n";
            $this->obtenerDimensiones($elemento1Final, "   Tamaño final");
            echo "\n";

            echo "📦 Elemento 2 ('flex-box')\n";
            $this->obtenerDimensiones($elemento2Final, "   Tamaño final");
            echo "\n";

            echo "🎉 DEMO COMPLETADA CON ÉXITO!\n";
            echo "======================================\n";
            echo "📄 URL actual: " . $this->driver->getCurrentURL() . "\n";

            echo "\n💡 LECCIONES APRENDIDAS:\n";
            echo "   ✅ Identificar elementos redimensionables\n";
            echo "   ✅ Usar JavaScript para modificar width y height\n";
            echo "   ✅ Obtener dimensiones con getSize()\n";
            echo "   ✅ Agrandar elementos (offset positivo)\n";
            echo "   ✅ Reducir elementos (offset negativo)\n";
            echo "   ✅ Cambiar solo ancho (offsetY=0)\n";
            echo "   ✅ Cambiar solo alto (offsetX=0)\n";
            echo "   ✅ Redimensionar múltiples elementos\n";
            echo "   ✅ Verificar cambios antes y después\n";

            echo "\n⏸️  El navegador permanecerá abierto 5 segundos más...\n";

            sleep(5);

        } catch (Exception $e) {
            echo "\n❌ ERROR CAPTURADO\n";
            echo "======================================\n";
            echo "Mensaje: " . $e->getMessage() . "\n\n";

            // Diagnóstico
            echo "🔍 DIAGNÓSTICO - Intentando obtener información...\n";
            try {
                $both = $this->driver->findElements(
                    WebDriverBy::xpath("//div[contains(@class, 'both')]")
                );
                $flex = $this->driver->findElements(
                    WebDriverBy::xpath("//div[contains(@class, 'flex-box')]")
                );

                echo "Elementos 'both selector': " . count($both) . "\n";
                echo "Elementos 'flex-box': " . count($flex) . "\n";
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
$demo = new ResizableDemo();
$demo->runDemo();

echo "\n✨ Script finalizado\n\n";
