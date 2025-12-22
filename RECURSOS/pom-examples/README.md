# 🏗️ Page Object Model (POM) Examples

Esta carpeta contiene ejemplos de implementación del patrón **Page Object Model**, una de las mejores prácticas en automatización de pruebas web.

## 🎯 ¿Qué es Page Object Model?

Page Object Model es un patrón de diseño que:
- ✅ Separa la lógica de prueba de la estructura de la página
- ✅ Mejora la mantenibilidad del código
- ✅ Reduce la duplicación
- ✅ Facilita la lectura y escritura de tests
- ✅ Hace los tests más robustos ante cambios en la UI

## 📁 Estructura

```
pom-examples/
│
├── POMExample/              # Ejemplo completo de POM
│   ├── Pages/              # Clases Page Object
│   │   ├── LoginPage.php   # Página de login
│   │   └── ProductsPage.php # Página de productos
│   │
│   └── Tests/              # Tests usando POM
│       ├── ParentTest.php  # Clase base para tests
│       └── Login/
│           ├── LoginWithOutPOMTest.php  # Test SIN POM (anti-patrón)
│           └── LoginWithPOMTest.php     # Test CON POM (recomendado)
│
└── POMEjercicio/            # Ejercicio de POM
    └── Pages/
        ├── Guru99HomePage.php
        └── Guru99Login.php
```

## 🔄 Comparación: Sin POM vs Con POM

### ❌ Sin POM (No Recomendado)

```php
// LoginWithOutPOMTest.php
public function testLogin() {
    $this->driver->get('https://example.com/login');
    $this->driver->findElement(WebDriverBy::id('username'))->sendKeys('user');
    $this->driver->findElement(WebDriverBy::id('password'))->sendKeys('pass');
    $this->driver->findElement(WebDriverBy::id('loginButton'))->click();
    // ... más código duplicado
}
```

**Problemas:**
- 🔴 Código duplicado en múltiples tests
- 🔴 Difícil de mantener
- 🔴 Si cambia un ID, hay que modificar todos los tests
- 🔴 Mezcla lógica de prueba con detalles de la UI

### ✅ Con POM (Recomendado)

```php
// LoginPage.php (Page Object)
class LoginPage {
    private $usernameField;
    private $passwordField;
    private $loginButton;
    
    public function login($username, $password) {
        $this->usernameField->sendKeys($username);
        $this->passwordField->sendKeys($password);
        $this->loginButton->click();
    }
}

// LoginWithPOMTest.php
public function testLogin() {
    $loginPage = new LoginPage($this->driver);
    $loginPage->login('user', 'pass');
    // Test limpio y legible
}
```

**Ventajas:**
- ✅ Código reutilizable
- ✅ Fácil de mantener
- ✅ Cambios centralizados
- ✅ Tests más legibles
- ✅ Separación clara de responsabilidades

## 📚 Componentes del Patrón POM

### 1. Page Objects (Clases de Página)

Representan páginas o componentes de la aplicación:

```php
class LoginPage {
    private $driver;
    private $usernameInput;
    private $passwordInput;
    private $loginButton;
    
    public function __construct(RemoteWebDriver $driver) {
        $this->driver = $driver;
        $this->initElements();
    }
    
    private function initElements() {
        $this->usernameInput = $this->driver->findElement(
            WebDriverBy::id('username')
        );
        // ... más elementos
    }
    
    // Métodos que representan acciones de la página
    public function enterUsername($username) {
        $this->usernameInput->clear();
        $this->usernameInput->sendKeys($username);
    }
    
    public function enterPassword($password) {
        $this->passwordInput->clear();
        $this->passwordInput->sendKeys($password);
    }
    
    public function clickLogin() {
        $this->loginButton->click();
        return new HomePage($this->driver); // Retorna la siguiente página
    }
    
    // Método compuesto
    public function login($username, $password) {
        $this->enterUsername($username);
        $this->enterPassword($password);
        return $this->clickLogin();
    }
}
```

### 2. Test Classes (Clases de Prueba)

Usan los Page Objects para crear tests:

```php
class LoginTest extends ParentTest {
    public function testSuccessfulLogin() {
        $loginPage = new LoginPage($this->driver);
        $homePage = $loginPage->login('validUser', 'validPass');
        
        $this->assertTrue($homePage->isLoaded());
    }
    
    public function testInvalidLogin() {
        $loginPage = new LoginPage($this->driver);
        $loginPage->login('invalid', 'invalid');
        
        $this->assertTrue($loginPage->hasErrorMessage());
    }
}
```

### 3. Parent Test (Clase Base)

Maneja configuración común:

```php
class ParentTest extends PHPUnit\Framework\TestCase {
    protected $driver;
    
    protected function setUp(): void {
        $this->driver = RemoteWebDriver::create(/* ... */);
    }
    
    protected function tearDown(): void {
        $this->driver->quit();
    }
}
```

