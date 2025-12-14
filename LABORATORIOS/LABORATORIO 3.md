# Guía Completa: Localizadores Selenium en PHP
## Encontrando elementos en https://www.selenium.dev/selenium/web/web-form.html

---

## INTRODUCCIÓN

Los **localizadores** son estrategias de búsqueda para encontrar elementos HTML en una página web. Selenium WebDriver proporciona múltiples formas de ubicar elementos, cada una con ventajas específicas.

En esta guía usaremos la página oficial de Selenium que contiene un formulario completo con todo tipo de elementos (inputs, selects, checkboxes, radio buttons, etc.).

---

## MÉTODOS DE LOCALIZACIÓN (EN ORDEN DE VELOCIDAD)

| Método | Velocidad | Fiabilidad | Uso Recomendado |
|--------|-----------|-----------|-----------------|
| **By ID** | ⚡⚡⚡ | ⭐⭐⭐⭐⭐ | Elemento único con ID |
| **By Name** | ⚡⚡⚡ | ⭐⭐⭐⭐ | Campos de formulario |
| **By CSS Selector** | ⚡⚡ | ⭐⭐⭐⭐⭐ | Selectores flexibles |
| **By XPath** | ⚡ | ⭐⭐⭐⭐⭐ | Búsquedas complejas |
| **By Class** | ⚡⚡ | ⭐⭐⭐ | Múltiples elementos |
| **By Tag Name** | ⚡⚡ | ⭐⭐ | Todos los elementos tipo |
| **By Link Text** | ⚡⚡ | ⭐⭐⭐⭐ | Enlaces específicos |

---

## PARTE 1: SETUP INICIAL

### Paso 1: Usar configuración anterior

Reutiliza `selenium-config.php` y `chromedriver.exe` del proyecto anterior.

### Paso 2: Crear archivo de prueba

Crea: `inspeccionar-formulario.php`

