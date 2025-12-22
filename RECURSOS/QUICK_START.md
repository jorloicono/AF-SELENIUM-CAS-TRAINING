# 🚀 Guía Rápida - Selenium + PHP

## ⚡ Inicio Rápido (5 minutos)

### 1. Verificar Instalación
```bash
# Verificar PHP
php --version

# Verificar Composer
composer --version

# Instalar dependencias
composer install
```

### 2. Primer Test
```bash
php examples/1.CrearInstanciayObtenerTitulo.php
```

### 3. Explorar Ejemplos
```bash
# Ver lista de ejemplos
ls examples/

# Ejecutar ejemplo de formulario
php examples/7.RellenarFormulario.php
```

## 📚 Rutas de Aprendizaje

### 🌱 Principiante (1-2 semanas)

**Día 1-3: Fundamentos**
```bash
php examples/1.CrearInstanciayObtenerTitulo.php
php examples/3.BuscarId.php
php examples/4.BuscarLink.php
php examples/5.BuscarTag.php
php examples/6.BuscarXPath.php
php examples/7.RellenarFormulario.php
```

**Día 4-7: Esperas**
```bash
php examples/8.DemoErrorNoUsoEsperas.php  # Ver el problema
php examples/9.EjemploImplicitWait.php    # Solución básica
php examples/11.FluenttWait.php           # Solución avanzada
```

**Día 8-14: Práctica**
```bash
php exercises/Ejercicio\ 1.php
php exercises/Ejercicio\ 3.php
```

### 🌿 Intermedio (2-3 semanas)

**Semana 1: Interacciones**
```bash
php examples/16.AlertTest.php           # Alertas
php examples/19.Iframes.php             # Iframes
php examples/21.WebTables.php           # Tablas
php examples/23.Ventanas.php            # Ventanas
```

**Semana 2: Componentes UI**
```bash
php examples/24.Accordion.php
php examples/25.Tabs.php
php examples/26.Sortable.php
```

**Semana 3: Ejercicios**
```bash
php exercises/Ejercicio\ 4.php
php exercises/Ejercicio\ 5.php
php labs/Laboratorio\ 5/lab5.php
```

### 🌳 Avanzado (2-4 semanas)

**Page Object Model**
```bash
# Comparar enfoques
php pom-examples/POMExample/Tests/Login/LoginWithOutPOMTest.php
php pom-examples/POMExample/Tests/Login/LoginWithPOMTest.php
```

**Proyecto Final**
```bash
php exercises/Ejercicio\ 6.php
# Crear tu propio proyecto con POM
```

## 🔍 Comandos Útiles

### Exploración
```bash
# Ver estructura del proyecto
tree /F

# Buscar un ejemplo específico
dir examples\*Alert* /s

# Ver contenido de un archivo
cat examples/1.CrearInstanciayObtenerTitulo.php
```

### Desarrollo
```bash
# Ejecutar test
php examples/test.php

# Ver errores detallados
php -d display_errors=On examples/test.php

# Verificar sintaxis
php -l examples/test.php
```

### Debugging
```bash
# Ver variables de entorno
php -i | grep selenium

# Verificar ChromeDriver
cd drivers
./chromedriver.exe --version
```

## 📖 Cheat Sheet

### Inicializar WebDriver
```php
require_once __DIR__ . '/config/selenium-config.php';
require_once __DIR__ . '/vendor/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

$driver = RemoteWebDriver::create(
    CHROMEDRIVER_HOST,
    DesiredCapabilities::chrome()
);
```

### Localizar Elementos
```php
use Facebook\WebDriver\WebDriverBy;

// Por ID
$element = $driver->findElement(WebDriverBy::id('myId'));

// Por nombre
$element = $driver->findElement(WebDriverBy::name('username'));

// Por clase CSS
$element = $driver->findElement(WebDriverBy::className('btn-primary'));

// Por CSS Selector
$element = $driver->findElement(WebDriverBy::cssSelector('.container > button'));

// Por XPath
$element = $driver->findElement(WebDriverBy::xpath('//button[@id="submit"]'));

// Por texto de enlace
$element = $driver->findElement(WebDriverBy::linkText('Click aquí'));

// Por texto parcial de enlace
$element = $driver->findElement(WebDriverBy::partialLinkText('Click'));

// Por nombre de etiqueta
$elements = $driver->findElements(WebDriverBy::tagName('button'));
```

