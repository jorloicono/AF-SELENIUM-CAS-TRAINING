# Guía Maestra: Automatización de SauceDemo con PHP en GitHub Actions

## 1. Conceptos Fundamentales

Antes de copiar los archivos, entendamos qué estamos construyendo.
El objetivo es simular a un usuario real que:

1. Entra a `saucedemo.com`.
2. Se loguea.
3. Verifica que hay productos.
4. Agrega uno al carrito.
5. Verifica que el carrito tiene el número "1" .

Para lograr esto sin tocar tu PC, usaremos **GitHub Actions** como nuestro "computador virtual". Este computador necesita instrucciones precisas sobre cómo instalar PHP, el navegador Chrome y cómo conectar ambos.

### La Arquitectura (Page Object Model)

Al igual que en el documento original, no escribiremos todo el código en un solo archivo desordenado. Dividiremos el proyecto en capas:

* **Pages (Páginas):** Archivos que solo conocen los selectores (IDs, Clases) de la web. Si la web cambia, solo editamos aquí.
* **Tests (Pruebas):** Archivos que ejecutan la lógica del negocio (Pasos 1 al 5).
* **BaseTest:** Configuración técnica (abrir navegador, tomar fotos).

---

## 2. Creación de Archivos en GitHub (Paso a Paso)

Ve a tu repositorio en GitHub, pulsa **"Add file" > "Create new file"** y crea la siguiente estructura exacta.

### Paso 1: El Cerebro de Dependencias (`composer.json`)

En Java usábamos `pom.xml`. En PHP usamos `composer.json`. Este archivo le dice a GitHub: "Necesito descargar Selenium y PHPUnit".

**Ruta del archivo:** `composer.json`

```json
{
    "name": "laboratorio/saucedemo-php-ci",
    "description": "Automatización de SauceDemo portada a PHP",
    "require-dev": {
        "php-webdriver/webdriver": "^1.14",
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "SauceDemo\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "SauceDemo\\Tests\\": "tests/"
        }
    }
}

```

> **Explicación detallada:**
> * `php-webdriver/webdriver`: Es la librería que permite a PHP "hablar" con Google Chrome.
> * `autoload (psr-4)`: Esta configuración mágica permite que PHP encuentre tus archivos automáticamente sin que tengas que escribir `include 'archivo.php'` en cada línea. Mapea la carpeta `src/` al nombre `SauceDemo`.

---

### Paso 2: La Configuración Base (`tests/BaseTest.php`)

Este archivo es el equivalente al `BaseTest.java` original. Su trabajo es preparar el navegador antes de cada test y cerrarlo al finalizar.

**Ruta del archivo:** `tests/BaseTest.php`

```php
<?php
namespace SauceDemo\Tests;

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;

class BaseTest extends TestCase {
    protected $driver;

    // Se ejecuta ANTES de cada prueba (@BeforeEach en Java)
    protected function setUp(): void {
        // Configuramos Chrome para entornos de servidor (CI/CD)
        $options = new ChromeOptions();
        
        [cite_start]// --headless=new: Ejecuta Chrome sin interfaz gráfica (vital para servidores Linux) [cite: 90]
        [cite_start]// --no-sandbox: Necesario para permisos de seguridad en Docker/GitHub Actions [cite: 91]
        [cite_start]// --window-size: Define el tamaño de pantalla para asegurar que los elementos sean visibles [cite: 93]
        $options->addArguments(['--headless=new', '--no-sandbox', '--disable-dev-shm-usage', '--window-size=1920,1080']);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        // Conectamos al puerto 4444. En Java, WebDriverManager lo hace solo. 
        // En PHP/CI, levantaremos el servidor manualmente en el workflow (veremos esto al final).
        $this->driver = RemoteWebDriver::create('http://localhost:4444/', $capabilities);
        
        [cite_start]// Espera implícita de 10 segundos para encontrar elementos [cite: 95]
        $this->driver->manage()->timeouts()->implicitlyWait(10);
    }

    // Se ejecuta DESPUÉS de cada prueba (@AfterEach en Java)
    protected function tearDown(): void {
        if ($this->driver) {
            [cite_start]// Lógica de Captura de Pantalla [cite: 182-190]
            // Si el test falla, queremos ver qué pasó.
            $testName = $this->name();
            $timestamp = date('Ymd_His');
            $screenshotDir = 'target/screenshots';

            // Crear carpeta si no existe
            if (!is_dir($screenshotDir)) {
                mkdir($screenshotDir, 0777, true);
            }

            try {
                // Guardar la imagen con el nombre del test y la hora
                $this->driver->takeScreenshot("$screenshotDir/{$testName}_{$timestamp}.png");
            } catch (\Exception $e) {
                // Si falla la captura, no rompemos el test, solo lo ignoramos
            }

            [cite_start]// Cerrar el navegador para liberar memoria [cite: 100]
            $this->driver->quit();
        }
    }
}

```