```php
<?php
/**
 * Script para Inspeccionar y Localizar Elementos
 * Página: https://www.selenium.dev/selenium/web/web-form.html
 * 
 * Demostraciones de todos los métodos de localización en Selenium
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class FormInspector {
    private $driver;
    private $chromeDriverProcess;
    private $url = 'https://www.selenium.dev/selenium/web/web-form.html';
    
    public function __construct() {
        echo "\n╔════════════════════════════════════════════════════════════╗\n";
        echo "║  INSPECTOR DE ELEMENTOS SELENIUM - PHP WebDriver          ║\n";
        echo "║  Página: https://www.selenium.dev/selenium/web/web-form.html║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";
        
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }
    
    private function iniciarChromeDriver() {
        echo "[1] Iniciando ChromeDriver...\n";
        
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
        
        sleep(2);
        echo "    ✓ ChromeDriver iniciado\n\n";
    }
    
    private function conectarDriver() {
        echo "[2] Conectando WebDriver...\n";
        
        try {
            $capabilities = DesiredCapabilities::chrome();
            $this->driver = RemoteWebDriver::create(CHROMEDRIVER_HOST, $capabilities, 5000);
            echo "    ✓ Conectado\n\n";
        } catch (Exception $e) {
            $this->detenerChromeDriver();
            throw new Exception("Error: " . $e->getMessage());
        }
    }
    
    /**
     * MÉTODO 1: LOCALIZAR POR ID
     * 
     * El ID es el método MÁS RÁPIDO y CONFIABLE
     * IDs deben ser únicos en la página
     */
    public function demostraciónPorID() {
        echo "\n" . str_repeat("═", 62) . "\n";
        echo "MÉTODO 1: LOCALIZAR POR ID (El más rápido)\n";
        echo str_repeat("═", 62) . "\n\n";
        
        try {
            // Localizamos el input de texto por su ID
            $textInput = $this->driver->findElement(WebDriverBy::id('my-text-input'));
            
            echo "✓ Elemento encontrado por ID: my-text-input\n";
            echo "  - Tipo de elemento: " . $textInput->getTagName() . "\n";
            echo "  - Atributo type: " . $textInput->getAttribute('type') . "\n";
            echo "  - Placeholder: " . $textInput->getAttribute('placeholder') . "\n";
            
            // Escribimos en el input
            $textInput->clear();
            $textInput->sendKeys('Datos encontrados por ID');
            echo "  - Texto enviado: 'Datos encontrados por ID'\n";
            
            // Leemos el valor
            $valor = $textInput->getAttribute('value');
            echo "  - Valor actual en input: '$valor'\n\n";
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * MÉTODO 2: LOCALIZAR POR NAME
     * 
     * El atributo NAME es muy útil para formularios
     * Común en campos HTML tradicionales
     */
    public function demostraciónPorName() {
        echo str_repeat("═", 62) . "\n";
        echo "MÉTODO 2: LOCALIZAR POR NAME (Formularios)\n";
        echo str_repeat("═", 62) . "\n\n";
        
        try {
            // Encontramos input por nombre
            $elements = $this->driver->findElements(WebDriverBy::name('my-password'));
            
            if (count($elements) > 0) {
                $passwordInput = $elements[0];
                echo "✓ Elemento encontrado por name: my-password\n";
                echo "  - Tipo: " . $passwordInput->getTagName() . "\n";
                echo "  - Type HTML: " . $passwordInput->getAttribute('type') . "\n";
                
                // Escribimos contraseña
                $passwordInput->clear();
                $passwordInput->sendKeys('Contraseña123');
                echo "  - Contraseña enviada\n\n";
            } else {
                echo "⚠ No se encontró elemento con name: my-password\n\n";
            }
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * MÉTODO 3: LOCALIZAR POR CLASS NAME
     * 
     * Busca por clase CSS
     * CUIDADO: Las clases pueden no ser únicas
     */
    public function demostraciónPorClass() {
        echo str_repeat("═", 62) . "\n";
        echo "MÉTODO 3: LOCALIZAR POR CLASS NAME\n";
        echo str_repeat("═", 62) . "\n\n";
        
        try {
            // Encontramos el textarea por su clase
            $elements = $this->driver->findElements(WebDriverBy::className('form-control'));
            
            echo "✓ Se encontraron " . count($elements) . " elementos con class 'form-control'\n\n";
            
            // Mostramos información de cada elemento encontrado
            foreach ($elements as $index => $element) {
                try {
                    $tagName = $element->getTagName();
                    $type = $element->getAttribute('type') ?: 'N/A';
                    $name = $element->getAttribute('name') ?: 'sin-nombre';
                    
                    echo "  Elemento " . ($index + 1) . ":\n";
                    echo "    - Tag: <$tagName>\n";
                    echo "    - Type: $type\n";
                    echo "    - Name: $name\n";
                } catch (Exception $e) {
                    // Ignoramos errores en propiedades específicas
                }
            }
            echo "\n";
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * MÉTODO 4: LOCALIZAR POR CSS SELECTOR
     * 
     * Selectores CSS muy poderosos y flexibles
     * Similar a jQuery o selectores CSS normales
     */
    public function demostraciónPorCSSSelector() {
        echo str_repeat("═", 62) . "\n";
        echo "MÉTODO 4: LOCALIZAR POR CSS SELECTOR (Muy flexible)\n";
        echo str_repeat("═", 62) . "\n\n";
        
        try {
            // Ejemplo 1: Por ID usando CSS
            $elemento1 = $this->driver->findElement(WebDriverBy::cssSelector('#my-text-input'));
            echo "✓ CSS Selector #my-text-input (busca por ID):\n";
            echo "  - Elemento encontrado: " . $elemento1->getTagName() . "\n";
            echo "  - Placeholder: " . $elemento1->getAttribute('placeholder') . "\n\n";
            
            // Ejemplo 2: Por atributo
            $elemento2 = $this->driver->findElement(WebDriverBy::cssSelector('input[type="password"]'));
            echo "✓ CSS Selector input[type=\"password\"]:\n";
            echo "  - Elemento encontrado: " . $elemento2->getTagName() . "\n";
            echo "  - Type: " . $elemento2->getAttribute('type') . "\n\n";
            
            // Ejemplo 3: Selectores combinados
            $elemento3 = $this->driver->findElement(WebDriverBy::cssSelector('input.form-control[name=\"my-text-input\"]'));
            echo "✓ CSS Selector input.form-control[name=\"my-text-input\"]:\n";
            echo "  - Elemento encontrado\n\n";
            
            // Ejemplo 4: Pseudo-selectores
            $botones = $this->driver->findElements(WebDriverBy::cssSelector('button:not([type=\"submit\"])'));
            echo "✓ CSS Selector button:not([type=\"submit\"]):\n";
            echo "  - Se encontraron " . count($botones) . " botones (sin submit)\n\n";
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * MÉTODO 5: LOCALIZAR POR XPATH
     * 
     * La estrategia MÁS PODEROSA pero más lenta
     * Permite búsquedas muy complejas y específicas
     */
    public function demostraciónPorXPath() {
        echo str_repeat("═", 62) . "\n";
        echo "MÉTODO 5: LOCALIZAR POR XPATH (El más poderoso)\n";
        echo str_repeat("═", 62) . "\n\n";
        
        try {
            // Ejemplo 1: XPath absoluto por ID
            $elem1 = $this->driver->findElement(WebDriverBy::xpath("//input[@id='my-text-input']"));
            echo "✓ XPath //input[@id='my-text-input']:\n";
            echo "  - Elemento encontrado\n\n";
            
            // Ejemplo 2: XPath por atributo type
            $elem2 = $this->driver->findElement(WebDriverBy::xpath("//input[@type='password']"));
            echo "✓ XPath //input[@type='password']:\n";
            echo "  - Encontrado input tipo password\n\n";
            
            // Ejemplo 3: XPath con predicado (posición)
            $elem3 = $this->driver->findElement(WebDriverBy::xpath("(//input[@type='text'])[1]"));
            echo "✓ XPath (//input[@type='text'])[1] (primer input text):\n";
            echo "  - Elemento encontrado\n\n";
            
            // Ejemplo 4: XPath con contains() - búsqueda parcial
            $elem4 = $this->driver->findElement(WebDriverBy::xpath("//input[contains(@name, 'text')]"));
            echo "✓ XPath //input[contains(@name, 'text')]:\n";
            echo "  - Encontrado por nombre que contiene 'text'\n\n";
            
            // Ejemplo 5: XPath con starts-with()
            $elementos = $this->driver->findElements(WebDriverBy::xpath("//input[starts-with(@id, 'my-')]"));
            echo "✓ XPath //input[starts-with(@id, 'my-')]:\n";
            echo "  - Se encontraron " . count($elementos) . " elementos con ID que empieza con 'my-'\n\n";
            
            // Ejemplo 6: XPath con text()
            try {
                $enlace = $this->driver->findElement(WebDriverBy::xpath("//a[text()='Return to index']"));
                echo "✓ XPath //a[text()='Return to index']:\n";
                echo "  - Encontrado enlace por texto exacto\n";
                echo "  - URL: " . $enlace->getAttribute('href') . "\n\n";
            } catch (Exception $e) {
                echo "⚠ Enlace no encontrado\n\n";
            }
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * MÉTODO 6: LOCALIZAR POR TAG NAME
     * 
     * Busca todos los elementos de un tipo específico
     * Retorna múltiples elementos
     */
    public function demostraciónPorTagName() {
        echo str_repeat("═", 62) . "\n";
        echo "MÉTODO 6: LOCALIZAR POR TAG NAME (Múltiples elementos)\n";
        echo str_repeat("═", 62) . "\n\n";
        
        try {
            // Encontramos todos los inputs
            $inputs = $this->driver->findElements(WebDriverBy::tagName('input'));
            echo "✓ Tag <input>: Se encontraron " . count($inputs) . " elementos\n\n";
            
            // Contamos por tipo
            $tipos = array();
            foreach ($inputs as $input) {
                $type = $input->getAttribute('type');
                $tipos[$type] = ($tipos[$type] ?? 0) + 1;
            }
            
            echo "  Desglose por tipo:\n";
            foreach ($tipos as $tipo => $cantidad) {
                echo "    - type='$tipo': $cantidad elementos\n";
            }
            echo "\n";
            
            // Encontramos todos los botones
            $botones = $this->driver->findElements(WebDriverBy::tagName('button'));
            echo "✓ Tag <button>: Se encontraron " . count($botones) . " elementos\n\n";
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * MÉTODO 7: LOCALIZAR POR LINK TEXT
     * 
     * Especialmente útil para enlaces (<a>)
     */
    public function demostraciónPorLinkText() {
        echo str_repeat("═", 62) . "\n";
        echo "MÉTODO 7: LOCALIZAR POR LINK TEXT (Enlaces)\n";
        echo str_repeat("═", 62) . "\n\n";
        
        try {
            // Búsqueda exacta del texto del enlace
            $enlace = $this->driver->findElement(WebDriverBy::linkText('Return to index'));
            
            echo "✓ Link Text exacto 'Return to index':\n";
            echo "  - Elemento encontrado: <a>\n";
            echo "  - Href: " . $enlace->getAttribute('href') . "\n";
            echo "  - Texto visible: " . $enlace->getText() . "\n\n";
            
        } catch (Exception $e) {
            echo "⚠ No se encontró enlace: " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * MÉTODO 8: LOCALIZAR POR PARTIAL LINK TEXT
     * 
     * Búsqueda parcial del texto del enlace
     */
    public function demostraciónPorPartialLinkText() {
        echo str_repeat("═", 62) . "\n";
        echo "MÉTODO 8: LOCALIZAR POR PARTIAL LINK TEXT (Búsqueda parcial)\n";
        echo str_repeat("═", 62) . "\n\n";
        
        try {
            // Búsqueda parcial
            $enlace = $this->driver->findElement(WebDriverBy::partialLinkText('Return'));
            
            echo "✓ Partial Link Text 'Return':\n";
            echo "  - Elemento encontrado\n";
            echo "  - Texto completo: " . $enlace->getText() . "\n\n";
            
        } catch (Exception $e) {
            echo "⚠ No se encontró enlace: " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * COMPARACIÓN: ID vs NAME vs CLASS vs XPATH vs CSS
     * 
     * Un ejemplo práctico demostrando las diferencias
     */
    public function compararMetodos() {
        echo str_repeat("═", 62) . "\n";
        echo "COMPARACIÓN: Diferentes formas de localizar el MISMO elemento\n";
        echo str_repeat("═", 62) . "\n\n";
        
        $element_id = 'my-text-input';
        
        echo "Supongamos el HTML:\n";
        echo "  <input id=\"my-text-input\" \n";
        echo "         type=\"text\" \n";
        echo "         class=\"form-control\"\n";
        echo "         name=\"my-text-input\">\n\n";
        
        echo "MÉTODOS EQUIVALENTES para localizar este elemento:\n\n";
        
        try {
            // Por ID
            echo "1. Por ID (más rápido):\n";
            echo "   \$driver->findElement(WebDriverBy::id('my-text-input'));\n";
            $this->driver->findElement(WebDriverBy::id($element_id));
            echo "   ✓ Funciona\n\n";
            
            // Por Name
            echo "2. Por Name:\n";
            echo "   \$driver->findElement(WebDriverBy::name('my-text-input'));\n";
            $this->driver->findElement(WebDriverBy::name($element_id));
            echo "   ✓ Funciona\n\n";
            
            // Por CSS - ID
            echo "3. Por CSS Selector (ID):\n";
            echo "   \$driver->findElement(WebDriverBy::cssSelector('#my-text-input'));\n";
            $this->driver->findElement(WebDriverBy::cssSelector('#' . $element_id));
            echo "   ✓ Funciona\n\n";
            
            // Por CSS - Type
            echo "4. Por CSS Selector (Type + Name):\n";
            echo "   \$driver->findElement(WebDriverBy::cssSelector('input[name=\"my-text-input\"]'));\n";
            $this->driver->findElement(WebDriverBy::cssSelector('input[name="' . $element_id . '"]'));
            echo "   ✓ Funciona\n\n";
            
            // Por XPath - ID
            echo "5. Por XPath (ID):\n";
            echo "   \$driver->findElement(WebDriverBy::xpath(\"//input[@id='my-text-input']\"));\n";
            $this->driver->findElement(WebDriverBy::xpath("//input[@id='$element_id']"));
            echo "   ✓ Funciona\n\n";
            
            // Por XPath - Type
            echo "6. Por XPath (Type + Nombre):\n";
            echo "   \$driver->findElement(WebDriverBy::xpath(\"//input[@name='my-text-input']\"));\n";
            $this->driver->findElement(WebDriverBy::xpath("//input[@name='$element_id']"));
            echo "   ✓ Funciona\n\n";
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * TABLA COMPARATIVA DE ATRIBUTOS
     * 
     * Extrae y muestra información detallada de varios elementos
     */
    public function analizarElementosPrincipal() {
        echo str_repeat("═", 62) . "\n";
        echo "ANÁLISIS DETALLADO DE ELEMENTOS PRINCIPALES\n";
        echo str_repeat("═", 62) . "\n\n";
        
        try {
            // Buscamos todos los inputs de formulario
            $elementos_a_analizar = array(
                array('selector' => 'id', 'valor' => 'my-text-input', 'nombre' => 'Text Input'),
                array('selector' => 'id', 'valor' => 'my-textarea', 'nombre' => 'Textarea'),
                array('selector' => 'id', 'valor' => 'my-select', 'nombre' => 'Select Dropdown'),
                array('selector' => 'id', 'valor' => 'my-file', 'nombre' => 'File Input'),
            );
            
            foreach ($elementos_a_analizar as $elem_info) {
                try {
                    if ($elem_info['selector'] === 'id') {
                        $elemento = $this->driver->findElement(WebDriverBy::id($elem_info['valor']));
                    } else {
                        $elemento = $this->driver->findElement(WebDriverBy::name($elem_info['valor']));
                    }
                    
                    echo "━ " . $elem_info['nombre'] . ":\n";
                    echo "  ID: " . $elemento->getAttribute('id') . "\n";
                    echo "  Tag: <" . $elemento->getTagName() . ">\n";
                    echo "  Type: " . ($elemento->getAttribute('type') ?: 'N/A') . "\n";
                    echo "  Habilitado: " . ($elemento->isEnabled() ? 'Sí' : 'No') . "\n";
                    echo "  Visible: " . ($elemento->isDisplayed() ? 'Sí' : 'No') . "\n";
                    echo "\n";
                    
                } catch (Exception $e) {
                    echo "✗ No se encontró: " . $elem_info['nombre'] . "\n\n";
                }
            }
            
        } catch (Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    /**
     * Ejecutor principal
     */
    public function ejecutar() {
        try {
            // Navegamos a la página
            echo "[3] Navegando a página de prueba...\n";
            $this->driver->get($this->url);
            sleep(3);
            echo "    ✓ Página cargada\n\n";
            
            // Ejecutamos todas las demostraciones
            $this->demostraciónPorID();
            $this->demostraciónPorName();
            $this->demostraciónPorClass();
            $this->demostraciónPorCSSSelector();
            $this->demostraciónPorXPath();
            $this->demostraciónPorTagName();
            $this->demostraciónPorLinkText();
            $this->demostraciónPorPartialLinkText();
            $this->compararMetodos();
            $this->analizarElementosPrincipal();
            
        } catch (Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n\n";
        } finally {
            $this->detener();
        }
    }
    
    private function detener() {
        echo "\n" . str_repeat("═", 62) . "\n";
        echo "FINALIZANDO...\n";
        echo str_repeat("═", 62) . "\n\n";
        
        if ($this->driver !== null) {
            try {
                $this->driver->quit();
                echo "✓ Navegador cerrado\n";
            } catch (Exception $e) {
                echo "⚠ Error al cerrar: " . $e->getMessage() . "\n";
            }
        }
        
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
            echo "✓ ChromeDriver detenido\n";
        }
        
        echo "\n✓ PROCESO COMPLETADO\n\n";
    }
}

// Ejecutar el inspector
try {
    $inspector = new FormInspector();
    $inspector->ejecutar();
} catch (Exception $e) {
    echo "\n✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
?>
```