### Interacciones Básicas
```php
// Navegar
$driver->get('https://example.com');

// Click
$element->click();

// Escribir texto
$element->sendKeys('mi texto');

// Limpiar input
$element->clear();

// Obtener texto
$text = $element->getText();

// Obtener atributo
$value = $element->getAttribute('value');

// Verificar si está visible
$isVisible = $element->isDisplayed();

// Verificar si está habilitado
$isEnabled = $element->isEnabled();

// Verificar si está seleccionado (checkbox/radio)
$isSelected = $element->isSelected();
```

### Esperas
```php
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

// Espera explícita
$wait = new WebDriverWait($driver, 10); // 10 segundos
$element = $wait->until(
    WebDriverExpectedCondition::presenceOfElementLocated(
        WebDriverBy::id('myElement')
    )
);

// Espera implícita
$driver->manage()->timeouts()->implicitlyWait(10);
```

### Alertas
```php
// Cambiar a alerta
$alert = $driver->switchTo()->alert();

// Obtener texto
$text = $alert->getText();

// Aceptar (OK)
$alert->accept();

// Cancelar
$alert->dismiss();

// Enviar texto (prompt)
$alert->sendKeys('mi respuesta');
```

### Iframes
```php
// Cambiar a iframe por índice
$driver->switchTo()->frame(0);

// Cambiar a iframe por nombre/ID
$driver->switchTo()->frame('iframeName');

// Cambiar a iframe por elemento
$iframe = $driver->findElement(WebDriverBy::id('myIframe'));
$driver->switchTo()->frame($iframe);

// Volver al contenido principal
$driver->switchTo()->defaultContent();

// Volver al frame padre
$driver->switchTo()->parentFrame();
```

### Ventanas
```php
// Obtener handle actual
$mainWindow = $driver->getWindowHandle();

// Obtener todos los handles
$windows = $driver->getWindowHandles();

// Cambiar a ventana
$driver->switchTo()->window($windows[1]);

// Volver a ventana principal
$driver->switchTo()->window($mainWindow);

// Cerrar ventana actual
$driver->close();
```

### Acciones Avanzadas
```php
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverKeys;

$actions = new WebDriverActions($driver);

// Hover sobre elemento
$actions->moveToElement($element)->perform();

// Click derecho
$actions->contextClick($element)->perform();

// Doble click
$actions->doubleClick($element)->perform();

// Drag and Drop
$actions->dragAndDrop($source, $target)->perform();

// Presionar tecla
$actions->sendKeys(WebDriverKeys::ENTER)->perform();

// Combinación de teclas
$actions->keyDown(WebDriverKeys::CONTROL)
        ->sendKeys('a')
        ->keyUp(WebDriverKeys::CONTROL)
        ->perform();
```

### Screenshots
```php
// Captura de pantalla completa
$driver->takeScreenshot('screenshot.png');

// Captura de un elemento específico
$element->takeElementScreenshot('element.png');
```

### Navegación
```php
// Ir a URL
$driver->get('https://example.com');

// Navegar atrás
$driver->navigate()->back();

// Navegar adelante
$driver->navigate()->forward();

// Refrescar página
$driver->navigate()->refresh();

// Obtener URL actual
$currentUrl = $driver->getCurrentURL();

// Obtener título
$title = $driver->getTitle();
```

### Cerrar WebDriver
```php
// Cerrar ventana actual
$driver->close();

// Cerrar todas las ventanas y terminar sesión
$driver->quit();
```

## 🆘 Solución Rápida de Problemas

| Problema | Solución Rápida |
|----------|-----------------|
| ChromeDriver no encontrado | Verificar `drivers/chromedriver.exe` existe |
| Elemento no encontrado | Agregar espera explícita |
| Timeout | Aumentar tiempo de espera |
| Puerto en uso | Cambiar `CHROMEDRIVER_PORT` en config |
| Error de dependencias | `composer install` o `composer update` |
| Alert no manejada | Usar `$driver->switchTo()->alert()` |
| Iframe no accesible | Usar `$driver->switchTo()->frame()` |
| Stale element | Re-localizar el elemento |

## 📱 Contacto y Recursos

- **Documentación completa:** Ver `README.md`
- **Ejemplos:** Carpeta `examples/` con README propio
- **Ejercicios:** Carpeta `exercises/` con README propio
- **POM:** Carpeta `pom-examples/` con guía completa

---

**💡 Tip:** Comienza con los ejemplos numerados del 1 al 7, luego avanza progresivamente. ¡No te saltes pasos!
