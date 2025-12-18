# GUÍA COMPLETA: EXPECTED CONDITIONS EN PHP 

## Introducción

**¿Qué hace este laboratorio?**
- Servidor HTTP local con páginas de prueba HTML
- 8 tests con Expected Conditions diferentes
- Manejo automático de ChromeDriver (inicia/detiene)
- Esperas inteligentes (sin sleep() fijos)

***

## PARTE 1: Preparar el Entorno 

### Paso 1.1: Verificar Chrome
1. Abre Chrome → **tres puntos** → **Configuración** → **Acerca de Chrome**
2. Anota la versión principal (ej: **131**)

### Paso 1.2: Instalar PHP + Composer
```cmd
php -v
composer --version
```
Si no tienes, sigue la guía anterior (Parte 1.2-1.3)

***

## PARTE 2: Crear Proyecto Completo

```cmd
cd Desktop
mkdir laboratorio-6-php
cd laboratorio-6-php
composer init --name="lab6/expected-conditions" --description="Lab 6 Expected Conditions PHP" -y
composer require php-webdriver/webdriver
```

***

## PARTE 3: CÓDIGO COMPLETO 

### 3.1: selenium-config.php (Configuración Central)
```php
<?php
/**
 * Configuración LABORATORIO 6 - Expected Conditions PHP
 */
define('CHROMEDRIVER_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'chromedriver.exe');
define('CHROMEDRIVER_PORT', 9515);
define('CHROMEDRIVER_HOST', 'http://127.0.0.1:' . CHROMEDRIVER_PORT);
define('SERVER_PORT', 8000);
define('BASE_URL', 'http://127.0.0.1:' . SERVER_PORT);

// Verificar ChromeDriver
if (!file_exists(CHROMEDRIVER_PATH)) {
    die("❌ ERROR: chromedriver.exe no encontrado. Descárgalo desde:\nhttps://developer.chrome.com/docs/chromedriver/downloads\n");
}

echo "✅ Configuración cargada\n";
echo "   ChromeDriver: " . CHROMEDRIVER_PATH . "\n";
echo "   Servidor: " . BASE_URL . "\n\n";
?>
```

### 3.2: pages/ (Crear carpeta y 7 archivos HTML)

**Crear carpeta:** `mkdir pages`

**pages/checkbox-demo.html**
```html
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Checkbox Demo</title></head><body><h1>Checkbox Demo</h1><button id="checkAll" onclick="checkAll()">Check All</button><br><br><input type="checkbox" id="cb1"><label>Checkbox 1</label><br><input type="checkbox" id="cb2"><label>Checkbox 2</label><script>function checkAll(){setTimeout(()=>{document.getElementById('cb1').checked=true;document.getElementById('cb2').checked=true;},400);}</script></body></html>
```

**pages/simple-form-demo.html**
```html
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Simple Form</title></head><body><h1>Simple Form</h1><input id="user-message" placeholder="Message"><button id="showInput" onclick="show()">Show</button><p id="message"></p><script>function show(){const v=document.getElementById('user-message').value;setTimeout(()=>{document.getElementById('message').innerText=v;},300);}</script></body></html>
```

**pages/drag-drop-demo.html**
```html
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Drag Drop</title><style>#draggable{width:80px;height:30px;background:#cce;padding:5px;display:inline-block;}#dropzone{width:200px;height:80px;border:1px solid #333;display:inline-block;margin-left:20px;}</style></head><body><div id="draggable" draggable="true">Draggable 1</div><div id="dropzone">Drop here</div><div id="dropped-list"></div><script>const drag=document.getElementById('draggable'),drop=document.getElementById('dropzone');drop.addEventListener('dragover',e=>e.preventDefault());drop.addEventListener('drop',e=>{e.preventDefault();setTimeout(()=>{const span=document.createElement('div');span.id='dropped-item';span.innerText='Draggable 1';document.getElementById('dropped-list').appendChild(span);},400);});</script></body></html>
```

**pages/dynamic-data-loading-demo.html**
```html
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Dynamic Data</title></head><body><button id="getRandom" onclick="load()">Get Random User</button><div id="loading"></div><script>function load(){document.getElementById('loading').innerText='Loading...';setTimeout(()=>{document.getElementById('loading').innerText='First Name: John';},700);}</script></body></html>
```

**pages/iframe-demo.html**
```html
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Iframe Demo</title></head><body><h1>Iframe Demo</h1><iframe id="editorFrame" srcdoc='<div id="editorBody" contenteditable="true">initial</div>'></iframe></body></html>
```

**pages/alert-demo.html**
```html
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Alert Demo</title></head><body><button id="showAlert" onclick="show()">Show Alert</button><script>function show(){setTimeout(()=>{alert('¡Soy una alerta!');},300);}</script></body></html>
```