---

## PARTE 2: EJECUTAR EL SCRIPT

### En PowerShell:

```cmd
php inspeccionar-formulario.php
```

### Esperado:

```
╔════════════════════════════════════════════════════════════╗
║  INSPECTOR DE ELEMENTOS SELENIUM - PHP WebDriver          ║
║  Página: https://www.selenium.dev/selenium/web/web-form.html║
╚════════════════════════════════════════════════════════════╝

[1] Iniciando ChromeDriver...
    ✓ ChromeDriver iniciado

[2] Conectando WebDriver...
    ✓ Conectado

[3] Navegando a página de prueba...
    ✓ Página cargada

════════════════════════════════════════════════════════════
MÉTODO 1: LOCALIZAR POR ID (El más rápido)
════════════════════════════════════════════════════════════

✓ Elemento encontrado por ID: my-text-input
  - Tipo de elemento: input
  - Atributo type: text
  - Placeholder: Text input
  - Texto enviado: 'Datos encontrados por ID'
  - Valor actual en input: 'Datos encontrados por ID'

... (más salida)
```

---

## PARTE 3: REFERENCIA RÁPIDA DE SINTAXIS PHP

### ID (El más rápido)
```php
$elemento = $driver->findElement(WebDriverBy::id('my-text-input'));
```

