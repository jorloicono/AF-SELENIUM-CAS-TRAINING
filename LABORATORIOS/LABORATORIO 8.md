## ¿QUÉ ES BEHAT Y POR QUÉ USARLO?

**Behat** es un framework **BDD (Behavior Driven Development)** para PHP que permite escribir **tests en lenguaje humano** usando **Gherkin** (`.feature` files). Es la **versión PHP de Cucumber**.

### **Ventajas sobre PHPUnit tradicional:**

| Aspecto | PHPUnit (Código PHP) | Behat (Gherkin) |
|---------|---------------------|-----------------|
| **Legibilidad** | `$this->login('user')` | `Given I login as "standard_user"` |
| **Colaboración** | Solo devs | QA, Product, Stakeholders |
| **Mantenimiento** | Requiere programador | Lenguaje natural |
| **Reportes** | Consola básica | HTML, PDF, Video |
| **Reutilización** | Methods | Scenarios/Steps |

### **Flujo de Ejecución Behat:**
```
.feature (Gherkin) → Context (PHP) → Selenium → Navegador → Reporte
```

***

## 📁 ESTRUCTURA COMPLETA DEL PROYECTO (EXPLICADA)

```
behat-selenium-lab/                    ← Raíz del proyecto
├── composer.json                      ← Dependencias PHP
├── behat.yml                          ← Configuración Behat (CRÍTICA)
├── chromedriver.exe                   ← Driver para Selenium
├── features/                          ← Archivos Gherkin (.feature)
│   ├── login.feature                  ← Tests de login
│   ├── cart.feature                   ← Tests de carrito
│   └── bootstrap.feature              ← Tests de carga inicial
├── features/bootstrap/                 ← Glue Code (PHP que interpreta Gherkin)
│   └── FeatureContext.php             ← TODA la lógica de Selenium aquí
├── reports/                           ← Reportes generados automáticamente
├── run-tests.bat                      ← Windows: Todo en 1 clic
├── start-chromedriver.bat             ← Solo ChromeDriver
└── README.md                          ← Documentación
```

***

## PARTE 1: INSTALACIÓN PASO A PASO 

### **Paso 1.1: Crear Proyecto**
```bash
# Crear directorio y navegar
mkdir behat-selenium-lab
cd behat-selenium-lab
```

### **Paso 1.2: composer.json COMPLETO (con explicaciones)**
```json
{
    "name": "behat-selenium-lab",
    "description": "Behat + Selenium Lab para SauceDemo",
    "require-dev": {
        "behat/behat": "^3.13",                    // ✅ MOTOR BDD principal
        "behat/mink": "^1.11",                     // ✅ Puente a navegadores
        "behat/mink-extension": "^2.3",            // ✅ Extensión Mink para Behat
        "behat/mink-selenium2-driver": "^1.7",     // ✅ Driver Selenium WebDriver
        "symfony/finder": "^6.0"                   // ✅ Ayuda para archivos
    },
    "require": {
        "php": ">=8.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    },
    "config": {
        "bin-dir": "bin/"
    }
}
```

**Explicación de cada paquete:**
- `behat/behat`: **Motor principal** que parsea `.feature` files
- `behat/mink`: **Abstracción** para interactuar con navegadores
- `mink-selenium2-driver`: **Conector** específico a Selenium WebDriver
- `symfony/finder`: **Utilidades** para encontrar archivos

### **Paso 1.3: Instalar**
```bash
composer install
```

### **Paso 1.4: Inicializar Behat (CREA ESTRUCTURA AUTOMÁTICA)**
```bash
# Crea features/, features/bootstrap/, behat.yml básico
bin/behat --init
```

***

## PARTE 2: behat.yml CONFIGURACIÓN COMPLETA 

