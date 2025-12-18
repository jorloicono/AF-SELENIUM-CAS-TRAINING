# Ejercicio: Implicit y Explicit Waits en PHP - Solo Waits

## Objetivo

Dominar la diferencia entre **Implicit Waits** y **Explicit Waits** sin Expected Conditions en PHP con Selenium.

---

## 📋 Parte 1: Preguntas Teóricas

### Pregunta 1: ¿Cuál es la diferencia fundamental?

**Implicit Wait:**
- ¿Cuándo se configura?
- ¿A qué elementos se aplica?
- ¿Se puede cambiar durante la ejecución?
- ¿Qué excepción lanza si no encuentra el elemento?

**Tu respuesta:**
```
Implicit se configura: _________________________________________
Se aplica a: _________________________________________
Se puede cambiar: _________________________________________
Excepción: _________________________________________
```

---

### Pregunta 2: ¿Cuándo usar Explicit Wait?

Indica si deberías usar Explicit Wait en estos casos:

- [ ] Un elemento que siempre está en la página
- [ ] Un elemento que aparece después de hacer clic
- [ ] Un elemento que aparece 2 segundos después de llenar un campo
- [ ] Un elemento que cambia de visibilidad dinámicamente
- [ ] Un botón que siempre está presente desde carga

**Tu respuesta:** ✓ casos: _____, _____

---

### Pregunta 3: ¿Cómo implementar un Explicit Wait sin ExpectedConditions en PHP?

Rellena el código:

```php
$wait = new WebDriverWait($driver, ?);

$element = $wait->until(function ($driver) {
    try {
        $elem = $driver->findElement(WebDriverBy::id('campo-dinamico'));
        return ? ? ? : null;
    } catch (Exception $e) {
        return ?;
    }
});
```

**Tu respuesta:**
- Timeout: `?` segundos
- Condición: `$elem->???()` → debe devolver true
- Si falla: `?`

---

### Pregunta 4: ¿Qué sucede en estos escenarios?

**Escenario 1:**
```php
$driver->manage()->timeouts()->implicitlyWait(10);
// ... elemento no aparece en 15 segundos
$elem = $driver->findElement(WebDriverBy::id('campo'));
```
¿Qué pasa? _____________________________________________

**Escenario 2:**
```php
$wait = new WebDriverWait($driver, 3);
$elem = $wait->until(function ($driver) {
    $e = $driver->findElement(WebDriverBy::id('campo'));
    return $e->isDisplayed() ? $e : null;
});
// El elemento aparece después de 5 segundos
```
¿Qué pasa? _____________________________________________

**Tu respuesta:**
- Escenario 1: _________________________________________
- Escenario 2: _________________________________________

---

## 💻 Parte 2: Ejercicio Práctico Minimalista

### Configuración

Descarga estas 2 páginas HTML en una carpeta:

**`simple-form.html`** - Página con elementos dinámicos:
```html
<!DOCTYPE html>
<html>
<head>
    <title>Wait Practice</title>
</head>
<body>
    <h1>Prueba de Waits</h1>
    
    <!-- Elemento que siempre está (para Implicit) -->
    <input id="always-here" type="text" placeholder="Siempre visible">
    
    <!-- Elemento que aparece después 2 segundos (para Explicit) -->
    <input id="dynamic-field" type="text" placeholder="Aparece dinamicamente" style="display:none;">
    
    <button onclick="showField()">Mostrar Campo Dinámico</button>
    
    <script>
        function showField() {
            setTimeout(() => {
                document.getElementById('dynamic-field').style.display = 'block';
            }, 2000);
        }
    </script>
</body>
</html>
```

Guarda como `simple-form.html` y nota su ruta.

---

### Tarea 1: Implicit Wait

**Código base:**
```php
<?php
require 'vendor/autoload.php';

use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverBy;

$driver = ChromeDriver::start();

// TODO: Configura Implicit Wait de 10 segundos
// $driver->manage()->timeouts()->implicitlyWait(?);

try {
    $driver->get('file:///C:/ruta/a/simple-form.html');
    
    // TODO: Localiza el campo "always-here" usando findElement
    // No deberías agregar esperas adicionales
    // $elem = $driver->findElement(?);
    
    // TODO: Escribe "Test Implicit" en el campo
    // $elem->?('Test Implicit');
    
    echo "✓ Implicit Wait funcionó\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
} finally {
    $driver->quit();
}
?>
```