### Name (Para formularios)
```php
$elemento = $driver->findElement(WebDriverBy::name('my-password'));
```

### Class (Múltiples elementos)
```php
$elementos = $driver->findElements(WebDriverBy::className('form-control'));
```

### CSS Selector (Muy flexible)
```php
// Por ID
$driver->findElement(WebDriverBy::cssSelector('#my-text-input'));

// Por clase
$driver->findElement(WebDriverBy::cssSelector('.form-control'));

// Por atributo
$driver->findElement(WebDriverBy::cssSelector('input[type="password"]'));

// Combinado
$driver->findElement(WebDriverBy::cssSelector('input.form-control[name="my-input"]'));
```

### XPath (El más poderoso)
```php
// Por ID
$driver->findElement(WebDriverBy::xpath("//input[@id='my-text-input']"));

// Por atributo
$driver->findElement(WebDriverBy::xpath("//input[@type='password']"));

// Por posición
$driver->findElement(WebDriverBy::xpath("(//input[@type='text'])[1]"));

// Con contains()
$driver->findElement(WebDriverBy::xpath("//input[contains(@name, 'text')]"));

// Con starts-with()
$driver->findElement(WebDriverBy::xpath("//input[starts-with(@id, 'my-')]"));

// Con text()
$driver->findElement(WebDriverBy::xpath("//a[text()='Return to index']"));
```