```yaml
# behat.yml - CONFIGURACIÓN DETALLADA
default:                                    # Perfil por defecto
  extensions:                               # Extensiones/plugins
    Behat\MinkExtension:                    # 🔌 Extensión Mink (OBLIGATORIA)
      # BASE_URL se usa en todos los steps como "Given I am on login page"
      base_url: 'https://www.saucedemo.com/' # URL raíz de la app
      
      # CONFIGURACIÓN DEL NAVEGADOR
      browser_name: 'chrome'                # chrome, firefox, edge
      browser_aliases:                      # Alias para simplicidad
        chrome:
          driver: selenium2
        firefox:
          driver: selenium2
      
      # CONFIGURACIÓN SELENIUM GRID/DRIVER
      selenium2:                             # Driver Selenium
        wd_host: "127.0.0.1:9515/wd/hub"     # ChromeDriver URL
        capabilities:                         # Opciones del navegador
          "browserName": "chrome"
          "chromeOptions":                    # Opciones específicas Chrome
            "args": [                         # Argumentos de línea de comandos
              "--disable-gpu",                # Desactivar GPU (más estable)
              "--no-sandbox",                 # Modo sin sandbox (Linux)
              "--disable-dev-shm-usage",      # Usar disco en vez de RAM
              "--window-size=1920,1080"       # Tamaño fijo para tests
            ]
            "prefs": {                        # Preferencias Chrome
              "profile.default_content_setting_values.notifications": 2
            }
  
  # SUITES - Agrupación lógica de tests
  suites:
    ui:                                   # Suite para tests UI
      paths: [ "%paths.base%/features" ]  # Dónde buscar .feature files
      contexts:                           # Clases PHP que interpretan steps
        - FeatureContext                   # Nuestra clase principal
        - App\AdvancedContext              # Contextos adicionales (opcional)

    api:                                  # Suite para tests API (futuro)
      contexts:
        - ApiContext

  # FORMATOS DE REPORTE
  formatters:
    html-pretty:                          # Reporte HTML detallado
      output_path: reports/html-report.html
    junit:                                # Para CI/CD (Jenkins, GitHub Actions)
      output_path: reports/junit.xml
```

**PUNTOS CRÍTICOS:**
1. **`wd_host`**: Debe coincidir con ChromeDriver (`9515/wd/hub`)
2. **`base_url`**: Se usa automáticamente en `Given I am on login page`
3. **`capabilities`**: Configuración avanzada del navegador

***

## PARTE 3: FeatureContext.php 

### **features/bootstrap/FeatureContext.php COMPLETO**