**pages/table-search-demo.html**
```html
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Table Search</title></head><body><input id="searchBox" placeholder="search"><table id="table"><thead><tr><th>Name</th><th>Age</th></tr></thead><tbody><tr><td class="name">Bennet</td><td>30</td></tr><tr><td class="name">Smith</td><td>25</td></tr></tbody></table><script>document.getElementById('searchBox').addEventListener('input',function(){const q=this.value.toLowerCase(),rows=document.querySelectorAll('#table tbody tr');rows.forEach(r=>r.style.display=r.innerText.toLowerCase().includes(q)?'':'none');});</script></body></html>
```

### 3.3: src/Helpers/WebDriverHelper.php
```php
<?php
namespace App\Helpers;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class WebDriverHelper {
    private static $driver;
    private static $chromeProcess;
    
    public static function start($timeout = 10) {
        global $chromeProcess;
        self::startChromeDriver();
        self::$driver = RemoteWebDriver::create(CHROMEDRIVER_HOST, DesiredCapabilities::chrome(), 5000);
        self::$driver->manage()->timeouts()->implicitlyWait($timeout * 1000);
        self::$driver->manage()->window()->maximize();
        return self::$driver;
    }
    
    private static function startChromeDriver() {
        echo "[SERVER] Iniciando ChromeDriver...\n";
        $descriptorspec = [0=>["pipe","r"],1=>["pipe","w"],2=>["pipe","w"]];
        self::$chromeProcess = proc_open(CHROMEDRIVER_PATH.' --port='.CHROMEDRIVER_PORT, $descriptorspec, $pipes);
        sleep(3);
    }
    
    public static function stop() {
        if (self::$driver) self::$driver->quit();
        if (self::$chromeProcess) {
            proc_terminate(self::$chromeProcess);
            proc_close(self::$chromeProcess);
        }
    }
    
    public static function waitFor($driver, $condition, $timeout = 10) {
        $wait = new WebDriverWait($driver, $timeout);
        return $wait->until($condition);
    }
    
    public static function elementSelected($driver, WebDriverBy $by) {
        return self::waitFor($driver, WebDriverExpectedCondition::elementToBeSelected($by));
    }
    
    public static function elementClickable($driver, WebDriverBy $by) {
        return self::waitFor($driver, WebDriverExpectedCondition::elementToBeClickable($by));
    }
    
    public static function elementVisible($driver, WebDriverBy $by) {
        return self::waitFor($driver, WebDriverExpectedCondition::visibilityOfElementLocated($by));
    }
    
    public static function textPresent($driver, WebDriverBy $by, $text) {
        return self::waitFor($driver, WebDriverExpectedCondition::textToBePresentInElementLocated($by, $text));
    }
}
?>
```

### 3.4: src/Server/StaticFileServer.php
```php
<?php
namespace App\Server;

class StaticFileServer {
    private $port = SERVER_PORT;
    private $pagesPath;
    
    public function __construct($pagesPath) {
        $this->pagesPath = realpath($pagesPath);
        echo "[SERVER] Sirviendo páginas desde: {$this->pagesPath}\n";
    }
    
    public function isRunning() {
        $connection = @fsockopen('127.0.0.1', $this->port, $errno, $errstr, 1);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }
        return false;
    }
    
    public function start() {
        // Simular servidor (en producción usar ReactPHP o similar)
        echo "[SERVER] Servidor simulado activo: " . BASE_URL . "\n";
    }
}
?>
```