### Tag Name (Todos del tipo)
```php
$inputs = $driver->findElements(WebDriverBy::tagName('input'));
```

### Link Text (Enlaces)
```php
// Exacto
$driver->findElement(WebDriverBy::linkText('Return to index'));

// Parcial
$driver->findElement(WebDriverBy::partialLinkText('Return'));
```

---

## PARTE 4: OPERACIONES COMUNES CON ELEMENTOS

Una vez encontrado un elemento:

```php
// LECTURA
$texto = $elemento->getText();                    // Texto visible
$valor = $elemento->getAttribute('value');        // Atributo específico
$id = $elemento->getAttribute('id');
$class = $elemento->getAttribute('class');

// ESCRITURA
$elemento->clear();                               // Limpia el valor
$elemento->sendKeys('Texto a escribir');         // Escribe texto

// INTERACCIÓN
$elemento->click();                               // Click
$elemento->submit();                              // Envía formulario
$elemento->doubleClick();                        // Doble click

// ESTADO
$habilitado = $elemento->isEnabled();            // ¿Está habilitado?
$visible = $elemento->isDisplayed();             // ¿Se ve?
$seleccionado = $elemento->isSelected();         // ¿Está seleccionado?

// PROPIEDADES
$tagName = $elemento->getTagName();              // <input>, <div>, etc.
$tamaño = $elemento->getSize();                  // Dimensiones {width, height}
$ubicación = $elemento->getLocation();           // Posición {x, y}
```