```php
<?php
/**
 * FeatureContext.php - INTERPRETADOR DE GHERKIN A SELENIUM
 * 
 * RESPONSABILIDADES:
 * 1. Mapear steps Gherkin → Código Selenium PHP
 * 2. Manejar sesiones de navegador
 * 3. Manejar screenshots automáticos en fallos
 * 4. Logs detallados
 * 
 * HERENCIA: MinkContext (proporciona 100+ steps predefinidos)
 */

use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;

// CLASE PRINCIPAL - EXTENDE MinkContext (200+ steps gratis)
class FeatureContext extends MinkContext implements Context
{
    /**
     * @var string[] Sesiones de navegador disponibles
     */
    private $sessionNames = ['default'];
    
    /**
     * @var array Capturas de pantalla de fallos
     */
    private $screenshots = [];
    
    /**
     * HOOK: BeforeScenario - Se ejecuta ANTES de cada scenario
     */
    public function beforeScenario(BeforeScenarioScope $event)
    {
        $scenario = $event->getScenario();
        echo "\n🚀 Iniciando: " . $scenario->getTitle() . "\n";
        
        // Tomar screenshot inicial
        $this->takeScreenshot('before_scenario');
    }
    
    /**
     * HOOK: AfterScenario - Se ejecuta DESPUÉS de cada scenario
     */
    public function afterScenario(AfterScenarioScope $event)
    {
        $scenario = $event->getScenario();
        $result = $event->getTestResult();
        
        if ($result->isPassed()) {
            echo "✅ Scenario PASSED: " . $scenario->getTitle() . "\n";
        } else {
            echo "❌ Scenario FAILED: " . $scenario->getTitle() . "\n";
            $this->takeScreenshot('after_failure');
        }
    }
    
    /**
     * ===== STEPS PERSONALIZADOS =====
     */
    
    /**
     * @Given /^I am on the (login|inventory|cart) page$/
     * 
     * Step personalizado para navegación rápida
     * Sintaxis en .feature: Given I am on the login page
     */
    public function iAmOnThePage($page)
    {
        $pages = [
            'login' => '/',
            'inventory' => '/inventory.html',
            'cart' => '/cart.html'
        ];
        
        $url = $this->getMinkParameter('base_url') . ($pages[$page] ?? $page);
        $this->visit($url);
    }
    
    /**
     * @When /^I login with username "([^"]*)" and password "([^"]*)"$/
     * 
     * Step reutilizable para login
     * Sintaxis: When I login with username "standard_user" and password "secret_sauce"
     */
    public function iLoginWithUsernameAndPassword($username, $password)
    {
        $page = $this->getSession()->getPage();
        
        // Llenar campos usando data-test attributes (más robustos que IDs)
        $page->fillField('user-name', $username);
        $page->fillField('password', $password);
        $page->pressButton('login-button');
    }
    
    /**
     * @Then /^the "([^"]*)" field should be visible$/
     */
    public function theFieldShouldBeVisible($field)
    {
        $page = $this->getSession()->getPage();
        $element = $page->findField($field);
        
        if (!$element) {
            throw new Exception("Campo '$field' no encontrado");
        }
        
        if (!$element->isVisible()) {
            throw new Exception("Campo '$field' no es visible");
        }
    }
    
    /**
     * @Then /^the "([^"]*)" button should be enabled$/
     */
    public function theButtonShouldBeEnabled($button)
    {
        $page = $this->getSession()->getPage();
        $element = $page->findButton($button);
        
        if (!$element->isVisible()) {
            throw new Exception("Botón '$button' no es visible");
        }
        
        // Verificar que no está disabled
        $attribute = $element->getAttribute('disabled');
        if ($attribute !== null) {
            throw new Exception("Botón '$button' está deshabilitado");
        }
    }
    
    /**
     * @Then /^the shopping cart badge should show "([^"]*)"$/
     */
    public function theShoppingCartBadgeShouldShow($expectedCount)
    {
        $page = $this->getSession()->getPage();
        $badge = $page->find('css', '.shopping_cart_link > a > span');
        
        if (!$badge) {
            throw new Exception('Badge del carrito no encontrado');
        }
        
        $actualCount = $badge->getText();
        if ($actualCount !== $expectedCount) {
            throw new Exception("Badge esperaba '$expectedCount', obtuvo '$actualCount'");
        }
    }
    
    /**
     * @Then /^the "([^"]*)" button should become "([^"]*)"$/
     */
    public function theButtonShouldBecome($originalButton, $newButtonText)
    {
        $page = $this->getSession()->getPage();
        $button = $page->findButton($originalButton);
        
        // Esperar a que cambie el texto del botón
        $this->getSession()->wait(5000, 500); // Poll cada 500ms hasta 5s
        
        $currentText = $button->getText();
        if (strpos($currentText, $newButtonText) === false) {
            throw new Exception("Botón no cambió. Esperado: '$newButtonText', Actual: '$currentText'");
        }
    }
    
    /**
     * @Given /^I click on "([^"]*)"$/
     */
    public function iClickOn($buttonId)
    {
        $page = $this->getSession()->getPage();
        $button = $page->findButton($buttonId);
        
        if (!$button) {
            // Intentar por ID
            $button = $page->find('css', '#' . $buttonId);
        }
        
        if (!$button) {
            throw new Exception("Elemento '$buttonId' no encontrado");
        }
        
        $button->click();
    }
    
    /**
     * @Given /^I click on shopping cart icon$/
     */
    public function iClickOnShoppingCartIcon()
    {
        $page = $this->getSession()->getPage();
        $cartIcon = $page->findLink('Shopping Cart');
        $cartIcon->click();
    }
    
    /**
     * ===== MÉTODOS UTILITARIOS =====
     */
    
    /**
     * Tomar screenshot automático
     */
    private function takeScreenshot($context = 'general')
    {
        try {
            $screenshot = $this->getSession()->getDriver()->getScreenshot();
            $filename = "screenshots/" . date('Y-m-d_H-i-s') . "_$context.png";
            file_put_contents($filename, $screenshot);
        } catch (Exception $e) {
            // Silenciosamente ignorar errores de screenshot
        }
    }
}
?>
```

***

## 📄 PARTE 4: FEATURES GHERKIN COMPLETAS (Lenguaje Humano)

