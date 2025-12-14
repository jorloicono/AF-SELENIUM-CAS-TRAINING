# Guía Completa: PHP + Selenium + ChromeDriver desde Cero

## Introducción

Esta guía te enseña a crear un proyecto de automatización web con PHP y Selenium. Automatizaremos la navegación a **Wikipedia** y extraeremos el título de la página principal de forma segura y confiable.

**Requisitos del sistema:**
- Windows 10/11
- PHP 7.4 o superior
- Google Chrome instalado
- Internet para descargas

---

## PARTE 1: Preparar el Entorno

### Paso 1.1: Verificar versión de Chrome

Es **crítico** que descargues ChromeDriver exactamente compatible con tu versión de Chrome.

1. Abre Chrome en tu computadora
2. Haz clic en los **tres puntos** (⋮) en la esquina superior derecha
3. Selecciona **Configuración → Acerca de Chrome**
4. Verás algo como: **"Versión 131.0.6778.264 (Build oficial) (64 bits)"**
5. **Anota el número de versión principal (ejemplo: 131)**

### Paso 1.2: Instalar PHP (si no lo tienes)

Verifica si PHP está instalado:

```cmd
php -v
```

Si ves una versión (ej: PHP 8.2.0), ya tienes PHP. Si no, descarga desde:
- https://windows.php.net/download/

Extrae en `C:\php\` y agrega a PATH del sistema (busca "variables de entorno" en Windows).

### Paso 1.3: Instalar Composer (gestor de dependencias PHP)

Composer permite instalar librerías PHP automáticamente.

1. Descarga el instalador: https://getcomposer.org/download/
2. Ejecuta `Composer-Setup.exe`
3. Sigue el asistente (selecciona tu ruta PHP en `C:\php\php.exe`)
4. Verifica la instalación:

```cmd
composer --version
```

Deberías ver: `Composer version 2.x.x`

---

## PARTE 2: Crear el Proyecto PHP

### Paso 2.1: Crear carpeta del proyecto

Abre PowerShell o CMD y ejecuta:

```cmd
cd Desktop
mkdir selenium-php-proyecto
cd selenium-php-proyecto
```

Ahora estás en: `C:\Users\[TuUsuario]\Desktop\selenium-php-proyecto\`

### Paso 2.2: Inicializar proyecto con Composer

```cmd
composer init
```

Responde las preguntas (presiona Enter para valores por defecto):
- **package name:** `mi-proyecto/selenium-test`
- **description:** `Automatización con PHP y Selenium`
- **author:** Tu nombre
- **require:** (Enter para saltar)

Se creará `composer.json` automáticamente.

### Paso 2.3: Instalar php-webdriver

Esta es la librería que controlará el navegador:

```cmd
composer require php-webdriver/webdriver
```

Espera a que termine (descargará ~50MB). Verás una carpeta `vendor/` creada con todas las dependencias.

---

## PARTE 3: Descargar ChromeDriver

### Paso 3.1: Descargar archivo compatible

1. Ve a: https://developer.chrome.com/docs/chromedriver/downloads
2. En la tabla, **encuentra tu versión de Chrome** (ej: versión 131)
3. Descarga **win64** (para Windows 64 bits)
4. Se descargará como `chromedriver-win64.zip`

### Paso 3.2: Extraer en la carpeta del proyecto

1. Abre la carpeta descargar
2. **Haz clic derecho** en `chromedriver-win64.zip` → **Extraer aquí**
3. Se creará carpeta `chromedriver-win64\`
4. Dentro encontrarás `chromedriver.exe`
5. **Mueve este archivo** a la raíz de tu proyecto:
   ```
   C:\Users\Jorge\Desktop\selenium-php-proyecto\chromedriver.exe
   ```

Verificación:
```cmd
dir chromedriver.exe
```

Deberías ver listado el archivo.

---

## PARTE 4: Crear el Script PHP

### Paso 4.1: Crear archivo de configuración

Crea archivo `selenium-config.php`:

```php
<?php
/**
 * Configuración de Selenium + PHP
 * 
 * Este archivo contiene la configuración centralizada
 * para las pruebas de automatización
 */

