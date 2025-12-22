<?php
/**
 * ========================================
 * ACCORDION DEMO - Aprende a automatizar componentes de acordeón
 * ========================================
 * 
 * Este script demuestra cómo interactuar con elementos de tipo accordion (acordeón)
 * en una página web usando Selenium WebDriver.
 * 
 * URL: https://automationtesting.co.uk/accordion.html
 * 
 * CONCEPTOS QUE APRENDERÁS:
 * -------------------------
 * 1. Identificar elementos de acordeón usando CLASES CSS (.accordion-header, .accordion-content)
 * 2. Usar selectores CSS con WebDriverBy::className() y cssSelector()
 * 3. Expandir y colapsar paneles dinámicamente
 * 4. Leer contenido de elementos ocultos/visibles
 * 5. Iterar sobre múltiples elementos del mismo tipo
 * 
 * ESTRUCTURA DE UN ACCORDION:
 * --------------------------
 * Un acordeón típicamente tiene:
 * - Cabeceras/títulos clickeables con clase 'accordion-header'
 * - Paneles con contenido con clase 'accordion-content'
 * - Solo un panel visible a la vez (o múltiples según el diseño)
 */
require_once 'vendor/autoload.php';
require_once 'selenium-config.php';

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class AccordionRealDemo
{
    private $driver, $chromeProcess;

    public function __construct()
    {
        echo "\n🎯 === ACCORDION REAL (automationtesting.co.uk) ===\n";
        $this->startChromeDriver();
        $this->connectDriver();
    }

    private function startChromeDriver()
    {
        $desc = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $this->chromeProcess = proc_open(CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT, $desc, $pipes);
        sleep(3);
    }

    private function connectDriver()
    {
        $caps = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create(CHROMEDRIVER_HOST, $caps, 10000);
        $this->driver->manage()->window()->maximize();
    }

    public function ejecutar()
    {
        try {
            echo "[1/8] ⏳ Cargando página con accordion...\n";
            $this->driver->get('https://automationtesting.co.uk/accordion.html');
            sleep(2);

            echo "\n[2/8] 🔍 INSPECCIONANDO LA ESTRUCTURA\n";
            echo "--------------------------------------\n";

            // ✅ Título página
            $title = $this->driver->findElement(WebDriverBy::tagName('h2'));
            echo "✅ Título de página: '" . $title->getText() . "'\n";

            // 🔍 Buscar elementos del accordion usando CLASES CSS
            echo "\n🔍 Buscando elementos por clase CSS...\n";

            // IMPORTANTE: Usamos className() para buscar por clase CSS específica
            $accordionHeaders = $this->driver->findElements(WebDriverBy::className('accordion-header'));

            echo "📋 Headers del accordion encontrados: " . count($accordionHeaders) . "\n";
            foreach ($accordionHeaders as $i => $header) {
                $text = $header->getText();
                echo "   [$i] .accordion-header: '$text'\n";
            }

            // También buscar los paneles de contenido
            $accordionContents = $this->driver->findElements(WebDriverBy::className('accordion-content'));
            echo "\n📦 Paneles de contenido encontrados: " . count($accordionContents) . "\n";

            // ==========================================
            // INTERACCIÓN 1: EXPANDIR PRIMER PANEL
            // ==========================================
            echo "\n[3/8] 👆 EXPANDIR PRIMER PANEL\n";
            echo "--------------------------------------\n";
            try {
                // Usamos el primer elemento del array de headers
                $firstButton = $accordionHeaders[0];
                echo "✅ Header [0]: '" . $firstButton->getText() . "'\n";

                // Hacer scroll hasta el elemento (buena práctica)
                echo "⬇️ Haciendo scroll al elemento...\n";
                $this->driver->executeScript("arguments[0].scrollIntoView(true);", [$firstButton]);
                sleep(1);

                echo "👆 Haciendo clic para expandir...\n";
                $firstButton->click();
                echo "✅ Panel EXPANDIDO\n";
                sleep(2);

                // Leer el contenido del panel expandido usando el índice correspondiente
                echo "\n📖 Leyendo contenido del panel...\n";
                try {
                    $firstPanel = $accordionContents[0];
                    $contentText = $firstPanel->getText();
                    if (!empty($contentText)) {
                        echo "📄 Contenido (primeros 150 caracteres):\n";
                        echo "   " . substr($contentText, 0, 150) . "...\n";
                    }
                } catch (Exception $e) {
                    echo "ℹ️  Panel no tiene contenido visible\n";
                }
            } catch (Exception $e) {
                echo "❌ No se encontró el elemento\n";
                throw $e;
            }

            // ==========================================
            // INTERACCIÓN 2: EXPANDIR SEGUNDO PANEL
            // ==========================================
            echo "\n[4/8] 👆 EXPANDIR SEGUNDO PANEL\n";
            echo "--------------------------------------\n";
            try {
                $secondButton = $accordionHeaders[1];
                echo "✅ Header [1]: '" . $secondButton->getText() . "'\n";

                $this->driver->executeScript("arguments[0].scrollIntoView(true);", [$secondButton]);
                sleep(1);

                echo "👆 Haciendo clic para expandir...\n";
                $secondButton->click();
                echo "✅ Panel EXPANDIDO\n";
                sleep(2);

                // Leer contenido
                try {
                    $secondPanel = $accordionContents[1];
                    $contentText2 = $secondPanel->getText();
                    if (!empty($contentText2)) {
                        echo "📄 Contenido: " . substr($contentText2, 0, 150) . "...\n";
                    }
                } catch (Exception $e) {
                    echo "ℹ️  Panel no tiene contenido visible\n";
                }
            } catch (Exception $e) {
                echo "❌ No se encontró el elemento\n";
            }

            // ==========================================
            // INTERACCIÓN 3: COLAPSAR PRIMER PANEL
            // ==========================================
            echo "\n[5/8] 👇 COLAPSAR PRIMER PANEL\n";
            echo "--------------------------------------\n";
            try {
                echo "👆 Haciendo clic nuevamente para colapsar...\n";
                $firstButton->click();
                echo "✅ Panel COLAPSADO\n";
                sleep(1);
            } catch (Exception $e) {
                echo "⚠️  No se pudo colapsar (puede que el elemento ya no sea válido)\n";
            }

            // ==========================================
            // INTERACCIÓN 4: EXPANDIR TERCER PANEL
            // ==========================================
            echo "\n[6/8] 👆 EXPANDIR TERCER PANEL\n";
            echo "--------------------------------------\n";
            try {
                $thirdButton = $accordionHeaders[2];
                echo "✅ Header [2]: '" . $thirdButton->getText() . "'\n";

                $this->driver->executeScript("arguments[0].scrollIntoView(true);", [$thirdButton]);
                sleep(1);

                echo "👆 Haciendo clic para expandir...\n";
                $thirdButton->click();
                echo "✅ Panel EXPANDIDO\n";
                sleep(2);
            } catch (Exception $e) {
                echo "❌ No se encontró el elemento\n";
            }

            // ==========================================
            // VERIFICACIÓN FINAL
            // ==========================================
            echo "\n[7/8] ✅ VERIFICACIÓN DE ESTADOS\n";
            echo "--------------------------------------\n";
            // Verificar atributos aria-expanded (accesibilidad)
            // Estos atributos indican si un acordeón está expandido (true) o colapsado (false)
            try {
                $aria1 = $firstButton->getAttribute('aria-expanded');
                $aria2 = isset($secondButton) ? $secondButton->getAttribute('aria-expanded') : null;
                $aria3 = isset($thirdButton) ? $thirdButton->getAttribute('aria-expanded') : null;

                echo "📊 Estado aria-expanded:\n";
                echo "   Panel 1: " . ($aria1 ?: 'no presente') . "\n";
                echo "   Panel 2: " . ($aria2 ?: 'no presente') . "\n";
                echo "   Panel 3: " . ($aria3 ?: 'no presente') . "\n";
            } catch (Exception $e) {
                echo "ℹ️  Los atributos aria no están disponibles en estos elementos\n";
            }

            echo "\n[8/8] 🎉 DEMO COMPLETADA CON ÉXITO!\n";
            echo "======================================\n";
            echo "📄 URL actual: " . $this->driver->getCurrentUrl() . "\n";
            echo "\n💡 LECCIONES APRENDIDAS:\n";
            echo "   ✅ Buscar elementos por clase CSS (.accordion-header, .accordion-content)\n";
            echo "   ✅ Usar WebDriverBy::className() para selectores específicos\n";
            echo "   ✅ Trabajar con arrays de elementos (índices [0], [1], [2])\n";
            echo "   ✅ Expandir y colapsar paneles dinámicamente\n";
            echo "   ✅ Leer contenido de paneles específicos\n";
            echo "\n⏸️  El navegador permanecerá abierto 5 segundos más...\n";

            sleep(5); // Pausa final para observar el resultado

        } catch (Exception $e) {
            echo "\n❌ ERROR CAPTURADO\n";
            echo "======================================\n";
            echo "Mensaje: " . $e->getMessage() . "\n\n";

            // Diagnóstico útil: mostrar todos los headers disponibles
            echo "🔍 DIAGNÓSTICO - Headers disponibles en la página:\n";
            try {
                $allHeaders = $this->driver->findElements(WebDriverBy::xpath("//h1 | //h2 | //h3 | //h4"));
                foreach ($allHeaders as $h) {
                    echo "   - '" . $h->getText() . "' <" . $h->getTagName() . ">\n";
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

new AccordionRealDemo()->ejecutar();