### **features/login.feature** - **Tests de Login Exhaustivos**
```gherkin
@smoke @login @critical
Feature: Login Functionality
  # DESCRIPCIÓN EN LENGUAJE NEGOCIO
  As a new visitor of SauceDemo
  I want to login with valid credentials
  So that I can access the product catalog
  
  # Background se ejecuta ANTES de CADA scenario
  Background:
    Given I am on the login page

  # Scenario Outline = Data-Driven Testing (CSV-like)
  @positive
  Scenario Outline: Successful login with valid users
    When I login with username "<username>" and password "<password>"
    Then I should be redirected to the inventory page
    And I should see "Products"
    And the shopping cart badge should show "0"
    And the "add-to-cart-sauce-labs-backpack" button should be visible

    Examples:
      | username              | password     |
      | standard_user         | secret_sauce |
      | problem_user          | secret_sauce |
      | performance_glitch_user | secret_sauce |

  @negative @critical
  Scenario Outline: Failed login scenarios
    When I fill in "user-name" with "<username>"
    And I fill in "password" with "<password>"
    And I press "login-button"
    Then I should see error message "<error_message>"

    Examples:
      | username      | password | error_message                                           |
      | invalid_user  | secret_sauce | Epic sadface: Username and password do not match any account in this store |
      | standard_user | invalid_pass | Epic sadface: Username and password do not match any account in this store |
      |               | secret_sauce | Epic sadface: Username is required                      |
      | standard_user |           | Epic sadface: Password is required                      |

  @negative @security
  Scenario: Locked out user
    When I login with username "locked_out_user" and password "secret_sauce"
    Then I should see error message "Sorry, this user has been locked out of the system"
```

### **features/cart.feature** - **Tests de Carrito**
```gherkin
@e2e @cart
Feature: Shopping Cart Functionality
  As a logged-in customer
  I want to add/remove products from cart
  So that I can checkout with selected items

  Background:
    Given I am on the login page
    When I login with username "standard_user" and password "secret_sauce"

  @smoke
  Scenario: Add single product to cart
    When I click on "add-to-cart-sauce-labs-backpack"
    Then the "add-to-cart-sauce-labs-backpack" button should become "remove-from-cart-sauce-labs-backpack"
    And the shopping cart badge should show "1"

  @regression
  Scenario: Remove product from cart
    Given I click on "add-to-cart-sauce-labs-backpack"
    When I click on "remove-from-cart-sauce-labs-backpack"
    Then the shopping cart badge should show "0"
    And the "add-to-cart-sauce-labs-backpack" button should be visible

  @e2e
  Scenario: View cart contents
    Given I click on "add-to-cart-sauce-labs-backpack"
    When I click on shopping cart icon
    Then I should see "Your Cart"
    And I should see "Sauce Labs Backpack"
    And the checkout button should be enabled
```

### **features/bootstrap.feature** - **Tests de Infraestructura**
```gherkin
@bootstrap @smoke
Feature: Application Bootstrap and Page Load
  Sanity checks que la aplicación carga correctamente

  Scenario: Login page loads correctly
    Given I am on the login page
    Then the page title should be "Swag Labs"
    And I should see "Swag Labs"
    And the "user-name" field should be visible
    And the "password" field should be visible
    And the "login-button" button should be enabled

  Scenario: Page responsiveness
    Given I am on the login page
    When the window is resized to mobile
    Then the login form should be responsive
```

***

## 🎮 PARTE 5: Scripts de Ejecución (Windows/Linux)

### **run-tests.bat** (Windows - TODO EN 1 CLIC)
```batch
@echo off
echo.
echo 🚀🚀🚀 BEHAT + SELENIUM LAB - INICIANDO 🚀🚀🚀
echo.

REM 1. Crear directorios necesarios
if not exist screenshots mkdir screenshots
if not exist reports mkdir reports

REM 2. Iniciar ChromeDriver en background
echo [1/4] Iniciando ChromeDriver...
start /min cmd /c "chromedriver.exe --port=9515 --verbose --log-path=chromedriver.log"
timeout /t 4 /nobreak >nul

REM 3. Verificar que ChromeDriver está listo
echo [2/4] Verificando ChromeDriver...
curl -s http://localhost:9515/status >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ ChromeDriver no disponible. Revisa chromedriver.exe
    pause
    exit /b 1
)

REM 4. Ejecutar todos los tests
echo [3/4] Ejecutando tests Behat...
bin\behat --format=progress --colors --stop-on-failure

REM 5. Generar reporte HTML
echo [4/4] Generando reporte HTML...
bin\behat --format=html --out=reports\report.html --no-paths

REM 6. Terminar ChromeDriver
taskkill /f /im chromedriver.exe 2>nul
echo.
echo ✅✅✅ LABORATORIO COMPLETADO ✅✅✅
echo Reporte: reports\report.html
echo Screenshots: screenshots\
pause
```