define('CHROMEDRIVER_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'chromedriver.exe');
define('CHROMEDRIVER_PORT', 9515);
define('CHROMEDRIVER_HOST', 'http://127.0.0.1:' . CHROMEDRIVER_PORT);

// Verificar que ChromeDriver existe
if (!file_exists(CHROMEDRIVER_PATH)) {
    die('ERROR: chromedriver.exe no encontrado en: ' . CHROMEDRIVER_PATH . "\n");
}

echo "✓ ChromeDriver encontrado en: " . CHROMEDRIVER_PATH . "\n";
?>
```

### Paso 4.2: Crear script principal

Crea archivo `wikipedia-titulo.php`:

```php
<?php
/**
 * Script de Automatización: Obtener título de Wikipedia
 * 
 * Este script:
 * 1. Inicia ChromeDriver automáticamente
 * 2. Abre Wikipedia en Chrome
 * 3. Extrae el título de la página
 * 4. Cierra navegador y ChromeDriver
 */

require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class WikipediaBot {
    private $driver;
    private $chromeDriverProcess;
    
    public function __construct() {
        echo "\n========== AUTOMATIZACIÓN WIKIPEDIA ==========\n\n";
        $this->iniciarChromeDriver();
        $this->conectarDriver();
    }
    
    /**
     * Inicia ChromeDriver como proceso independiente
     */
    private function iniciarChromeDriver() {
        echo "[1/4] Iniciando ChromeDriver en puerto " . CHROMEDRIVER_PORT . "...\n";
        
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
        
        // Esperar a que ChromeDriver esté listo
        sleep(2);
        
        if (!is_resource($this->chromeDriverProcess)) {
            throw new Exception("No se pudo iniciar ChromeDriver");
        }
        
        echo "   ✓ ChromeDriver iniciado correctamente\n\n";
    }
    
    /**
     * Conecta WebDriver con ChromeDriver
     */
    private function conectarDriver() {
        echo "[2/4] Conectando con Selenium WebDriver...\n";
        
        try {
            $capabilities = DesiredCapabilities::chrome();
            $this->driver = RemoteWebDriver::create(
                CHROMEDRIVER_HOST,
                $capabilities,
                5000 // timeout de 5 segundos
            );
            echo "   ✓ Conexión establecida con éxito\n\n";
        } catch (Exception $e) {
            $this->detenerChromeDriver();
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }
    
    /**
     * Abre Wikipedia y obtiene información
     */
    public function obtenerInfoWikipedia() {
        echo "[3/4] Navegando a Wikipedia...\n";
        
        try {
            // Navegar a Wikipedia
            $this->driver->get('https://es.wikipedia.org/wiki/Wikipedia:Portada');
            
            // Esperar a que la página cargue
            sleep(2);
            
            // Obtener título de la página
            $titulo = $this->driver->getTitle();
            
            echo "   ✓ Página cargada correctamente\n\n";
            
            echo "[4/4] Información obtenida:\n";
            echo "   ═══════════════════════════════════════\n";
            echo "   TÍTULO DE LA PÁGINA:\n";
            echo "   \"" . htmlspecialchars($titulo) . "\"\n";
            echo "   ═══════════════════════════════════════\n\n";
            
            // Información adicional
            $url = $this->driver->getCurrentURL();
            echo "   URL actual: " . htmlspecialchars($url) . "\n";
            
            // Obtener el H1 principal de Wikipedia
            try {
                $h1 = $this->driver->findElement(WebDriverBy::cssSelector('h1.firstHeading'));
                $titulo_contenido = $h1->getText();
                echo "   Encabezado H1: " . htmlspecialchars($titulo_contenido) . "\n";
            } catch (Exception $e) {
                echo "   [No se encontró encabezado H1]\n";
            }
            
            echo "\n";
            
        } catch (Exception $e) {
            echo "   ✗ Error durante la navegación: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }
    
    /**
     * Cierra navegador y ChromeDriver
     */
    public function detener() {
        echo "Finalizando...\n";
        
        if ($this->driver !== null) {
            try {
                $this->driver->quit();
                echo "   ✓ Navegador cerrado\n";
            } catch (Exception $e) {
                echo "   ⚠ Error al cerrar navegador: " . $e->getMessage() . "\n";
            }
        }
        
        $this->detenerChromeDriver();
    }
    
    /**
     * Detiene el proceso de ChromeDriver
     */
    private function detenerChromeDriver() {
        if (is_resource($this->chromeDriverProcess)) {
            proc_terminate($this->chromeDriverProcess);
            proc_close($this->chromeDriverProcess);
            echo "   ✓ ChromeDriver detenido\n";
        }
    }
    
    /**
     * Ejecutor principal
     */
    public function ejecutar() {
        try {
            $this->obtenerInfoWikipedia();
        } finally {
            $this->detener();
            echo "\n========== PROCESO COMPLETADO ==========\n\n";
        }
    }
}

// Ejecutar el bot
try {
    $bot = new WikipediaBot();
    $bot->ejecutar();
} catch (Exception $e) {
    echo "\n✗ ERROR FATAL: " . $e->getMessage() . "\n\n";
    exit(1);
}
?>
```

---

## PARTE 5: Ejecutar el Script

### Paso 5.1: Estructura final de carpetas

Tu proyecto debe verse así:

```
selenium-php-proyecto/
├── vendor/                    (creado por Composer)
├── composer.json
├── composer.lock
├── chromedriver.exe          ← IMPORTANTE
├── selenium-config.php       ← Configuración
└── wikipedia-titulo.php      ← Script principal
```

### Paso 5.2: Ejecutar desde PowerShell

```cmd
php wikipedia-titulo.php
```

### Esperado: Resultado exitoso

```
========== AUTOMATIZACIÓN WIKIPEDIA ==========

[1/4] Iniciando ChromeDriver en puerto 9515...
   ✓ ChromeDriver iniciado correctamente

[2/4] Conectando con Selenium WebDriver...
   ✓ Conexión establecida con éxito

[3/4] Navegando a Wikipedia...
   ✓ Página cargada correctamente

[4/4] Información obtenida:
   ═══════════════════════════════════════
   TÍTULO DE LA PÁGINA:
   "Wikipedia, la enciclopedia libre"
   ═══════════════════════════════════════

   URL actual: https://es.wikipedia.org/wiki/Wikipedia:Portada
   Encabezado H1: Wikipedia

Finalizando...
   ✓ Navegador cerrado
   ✓ ChromeDriver detenido

========== PROCESO COMPLETADO ==========
```

---

## PARTE 6: Solución de Problemas

### Error: "chromedriver.exe no encontrado"

**Causa:** El archivo no está en la carpeta correcta.

**Solución:**
```cmd
# Verifica que exista en la raíz del proyecto
dir chromedriver.exe

# Si no aparece, cópialo manualmente de la carpeta descargas
# a: C:\Users\Jorge\Desktop\selenium-php-proyecto\
```

### Error: "Conexión rechazada" o "Connection refused"

**Causa:** ChromeDriver tarda en iniciar o puerto 9515 está en uso.

**Solución:**
```cmd
# Busca proceso que use puerto 9515
netstat -ano | findstr :9515

# Si hay uno, elimínalo (reemplaza PID):
taskkill /PID <PID> /F

# Intenta de nuevo
php wikipedia-titulo.php
```

### Error: "Version mismatch between Chrome and ChromeDriver"

**Causa:** ChromeDriver es versión diferente a Chrome.

**Solución:**
1. Verifica versión de Chrome nuevamente (Configuración → Acerca de)
2. Descarga ChromeDriver **exacto** de https://developer.chrome.com/docs/chromedriver/downloads
3. Reemplaza el archivo en el proyecto

### Chrome no se abre visualmente

**Comportamiento normal:** Si ChromeDriver funciona, Chrome puede no mostrar ventana visible. Verifica que el script complete sin errores en la terminal.

**Para ver Chrome en pantalla** (modo debug):
Modifica `wikipedia-titulo.php` línea 52:
```php
$capabilities = DesiredCapabilities::chrome();
// Agregar antes de crear driver:
// (código para mostrar ventana)
```

---

## PARTE 7: Próximos Pasos y Expansión

### Crear más scripts de automatización

```php
// ejemplo-generico.php
<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;

$driver = RemoteWebDriver::create(CHROMEDRIVER_HOST, DesiredCapabilities::chrome());

// Tu código aquí
$driver->get('https://ejemplo.com');
$titulo = $driver->getTitle();
echo $titulo;

$driver->quit();
?>
```

### Comandos útiles de WebDriver

```php
// Navegación
$driver->get('https://url.com');                    // Navegar
$driver->navigate()->back();                        // Atrás
$driver->navigate()->forward();                     // Adelante

// Elementos
$elemento = $driver->findElement(WebDriverBy::id('id'));
$elemento = $driver->findElement(WebDriverBy::className('clase'));
$elemento = $driver->findElement(WebDriverBy::cssSelector('.selector'));
$elemento = $driver->findElement(WebDriverBy::xpath('//xpath'));

// Interacción
$elemento->click();                                 // Clic
$elemento->sendKeys('texto');                       // Escribir
$elemento->submit();                                // Enviar formulario
$elemento->getText();                               // Obtener texto

// Esperas
$driver->wait(10)->until(
    WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('id'))
);
```

---

## Documentación Oficial

- **php-webdriver:** https://github.com/php-webdriver/php-webdriver
- **Selenium:** https://www.selenium.dev/
- **ChromeDriver:** https://developer.chrome.com/docs/chromedriver/
- **Wikipedia API (opcional):** https://www.mediawiki.org/wiki/API/en

---

## Resumen Final

✅ Has instalado PHP, Composer y ChromeDriver
✅ Has configurado un proyecto moderno con estructura profesional
✅ Has creado un script automático que navega Wikipedia y extrae datos
✅ Tienes base sólida para crear pruebas y automatizaciones complejas

**Felicidades: ¡Tu entorno Selenium + PHP está listo!**

---

*Guía creada: Diciembre 2025*
*Compatibilidad: Windows 10/11 + Chrome 110+*
