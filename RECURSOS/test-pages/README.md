# 🌐 Test Pages - Páginas HTML de Prueba

Esta carpeta contiene páginas HTML estáticas diseñadas específicamente para practicar automatización web con Selenium.

## 📋 Lista de Páginas

| Archivo | Descripción | Conceptos a Practicar |
|---------|-------------|----------------------|
| `index.html` | Página de inicio básica | Navegación, título, elementos simples |
| `index_completo.html` | Página completa con múltiples elementos | Formularios, botones, enlaces |
| `test-page.html` | Página general de pruebas | Elementos variados |
| `alertas-page.html` | Demostración de alertas JavaScript | Alertas, confirms, prompts |
| `form-inside-iframe.html` | Formulario dentro de iframe | Iframes, cambio de contexto |
| `iframes-page.html` | Página con múltiples iframes | Manejo de iframes |
| `nested-iframe.html` | Iframes anidados | Navegación entre iframes |
| `sales-table.html` | Tabla de ventas | Web tables, búsqueda en tablas |
| `tabs-accordion-test.html` | Tabs y accordions | Componentes UI interactivos |
| `sortable-test.html` | Lista ordenable | Drag & drop, sortable |
| `selectable-test.html` | Elementos seleccionables | Selección múltiple |
| `resizable-test.html` | Elemento redimensionable | Resize, acciones del ratón |
| `submitted-form.html` | Página de confirmación | Validación de flujos |

## 🚀 Cómo Usar

### Opción 1: Archivo Local (Recomendado para Pruebas Rápidas)

```php
$driver->get('file:///' . __DIR__ . '/../test-pages/index.html');
```

### Opción 2: Servidor Local

```php
// Usando el servidor del Laboratorio 5
require_once __DIR__ . '/../labs/Laboratorio 5/src/Server/StaticFileServer.php';

$server = new StaticFileServer(8080);
$server->setDocumentRoot(__DIR__);
$server->start();

$driver->get('http://localhost:8080/index.html');
```

### Opción 3: Servidor Web (Apache/Nginx)

Coloca las páginas en tu document root y accede vía HTTP:
```php
$driver->get('http://localhost/test-pages/index.html');
```

## 📚 Guía de Páginas

### 🎯 Páginas Básicas

#### `index.html`
**Propósito:** Primera página de prueba  
**Elementos:**
- Título de página
- Encabezados (h1, h2)
- Párrafos de texto
- Enlaces básicos

**Ejemplo de uso:**
```php
$driver->get('file:///' . TEST_PAGES_PATH . '/index.html');
$title = $driver->getTitle();
echo "Título: " . $title;
```

#### `index_completo.html`
**Propósito:** Página con elementos variados  
**Elementos:**
- Formularios completos
- Inputs (text, email, password)
- Checkboxes y radio buttons
- Select dropdowns
- Botones
- Enlaces

**Ejemplo de uso:**
```php
$driver->findElement(WebDriverBy::id('username'))->sendKeys('test');
$driver->findElement(WebDriverBy::id('email'))->sendKeys('test@test.com');
$driver->findElement(WebDriverBy::id('submitBtn'))->click();
```

### ⚠️ Alertas y Modales

#### `alertas-page.html`
**Propósito:** Practicar manejo de alertas JavaScript  
**Tipos de alertas:**
1. **Alert simple:** Solo botón OK
2. **Confirm:** Botones OK y Cancelar
3. **Prompt:** Input de texto

**Ejemplo de uso:**
```php
// Alert simple
$driver->findElement(WebDriverBy::id('alertBtn'))->click();
$alert = $driver->switchTo()->alert();
echo $alert->getText();
$alert->accept();

// Confirm
$driver->findElement(WebDriverBy::id('confirmBtn'))->click();
$alert = $driver->switchTo()->alert();
$alert->dismiss(); // o accept()

// Prompt
$driver->findElement(WebDriverBy::id('promptBtn'))->click();
$alert = $driver->switchTo()->alert();
$alert->sendKeys('Mi respuesta');
$alert->accept();
```

### 🖼️ Iframes

#### `iframes-page.html`
**Propósito:** Practicar cambio de contexto entre iframes  
**Características:**
- Multiple iframes en una página
- Elementos dentro de cada iframe
- Navegación entre frames

**Ejemplo de uso:**
```php
// Cambiar al iframe
$driver->switchTo()->frame('iframeName');

// Interactuar con elementos del iframe
$driver->findElement(WebDriverBy::id('elementInFrame'))->click();

// Volver al contexto principal
$driver->switchTo()->defaultContent();
```

#### `nested-iframe.html`
**Propósito:** Iframes anidados (iframe dentro de iframe)  
**Ejemplo de uso:**
```php
// Entrar al primer iframe
$driver->switchTo()->frame(0);

// Entrar al iframe anidado
$driver->switchTo()->frame(0);

// Interactuar con elemento
$element = $driver->findElement(WebDriverBy::id('nestedElement'));

// Salir nivel por nivel
$driver->switchTo()->parentFrame(); // Al primer iframe
$driver->switchTo()->defaultContent(); // A la página principal
```

#### `form-inside-iframe.html`
**Propósito:** Formulario completo dentro de un iframe  
**Práctica:** Rellenar formularios en iframes

### 📊 Tablas

#### `sales-table.html`
**Propósito:** Tabla de datos para búsqueda y extracción  
**Características:**
- Headers de tabla
- Múltiples filas de datos
- Columnas: Producto, Precio, Cantidad, Total