### **run-tests.sh** (Linux/Mac)
```bash
#!/bin/bash
echo "🚀🚀🚀 BEHAT + SELENIUM LAB - INICIANDO 🚀🚀🚀"

# 1. Crear directorios
mkdir -p screenshots reports

# 2. Iniciar ChromeDriver en background
echo "[1/4] Iniciando ChromeDriver..."
./chromedriver --port=9515 --verbose --log-path=chromedriver.log &
CHROME_PID=$!

sleep 3

# 3. Verificar ChromeDriver
echo "[2/4] Verificando ChromeDriver..."
curl -s http://localhost:9515/status >/dev/null 2>&1
if [ $? -ne 0 ]; then
    echo "❌ ChromeDriver no disponible"
    kill $CHROME_PID 2>/dev/null
    exit 1
fi

# 4. Ejecutar tests
echo "[3/4] Ejecutando tests Behat..."
./bin/behat --format=progress --colors --stop-on-failure

# 5. Generar reporte
echo "[4/4] Generando reporte HTML..."
./bin/behat --format=html --out=reports/report.html --no-paths

# 6. Limpiar
kill $CHROME_PID 2>/dev/null
echo "✅✅✅ LABORATORIO COMPLETADO ✅✅✅"
echo "Reporte: reports/report.html"
```

***

## 📊 PARTE 6: RESULTADO ESPERADO (DETALLADO)

```
🚀🚀🚀 BEHAT + SELENIUM LAB - INICIANDO 🚀🚀🚀

[1/4] Iniciando ChromeDriver...
ChromeDriver was started successfully.

Feature: Login Functionality
  Scenario Outline: Successful login with valid users # features/login.feature:12-standard_user
    🚀 Iniciando: Successful login with valid users: standard_user
    √ Given I am on the login page
    √ When I login with username "standard_user" and password "secret_sauce"
    √ Then I should be redirected to the inventory page
    √ And I should see "Products"
    ✅ Scenario PASSED: Successful login with valid users: standard_user

  Scenario Outline: Successful login with valid users # features/login.feature:12-problem_user
    🚀 Iniciando: Successful login with valid users: problem_user
    √ Given I am on the login page
    √ When I login with username "problem_user" and password "secret_sauce"
    √ Then I should be redirected to the inventory page
    √ And I should see "Products"
    ✅ Scenario PASSED: Successful login with valid users: problem_user

  Scenario Outline: Failed login scenarios # features/login.feature:22-invalid_user
    🚀 Iniciando: Failed login scenarios: invalid_user
    √ Given I am on the login page
    √ When I fill in "user-name" with "invalid_user"
    √ And I fill in "password" with "secret_sauce"
    √ And I press "login-button"
    √ Then I should see error message "Epic sadface: Username and password do not match any account in this store"
    ✅ Scenario PASSED: Failed login scenarios: invalid_user

[4/4] Generando reporte HTML...
✅✅✅ LABORATORIO COMPLETADO ✅✅✅
```

***

## 🎨 PARTE 7: REPORTES GENERADOS AUTOMÁTICAMENTE

### **reports/report.html** contiene:
```
📊 RESUMEN EJECUCIÓN
✅ 8 Scenarios PASSED
❌ 0 Scenarios FAILED
⏱️  Tiempo total: 00:42s

📁 DETALLE POR FEATURE
├── login.feature (5 PASSED / 0 FAILED)
├── cart.feature (3 PASSED / 0 FAILED)
└── bootstrap.feature (2 PASSED / 0 FAILED)

📸 SCREENSHOTS AUTOMÁTICOS
├── before_scenario_001.png
├── after_failure_002.png (si hay fallos)
└── ...
```

***

## 🔧 PARTE 8: COMANDOS AVANZADOS (PROFESIONAL)

```bash
# 1. Ejecutar por tags (ejecutar solo críticos)
bin/behat --tags @critical,@smoke

# 2. Ejecutar escenario específico (línea 12)
bin/behat features/login.feature:12

# 3. Reporte JUnit para Jenkins/GitHub Actions
bin/behat --format=junit --out=reports/junit.xml

# 4. Reporte HTML + Consola
bin/behat --format=html --out=reports/ --format=pretty

# 5. Solo features que fallaron
bin/behat --stop-on-failure

# 6. Con diferentes navegadores
BASE_URL=https://saucedemo.com BROWSER=firefox bin/behat

# 7. Tests en paralelo (extensión adicional)
composer require ericxunningham/behat-parallel-extension
```

***


**Ejecuta:** `run-tests.bat` (Windows) o `./run-tests.sh` 🚀
