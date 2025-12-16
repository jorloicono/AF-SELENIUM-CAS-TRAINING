# GUÍA COMPLETA: POM 

## Objetivo del Laboratorio

Este laboratorio enseña cómo implementar el patrón **Page Object Model (POM)** en PHP para automatizar pruebas de aplicaciones web. 

El caso de uso práctico es:
1. Acceder al sitio de demostración de Guru99 Bank
2. Verificar que el texto "Guru99 Bank" aparece en la página de login
3. Iniciar sesión con credenciales de prueba
4. Verificar que el dashboard muestra el ID del usuario logueado

Este enfoque es profesional y mantenible porque:
- **Separa la lógica de prueba de la lógica de interacción** con la UI
- **Permite reutilizar código** entre diferentes casos de prueba
- **Facilita mantenimiento** si los selectores o estructura HTML cambian
- **Mejora la legibilidad** del código de automatización

---

## Estructura de Carpetas del Proyecto

```
laboratorio-5-php/
│
├── composer.json                    # Configuración de dependencias PHP
├── composer.lock                    # Lock file de dependencias
├── geckodriver                      # Driver ejecutable de Firefox (descargado)
│
├── src/
│   ├── Pages/
│   │   ├── Guru99Login.php         # Página de Login - POM
│   │   └── Guru99HomePage.php      # Página de Home - POM
│   │
│   ├── Tests/
│   │   └── Test99GuruLogin.php     # Caso de prueba principal
│   │
│   └── Helpers/
│       └── WebDriverHelper.php     # Configuración y utilidades del driver
│
├── vendor/                         # Dependencias instaladas por Composer
│
├── run_test.php                    # Script para ejecutar las pruebas
│
└── README.md                       # Documentación del proyecto

```

---

## Paso 1: Configuración Inicial

### 1.1 Instalar Composer y Dependencias

Crea el archivo `composer.json` en la raíz del proyecto:

```json
{
    "name": "guru99-pom-test",
    "description": "Laboratorio POM - Automatización con Selenium en PHP",
    "require": {
        "php": ">=7.4",
        "facebook/webdriver": "^1.14"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.5"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

Desde consola, ejecuta:

```bash
composer install
```

Esto instalará automáticamente la librería de WebDriver.

### 1.2 Descargar GeckoDriver

Descarga el driver de Firefox desde: `https://github.com/mozilla/geckodriver/releases`

Coloca el ejecutable en la raíz del proyecto o en una ruta conocida. En Linux/Mac, hazlo ejecutable:

```bash
chmod +x geckodriver
```

---

## Paso 2: Crear la Clase Helper de WebDriver

Crea el archivo `src/Helpers/WebDriverHelper.php`:

```php
<?php

namespace App\Helpers;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxOptions;

class WebDriverHelper
{
    /**
     * Crea y retorna una instancia del WebDriver para Firefox
     * 
     * @param string $geckoDriverPath Ruta al ejecutable geckodriver
     * @param int $timeoutSeconds Timeout implícito en segundos
     * @return RemoteWebDriver Instancia configurada del driver
     */
    public static function createFirefoxDriver($geckoDriverPath, $timeoutSeconds = 10)
    {
        // Esperar a que Selenium Server esté disponible (máximo 5 intentos)
        $retries = 5;
        $driver = null;
        
        while ($retries > 0) {
            try {
                $driver = RemoteWebDriver::create(
                    'http://localhost:4444/wd/hub',
                    DesiredCapabilities::firefox()
                );
                break;
            } catch (\Exception $e) {
                $retries--;
                if ($retries > 0) {
                    echo "[INFO] Reintentando conexión a Selenium Server...\n";
                    sleep(1);
                } else {
                    throw new \Exception(
                        "No se pudo conectar a Selenium Server en localhost:4444/wd/hub. " .
                        "Asegúrate de que está ejecutándose."
                    );
                }
            }
        }
        
        // Configurar timeouts
        $driver->manage()->timeouts()->implicitlyWait($timeoutSeconds * 1000); // Convertir a milisegundos
        $driver->manage()->window()->maximize(); // Maximizar ventana del navegador
        
        return $driver;
    }
    
    /**
     * Crea y retorna una instancia del WebDriver para Chrome (alternativa)
     * 
     * @param string $chromeDriverPath Ruta al ejecutable chromedriver
     * @param int $timeoutSeconds Timeout implícito en segundos
     * @return RemoteWebDriver Instancia configurada del driver
     */
    public static function createChromeDriver($chromeDriverPath, $timeoutSeconds = 10)
    {
        try {
            $options = new ChromeOptions();
            $options->addArguments(['--disable-blink-features=AutomationControlled']);
            
            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
            
            $driver = RemoteWebDriver::create(
                'http://localhost:4444/wd/hub',
                $capabilities
            );
            
            $driver->manage()->timeouts()->implicitlyWait($timeoutSeconds * 1000);
            $driver->manage()->window()->maximize();
            
            return $driver;
        } catch (\Exception $e) {
            throw new \Exception(
                "Error al crear driver de Chrome: " . $e->getMessage()
            );
        }
    }
    
    /**
     * Cierra el driver y libera recursos
     * 
     * @param RemoteWebDriver $driver
     * @return void
     */
    public static function closeDriver(RemoteWebDriver $driver)
    {
        try {
            if ($driver !== null) {
                $driver->quit();
            }
        } catch (\Exception $e) {
            echo "[WARNING] Error al cerrar el driver: " . $e->getMessage() . "\n";
        }
    }
}
```

**¿Qué hace esta clase helper?**

- `createFirefoxDriver()`: Establece conexión con Selenium Server, configura Firefox y espera a que esté disponible
- `createChromeDriver()`: Similar, pero para Chrome (opcional)
- `closeDriver()`: Cierra limpiamente el navegador
- Maneja reintentos de conexión en caso de que Selenium no esté listo

---

## Paso 3: Crear la Página de Login (POM)

Crea el archivo `src/Pages/Guru99Login.php`:

```php
<?php

namespace App\Pages;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Guru99Login
{
    /**
     * Instancia del WebDriver para interactuar con el navegador
     * 
     * @var RemoteWebDriver
     */
    private $driver;
    
    /**
     * Localizador del campo de usuario (uid)
     * Estrategia: buscar por atributo "name" con valor "uid"
     * 
     * @var WebDriverBy
     */
    private $user99GuruName;
    
    /**
     * Localizador del campo de contraseña
     * Estrategia: buscar por atributo "name" con valor "password"
     * 
     * @var WebDriverBy
     */
    private $password99Guru;
    
    /**
     * Localizador del título de la página de login
     * Estrategia: buscar por atributo "class" con valor "barone"
     * 
     * @var WebDriverBy
     */
    private $titleText;
    
    /**
     * Localizador del botón de login
     * Estrategia: buscar por atributo "name" con valor "btnLogin"
     * 
     * @var WebDriverBy
     */
    private $login;
    
    /**
     * Constructor de la página de login
     * Recibe el driver y define todos los localizadores de elementos
     * 
     * @param RemoteWebDriver $driver Instancia del WebDriver
     */
    public function __construct(RemoteWebDriver $driver)
    {
        $this->driver = $driver;
        
        // Definir localizadores para cada elemento
        $this->user99GuruName = WebDriverBy::name('uid');
        $this->password99Guru = WebDriverBy::name('password');
        $this->titleText = WebDriverBy::className('barone');
        $this->login = WebDriverBy::name('btnLogin');
    }
    
    /**
     * Escribe el nombre de usuario en el campo correspondiente
     * 
     * @param string $strUserName Nombre de usuario a ingresar
     * @return void
     * @throws Exception Si el elemento no se encuentra
     */
    public function setUserName($strUserName)
    {
        try {
            $element = $this->driver->findElement($this->user99GuruName);
            $element->clear(); // Limpiar el campo primero
            $element->sendKeys($strUserName);
        } catch (\Exception $e) {
            throw new \Exception("Error al establecer el nombre de usuario: " . $e->getMessage());
        }
    }
    
    /**
     * Escribe la contraseña en el campo correspondiente
     * 
     * @param string $strPassword Contraseña a ingresar
     * @return void
     * @throws Exception Si el elemento no se encuentra
     */
    public function setPassword($strPassword)
    {
        try {
            $element = $this->driver->findElement($this->password99Guru);
            $element->clear(); // Limpiar el campo primero
            $element->sendKeys($strPassword);
        } catch (\Exception $e) {
            throw new \Exception("Error al establecer la contraseña: " . $e->getMessage());
        }
    }
    
    /**
     * Hace clic en el botón de login
     * 
     * @return void
     * @throws Exception Si el elemento no se encuentra
     */
    public function clickLogin()
    {
        try {
            $this->driver->findElement($this->login)->click();
            // Esperar a que la página se cargue (implícitamente)
            sleep(2);
        } catch (\Exception $e) {
            throw new \Exception("Error al hacer clic en el botón de login: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene el título de la página de login
     * Este título generalmente contiene "Guru99 Bank"
     * 
     * @return string Texto del título
     * @throws Exception Si el elemento no se encuentra
     */
    public function getLoginTitle()
    {
        try {
            $titleElement = $this->driver->findElement($this->titleText);
            return $titleElement->getText();
        } catch (\Exception $e) {
            throw new \Exception("Error al obtener el título: " . $e->getMessage());
        }
    }
    
    /**
     * Método de alto nivel que encapsula todo el flujo de login
     * Esto es la esencia del POM: un método que representa una acción de negocio
     * 
     * @param string $strUserName Nombre de usuario
     * @param string $strPassword Contraseña
     * @return void
     */
    public function loginToGuru99($strUserName, $strPassword)
    {
        echo "[INFO] Iniciando sesión con usuario: $strUserName\n";
        
        // Llenar el campo de usuario
        $this->setUserName($strUserName);
        
        // Llenar el campo de contraseña
        $this->setPassword($strPassword);
        
        // Hacer clic en el botón de login
        $this->clickLogin();
        
        echo "[INFO] Sesión iniciada exitosamente\n";
    }
}
```

**¿Qué hace esta clase?**

- **Constructor**: Recibe el driver y define los localizadores de todos los elementos interactivos de la página
- **setUserName()**: Escribe el usuario en el campo de login
- **setPassword()**: Escribe la contraseña en el campo de login
- **clickLogin()**: Hace clic en el botón para enviar el formulario
- **getLoginTitle()**: Obtiene el texto del título para verificar que estamos en la página correcta
- **loginToGuru99()**: Método de alto nivel que encadena todas las acciones anteriores, representando el flujo de negocio "iniciar sesión"

---

## Paso 4: Crear la Página de Home (POM)

Crea el archivo `src/Pages/Guru99HomePage.php`:

```php
<?php

namespace App\Pages;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Guru99HomePage
{
    /**
     * Instancia del WebDriver
     * 
     * @var RemoteWebDriver
     */
    private $driver;
    
    /**
     * Localizador del elemento que contiene el nombre del usuario logueado
     * Estrategia: buscar mediante XPath una fila de tabla con clase "heading3"
     * Este elemento muestra algo como: "Manger ID : mgr123"
     * 
     * @var WebDriverBy
     */
    private $homePageUserName;
    
    /**
     * Constructor de la página de Home
     * 
     * @param RemoteWebDriver $driver Instancia del WebDriver
     */
    public function __construct(RemoteWebDriver $driver)
    {
        $this->driver = $driver;
        
        // Definir el localizador usando XPath
        $this->homePageUserName = WebDriverBy::xpath("//table//tr[@class='heading3']");
    }
    
    /**
     * Obtiene el texto del dashboard que contiene el ID del administrador
     * Este método es usado por los tests para verificar que el login fue exitoso
     * y que el usuario correcto se encuentra logueado
     * 
     * @return string Texto del elemento que contiene el ID del usuario
     * @throws Exception Si el elemento no se encuentra en la página
     */
    public function getHomePageDashboardUserName()
    {
        try {
            $element = $this->driver->findElement($this->homePageUserName);
            $text = $element->getText();
            
            echo "[INFO] Texto del dashboard obtenido: $text\n";
            
            return $text;
        } catch (\Exception $e) {
            throw new \Exception(
                "Error al obtener el nombre de usuario del dashboard: " . $e->getMessage()
            );
        }
    }
    
    /**
     * Verifica que el usuario logueado es el esperado
     * Método de conveniencia para las pruebas
     * 
     * @param string $expectedUserName El nombre de usuario que se espera
     * @return bool True si el usuario es el esperado
     */
    public function isUserLoggedInCorrectly($expectedUserName)
    {
        $dashboardText = $this->getHomePageDashboardUserName();
        
        // Buscar el nombre de usuario en el texto del dashboard (case-insensitive)
        if (stripos($dashboardText, $expectedUserName) !== false) {
            echo "[SUCCESS] El usuario '$expectedUserName' está logueado correctamente\n";
            return true;
        } else {
            echo "[ERROR] El usuario esperado '$expectedUserName' no se encontró en: $dashboardText\n";
            return false;
        }
    }
}
```

**¿Qué hace esta clase?**

- **Constructor**: Recibe el driver y define el localizador para obtener el texto del dashboard
- **getHomePageDashboardUserName()**: Obtiene el texto que contiene el ID del usuario logueado
- **isUserLoggedInCorrectly()**: Método de conveniencia que verifica si el usuario logueado es el esperado

---

## Paso 5: Crear la Clase de Prueba Principal

Crea el archivo `src/Tests/Test99GuruLogin.php`:

```php
<?php

namespace App\Tests;

use App\Pages\Guru99Login;
use App\Pages\Guru99HomePage;
use App\Helpers\WebDriverHelper;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class Test99GuruLogin
{
    /**
     * Ruta al ejecutable de geckodriver
     * Ajusta esta ruta según tu sistema operativo y ubicación del archivo
     * Linux/Mac: /path/to/geckodriver
     * Windows: C:\\path\\to\\geckodriver.exe
     * 
     * @var string
     */
    private $geckoDriverPath = './geckodriver';
    
    /**
     * URL de la aplicación a probar
     * 
     * @var string
     */
    private $applicationUrl = 'https://demo.guru99.com/V4/';
    
    /**
     * Usuario de prueba
     * 
     * @var string
     */
    private $testUsername = 'mgr123';
    
    /**
     * Contraseña de prueba
     * 
     * @var string
     */
    private $testPassword = 'mgr!23';
    
    /**
     * Instancia del WebDriver
     * 
     * @var RemoteWebDriver
     */
    private $driver;
    
    /**
     * Objeto para interactuar con la página de login
     * 
     * @var Guru99Login
     */
    private $objLogin;
    
    /**
     * Objeto para interactuar con la página de home
     * 
     * @var Guru99HomePage
     */
    private $objHomePage;
    
    /**
     * Configuración inicial: crear el driver y abrir la aplicación
     * Este método se ejecuta antes de la prueba principal
     * 
     * @return void
     * @throws Exception Si no se puede crear el driver o abrir la URL
     */
    public function setup()
    {
        echo "\n========================================\n";
        echo "LABORATORIO 5 - POM EN PHP\n";
        echo "========================================\n";
        echo "[SETUP] Iniciando configuración del entorno...\n";
        
        try {
            // Crear el driver de Firefox
            $this->driver = WebDriverHelper::createFirefoxDriver($this->geckoDriverPath, 10);
            echo "[SETUP] Driver creado exitosamente\n";
            
            // Abrir la URL de la aplicación
            $this->driver->get($this->applicationUrl);
            echo "[SETUP] URL abierta: $this->applicationUrl\n";
            
            // Esperar a que la página cargue
            sleep(2);
            
        } catch (\Exception $e) {
            echo "[ERROR] Fallo en el setup: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    /**
     * Prueba principal: verificar que la página de inicio existe y login funciona
     * 
     * Pasos:
     * 1. Verificar que el título contiene "Guru99 Bank"
     * 2. Iniciar sesión con credenciales válidas
     * 3. Verificar que estamos en la página de home
     * 4. Verificar que el usuario logueado es el correcto
     * 
     * @return void
     * @throws Exception Si alguna verificación falla
     */
    public function testHomePageAppearCorrect()
    {
        echo "\n[TEST] Iniciando: testHomePageAppearCorrect\n";
        echo "[TEST] Descripción: Verificar que la página de login existe y el login funciona\n";
        
        try {
            // PASO 1: Crear objeto de página de login
            echo "\n[PASO 1] Creando objeto de página de login...\n";
            $this->objLogin = new Guru99Login($this->driver);
            echo "[PASO 1] OK - Objeto creado\n";
            
            // PASO 2: Verificar el título de la página de login
            echo "\n[PASO 2] Obteniendo y verificando el título de login...\n";
            $loginPageTitle = $this->objLogin->getLoginTitle();
            echo "[PASO 2] Título obtenido: '$loginPageTitle'\n";
            
            // Verificar que contiene "Guru99 Bank"
            if (stripos($loginPageTitle, 'guru99 bank') === false) {
                throw new \Exception(
                    "Verificación fallida: El título no contiene 'Guru99 Bank'. " .
                    "Título actual: '$loginPageTitle'"
                );
            }
            echo "[PASO 2] ✓ OK - El título contiene 'Guru99 Bank'\n";
            
            // PASO 3: Iniciar sesión
            echo "\n[PASO 3] Iniciando sesión...\n";
            $this->objLogin->loginToGuru99($this->testUsername, $this->testPassword);
            echo "[PASO 3] OK - Sesión iniciada\n";
            
            // PASO 4: Crear objeto de página home
            echo "\n[PASO 4] Creando objeto de página home...\n";
            $this->objHomePage = new Guru99HomePage($this->driver);
            echo "[PASO 4] OK - Objeto creado\n";
            
            // PASO 5: Verificar el texto del dashboard
            echo "\n[PASO 5] Verificando el dashboard...\n";
            $dashboardText = $this->objHomePage->getHomePageDashboardUserName();
            
            // Verificar que contiene el ID del administrador
            if (stripos($dashboardText, 'manger id : ' . $this->testUsername) === false) {
                throw new \Exception(
                    "Verificación fallida: El dashboard no contiene el ID del manager. " .
                    "Esperado contener: 'manger id : $this->testUsername'. " .
                    "Obtenido: '$dashboardText'"
                );
            }
            echo "[PASO 5] ✓ OK - El dashboard muestra el ID correcto\n";
            
            // RESULTADO FINAL
            echo "\n========================================\n";
            echo "✓ TEST PASADO EXITOSAMENTE\n";
            echo "========================================\n";
            
        } catch (\Exception $e) {
            echo "\n========================================\n";
            echo "✗ TEST FALLÓ\n";
            echo "Error: " . $e->getMessage() . "\n";
            echo "========================================\n";
            throw $e;
        }
    }
    
    /**
     * Limpieza: cerrar el driver y liberar recursos
     * Este método se ejecuta después de la prueba (haya pasado o fallado)
     * 
     * @return void
     */
    public function tearDown()
    {
        echo "\n[TEARDOWN] Cerrando navegador y liberando recursos...\n";
        
        WebDriverHelper::closeDriver($this->driver);
        
        echo "[TEARDOWN] OK - Recursos liberados\n";
        echo "[TEARDOWN] Fin del laboratorio\n";
    }
    
    /**
     * Ejecuta la prueba completa: setup -> test -> teardown
     * 
     * @return void
     */
    public function run()
    {
        try {
            $this->setup();
            $this->testHomePageAppearCorrect();
        } catch (\Exception $e) {
            // El error ya fue reportado en el test
            exit(1);
        } finally {
            $this->tearDown();
        }
    }
}
```

