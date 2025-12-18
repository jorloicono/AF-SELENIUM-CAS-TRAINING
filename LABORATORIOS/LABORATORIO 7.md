# LABORATORIO COMPLETO: PARALELIZACIÓN DE PRUEBAS + SAUCEDOMO LOGIN (CSV)

## Introducción

Este laboratorio demuestra **paralelización de pruebas** con **Selenium Grid** y **PHP**, testando **login de SauceDemo.com** con **múltiples usuarios** desde un **archivo CSV**.

**Funcionalidades:**
- Leer usuarios desde CSV
- Ejecutar tests en paralelo (múltiples navegadores)
- Selenium Grid con Chrome/Firefox
- Reportes de resultados
- Manejo de fallos independientes

***

## Estructura del Proyecto

```
saucedemo-parallel/
├── composer.json
├── users.csv                 ← Usuarios de prueba
├── selenium-config.php
├── src/
│   ├── BaseTest.php
│   ├── SauceDemoLoginTest.php
│   └── ParallelRunner.php
├── grid/                     ← Selenium Grid
│   ├── selenium-server-standalone.jar
│   └── config.json
├── chromedriver.exe
├── geckodriver.exe
├── reports/                  ← Reportes generados
└── run_parallel_tests.php
```

***

## PARTE 1: Preparación

### 1.1 users.csv (Usuarios de Prueba)
```csv
username,password,expected_role,expected_url
standard_user,secret_sauce,standard_user,/inventory.html
locked_out_user,secret_sauce,locked_out_user,/
problem_user,secret_sauce,problem_user,/inventory.html
performance_glitch_user,secret_sauce,performance_glitch_user,/inventory.html
error_user,secret_sauce,error_user,/
```

### 1.2 composer.json
```json
{
    "name": "saucedemo-parallel-tests",
    "require": {
        "php": ">=7.4",
        "php-webdriver/webdriver": "^1.14"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

***

## PARTE 2: Configuración Selenium Grid

### 2.1 Descargar Selenium Grid
```bash
# Descargar Selenium Server Standalone 4.x
wget https://github.com/SeleniumHQ/selenium/releases/download/selenium-4.15.0/selenium-server-4.15.0.jar
# Mover a grid/selenium-server-standalone.jar
```

### 2.2 Iniciar Selenium Grid Hub
```bash
java -jar grid/selenium-server-standalone.jar hub --port 4444
```

### 2.3 Iniciar Nodos (Chrome y Firefox)
```bash
# Terminal 1 - Chrome Node
java -jar grid/selenium-server-standalone.jar node \
  --selenium-grid-hub http://localhost:4444 \
  --detect-drivers

# Terminal 2 - Firefox Node  
java -jar grid/selenium-server-standalone.jar node \
  --selenium-grid-hub http://localhost:4444 \
  --detect-drivers
```

***

## PARTE 3: BaseTest Mejorada para Paralelización

### src/BaseTest.php
```php
<?php
namespace App;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class BaseTest {
    protected $driver;
    protected $testId;
    protected $browser;
    
    public function __construct($testId, $browser = 'chrome') {
        $this->testId = $testId;
        $this->browser = $browser;
    }
    
    public function setup() {
        echo "[TEST {$this->testId}] [{$this->browser^^}] Iniciando...\n";
        
        $capabilities = match($this->browser) {
            'chrome' => DesiredCapabilities::chrome(),
            'firefox' => DesiredCapabilities::firefox(),
            default => DesiredCapabilities::chrome()
        };
        
        // Conectar a Selenium Grid Hub
        $this->driver = RemoteWebDriver::create(
            'http://localhost:4444',  // Selenium Grid Hub
            $capabilities,
            10000
        );
        
        $this->driver->manage()->window()->maximize();
        echo "[TEST {$this->testId}] [{$this->browser^^}] Driver listo\n";
    }
    
    public function teardown() {
        if ($this->driver) {
            $this->driver->quit();
            echo "[TEST {$this->testId}] [{$this->browser^^}] Finalizado\n\n";
        }
    }
    
    public function log($message) {
        echo "[TEST {$this->testId}] [{$this->browser^^}] $message\n";
    }
}
?>
```

***

## PARTE 4: Test de Login SauceDemo

### src/SauceDemoLoginTest.php
```php
<?php
namespace App;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class SauceDemoLoginTest extends BaseTest {
    