## 🚀 Cómo Ejecutar los Ejemplos

### Ejecutar Test SIN POM (para comparación)

```bash
php pom-examples/POMExample/Tests/Login/LoginWithOutPOMTest.php
```

### Ejecutar Test CON POM (recomendado)

```bash
php pom-examples/POMExample/Tests/Login/LoginWithPOMTest.php
```

### Ejecutar Ejercicio de POM

```bash
# Implementa tu propio test usando los Page Objects de Guru99
```

## 💡 Mejores Prácticas

### 1. Nombrar Page Objects Claramente
```php
// ✅ Bueno
class LoginPage { }
class ProductListPage { }
class CheckoutPage { }

// ❌ Malo
class Page1 { }
class Helper { }
```

### 2. Métodos que Retornan Page Objects
```php
// Page Objects deben retornar otros Page Objects
public function clickLogin(): HomePage {
    $this->loginButton->click();
    return new HomePage($this->driver);
}
```

### 3. Encapsular Localizadores
```php
// ✅ Bueno: Localizadores privados
private function getUsernameInput() {
    return $this->driver->findElement(WebDriverBy::id('username'));
}

// ❌ Malo: Exponer localizadores
public $usernameLocator = '#username';
```

### 4. Métodos Descriptivos
```php
// ✅ Bueno
public function enterUsername($username) { }
public function clickSubmitButton() { }

// ❌ Malo
public function input($field, $value) { }
public function click($element) { }
```

### 5. Verificaciones en Page Objects
```php
// Los Page Objects pueden incluir verificaciones simples
public function isErrorDisplayed(): bool {
    try {
        return $this->errorMessage->isDisplayed();
    } catch (NoSuchElementException $e) {
        return false;
    }
}
```

## 🎓 Niveles de Implementación

### Nivel 1: POM Básico
- Una clase por página
- Métodos para acciones básicas
- Tests simples

### Nivel 2: POM Intermedio
- Herencia de Page Objects
- Componentes reutilizables
- Esperas integradas

### Nivel 3: POM Avanzado
- Page Factories
- Elementos lazy-loaded
- Fluent interfaces
- Patrones de diseño adicionales (Factory, Builder, etc.)

## 📖 Ejemplos Incluidos

### POMExample
**Sitio:** SauceDemo (https://www.saucedemo.com/)

**Páginas implementadas:**
1. **LoginPage**: Maneja el login
2. **ProductsPage**: Lista de productos

**Tests:**
- Login exitoso
- Login fallido
- Navegación a productos

### POMEjercicio
**Sitio:** Guru99 Demo

**Páginas implementadas:**
1. **Guru99Login**: Página de acceso
2. **Guru99HomePage**: Página principal

**Objetivo:** Practicar la implementación de POM desde cero

## 🔧 Extender los Ejemplos

### Agregar una Nueva Página

1. Crear clase en `Pages/`:
```php
// Pages/CartPage.php
class CartPage {
    private $driver;
    
    public function __construct(RemoteWebDriver $driver) {
        $this->driver = $driver;
    }
    
    // Métodos de la página
}
```

2. Usar en tests:
```php
$cartPage = new CartPage($this->driver);
$cartPage->addItem('producto');
```

### Agregar Nuevos Tests

1. Crear archivo en `Tests/`:
```php
// Tests/Cart/AddToCartTest.php
class AddToCartTest extends ParentTest {
    public function testAddProduct() {
        // Tu test aquí
    }
}
```

## ❓ Preguntas Frecuentes

**¿Cuándo usar POM?**
- Proyectos con más de 5-10 tests
- Aplicaciones con UI que cambia frecuentemente
- Equipos múltiples trabajando en tests
- Proyectos a largo plazo

**¿Cuándo NO usar POM?**
- Scripts de automatización únicos/simples
- Pruebas muy pequeñas (1-2 tests)
- Prototipos rápidos

**¿Puedo mezclar POM con otros patrones?**
- Sí, POM se complementa bien con:
  - Page Factory
  - Screenplay Pattern
  - Builder Pattern
  - Factory Pattern

## 📚 Recursos Adicionales

- [Selenium POM Documentation](https://www.selenium.dev/documentation/test_practices/encouraged/page_object_models/)
- [Martin Fowler - Page Object](https://martinfowler.com/bliki/PageObject.html)

## 🎯 Próximos Pasos

1. **Estudia** los ejemplos sin POM vs con POM
2. **Compara** las diferencias en mantenibilidad
3. **Implementa** tus propios Page Objects
4. **Refactoriza** ejercicios anteriores usando POM
5. **Crea** un proyecto completo con POM

---

El patrón POM es fundamental para automatización profesional. ¡Domínalo! 🚀