---

### Paso 3: Los Objetos de Página (Page Objects)

Aquí traducimos las clases Java `LoginPage.java` y `ProductsPage.java`.

**Ruta del archivo:** `src/Pages/LoginPage.php`
Encapsula el login. Si mañana cambia el ID del botón de login, solo cambiamos este archivo.

```php
<?php
namespace SauceDemo\Pages;

use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;

class LoginPage {
    private $driver;
    [cite_start]// Definición de selectores [cite: 117-119]
    private $usernameField;
    private $passwordField;
    private $loginButton;

    public function __construct(WebDriver $driver) {
        $this->driver = $driver;
        // Localizadores por ID
        $this->usernameField = WebDriverBy::id("user-name");
        $this->passwordField = WebDriverBy::id("password");
        $this->loginButton = WebDriverBy::id("login-button");
    }

    [cite_start]// Acción: Ir a la URL [cite: 125]
    public function open() {
        $this->driver->get("https://www.saucedemo.com/");
    }

    [cite_start]// Acción: Llenar formulario y clickear [cite: 126-134]
    public function login($username, $password) {
        $this->driver->findElement($this->usernameField)->clear(); // Limpieza por seguridad
        $this->driver->findElement($this->usernameField)->sendKeys($username);
        $this->driver->findElement($this->passwordField)->sendKeys($password);
        $this->driver->findElement($this->loginButton)->click();
    }
}

```

**Ruta del archivo:** `src/Pages/ProductsPage.php`
Encapsula la validación de inventario y carrito.

```php
<?php
namespace SauceDemo\Pages;

use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;

class ProductsPage {
    private $driver;
    private $productTitles;
    private $addToCartButton;
    private $cartBadge;

    public function __construct(WebDriver $driver) {
        $this->driver = $driver;
        [cite_start]// Selector para TODOS los títulos de productos (ClassName) [cite: 148]
        $this->productTitles = WebDriverBy::className("inventory_item_name");
        
        [cite_start]// XPath complejo para seleccionar el botón "Add to cart" del PRIMER producto [cite: 149]
        $this->addToCartButton = WebDriverBy::xpath("(//button[contains(@id,'add-to-cart')])[1]");
        
        [cite_start]// Selector del ícono rojo del carrito con el número [cite: 150]
        $this->cartBadge = WebDriverBy::className("shopping_cart_badge");
    }

    // Obtener lista de textos de todos los productos
    [cite_start]// Equivalente al Stream de Java [cite: 154]
    public function getAllProductTitles() {
        $elements = $this->driver->findElements($this->productTitles);
        // Transformamos (mapeamos) la lista de Elementos Web a una lista de Textos (Strings)
        return array_map(function($element) {
            return $element->getText();
        }, $elements);
    }

    [cite_start]// Click en añadir al carrito [cite: 160]
    public function addFirstProductToCart() {
        $this->driver->findElement($this->addToCartButton)->click();
    }

    [cite_start]// Obtener el número actual del carrito [cite: 163]
    public function getCartCount() {
        return $this->driver->findElement($this->cartBadge)->getText();
    }
}

```

---

### Paso 4: El Test Principal (`tests/SauceDemoTest.php`)

Este archivo une todo. Hereda de `BaseTest` para tener el navegador listo e instancia las Páginas (`Pages`) para interactuar con la web.

**Ruta del archivo:** `tests/SauceDemoTest.php`

```php
<?php
namespace SauceDemo\Tests;

use SauceDemo\Pages\LoginPage;
use SauceDemo\Pages\ProductsPage;

[cite_start]// Heredamos de BaseTest para obtener el setUp (Chrome) y tearDown (Capturas) automáticos [cite: 205]
class SauceDemoTest extends BaseTest {

    public function testLoginAndAddToCart() {
        [cite_start]// 1. Inicializar los Page Objects [cite: 209-210]
        $loginPage = new LoginPage($this->driver);
        $productsPage = new ProductsPage($this->driver);

        [cite_start]// 2. Abrir la web e iniciar sesión [cite: 211-212]
        $loginPage->open();
        $loginPage->login("standard_user", "secret_sauce");

        [cite_start]// 3. Validación: Verificar que la lista de productos no está vacía [cite: 214-215]
        $titles = $productsPage->getAllProductTitles();
        // PHPUnit Assertion: Validamos que el array de títulos sea mayor a 0
        $this->assertGreaterThan(0, count($titles), "FALLO: No se encontraron productos en la lista.");

        [cite_start]// 4. Acción: Añadir el primer producto al carrito [cite: 217]
        $productsPage->addFirstProductToCart();

        [cite_start]// 5. Validación final: El carrito debe mostrar '1' [cite: 218]
        $currentCount = $productsPage->getCartCount();
        $this->assertEquals("1", $currentCount, "FALLO: El contador del carrito no muestra 1.");
    }
}

```

