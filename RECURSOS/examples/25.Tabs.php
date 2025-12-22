<?php
/**
 * ========================================
 * TABS DEMO - Aprende a automatizar componentes de pestañas (tabs)
 * ========================================
 * 
 * Este script demuestra cómo interactuar con elementos de tipo tabs (pestañas)
 * en una página web usando Selenium WebDriver.
 * 
 * URL: https://www.tutorialspoint.com/selenium/practice/tabs.php
 * 
 * CONCEPTOS QUE APRENDERÁS:
 * -------------------------
 * 1. Identificar tabs usando role='tab' (accesibilidad)
 * 2. Usar XPath con atributos ARIA
 * 3. Cambiar entre diferentes pestañas
 * 4. Leer contenido de paneles asociados a tabs
 * 5. Verificar estados activos/inactivos de tabs
 * 
 * ESTRUCTURA DE TABS:
 * ------------------
 * Los tabs típicamente tienen:
 * - Botón/enlace con role="tab" para cada pestaña
 * - Panel con role="tabpanel" para mostrar contenido
 * - Atributos aria-selected para indicar el tab activo
 */
require_once 'vendor/autoload.php';
require_once 'selenium-config.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;

class DemoTabsInspeccion
{
    private $driver;
    private $chromeProcess;

    public function __construct()
    {
        echo "\n🎯 === TABS DEMO (tutorialspoint.com) ===\n";
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
        sleep(2);
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
            echo "[1/7] ⏳ Cargando página con tabs...\n";
            $this->driver->get('https://www.tutorialspoint.com/selenium/practice/tabs.php');
            sleep(2);

            echo "\n[2/7] 🔍 IDENTIFICANDO ESTRUCTURA DE TABS\n";
            echo "--------------------------------------\n";

            // Buscar tabs usando role='tab'
            $tabs = $this->driver->findElements(WebDriverBy::xpath("//button[@role='tab']"));
            echo "📋 Tabs encontrados: " . count($tabs) . "\n";

            foreach ($tabs as $i => $tab) {
                $text = $tab->getText();
                $ariaSelected = $tab->getAttribute('aria-selected');
                $id = $tab->getAttribute('id');
                echo "   [$i] Tab: '$text' | ID: $id | Activo: $ariaSelected\n";
            }

            echo "\n[3/7] 👆 CLIC EN TAB 'HOME' (ya está activo)\n";
            echo "--------------------------------------\n";
            $homeTab = $tabs[0];
            echo "✅ Tab seleccionado: '" . $homeTab->getText() . "'\n";

            // Leer contenido del panel asociado
            try {
                $homePanel = $this->driver->findElement(WebDriverBy::id('nav-home'));
                $contentText = $homePanel->getText();
                echo "📄 Contenido (150 chars): " . substr($contentText, 0, 150) . "...\n";
            } catch (Exception $e) {
                echo "ℹ️  No se pudo leer el contenido del panel\n";
            }

            // ==========================================
            // INTERACCIÓN 1: CAMBIAR A TAB 'PROFILE'
            // ==========================================
            echo "\n[4/7] 👆 CAMBIAR A TAB 'PROFILE'\n";
            echo "--------------------------------------\n";
            $profileTab = $tabs[1];
            echo "📌 Cambiando a tab: '" . $profileTab->getText() . "'\n";

            $this->driver->executeScript("arguments[0].scrollIntoView(true);", [$profileTab]);
            sleep(1);

            $profileTab->click();
            echo "✅ Tab activado\n";
            sleep(2);

            // Verificar estado
            $ariaSelected = $profileTab->getAttribute('aria-selected');
            echo "📊 Estado aria-selected: $ariaSelected\n";

            // Leer contenido del panel de Profile
            try {
                $profilePanel = $this->driver->findElement(WebDriverBy::id('nav-profile'));
                $profileContent = $profilePanel->getText();
                echo "📄 Contenido: " . substr($profileContent, 0, 150) . "...\n";
            } catch (Exception $e) {
                echo "ℹ️  No se pudo leer el contenido del panel\n";
            }

            // ==========================================
            // INTERACCIÓN 2: CAMBIAR A TAB 'CONTACT'
            // ==========================================
            echo "\n[5/7] 👆 CAMBIAR A TAB 'CONTACT'\n";
            echo "--------------------------------------\n";
            $contactTab = $tabs[2];
            echo "📌 Cambiando a tab: '" . $contactTab->getText() . "'\n";

            $this->driver->executeScript("arguments[0].scrollIntoView(true);", [$contactTab]);
            sleep(1);

            $contactTab->click();
            echo "✅ Tab activado\n";
            sleep(2);

            // Verificar estado
            $ariaSelected = $contactTab->getAttribute('aria-selected');
            echo "📊 Estado aria-selected: $ariaSelected\n";

            // Leer contenido del panel de Contact
            try {
                $contactPanel = $this->driver->findElement(WebDriverBy::id('nav-contact'));
                $contactContent = $contactPanel->getText();
                echo "📄 Contenido: " . substr($contactContent, 0, 150) . "...\n";
            } catch (Exception $e) {
                echo "ℹ️  No se pudo leer el contenido del panel\n";
            }

            // ==========================================
            // VERIFICAR TODOS LOS ESTADOS
            // ==========================================
            echo "\n[6/7] ✅ VERIFICACIÓN DE ESTADOS FINALES\n";
            echo "--------------------------------------\n";

            // Re-buscar los tabs para evitar stale element reference
            $tabs = $this->driver->findElements(WebDriverBy::xpath("//button[@role='tab']"));
            foreach ($tabs as $i => $tab) {
                $text = $tab->getText();
                $selected = $tab->getAttribute('aria-selected');
                $status = ($selected === 'true') ? '✅ ACTIVO' : '⚪ Inactivo';
                echo "   Tab [$i] '$text': $status\n";
            }

            echo "\n[7/7] 🎉 DEMO COMPLETADA CON ÉXITO!\n";
            echo "======================================\n";
            echo "📄 URL actual: " . $this->driver->getCurrentUrl() . "\n";
            echo "\n💡 LECCIONES APRENDIDAS:\n";
            echo "   ✅ Identificar tabs con role='tab'\n";
            echo "   ✅ Usar XPath con atributos ARIA\n";
            echo "   ✅ Cambiar entre pestañas haciendo clic\n";
            echo "   ✅ Leer contenido de paneles (role='tabpanel')\n";
            echo "   ✅ Verificar estado activo con aria-selected\n";
            echo "\n⏸️  El navegador permanecerá abierto 5 segundos más...\n";

            sleep(5);

        } catch (Exception $e) {
            echo "\n❌ ERROR CAPTURADO\n";
            echo "======================================\n";
            echo "Mensaje: " . $e->getMessage() . "\n\n";

            // Diagnóstico
            echo "🔍 DIAGNÓSTICO - Intentando obtener información...\n";
            try {
                $tabs = $this->driver->findElements(WebDriverBy::xpath("//button[@role='tab']"));
                echo "Tabs encontrados: " . count($tabs) . "\n";
                foreach ($tabs as $tab) {
                    echo "   - '" . $tab->getText() . "'\n";
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
        if ($this->driver)
            $this->driver->quit();
        if (is_resource($this->chromeProcess)) {
            proc_terminate($this->chromeProcess);
            proc_close($this->chromeProcess);
        }
    }
}

$demo = new DemoTabsInspeccion();
$demo->ejecutar();
