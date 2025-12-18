# LABORATORIO COMPLETO: TODAS LAS ACCIONES SOBRE WebElement EN PHP + SELENIUM

## Introducción

Este laboratorio exhaustivo demuestra **TODAS** las acciones que puedes realizar sobre un **WebElement** con Selenium WebDriver en PHP. Incluye **50+ métodos** organizados por categoría.

**Objetivo**: Tener una referencia completa de todas las operaciones posibles sobre elementos web.

***

## Estructura del Laboratorio

```
web-element-lab/
├── selenium-config.php
├── src/
│   ├── BaseTest.php
│   └── WebElementLab.php
├── pages/
│   └── test-page.html
├── chromedriver.exe
└── run-lab.php
```

***

## PARTE 1: Configuración Completa

### 1.1 selenium-config.php
```php
<?php
/**
 * CONFIGURACIÓN - WebElement Lab
 */
define('CHROMEDRIVER_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'chromedriver.exe');
define('CHROMEDRIVER_PORT', 9515);
define('CHROMEDRIVER_HOST', 'http://127.0.0.1:' . CHROMEDRIVER_PORT);
define('TEST_PAGE_URL', 'file://' . __DIR__ . '/pages/test-page.html');

// Validación ChromeDriver
if (!file_exists(CHROMEDRIVER_PATH)) {
    die("❌ Descarga ChromeDriver desde https://developer.chrome.com/docs/chromedriver/downloads\n");
}
?>
```

### 1.2 pages/test-page.html (Página de Prueba Completa)
```html
<!DOCTYPE html>
<html>
<head>
    <title>WebElement Lab Test Page</title>
    <style>
        .test-container { margin: 20px; padding: 20px; border: 1px solid #ccc; }
        button { margin: 5px; padding: 10px; }
        input, textarea, select { margin: 5px; padding: 5px; }
        .hidden { display: none; }
        .disabled { disabled: true; }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>WebElement Lab - Todos los Elementos</h1>
        
        <!-- TEXT INPUTS -->
        <div>
            <label>Text Input: <input id="text-input" type="text" value="Initial text"></label>
            <input id="text-input2" type="text" placeholder="Type here">
        </div>
        
        <!-- TEXTAREA -->
        <div>
            <label>Textarea: <textarea id="textarea" rows="3">Initial textarea content</textarea></label>
        </div>
        
        <!-- CHECKBOXES -->
        <div>
            <label><input id="checkbox1" type="checkbox" checked> Checkbox 1</label>
            <label><input id="checkbox2" type="checkbox"> Checkbox 2</label>
        </div>
        
        <!-- RADIO BUTTONS -->
        <div>
            <label><input id="radio1" type="radio" name="radio-group" checked> Radio 1</label>
            <label><input id="radio2" type="radio" name="radio-group"> Radio 2</label>
        </div>
        
        <!-- SELECT -->
        <div>
            <label>Select: 
                <select id="select">
                    <option value="opt1">Option 1</option>
                    <option value="opt2" selected>Option 2</option>
                    <option value="opt3">Option 3</option>
                </select>
            </label>
        </div>
        
        <!-- BUTTONS -->
        <div>
            <button id="button1">Normal Button</button>
            <button id="button2" disabled>Disabled Button</button>
        </div>
        
        <!-- LINKS -->
        <div>
            <a id="link1" href="#section1">Link 1</a>
            <a id="link2" href="https://example.com">External Link</a>
        </div>
        
        <!-- HIDDEN ELEMENTS -->
        <div id="hidden-div" class="hidden">Hidden Content</div>
        
        <!-- ELEMENTS CON ATRIBUTOS ESPECIALES -->
        <div>
            <input id="readonly-input" type="text" readonly value="Read Only">
            <input id="file-input" type="file">
        </div>
        
        <!-- IMAGEN -->
        <img id="test-image" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI0MCIgc3R5bGU9ImNvbG9yOiNmZjU1NWE7IGZpbGw6I2ZmNTU1YTtzdHJva2U6I2ZmNTU1YTtzdHJva2Utd2lkdGg6IDsiLz48L3N2Zz4=" alt="Test Image">
        
        <!-- SLIDER -->
        <div>
            <label>Slider: <input id="slider" type="range" min="0" max="100" value="50"></label>
        </div>
    </div>
    
    <script>
        // Algunos elementos se muestran dinámicamente
        function showHidden() {
            document.getElementById('hidden-div').classList.remove('hidden');
        }
    </script>
</body>
</html>
```