---

### Paso 5: El Workflow de GitHub Actions (La Clave)

Aquí es donde ocurre la magia de "nada en local". Este archivo configura el servidor de Linux que GitHub nos presta gratis para correr la prueba.

**Atención:** A diferencia de Java donde `WebDriverManager` hacía todo oculto, en PHP debemos ser explícitos al levantar el driver de Chrome.

**Ruta del archivo:** `.github/workflows/selenium-php.yml`
*(Nota: la carpeta `.github` empieza con un punto y dentro debe estar `workflows`)*

```yaml
name: Selenium PHP Automation

# [cite_start]Eventos que disparan la prueba: Push y Pull Request en la rama main [cite: 229-233]
on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  test-suite:
    [cite_start]runs-on: ubuntu-latest # Usamos una máquina virtual Linux (Ubuntu) [cite: 236]

    steps:
    # [cite_start]1. Descargar tu código del repositorio al servidor [cite: 239]
    - name: Checkout Code
      uses: actions/checkout@v4

    # [cite_start]2. Instalar PHP 8.2 y Composer [cite: 240]
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        tools: composer

    # 3. Instalar dependencias del proyecto (Selenium Client y PHPUnit)
    - name: Install Dependencies
      run: composer install --prefer-dist --no-progress

    # 4. Instalar Google Chrome (Navegador)
    # [cite_start]Equivalente a la acción setup-chrome del PDF [cite: 247]
    - name: Setup Chrome
      uses: browser-actions/setup-chrome@v1
      with:
        chrome-version: stable

    # 5. Descargar e Iniciar ChromeDriver (El conductor)
    # Este paso es crítico. Descargamos el driver compatible con la versión de Chrome instalada.
    - name: Start ChromeDriver
      run: |
        echo "Detectando versión de Chrome..."
        CHROME_VERSION=$(google-chrome --version | grep -oP '\d+\.\d+\.\d+')
        echo "Chrome Version: $CHROME_VERSION"
        
        echo "Descargando ChromeDriver..."
        # URL genérica para descargar drivers de prueba de Google
        wget -q "https://storage.googleapis.com/chrome-for-testing-public/127.0.6533.72/linux64/chromedriver-linux64.zip" -O chromedriver.zip
        unzip chromedriver.zip
        
        echo "Iniciando ChromeDriver en puerto 4444..."
        # El símbolo '&' al final ejecuta el proceso en segundo plano para no bloquear el script
        ./chromedriver-linux64/chromedriver --port=4444 &
        
        echo "Esperando a que ChromeDriver esté listo..."
        sleep 5

    # 6. Ejecutar los Tests
    # [cite_start]Lanza PHPUnit apuntando al archivo de test [cite: 251]
    - name: Run PHPUnit Tests
      run: vendor/bin/phpunit tests/SauceDemoTest.php

    # 7. Subir reportes y capturas de pantalla (Artifacts)
    # [cite_start]Se ejecuta siempre (if: always), incluso si el test falla, para que puedas ver la captura del error [cite: 253]
    - name: Upload Screenshots
      if: always()
      uses: actions/upload-artifact@v4
      with:
        name: screenshots
        path: target/screenshots/
        retention-days: 5

```

---

## 3. Cómo ver los resultados

Una vez que hayas creado estos 5 archivos y hecho "Commit":

1. Ve a la pestaña **Actions** en tu repositorio de GitHub.
2. Verás un workflow con el nombre "Selenium PHP Automation" en color amarillo (ejecutándose) o verde (éxito).
3. Haz clic en él.
4. Si quieres ver las capturas de pantalla (especialmente útil si falla), desplázate hacia abajo hasta la sección **Artifacts** y descarga el archivo `screenshots.zip`. Dentro estará la imagen `loginAndAddToCart_YYYYMMDD.png` mostrando el estado final de la página.

**Resumen de Logros:**

* Hemos eliminado la complejidad de Java/Maven.
* Hemos mantenido la robustez del patrón POM.
* Hemos configurado un pipeline CI/CD completo que instala navegadores y ejecuta pruebas automáticamente en la nube.