---

## PARTE 5: BÚSQUEDA AVANZADA - MANEJO DE EXCEPCIONES

```php
<?php
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;

// Búsqueda segura con manejo de errores
try {
    $elemento = $driver->findElement(WebDriverBy::id('elemento-que-quizás-no-existe'));
    $elemento->sendKeys('Datos');
} catch (NoSuchElementException $e) {
    echo "Elemento no encontrado: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error inesperado: " . $e->getMessage();
}

// Búsqueda con espera (Wait) - para elementos dinámicos
use Facebook\WebDriver\WebDriverExpectedCondition;

$wait = $driver->wait(10, 500); // 10 segundos, checa cada 500ms

try {
    // Espera a que el elemento esté presente
    $elemento = $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::id('elemento-dinamico')
        )
    );
    
    // Espera a que sea visible
    $wait->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(
            WebDriverBy::id('elemento-dinamico')
        )
    );
    
} catch (TimeoutException $e) {
    echo "Tiempo de espera agotado";
}
?>
```

---

## PARTE 6: TABLA RESUMEN DE MÉTODOS

| Método | Sintaxis PHP | Velocidad | Casos de Uso |
|--------|-------------|-----------|-------------|
| **ID** | `WebDriverBy::id('id')` | ⚡⚡⚡ | Elementos únicos |
| **Name** | `WebDriverBy::name('name')` | ⚡⚡⚡ | Campos de formulario |
| **Class** | `WebDriverBy::className('class')` | ⚡⚡ | Elementos múltiples |
| **CSS** | `WebDriverBy::cssSelector('.class')` | ⚡⚡ | Selectores complejos |
| **XPath** | `WebDriverBy::xpath('//xpath')` | ⚡ | Búsquedas muy específicas |
| **Tag** | `WebDriverBy::tagName('button')` | ⚡⚡ | Todos los del tipo |
| **Link Text** | `WebDriverBy::linkText('text')` | ⚡⚡ | Enlaces exactos |
| **Partial Link** | `WebDriverBy::partialLinkText('text')` | ⚡⚡ | Enlaces parciales |