    public function testLogin($username, $password, $expectedRole, $expectedUrl) {
        $this->log("Probando login: $username");
        
        try {
            // PASO 1: Navegar a SauceDemo
            $this->driver->get('https://www.saucedemo.com/');
            
            // PASO 2: Ingresar credenciales
            $usernameField = $this->driver->findElement(WebDriverBy::id('user-name'));
            $passwordField = $this->driver->findElement(WebDriverBy::id('password'));
            $loginButton = $this->driver->findElement(WebDriverBy::id('login-button'));
            
            $usernameField->clear()->sendKeys($username);
            $passwordField->clear()->sendKeys($password);
            $loginButton->click();
            
            // PASO 3: Esperar resultado
            $wait = new WebDriverWait($this->driver, 10);
            
            // Verificar que no hay error de login
            try {
                $errorMessage = $wait->until(
                    WebDriverExpectedCondition::visibilityOfElementLocated(
                        WebDriverBy::xpath('//h3[@data-test="error"]')
                    )
                );
                $this->log("❌ LOGIN FALLÓ: " . $errorMessage->getText());
                return ['status' => 'FAILED', 'error' => $errorMessage->getText()];
            } catch (Exception $e) {
                // No hay error = login exitoso
            }
            
            // PASO 4: Verificar URL destino
            $currentUrl = $this->driver->getCurrentURL();
            if (strpos($currentUrl, $expectedUrl) === false) {
                $this->log("❌ URL incorrecta. Esperada: $expectedUrl, Obtenida: $currentUrl");
                return ['status' => 'FAILED', 'error' => 'URL incorrecta'];
            }
            
            // PASO 5: Verificar rol del usuario
            $headerText = $this->driver->findElement(WebDriverBy::xpath('//span[@class="title"]'))->getText();
            if (strpos($headerText, $expectedRole) === false) {
                $this->log("❌ Rol incorrecto. Esperado: $expectedRole, Obtenido: $headerText");
                return ['status' => 'FAILED', 'error' => 'Rol incorrecto'];
            }
            
            $this->log("✅ LOGIN EXITOSO para $username");
            return ['status' => 'PASSED'];
            
        } catch (Exception $e) {
            $this->log("❌ ERROR GENERAL: " . $e->getMessage());
            return ['status' => 'FAILED', 'error' => $e->getMessage()];
        }
    }
}
?>
```

***

## PARTE 5: Paralelización con PHP

### src/ParallelRunner.php
```php
<?php
namespace App;

class ParallelRunner {
    
    /**
     * Ejecuta tests en paralelo usando procesos PHP
     */
    public static function runParallel($users, $browsers = ['chrome', 'firefox']) {
        $processes = [];
        $results = [];
        $totalTests = count($users) * count($browsers);
        
        echo "🚀 EJECUTANDO $totalTests TESTS EN PARALELO\n";
        echo "Usuarios: " . count($users) . " | Navegadores: " . count($browsers) . "\n\n";
        
        // Crear proceso por cada combinación usuario+navegador
        foreach ($users as $index => $user) {
            foreach ($browsers as $browser) {
                $testId = "T" . str_pad($index + 1, 2, '0', STR_PAD_LEFT) . "-" . strtoupper($browser);
                
                $command = "php -f " . __DIR__ . "/run_single_test.php " . 
                          escapeshellarg($user['username']) . " " . 
                          escapeshellarg($user['password']) . " " .
                          escapeshellarg($user['expected_role']) . " " .
                          escapeshellarg($user['expected_url']) . " " .
                          escapeshellarg($browser) . " " .
                          escapeshellarg($testId);
                
                $descriptorSpec = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
                $pipes = [];
                $process = proc_open($command, $descriptorSpec, $pipes);
                
                $processes[$testId] = [
                    'process' => $process,
                    'pipes' => $pipes,
                    'user' => $user['username'],
                    'browser' => $browser
                ];
                
                echo "[$testId] Proceso iniciado: {$user['username']} en $browser\n";
            }
        }
        
        // Esperar que todos terminen
        foreach ($processes as $testId => $info) {
            $result = proc_get_status($info['process']);
            while ($result['running']) {
                sleep(1);
                $result = proc_get_status($info['process']);
            }
            
            proc_close($info['process']);
            
            // Leer resultado
            $stdout = stream_get_contents($info['pipes'][1]);
            $results[$testId] = json_decode($stdout, true) ?: ['status' => 'ERROR'];
            
            echo "[$testId] Finalizado: {$info['user']} ({$results[$testId]['status']})\n";
        }
        
        self::generateReport($results);
        return $results;
    }
    