**¿Qué hace esta clase?**

- **setup()**: Inicializa el driver de Firefox y abre la URL de la aplicación
- **testHomePageAppearCorrect()**: La prueba principal con verificaciones paso a paso
- **tearDown()**: Cierra limpiamente el navegador
- **run()**: Ejecuta la cadena completa de setup → test → teardown

---

## Paso 6: Script Principal de Ejecución

Crea el archivo `run_test.php` en la raíz del proyecto:

```php
<?php

/**
 * Script principal para ejecutar las pruebas del Laboratorio 5
 * 
 * Requisitos antes de ejecutar:
 * 1. Descargar geckodriver desde: https://github.com/mozilla/geckodriver/releases
 * 2. Colocar geckodriver en la raíz del proyecto o ajustar la ruta
 * 3. Tener Selenium Server ejecutándose en localhost:4444
 * 4. Instalar dependencias: composer install
 */

// Autoload de Composer (cargar todas las clases automáticamente)
require_once __DIR__ . '/vendor/autoload.php';

use App\Tests\Test99GuruLogin;

// Iniciar las pruebas
$test = new Test99GuruLogin();
$test->run();
```

---

## Paso 7: Documentación del Proyecto

Crea el archivo `README.md`:

```markdown
# Laboratorio 5: POM en PHP

## Descripción

Implementación del patrón Page Object Model (POM) para automatización de pruebas usando PHP y Selenium WebDriver.

Este laboratorio demuestra cómo estructurar código de automatización de forma profesional y mantenible.

## Requisitos Previos

- PHP 7.4 o superior
- Composer
- Firefox instalado en el sistema
- GeckoDriver (descargable desde https://github.com/mozilla/geckodriver/releases)
- Java (para ejecutar Selenium Server)
- Selenium Server Standalone

## Instalación

### 1. Clonar o crear el proyecto

```bash
mkdir laboratorio-5-php
cd laboratorio-5-php
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Descargar GeckoDriver

Descarga el archivo apropiado para tu sistema operativo desde:
https://github.com/mozilla/geckodriver/releases

Extrae el archivo y colócalo en la raíz del proyecto, o ajusta la ruta en `src/Tests/Test99GuruLogin.php`.

### 4. Iniciar Selenium Server

Abre una terminal y ejecuta:

```bash
java -jar selenium-server-standalone-4.x.x.jar -port 4444
```

(Reemplaza la versión según la que descargues)

## Ejecución

En otra terminal, desde la raíz del proyecto:

```bash
php run_test.php
```

## Estructura de Archivos

```
laboratorio-5-php/
├── src/
│   ├── Helpers/
│   │   └── WebDriverHelper.php       # Configuración del driver
│   ├── Pages/
│   │   ├── Guru99Login.php           # POM - Página de Login
│   │   └── Guru99HomePage.php        # POM - Página de Home
│   └── Tests/
│       └── Test99GuruLogin.php       # Caso de prueba
├── vendor/                           # Dependencias
├── run_test.php                      # Script de ejecución
├── composer.json                     # Configuración de Composer
└── geckodriver                       # Driver descargado

```

## Conceptos Clave

### Page Object Model (POM)

El POM es un patrón de diseño que:

1. **Encapsula la interacción** con elementos de la UI en clases específicas
2. **Separa la lógica de prueba** de la lógica de interacción con el navegador
3. **Facilita el mantenimiento** porque los cambios en la UI se hacen en un solo lugar
4. **Mejora la reutilización** de código entre diferentes casos de prueba

### Ejemplo

En lugar de escribir en cada test:

```php
$driver->findElement(WebDriverBy::name('uid'))->sendKeys('mgr123');
$driver->findElement(WebDriverBy::name('password'))->sendKeys('mgr!23');
$driver->findElement(WebDriverBy::name('btnLogin'))->click();
```

Con POM, escribes una sola vez:

```php
$loginPage->loginToGuru99('mgr123', 'mgr!23');
```

## Solución de Problemas

### Error: "Connection refused to localhost:4444"

- Asegúrate de que Selenium Server está ejecutándose
- Verifica que no hay otra aplicación usando el puerto 4444

### Error: "geckodriver not found"

- Descarga geckodriver desde https://github.com/mozilla/geckodriver/releases
- Colócalo en la raíz del proyecto o ajusta la ruta en `Test99GuruLogin.php`
- En Linux/Mac, hazlo ejecutable: `chmod +x geckodriver`

### La prueba se ejecuta muy rápido

- Puedes añadir `sleep(2)` entre pasos si necesitas ver lo que ocurre
- La velocidad es normal en automatización

## Próximos Pasos

Para extender este laboratorio:

1. Crear más casos de prueba (nuevos métodos en `Test99GuruLogin`)
2. Añadir nuevas páginas (crear nuevas clases en `src/Pages/`)
3. Implementar manejo de excepciones más robusto
4. Usar un framework de pruebas como PHPUnit
5. Crear reportes de ejecución
```

---

## Paso 8: Archivo composer.json Completo

```json
{
    "name": "laboratorio-5-pom-php",
    "description": "Laboratorio 5: Implementación del patrón POM en PHP para automatización con Selenium",
    "type": "project",
    "license": "MIT",
    "authors": [
        {
            "name": "Automatización QA",
            "role": "Automatización"
        }
    ],
    "require": {
        "php": ">=7.4",
        "facebook/webdriver": "^1.14"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.5"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

---

## Paso 9: Guía de Conceptos Fundamentales

### ¿Qué es WebDriver?

WebDriver es una interfaz que permite controlar un navegador web mediante código. En nuestro caso:

- **LocalWebDriver**: Controla el navegador en la misma máquina (requiere descargar drivers)
- **RemoteWebDriver**: Se conecta a un servidor Selenium que controla el navegador

Nosotros usamos `RemoteWebDriver` porque requiere Selenium Server ejecutándose en `localhost:4444`.

### ¿Qué es un localizador?

Un localizador es una instrucción para encontrar un elemento específico en la página HTML. Existen varias estrategias:

- `WebDriverBy::name()`: Busca por el atributo HTML `name`
- `WebDriverBy::id()`: Busca por el atributo HTML `id`
- `WebDriverBy::className()`: Busca por el atributo HTML `class`
- `WebDriverBy::xpath()`: Busca mediante una expresión XPath (muy flexible)
- `WebDriverBy::cssSelector()`: Busca mediante un selector CSS

Ejemplo en HTML:
```html
<input name="uid" type="text">
```

Se localiza con: `WebDriverBy::name('uid')`

### ¿Por qué usar POM?

**Sin POM** (código espagueti):

```php
// Test 1
$driver->findElement(WebDriverBy::name('uid'))->sendKeys('user1');
$driver->findElement(WebDriverBy::name('password'))->sendKeys('pass1');
$driver->findElement(WebDriverBy::name('btnLogin'))->click();

// Test 2
$driver->findElement(WebDriverBy::name('uid'))->sendKeys('user2');
$driver->findElement(WebDriverBy::name('password'))->sendKeys('pass2');
$driver->findElement(WebDriverBy::name('btnLogin'))->click();

// Si el selector del botón cambia a "id='login-button'", 
// tenemos que cambiar TODAS las pruebas
```

**Con POM** (código mantenible):

```php
// Test 1
$loginPage = new Guru99Login($driver);
$loginPage->loginToGuru99('user1', 'pass1');

// Test 2
$loginPage = new Guru99Login($driver);
$loginPage->loginToGuru99('user2', 'pass2');

// Si el selector del botón cambia, solo cambio Guru99Login.php
```

---

## Paso 10: Ejecución Paso a Paso

### Paso 1: Preparación del Entorno

```bash
# Crear el directorio del proyecto
mkdir laboratorio-5-php
cd laboratorio-5-php

# Crear la estructura de carpetas
mkdir -p src/Pages src/Tests src/Helpers
mkdir vendor

# Crear composer.json (ver Paso 8)
```

### Paso 2: Instalar Dependencias

```bash
composer install
```

### Paso 3: Descargar GeckoDriver

1. Ir a: https://github.com/mozilla/geckodriver/releases
2. Descargar la versión para tu sistema:
   - Linux: `geckodriver-v0.33.3-linux64.tar.gz`
   - macOS: `geckodriver-v0.33.3-macos.tar.gz`
   - Windows: `geckodriver-v0.33.3-win-aarch64.zip`
3. Extraer en la raíz del proyecto

### Paso 4: Crear Archivos PHP

1. Crear `src/Helpers/WebDriverHelper.php` (ver Paso 2)
2. Crear `src/Pages/Guru99Login.php` (ver Paso 3)
3. Crear `src/Pages/Guru99HomePage.php` (ver Paso 4)
4. Crear `src/Tests/Test99GuruLogin.php` (ver Paso 5)
5. Crear `run_test.php` (ver Paso 6)

### Paso 5: Iniciar Selenium Server

Abre una terminal y ejecuta:

```bash
java -jar selenium-server-standalone-4.x.x.jar -port 4444
```

Deberías ver algo como:
```
INFO: Selenium Server is up and running on port 4444
```

### Paso 6: Ejecutar las Pruebas

En otra terminal, desde la raíz del proyecto:

```bash
php run_test.php
```

Deberías ver una salida como:

```
========================================
LABORATORIO 5 - POM EN PHP
========================================
[SETUP] Iniciando configuración del entorno...
[SETUP] Driver creado exitosamente
[SETUP] URL abierta: https://demo.guru99.com/V4/

[TEST] Iniciando: testHomePageAppearCorrect
[TEST] Descripción: Verificar que la página de login existe y el login funciona

[PASO 1] Creando objeto de página de login...
[PASO 1] OK - Objeto creado

[PASO 2] Obteniendo y verificando el título de login...
[PASO 2] Título obtenido: 'Guru99 Bank - Demo Site'
[PASO 2] ✓ OK - El título contiene 'Guru99 Bank'

[PASO 3] Iniciando sesión...
[INFO] Iniciando sesión con usuario: mgr123
[INFO] Sesión iniciada exitosamente

[PASO 4] Creando objeto de página home...
[PASO 4] OK - Objeto creado

[PASO 5] Verificando el dashboard...
[INFO] Texto del dashboard obtenido: Manger ID : mgr123
[PASO 5] ✓ OK - El dashboard muestra el ID correcto

========================================
✓ TEST PASADO EXITOSAMENTE
========================================

[TEARDOWN] Cerrando navegador y liberando recursos...
[TEARDOWN] OK - Recursos liberados
[TEARDOWN] Fin del laboratorio
```

---

## Resumen de lo Aprendido

Este laboratorio enseña:

1. **Patrón POM**: Cómo organizar código de automatización de forma profesional
2. **Separación de responsabilidades**: Cada clase tiene una responsabilidad clara
3. **Reutilización**: Métodos de alto nivel que encapsulan acciones comunes
4. **Mantenibilidad**: Cambios en la UI se hacen en un solo lugar
5. **WebDriver en PHP**: Cómo usar Facebook WebDriver para automatizar con PHP

### Beneficios del POM

- ✓ Código más legible
- ✓ Más fácil de mantener
- ✓ Cambios en un solo lugar
- ✓ Reutilizable en múltiples tests
- ✓ Profesional y escalable