**Ejemplo de uso:**
```php
// Obtener todas las filas
$rows = $driver->findElements(WebDriverBy::cssSelector('tbody tr'));

foreach ($rows as $row) {
    $cells = $row->findElements(WebDriverBy::tagName('td'));
    $producto = $cells[0]->getText();
    $precio = $cells[1]->getText();
    echo "$producto - $precio\n";
}

// Buscar una celda específica
$cell = $driver->findElement(
    WebDriverBy::xpath("//td[text()='Laptop']")
);
```

### 🎨 Componentes UI

#### `tabs-accordion-test.html`
**Propósito:** Tabs y accordions interactivos  
**Componentes:**
- Sistema de tabs (pestañas)
- Accordions desplegables

**Ejemplo de uso:**
```php
// Cambiar de tab
$driver->findElement(WebDriverBy::id('tab2'))->click();
$content = $driver->findElement(WebDriverBy::id('content2'))->getText();

// Expandir accordion
$driver->findElement(WebDriverBy::className('accordion-header'))->click();
$content = $driver->findElement(WebDriverBy::className('accordion-content'));
```

#### `sortable-test.html`
**Propósito:** Lista de elementos ordenables (drag & drop)  
**Ejemplo de uso:**
```php
use Facebook\WebDriver\Interactions\WebDriverActions;

$actions = new WebDriverActions($driver);
$source = $driver->findElement(WebDriverBy::id('item1'));
$target = $driver->findElement(WebDriverBy::id('item3'));

$actions->dragAndDrop($source, $target)->perform();
```

#### `selectable-test.html`
**Propósito:** Elementos seleccionables (multi-selección)  
**Ejemplo de uso:**
```php
use Facebook\WebDriver\WebDriverKeys;

$item1 = $driver->findElement(WebDriverBy::id('item1'));
$item2 = $driver->findElement(WebDriverBy::id('item2'));

// Selección múltiple con Ctrl
$item1->click();
$actions = new WebDriverActions($driver);
$actions->keyDown(WebDriverKeys::CONTROL)
        ->click($item2)
        ->keyUp(WebDriverKeys::CONTROL)
        ->perform();
```

#### `resizable-test.html`
**Propósito:** Elemento redimensionable  
**Ejemplo de uso:**
```php
$resizer = $driver->findElement(WebDriverBy::className('resizer'));

$actions = new WebDriverActions($driver);
$actions->clickAndHold($resizer)
        ->moveByOffset(100, 50)
        ->release()
        ->perform();
```

### ✅ Páginas de Confirmación

#### `submitted-form.html`
**Propósito:** Página de confirmación después de enviar formulario  
**Uso:** Validar que el flujo de navegación funcione correctamente

## 💡 Tips de Uso

### 1. Rutas Absolutas vs Relativas

```php
// ✅ Bueno: Usar constante de configuración
$driver->get('file:///' . TEST_PAGES_PATH . '/index.html');

// ❌ Evitar: Rutas hardcodeadas
$driver->get('file:///C:/Users/miusuario/Desktop/...');
```

### 2. Esperar a que la Página Cargue

```php
$driver->get('file:///' . TEST_PAGES_PATH . '/index.html');

// Esperar a que un elemento esté presente
$wait = new WebDriverWait($driver, 10);
$wait->until(
    WebDriverExpectedCondition::presenceOfElementLocated(
        WebDriverBy::id('mainContent')
    )
);
```

### 3. Validar que Estás en la Página Correcta

```php
$currentUrl = $driver->getCurrentURL();
$this->assertStringContainsString('index.html', $currentUrl);
```

### 4. Screenshots para Debugging

```php
$driver->takeScreenshot(__DIR__ . '/screenshots/test-page.png');
```

## 🔧 Modificar las Páginas

Puedes editar estas páginas HTML para:
- Agregar nuevos elementos
- Cambiar IDs o clases
- Crear escenarios específicos
- Añadir JavaScript personalizado

**Ejemplo: Agregar un botón dinámico**
```html
<button id="dynamicBtn" onclick="addElement()">
    Agregar Elemento
</button>

<script>
function addElement() {
    const div = document.createElement('div');
    div.id = 'newElement';
    div.textContent = 'Elemento creado dinámicamente';
    document.body.appendChild(div);
}
</script>
```

## 🎓 Ejercicios Sugeridos

1. **Nivel Básico:**
   - Navegar por todas las páginas
   - Obtener el título de cada una
   - Encontrar elementos por ID

2. **Nivel Intermedio:**
   - Rellenar formularios completos
   - Manejar todas las alertas
   - Extraer datos de tablas

3. **Nivel Avanzado:**
   - Trabajar con iframes anidados
   - Implementar drag & drop
   - Crear un test suite completo usando todas las páginas

## 📚 Páginas Relacionadas con Ejemplos

| Página | Ejemplo Relacionado |
|--------|---------------------|
| `alertas-page.html` | `examples/16.AlertTest.php` |
| `iframes-page.html` | `examples/19.Iframes.php` |
| `nested-iframe.html` | `examples/20.IframesAnidados.php` |
| `sales-table.html` | `examples/21.WebTables.php` |
| `tabs-accordion-test.html` | `examples/24.Accordion.php`, `examples/25.Tabs.php` |
| `sortable-test.html` | `examples/26.Sortable.php` |
| `selectable-test.html` | `examples/27.Selectable.php` |
| `resizable-test.html` | `examples/28.Resizable.php` |

---

Estas páginas son tus aliadas para practicar sin depender de sitios externos. ¡Experimenta libremente! 🚀