    private static function generateReport($results) {
        $passed = 0;
        $failed = 0;
        
        foreach ($results as $result) {
            if ($result['status'] === 'PASSED') $passed++;
            else $failed++;
        }
        
        echo "\n" . str_repeat("═", 60) . "\n";
        echo "📊 REPORTE FINAL\n";
        echo "   ✅ Pasados: $passed\n";
        echo "   ❌ Fallidos: $failed\n";
        echo "   📈 Tasa éxito: " . round(($passed / count($results)) * 100, 1) . "%\n";
        echo str_repeat("═", 60) . "\n\n";
        
        // Guardar reporte HTML
        $report = "<html><body><h1>SauceDemo Parallel Test Report</h1>";
        foreach ($results as $testId => $result) {
            $status = $result['status'] === 'PASSED' ? '✅' : '❌';
            $report .= "<div>$testId: $status {$result['status']}</div>";
            if (isset($result['error'])) {
                $report .= "<div style='color:red;'>Error: {$result['error']}</div>";
            }
        }
        $report .= "</body></html>";
        
        file_put_contents('reports/report_' . date('Y-m-d_H-i-s') . '.html', $report);
        echo "📄 Reporte guardado en reports/\n";
    }
}
?>
```

***

## PARTE 6: Script Individual (Para Paralelización)

### run_single_test.php
```php
<?php
// Script ejecutado por cada proceso paralelo
if ($argc !== 7) {
    die("Uso: php run_single_test.php <username> <password> <role> <url> <browser> <testId>\n");
}

[$username, $password, $expectedRole, $expectedUrl, $browser, $testId] = 
    [$argv[1], $argv[2], $argv[3], $argv[4], $argv[5], $argv[6]];

require_once 'vendor/autoload.php';
require_once 'src/SauceDemoLoginTest.php';

use App\SauceDemoLoginTest;

$test = new SauceDemoLoginTest($testId, $browser);
$test->setup();

$result = $test->testLogin($username, $password, $expectedRole, $expectedUrl);
$test->teardown();

echo json_encode($result);
?>
```

***

## PARTE 7: Script Principal

### run_parallel_tests.php
```php
<?php
/**
 * EJECUTAR SAUCEDOMO TESTS EN PARALELO
 */

// Instalar dependencias
if (!file_exists('vendor/autoload.php')) {
    echo "Instalando dependencias...\n";
    exec('composer install');
}

require_once 'vendor/autoload.php';
require_once 'selenium-config.php';
require_once 'src/ParallelRunner.php';

use App\ParallelRunner;

// Leer usuarios desde CSV
$users = [];
if (($handle = fopen('users.csv', 'r')) !== false) {
    fgetcsv($handle); // Saltar header
    while (($row = fgetcsv($handle)) !== false) {
        $users[] = [
            'username' => $row[0],
            'password' => $row[1],
            'expected_role' => $row[2],
            'expected_url' => $row[3]
        ];
    }
    fclose($handle);
}

if (empty($users)) {
    die("❌ No se encontraron usuarios en users.csv\n");
}

// Navegadores para paralelización
$browsers = ['chrome', 'firefox'];

// EJECUTAR EN PARALELO
$results = ParallelRunner::runParallel($users, $browsers);
?>
```

***

## PARTE 8: EJECUCIÓN COMPLETA

```bash
# 1. Preparar proyecto
mkdir saucedemo-parallel && cd saucedemo-parallel
# Copiar todos los archivos

# 2. Instalar dependencias
composer install

# 3. Iniciar Selenium Grid (3 terminales)
# Terminal 1: Hub
java -jar grid/selenium-server-standalone.jar hub

# Terminal 2: Chrome Node
java -jar grid/selenium-server-standalone.jar node --selenium-grid-hub http://localhost:4444

# Terminal 3: Firefox Node  
java -jar grid/selenium-server-standalone.jar node --selenium-grid-hub http://localhost:4444

# 4. Ejecutar tests en paralelo (Terminal 4)
php run_parallel_tests.php
```

## RESULTADO ESPERADO

```
🚀 EJECUTANDO 10 TESTS EN PARALELO
Usuarios: 5 | Navegadores: 2

[T01-CHROME] Proceso iniciado: standard_user en chrome
[T01-FIREFOX] Proceso iniciado: standard_user en firefox
[T02-CHROME] Proceso iniciado: locked_out_user en chrome
[...]

[T01-CHROME] ✅ LOGIN EXITOSO para standard_user
[T02-CHROME] ❌ LOGIN FALLÓ: Epic sadface: Sorry, this user has been locked out
[...]

📊 REPORTE FINAL
   ✅ Pasados: 4
   ❌ Fallidos: 6  
   📈 Tasa éxito: 40.0%
📄 Reporte guardado en reports/
```

***

## 🏆 VENTAJAS DE ESTA IMPLEMENTACIÓN

| Característica | Beneficio |
|----------------|-----------|
| **CSV Config** | Fácil agregar nuevos usuarios |
| **Paralelización** | 10x más rápido |
| **Multi-Browser** | Chrome + Firefox simultáneo |
| **Selenium Grid** | Escalable a cientos de tests |
| **Reportes HTML** | Visualización de resultados |
| **Manejo de Errores** | Tests independientes |

**¡Paralelización profesional lista para usar!** 🚀