---

## PARTE 7: RECOMENDACIONES DE USO

✅ **USAR:**
- ID cuando el elemento tiene un ID único
- Name para campos de formulario
- CSS Selector para selectores simples y medianos
- XPath solo cuando sea absolutamente necesario

❌ **EVITAR:**
- XPath en bucles (muy lento)
- Selectores CSS demasiado largos
- Dependencias de clases que cambian constantemente
- Búsquedas sin manejo de excepciones

🎯 **MEJOR PRÁCTICA:**
```php
// Mal (acoplado a HTML)
$driver->findElement(WebDriverBy::xpath("//div/div/div/form/div/input[1]"));

// Bien (específico y mantenible)
$driver->findElement(WebDriverBy::id('email-input'));

// Si no hay ID, usar CSS simple
$driver->findElement(WebDriverBy::cssSelector('input[name="email"]'));
```

---

## Documentación Oficial

- **Selenium Locators:** https://www.selenium.dev/documentation/webdriver/elements/locators/
- **PHP WebDriver:** https://github.com/php-webdriver/php-webdriver
- **XPath Tutorial:** https://www.w3schools.com/xml/xpath_intro.asp
- **CSS Selectors:** https://www.w3schools.com/cssref/selectors_class.php

---

*Guía creada: Diciembre 2025*
*Compatible: PHP 7.4+ | Selenium 4.x | Chrome 110+*