**Tu tarea:** Completa el código (3 líneas)

**Validación:** El campo debe contener "Test Implicit"

---

### Tarea 2: Explicit Wait (el campo que aparece)

**Código base:**
```php
<?php
require 'vendor/autoload.php';

use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;

$driver = ChromeDriver::start();
$driver->manage()->timeouts()->implicitlyWait(10);

try {
    $driver->get('file:///C:/ruta/a/simple-form.html');
    
    // PASO 1: Haz clic en el botón
    $btn = $driver->findElement(WebDriverBy::cssSelector('button'));
    $btn->click();
    echo "✓ Botón clickeado, esperando campo dinámico...\n";
    
    // PASO 2: TODO - Usa Explicit Wait para esperar "dynamic-field"
    // Espera 5 segundos
    // Valida que esté visible
    // Sin ExpectedConditions
    
    // $wait = new WebDriverWait($driver, ?);
    // $elem = $wait->until(function ($driver) {
    //     try {
    //         $e = $driver->findElement(WebDriverBy::id('?'));
    //         return $e->?() ? $e : null;
    //     } catch (Exception $e) {
    //         return ?;
    //     }
    // });
    
    // PASO 3: Escribe "Test Explicit" en el campo dinámico
    // $elem->sendKeys('?');
    
    echo "✓ Explicit Wait funcionó\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
} finally {
    $driver->quit();
}
?>
```

**Tu tarea:** Completa Explicit Wait (5 líneas en el callable)

**Validación:** El campo debe contener "Test Explicit" tras esperar

---

### Tarea 3: Comparación

**Código base - ¿Qué pasará?**

```php
<?php
require 'vendor/autoload.php';

use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;

$driver = ChromeDriver::start();

// SIN Implicit Wait (comentado)
// $driver->manage()->timeouts()->implicitlyWait(10);

try {
    $driver->get('file:///C:/ruta/a/simple-form.html');
    
    // Haz clic en el botón
    $btn = $driver->findElement(WebDriverBy::cssSelector('button'));
    $btn->click();
    
    // TODO: ¿Qué pasará aquí?
    // $elem = $driver->findElement(WebDriverBy::id('dynamic-field'));
    
    echo "✓ Campo encontrado\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
} finally {
    $driver->quit();
}
?>
```

**Tu predicción:**
- ¿Se encontrará el elemento? SI / NO
- ¿Por qué? _________________________________________
- ¿Qué error se lanzaría? _________________________________________

---

## 📝 Parte 3: Análisis de Código

### Código A: ¿Está bien?

```php
$driver->manage()->timeouts()->implicitlyWait(10);

// ... cargar página

$elem = $driver->findElement(WebDriverBy::id('campo'));
$elem->sendKeys('texto');

$wait = new WebDriverWait($driver, 5);
$dinamico = $wait->until(function ($driver) {
    return $driver->findElement(WebDriverBy::id('dinamico'))->isDisplayed() 
           ? $driver->findElement(WebDriverBy::id('dinamico')) 
           : null;
});
```

**Preguntas:**
- ¿El Implicit Wait ayuda en el primer `findElement`? SI / NO
- ¿El Explicit Wait es necesario aquí? SI / NO
- ¿Hay redundancia? SI / NO
- ¿Cómo lo optimizarías? _________________________________________

---

### Código B: ¿Hay un error?

```php
$wait = new WebDriverWait($driver, 5);

$elem = $wait->until(function ($driver) {
    $e = $driver->findElement(WebDriverBy::id('campo'));
    return $e->isDisplayed();
});

$elem->sendKeys('texto'); // ¿Funcionará?
```

**Preguntas:**
- ¿Cuál es el problema? _________________________________________
- ¿Qué devuelve `isDisplayed()`? _________________________________________
- ¿Cómo lo arreglarías? _________________________________________

---


---

¡Completá el ejercicio y me avisas! 🚀