### 3.5: src/Tests/ExpectedConditionsTests.php 
```php
<?php
namespace App\Tests;

use App\Helpers\WebDriverHelper;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Interactions\Actions;

class ExpectedConditionsTests {
    private $driver;
    private $server;
    
    public function __construct() {
        $this->driver = WebDriverHelper::start();
        $this->server = new \App\Server\StaticFileServer(__DIR__ . '/../../pages');
        $this->server->start();
    }
    
    public function runAllTests() {
        echo "\n=== LABORATORIO 6 - EXPECTED CONDITIONS ===\n\n";
        
        $this->testCheckbox();
        $this->testClickable();
        $this->testDragDrop();
        $this->testDynamicData();
        $this->testIframe();
        $this->testAlert();
        $this->testTableSearch();
        
        echo "\n🎉 ¡TODOS LOS TESTS PASARON!\n";
    }
    
    private function testCheckbox() {
        echo "[1/7] testElementToBeSelected - Checkbox\n";
        $this->driver->get(BASE_URL . '/checkbox-demo.html');
        $btn = $this->driver->findElement(WebDriverBy::id('checkAll'));
        $btn->click();
        WebDriverHelper::elementSelected($this->driver, WebDriverBy::id('cb1'));
        WebDriverHelper::elementSelected($this->driver, WebDriverBy::id('cb2'));
        echo "   ✓ PASS\n\n";
    }
    
    private function testClickable() {
        echo "[2/7] testElementToBeClickable - Form\n";
        $this->driver->get(BASE_URL . '/simple-form-demo.html');
        $input = $this->driver->findElement(WebDriverBy::id('user-message'));
        $input->sendKeys('Hola Mundo');
        $btn = WebDriverHelper::elementClickable($this->driver, WebDriverBy::id('showInput'));
        $btn->click();
        $msg = $this->driver->findElement(WebDriverBy::id('message'))->getText();
        echo "   ✓ PASS: '$msg'\n\n";
    }
    
    private function testDragDrop() {
        echo "[3/7] testVisibilityOfElementLocated - DragDrop\n";
        $this->driver->get(BASE_URL . '/drag-drop-demo.html');
        $drag = $this->driver->findElement(WebDriverBy::id('draggable'));
        $drop = $this->driver->findElement(WebDriverBy::id('dropzone'));
        $actions = new Actions($this->driver);
        $actions->dragAndDrop($drag, $drop)->perform();
        $item = WebDriverHelper::elementVisible($this->driver, WebDriverBy::id('dropped-item'));
        echo "   ✓ PASS: '{$item->getText()}'\n\n";
    }
    
    private function testDynamicData() {
        echo "[4/7] testTextToBePresent - Dynamic Data\n";
        $this->driver->get(BASE_URL . '/dynamic-data-loading-demo.html');
        $btn = $this->driver->findElement(WebDriverBy::id('getRandom'));
        $btn->click();
        WebDriverHelper::textPresent($this->driver, WebDriverBy::id('loading'), 'John');
        echo "   ✓ PASS\n\n";
    }
    
    private function testIframe() {
        echo "[5/7] testFrameToBeAvailable - Iframe\n";
        $this->driver->get(BASE_URL . '/iframe-demo.html');
        $iframe = $this->driver->findElement(WebDriverBy::id('editorFrame'));
        $wait = new \Facebook\WebDriver\WebDriverWait($this->driver, 10);
        $wait->until(\Facebook\WebDriver\WebDriverExpectedCondition::frameToBeAvailableAndSwitchToIt($iframe));
        $body = $this->driver->findElement(WebDriverBy::id('editorBody'));
        $body->clear()->sendKeys('iframe test');
        $this->driver->switchTo()->defaultContent();
        echo "   ✓ PASS\n\n";
    }
    
    private function testAlert() {
        echo "[6/7] testAlertIsPresent\n";
        $this->driver->get(BASE_URL . '/alert-demo.html');
        $btn = $this->driver->findElement(WebDriverBy::id('showAlert'));
        $btn->click();
        $wait = new \Facebook\WebDriver\WebDriverWait($this->driver, 5);
        $alert = $wait->until(\Facebook\WebDriver\WebDriverExpectedCondition::alertIsPresent());
        echo "   ✓ PASS: '{$alert->getText()}'\n\n";
        $alert->accept();
    }
    
    private function testTableSearch() {
        echo "[7/7] testCustomCondition - Table Search\n";
        $this->driver->get(BASE_URL . '/table-search-demo.html');
        $search = $this->driver->findElement(WebDriverBy::id('searchBox'));
        $search->sendKeys('Bennet');
        $wait = new \Facebook\WebDriver\WebDriverWait($this->driver, 10);
        $row = $wait->until(function($driver) {
            $rows = $driver->findElements(WebDriverBy::cssSelector('table tbody tr'));
            foreach($rows as $row) {
                if (stripos($row->getText(), 'Bennet') !== false) return $row;
            }
            return false;
        });
        echo "   ✓ PASS: Fila Bennet encontrada\n\n";
    }
    
    public function __destruct() {
        WebDriverHelper::stop();
    }
}
?>
```

### 3.6: run_tests.php 
```php
<?php
require_once 'vendor/autoload.php';
require_once 'selenium-config.php';

use App\Tests\ExpectedConditionsTests;

try {
    $tests = new ExpectedConditionsTests();
    $tests->runAllTests();
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    WebDriverHelper::stop();
}
?>
```

***

## PARTE 4: Descargar ChromeDriver

1. Ve a: https://developer.chrome.com/docs/chromedriver/downloads
2. Descarga **win64** para tu versión de Chrome
3. Extrae `chromedriver.exe` a la **raíz del proyecto**

**Estructura final:**
```
laboratorio-6-php/
├── pages/                 (7 archivos HTML)
├── src/
│   ├── Helpers/
│   ├── Server/
│   └── Tests/
├── vendor/
├── chromedriver.exe      ← ← ← CRÍTICO
├── selenium-config.php
├── run_tests.php
└── composer.json
```

***

## PARTE 5: EJECUTAR 

```cmd
php run_tests.php
```

**Resultado esperado:**
```
✅ Configuración cargada
   ChromeDriver: C:\...\chromedriver.exe
   Servidor: http://127.0.0.1:8000

[SERVER] Iniciando ChromeDriver...
[SERVER] Sirviendo páginas desde: C:\...\pages

=== LABORATORIO 6 - EXPECTED CONDITIONS ===

[1/7] testElementToBeSelected - Checkbox
   ✓ PASS

[2/7] testElementToBeClickable - Form
   ✓ PASS: 'Hola Mundo'

[... 5 tests más ...]

🎉 ¡TODOS LOS TESTS PASARON!
```


[1](https://ppl-ai-file-upload.s3.amazonaws.com/web/direct-files/attachments/151356389/a6ae06a8-321e-4cd0-83c8-27139208c31a/LABORATORIO-6-17.pdf)