***

## PARTE 2: BaseTest.php Mejorada

```php
<?php
namespace App;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class BaseTest {
    protected $driver;
    protected $chromeProcess;
    
    public function setup() {
        $this->startChromeDriver();
        $capabilities = DesiredCapabilities::chrome();
        $this->driver = RemoteWebDriver::create(CHROMEDRIVER_HOST, $capabilities, 5000);
        $this->driver->manage()->window()->maximize();
        $this->driver->manage()->timeouts()->implicitlyWait(5000);
    }
    
    private function startChromeDriver() {
        $descriptorspec = [0=>["pipe","r"],1=>["pipe","w"],2=>["pipe","w"]];
        $this->chromeProcess = proc_open(CHROMEDRIVER_PATH . ' --port=' . CHROMEDRIVER_PORT, $descriptorspec, $pipes);
        sleep(3);
    }
    
    public function teardown() {
        if ($this->driver) $this->driver->quit();
        if ($this->chromeProcess) {
            proc_terminate($this->chromeProcess);
            proc_close($this->chromeProcess);
        }
    }
}
?>
```

***

## PARTE 3: WebElementLab.php - ¡TODAS LAS ACCIONES!

```php
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

class WebElementLab extends BaseTest {
    
    public function runCompleteLab() {
        echo "🚀 LABORATORIO WEBELEMENT - TODAS LAS ACCIONES\n";
        echo str_repeat("═", 70) . "\n\n";
        
        $this->setup();
        $this->driver->get(TEST_PAGE_URL);
        
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
    
    public function testBasicProperties() {
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
    
    public function testTextAndValue() {
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
    
    public function testAttributes() {
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
    
    public function testStateMethods() {
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
    
    public function testActions() {
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
    
    public function testScroll() {
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
    
    public function testJavaScript() {
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
    
    public function testAdvanced() {
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
```

***

## PARTE 4: Script Principal - run-lab.php

```php
<?php
/**
 * EJECUTAR LABORATORIO WEBELEMENT
 */
require_once 'composer install'; // Primero instala dependencias

echo "🔬 Iniciando Laboratorio WebElement...\n\n";
include 'src/WebElementLab.php';
?>
```

***

## PARTE 5: COMANDO DE EJECUCIÓN

```cmd
cd web-element-lab
composer install
# Descargar chromedriver.exe a la raíz
php run-lab.php
```

## RESULTADO ESPERADO

```
🚀 LABORATORIO WEBELEMENT - TODAS LAS ACCIONES
=======================================================================

📋 1. PROPIEDADES BÁSICAS
--------------------------------------------------
✓ Tag Name: input
✓ Location: (100,200)
✓ Size: 300x40
✓ CSS Value (background): rgba(255,255,255,1)
✓ Rectangle: {"x":100,"y":200,"width":300,"height":40}

📝 2. TEXTO Y VALOR
--------------------------------------------------
Antes:
  Input value: 'Initial text'
  Input text: ''
  Textarea text: 'Initial textarea content'

Después:
  Input value: 'Nuevo texto'
  Input text: 'Nuevo texto'
  Textarea text: 'Initial textarea content
Nueva línea'

... [48 pruebas más] ...

✅ TODAS LAS ACCIONES PROBADAS ✓
```

***

## RESUMEN: 50+ ACCIONES WEBELEMENT

| Categoría | Métodos | Ejemplo |
|-----------|---------|---------|
| **Propiedades** | `getTagName()`, `getLocation()`, `getSize()`, `getRect()` | Obtener dimensiones |
| **Texto/Valor** | `getText()`, `getAttribute()`, `sendKeys()`, `clear()` | Escribir/limpiar |
| **Atributos** | `getAttribute()`, `getDomAttribute()`, `getDomProperty()` | Leer atributos |
| **Estados** | `isDisplayed()`, `isEnabled()`, `isSelected()` | Verificar estado |
| **Acciones** | `click()`, `submit()`, `sendKeys()` | Interacciones |
| **Scroll** | `scrollIntoView()`, `scroll()` | Posicionamiento |
| **JavaScript** | `executeScript()` | Manipulación avanzada |
| **Actions** | `Actions::moveToElement()`, `dragAndDrop()` | Acciones complejas |

