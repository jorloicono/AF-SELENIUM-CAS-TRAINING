<?php
/**
 * LABORATORIO COMPLETO: TODAS LAS ACCIONES SOBRE WebElement
 * 
 * Este archivo demuestra 50+ métodos disponibles en WebElement
 * Organizados por categoría funcional
 */

require_once 'vendor/autoload.php';
require_once 'selenium-config.php';
require_once 'src/BaseTest.php';

use App\BaseTest;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Interactions\Actions;

class WebElementLab extends BaseTest
{

    public function runCompleteLab()
    {
        echo "🚀 LABORATORIO WEBELEMENT - TODAS LAS ACCIONES\n";
        echo str_repeat("═", 70) . "\n\n";

        $this->setup();
        $this->driver->get("C:\Users\Jorge\Desktop\AF-SELENIUM-CAS-TRAINING\RECURSOS\html\\test-page.html");

        $this->testBasicProperties();
        $this->testTextAndValue();
        $this->testAttributes();
        $this->testStateMethods();
        $this->testActions();
        $this->testScroll();
        $this->testJavaScript();
        $this->testAdvanced();

        $this->teardown();
    }

    // ============================================
    // CATEGORÍA 1: PROPIEDADES BÁSICAS
    // ============================================

    public function testBasicProperties()
    {
        echo "📋 1. PROPIEDADES BÁSICAS\n";
        echo str_repeat("-", 50) . "\n";

        $input = $this->driver->findElement(WebDriverBy::id('text-input'));

        echo "✓ Tag Name: " . $input->getTagName() . "\n";
        echo "✓ Location: (" . $input->getLocation()->getX() . "," . $input->getLocation()->getY() . ")\n";
        echo "✓ Size: " . $input->getSize()->getWidth() . "x" . $input->getSize()->getHeight() . "\n";
        echo "✓ CSS Value (background): " . $input->getCssValue('background-color') . "\n";
        echo "✓ Rectangle: " . json_encode($input->getRect()) . "\n\n";
    }

    // ============================================
    // CATEGORÍA 2: TEXTO Y VALOR
    // ============================================

    public function testTextAndValue()
    {
        echo "📝 2. TEXTO Y VALOR\n";
        echo str_repeat("-", 50) . "\n";

        $input = $this->driver->findElement(WebDriverBy::id('text-input'));
        $textarea = $this->driver->findElement(WebDriverBy::id('textarea'));

        echo "Antes:\n";
        echo "  Input value: '" . $input->getAttribute('value') . "'\n";
        echo "  Input text: '" . $input->getText() . "'\n";
        echo "  Textarea text: '" . $textarea->getText() . "'\n";

        $input->clear();
        $input->sendKeys('Nuevo texto');
        $textarea->sendKeys("\nNueva línea");

        echo "\nDespués:\n";
        echo "  Input value: '" . $input->getAttribute('value') . "'\n";
        echo "  Input text: '" . $input->getText() . "'\n";
        echo "  Textarea text: '" . $textarea->getText() . "'\n\n";
    }

    // ============================================
    // CATEGORÍA 3: ATRIBUTOS
    // ============================================

    public function testAttributes()
    {
        echo "🏷️  3. ATRIBUTOS HTML\n";
        echo str_repeat("-", 50) . "\n";

        $input = $this->driver->findElement(WebDriverBy::id('text-input'));

        echo "Atributos disponibles:\n";
        $attributes = ['id', 'type', 'value', 'placeholder', 'class', 'style', 'disabled'];

        foreach ($attributes as $attr) {
            $value = $input->getAttribute($attr);
            echo "  $attr: " . ($value ?: '(null)') . "\n";
        }

        echo "\nDOM Attributes:\n";
        $domAttrs = $input->getDomAttribute('id');
        echo "  DOM id: " . $domAttrs . "\n";

        echo "\nProperty 'value': " . $input->getDomProperty('value') . "\n\n";
    }

    // ============================================
    // CATEGORÍA 4: ESTADOS DEL ELEMENTO
    // ============================================

    public function testStateMethods()
    {
        echo "🔧 4. ESTADOS DEL ELEMENTO\n";
        echo str_repeat("-", 50) . "\n";

        $checkbox1 = $this->driver->findElement(WebDriverBy::id('checkbox1'));
        $checkbox2 = $this->driver->findElement(WebDriverBy::id('checkbox2'));
        $button2 = $this->driver->findElement(WebDriverBy::id('button2'));

        echo "Checkbox 1:\n";
        echo "  Selected: " . ($checkbox1->isSelected() ? '✓' : '✗') . "\n";
        echo "  Enabled: " . ($checkbox1->isEnabled() ? '✓' : '✗') . "\n";
        echo "  Displayed: " . ($checkbox1->isDisplayed() ? '✓' : '✗') . "\n";

        echo "\nCheckbox 2:\n";
        echo "  Selected: " . ($checkbox2->isSelected() ? '✓' : '✗') . "\n";
        $checkbox2->click();
        echo "  Después del click: " . ($checkbox2->isSelected() ? '✓' : '✗') . "\n";

        echo "\nButton Disabled:\n";
        echo "  Enabled: " . ($button2->isEnabled() ? '✓' : '✗') . "\n";
        echo "  Displayed: " . ($button2->isDisplayed() ? '✓' : '✗') . "\n\n";
    }

    // ============================================
    // CATEGORÍA 5: ACCIONES PRINCIPALES
    // ============================================

    public function testActions()
    {
        echo "🖱️  5. ACCIONES PRINCIPALES\n";
        echo str_repeat("-", 50) . "\n";

        $button1 = $this->driver->findElement(WebDriverBy::id('button1'));

        echo "Antes del click:\n";
        echo "  Rect: " . json_encode($button1->getRect()) . "\n";

        $button1->click();
        echo "✓ Click ejecutado\n";

        $button1->submit();
        echo "✓ Submit ejecutado\n";

        $input = $this->driver->findElement(WebDriverBy::id('text-input'));
        $input->sendKeys('Texto nuevo');
        echo "✓ SendKeys ejecutado\n";

        $input->clear();
        echo "✓ Clear ejecutado\n\n";
    }

    // ============================================
    // CATEGORÍA 6: SCROLL Y VISIBILIDAD
    // ============================================

    public function testScroll()
    {
        echo "📜 6. SCROLL Y VISIBILIDAD\n";
        echo str_repeat("-", 50) . "\n";

        // Scroll al elemento
        $textarea = $this->driver->findElement(WebDriverBy::id('textarea'));
        $textarea->scrollIntoView();
        echo "✓ Scroll al textarea\n";

        // Scroll más arriba
        $textarea->scrollIntoView(true); // alignToTop = true
        echo "✓ Scroll arriba del textarea\n";

        // Scroll a posición específica
        $textarea->scroll(0, 100);
        echo "✓ Scroll posición específica\n\n";
    }

    // ============================================
    // CATEGORÍA 7: JAVASCRIPT
    // ============================================

    public function testJavaScript()
    {
        echo "⚡ 7. JAVASCRIPT EN ELEMENTO\n";
        echo str_repeat("-", 50) . "\n";

        $input = $this->driver->findElement(WebDriverBy::id('text-input'));

        // Obtener valor vía JavaScript
        $jsValue = $this->driver->executeScript('return arguments[0].value;', $input);
        echo "Valor JS: $jsValue\n";

        // Cambiar estilo vía JS
        $this->driver->executeScript('arguments[0].style.backgroundColor = "yellow";', $input);
        echo "✓ Estilo cambiado vía JS\n";

        // Hacer elemento visible
        $hiddenDiv = $this->driver->findElement(WebDriverBy::id('hidden-div'));
        $this->driver->executeScript('arguments[0].style.display = "block";', $hiddenDiv);
        echo "✓ Elemento oculto ahora visible vía JS\n\n";
    }

    // ============================================
    // CATEGORÍA 8: ACCIONES AVANZADAS
    // ============================================

    public function testAdvanced()
    {
        echo "🔥 8. ACCIONES AVANZADAS\n";
        echo str_repeat("-", 50) . "\n";

        // Hover con Actions
        $button1 = $this->driver->findElement(WebDriverBy::id('button1'));
        $actions = new Actions($this->driver);
        $actions->moveToElement($button1)->perform();
        echo "✓ Hover ejecutado\n";

        // Drag slider
        $slider = $this->driver->findElement(WebDriverBy::id('slider'));
        $initialValue = $slider->getAttribute('value');
        echo "Valor inicial slider: $initialValue\n";

        $actions = new Actions($this->driver);
        $actions->dragAndDropBy($slider, 50, 0)->perform();
        $finalValue = $slider->getAttribute('value');
        echo "Valor final slider: $finalValue\n";

        // Upload file (simulado)
        $fileInput = $this->driver->findElement(WebDriverBy::id('file-input'));
        $fileInput->sendKeys(__FILE__); // Este archivo mismo
        echo "✓ Archivo seleccionado\n";

        echo "\n✅ TODAS LAS ACCIONES PROBADAS\n";
    }
}

// EJECUTAR
$lab = new WebElementLab();
$lab->runCompleteLab();
?>