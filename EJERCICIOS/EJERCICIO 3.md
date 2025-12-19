# 📄 Archivos HTML para los Ejercicios

Copia estos archivos HTML en tu carpeta del proyecto para que los ejercicios funcionen correctamente.

---

## 📁 Estructura de carpetas recomendada

```
proyecto-selenium/
├── server.php                    (servidor integrado)
├── ejercicio1.php
├── ejercicio2.php
├── ejercicio3.php
├── ejercicio4.php
├── ejercicio5.php
├── desafio-final.php
├── html/
│   ├── index.html
│   ├── alertas-page.html
│   ├── iframes-page.html
│   ├── nested-iframe.html
│   └── form-inside-iframe.html
├── vendor/
├── selenium-config.php
└── composer.json
```

---

# 🌐 Archivo: html/index.html

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicios: iFrames y Alertas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        a {
            display: block;
            padding: 15px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .info {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 4px;
            margin-top: 30px;
            font-size: 13px;
            color: #333;
        }

        .info strong {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Ejercicios de Selenium</h1>
        <p class="subtitle">iFrames y Alertas con PHP</p>

        <div class="nav">
            <a href="/alertas">📢 Ejercicio 1: Alertas</a>
            <a href="/iframes">📦 Ejercicio 2: iFrames</a>
        </div>

        <div class="info">
            <strong>ℹ️ Información:</strong>
            <p>Selecciona un ejercicio para comenzar. Estos ejercicios te enseñarán cómo manejar alertas e iFrames con Selenium en PHP.</p>
        </div>
    </div>
</body>
</html>
```

---

# 🌐 Archivo: html/alertas-page.html

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 1: Alertas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #999;
            font-size: 14px;
            margin-bottom: 30px;
        }

        section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
        }

        section:last-of-type {
            border-bottom: none;
        }

        h2 {
            font-size: 18px;
            color: #555;
            margin-bottom: 15px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        button {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            flex: 1;
            min-width: 150px;
        }

        .btn-alert {
            background: #ff6b6b;
            color: white;
        }

        .btn-alert:hover {
            background: #ff5252;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }

        .btn-confirm {
            background: #4ecdc4;
            color: white;
        }

        .btn-confirm:hover {
            background: #45b8ae;
            box-shadow: 0 4px 12px rgba(78, 205, 196, 0.3);
        }

        .btn-prompt {
            background: #ffd93d;
            color: #333;
        }

        .btn-prompt:hover {
            background: #ffce1f;
            box-shadow: 0 4px 12px rgba(255, 217, 61, 0.3);
        }

        .status {
            margin-top: 15px;
            padding: 12px;
            background: #f0f4f8;
            border-left: 4px solid #667eea;
            border-radius: 4px;
            font-size: 14px;
            min-height: 20px;
            color: #333;
        }

        .status.success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }

        #result {
            margin-top: 20px;
            padding: 20px;
            background: #e8f4f8;
            border-left: 4px solid #007bff;
            border-radius: 4px;
            display: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .back {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .back:hover {
            background: #5568d3;
            transform: translateX(-3px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📢 Ejercicio 1: Manejo de Alertas</h1>
        <p class="subtitle">Aprende a manejar alert, confirm y prompt con Selenium</p>

        <!-- SECCIÓN 1: Alert Simple -->
        <section>
            <h2>1️⃣ Alert Simple</h2>
            <p style="color: #666; font-size: 14px; margin-bottom: 15px;">
                Un alert es un cuadro de diálogo simple que muestra un mensaje. Solo tiene botón OK.
            </p>
            <div class="button-group">
                <button class="btn-alert" id="alertBtn">Mostrar Alert</button>
            </div>
            <div class="status" id="alertStatus"></div>
        </section>

        <!-- SECCIÓN 2: Confirm -->
        <section>
            <h2>2️⃣ Confirm (Sí/No)</h2>
            <p style="color: #666; font-size: 14px; margin-bottom: 15px;">
                Un confirm pide confirmación al usuario. Devuelve true (OK) o false (Cancelar).
            </p>
            <div class="button-group">
                <button class="btn-confirm" id="confirmYesBtn">Confirmar (SÍ)</button>
                <button class="btn-confirm" id="confirmNoBtn">Confirmar (NO)</button>
            </div>
            <div class="status" id="confirmStatus"></div>
        </section>

        <!-- SECCIÓN 3: Prompt -->
        <section>
            <h2>3️⃣ Prompt (Entrada de Texto)</h2>
            <p style="color: #666; font-size: 14px; margin-bottom: 15px;">
                Un prompt solicita entrada de texto al usuario. Devuelve el texto o null si cancela.
            </p>
            <div class="button-group">
                <button class="btn-prompt" id="promptBtn">Solicitar Entrada</button>
            </div>
            <div class="status" id="promptStatus"></div>
        </section>

        <!-- Resultado Final -->
        <div id="result">
            <strong>✅ Resultado del Prompt:</strong>
            <p style="margin-top: 10px;"></p>
        </div>

        <a href="/" class="back">← Volver al inicio</a>
    </div>

    <script>
        // ================================================
        // ALERT SIMPLE
        // ================================================
        document.getElementById('alertBtn').addEventListener('click', function() {
            alert('Esta es una alerta simple. ¡Haz clic en OK!');
            document.getElementById('alertStatus').textContent = '✓ Alert completado';
            document.getElementById('alertStatus').classList.add('success');
        });

        // ================================================
        // CONFIRM - SÍ (OK)
        // ================================================
        document.getElementById('confirmYesBtn').addEventListener('click', function() {
            let result = confirm('¿Estás seguro? (haz clic en OK para confirmar)');
            let status = document.getElementById('confirmStatus');
            if (result) {
                status.textContent = '✓ Resultado: Confirmado (OK)';
                status.classList.add('success');
            } else {
                status.textContent = '✗ Resultado: Cancelado (NO)';
                status.classList.remove('success');
            }
        });

        // ================================================
        // CONFIRM - NO (CANCELAR)
        // ================================================
        document.getElementById('confirmNoBtn').addEventListener('click', function() {
            let result = confirm('¿Estás seguro? (haz clic en Cancelar)');
            let status = document.getElementById('confirmStatus');
            if (result) {
                status.textContent = '✓ Resultado: Confirmado (OK)';
                status.classList.add('success');
            } else {
                status.textContent = '✓ Resultado: Cancelado (NO)';
                status.classList.add('success');
            }
        });

        // ================================================
        // PROMPT - ENTRADA DE TEXTO
        // ================================================
        document.getElementById('promptBtn').addEventListener('click', function() {
            let userInput = prompt('Ingresa tu nombre:', 'Anónimo');
            if (userInput !== null) {
                document.getElementById('promptStatus').textContent = '✓ Entrada recibida: ' + userInput;
                document.getElementById('promptStatus').classList.add('success');
                document.getElementById('result').style.display = 'block';
                document.getElementById('result').innerHTML = '<strong>✅ Datos ingresados:</strong><p>Nombre: <strong>' + userInput + '</strong></p>';
            }
        });
    </script>
</body>
</html>
```

---

# 🌐 Archivo: html/iframes-page.html

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 2: iFrames</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #999;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .iframe-section {
            margin: 30px 0;
            padding: 20px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .iframe-section h2 {
            font-size: 18px;
            color: #555;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .iframe-section p {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        iframe {
            width: 100%;
            height: 300px;
            border: 2px solid #667eea;
            border-radius: 6px;
            background: white;
        }

        .back {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .back:hover {
            background: #5568d3;
            transform: translateX(-3px);
        }

        .badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📦 Ejercicio 2: Trabajando con iFrames</h1>
        <p class="subtitle">Aprende a cambiar de contexto y acceder a elementos dentro de iFrames</p>

        <!-- iFrame 1: Contenido Estático -->
        <div class="iframe-section">
            <h2>
                1️⃣ iFrame Simple
                <span class="badge">Básico</span>
            </h2>
            <p>Este iFrame contiene un botón que puedes clickear. Haz clic en él desde tu script de Selenium.</p>
            <iframe id="iframe1" src="/nested-iframe"></iframe>
        </div>

        <!-- iFrame 2: Formulario -->
        <div class="iframe-section">
            <h2>
                2️⃣ Formulario en iFrame
                <span class="badge">Intermedio</span>
            </h2>
            <p>Este iFrame contiene un formulario. Tu script debe llenar los campos y enviar el formulario.</p>
            <iframe id="iframe2" src="/form-iframe"></iframe>
        </div>

        <a href="/" class="back">← Volver al inicio</a>
    </div>
</body>
</html>
```

---

# 🌐 Archivo: html/nested-iframe.html

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contenido del iFrame 1</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, #e0f4ff 0%, #e8f9ff 100%);
            min-height: 100vh;
        }

        .content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #007bff;
            margin-bottom: 10px;
            font-size: 20px;
        }

        p {
            color: #555;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        button {
            padding: 12px 24px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        #message {
            color: #28a745;
            font-weight: bold;
            margin-top: 15px;
            padding: 10px;
            background: #d4edda;
            border-left: 4px solid #28a745;
            border-radius: 4px;
            min-height: 20px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>✅ Contenido dentro del iFrame 1</h2>
        <p>Este contenido está renderizado dentro de un iFrame. Cada iFrame tiene su propio DOM separado del documento principal.</p>

        <button id="iframeBtn">🎯 Haz clic aquí</button>

        <div id="message"></div>
    </div>

    <script>
        document.getElementById('iframeBtn').addEventListener('click', function() {
            document.getElementById('message').textContent = '✓ Botón del iFrame fue clickeado';
            document.getElementById('message').style.display = 'block';
        });
    </script>
</body>
</html>
```

---

# 🌐 Archivo: html/form-inside-iframe.html

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario en iFrame</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, #fff3cd 0%, #fffaeb 100%);
            min-height: 100vh;
        }

        .content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #856404;
            margin-bottom: 10px;
            font-size: 20px;
        }

        p {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            font-size: 14px;
        }

        input,
        textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #ffc107;
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button {
            padding: 12px 20px;
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            color: #333;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        #formResult {
            margin-top: 15px;
            padding: 15px;
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
            border-radius: 4px;
            display: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-item {
            margin: 8px 0;
            font-size: 14px;
        }

        .result-item strong {
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>📝 Formulario dentro del iFrame</h2>
        <p>Llena este formulario. Cuando hagas clic en enviar, verás los datos aquí mismo dentro del iFrame.</p>

        <form id="iframeForm">
            <div class="form-group">
                <label for="iframeName">Nombre:</label>
                <input type="text" id="iframeName" placeholder="Tu nombre completo" required>
            </div>

            <div class="form-group">
                <label for="iframeMessage">Mensaje:</label>
                <textarea id="iframeMessage" placeholder="Escribe tu mensaje aquí" required></textarea>
            </div>

            <button type="submit">✉️ Enviar desde iFrame</button>
        </form>

        <div id="formResult">
            <div class="result-item"><strong>✅ Nombre:</strong> <span id="resultName"></span></div>
            <div class="result-item"><strong>📝 Mensaje:</strong> <span id="resultMessage"></span></div>
        </div>
    </div>

    <script>
        document.getElementById('iframeForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let name = document.getElementById('iframeName').value;
            let msg = document.getElementById('iframeMessage').value;

            document.getElementById('resultName').textContent = name;
            document.getElementById('resultMessage').textContent = msg;
            document.getElementById('formResult').style.display = 'block';
        });
    </script>
</body>
</html>
```

---

# 🚀 server.php (Servidor Integrado)

```php
<?php
/**
 * Servidor PHP Integrado para Ejercicios
 * Ejecutar: php server.php
 * Acceder: http://localhost:8000
 */

// Configuración
define('HOST', 'localhost');
define('PORT', 8000);

// Rutas disponibles
$routes = [
    '/'                 => 'html/index.html',
    '/alertas'          => 'html/alertas-page.html',
    '/iframes'          => 'html/iframes-page.html',
    '/nested-iframe'    => 'html/nested-iframe.html',
    '/form-iframe'      => 'html/form-inside-iframe.html',
];

// Obtener la ruta solicitada
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Buscar el archivo correspondiente
$file = isset($routes[$request_uri]) ? $routes[$request_uri] : null;

// Servir el archivo
if ($file && file_exists($file)) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
        header('Content-Type: text/html; charset=utf-8');
    }
    readfile($file);
} else {
    http_response_code(404);
    echo "404 - Página no encontrada: " . htmlspecialchars($request_uri);
}
?>
```

---

# Ejercicios Prácticos: iFrames y Alertas con Selenium PHP

## Objetivo
Escribir código funcional en PHP con Selenium para manejar alertas e iFrames. Cada ejercicio proporciona un enunciado y un template incompleto que debes completar.

---

# EJERCICIO 1: Manejo Básico de Alertas

## Enunciado
Crea un script que:
1. Navegue a `http://localhost:8000/alertas`
2. Haga clic en el botón de alert simple
3. Capture el texto del alert
4. Acepte el alert
5. Verifique que el estado se actualizo

## Template a Completar

```php
<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Ejercicio1_AlertaSimple
{
    private $driver;

    public function __construct()
    {
        echo "===== EJERCICIO 1: Alert Simple =====\n\n";
        $this->conectarDriver();
    }

    private function conectarDriver()
    {
        // TODO: Crear la conexión RemoteWebDriver
        // Usar DesiredCapabilities::chrome()
        // Conectar a CHROMEDRIVER_HOST
        
        echo "✓ Conexión establecida\n\n";
    }

    public function ejecutar()
    {
        try {
            // PASO 1: Navegar a la página de alertas
            // TODO: Usar $this->driver->get() con la URL base + "/alertas"
            echo "PASO 1: Navegando a página de alertas...\n";

            // PASO 2: Buscar y clickear el botón de alert
            // TODO: Usar findElement(WebDriverBy::id("alertBtn"))
            // TODO: Llamar a click()
            echo "PASO 2: Haciendo clic en el botón...\n";

            // PASO 3: Esperar a que aparezca la alerta
            // TODO: Crear WebDriverWait con 5 segundos
            // TODO: Usar until(WebDriverExpectedCondition::alertIsPresent())
            echo "PASO 3: Esperando alerta...\n";

            // PASO 4: Cambiar al contexto de la alerta
            // TODO: Usar $this->driver->switchTo()->alert()
            echo "PASO 4: Obteniendo alerta...\n";

            // PASO 5: Capturar el texto
            // TODO: Usar $alert->getText()
            // TODO: Mostrar en pantalla con echo
            echo "PASO 5: Texto capturado\n";

            // PASO 6: Aceptar la alerta
            // TODO: Usar $alert->accept()
            echo "PASO 6: Alerta aceptada\n\n";

            // PASO 7: Verificar que el estado se actualizó
            // TODO: Buscar elemento con id "alertStatus"
            // TODO: Obtener texto del elemento
            // TODO: Verificar que contiene "✓" usando strpos()
            echo "PASO 7: Estado verificado\n\n";

            echo "🎉 ¡EJERCICIO 1 COMPLETADO!\n\n";

        } catch (\Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n";
        } finally {
            if ($this->driver !== null) {
                $this->driver->quit();
            }
        }
    }
}

try {
    $script = new Ejercicio1_AlertaSimple();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
```

## Pistas
- `RemoteWebDriver::create(url, capabilities, timeout)`
- `WebDriverWait` necesita `$driver` y segundos como parámetros
- `switchTo()->alert()` devuelve un objeto `WebDriverAlert`
- `getText()` en un elemento devuelve string

## Resultado Esperado
```
===== EJERCICIO 1: Alert Simple =====

PASO 1: Navegando a página de alertas...
PASO 2: Haciendo clic en el botón...
PASO 3: Esperando alerta...
PASO 4: Obteniendo alerta...
PASO 5: Texto capturado
Texto del alert: Esta es una alerta simple. ¡Haz clic en OK!
PASO 6: Alerta aceptada
PASO 7: Estado verificado

🎉 ¡EJERCICIO 1 COMPLETADO!
```

---

# EJERCICIO 2: Confirm con Decisiones

## Enunciado
Crea un script que:
1. Navegue a `http://localhost:8000/alertas`
2. Haga clic en botón "Confirmar (Sí)"
3. Acepte el confirm
4. Verifique el resultado dice "Confirmado"
5. Haga clic en botón "Confirmar (No)"
6. Cancele el confirm (dismiss)
7. Verifique el resultado dice "Cancelado"

## Template a Completar

```php
<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Ejercicio2_Confirm
{
    private $driver;

    public function __construct()
    {
        echo "===== EJERCICIO 2: Confirm (Sí/No) =====\n\n";
        $this->conectarDriver();
    }

    private function conectarDriver()
    {
        echo "[1/2] Conectando driver...\n";
        
        // TODO: Crear conexión RemoteWebDriver igual que en Ejercicio 1
        
        echo "   ✓ Conexión establecida\n\n";
    }

    public function ejecutar()
    {
        try {
            // TODO: Navegar a /alertas
            echo "[2/2] Ejecutando pruebas...\n\n";

            // ============================================
            // PARTE 1: Confirm - ACEPTAR (SÍ)
            // ============================================
            echo "PARTE 1: Confirm - Aceptar\n";
            echo "---------------------\n";

            // TODO: Buscar botón con id "confirmYesBtn"
            // TODO: Hacer clic
            echo "   1. Botón clickeado\n";

            // TODO: Esperar alerta
            echo "   2. Esperando alerta\n";

            // TODO: Cambiar a alerta y obtener texto
            echo "   3. Alerta obtenida\n";

            // TODO: Usar accept() en la alerta
            echo "   4. Alerta aceptada\n";

            // TODO: Esperar un poco (sleep(1))
            sleep(1);

            // TODO: Buscar elemento "confirmStatus"
            // TODO: Obtener su texto
            // TODO: Verificar que contiene "Confirmado"
            // TODO: Si no contiene, lanzar excepción
            echo "   ✓ Resultado verificado: CONFIRMADO\n\n";

            // ============================================
            // PARTE 2: Confirm - CANCELAR (NO)
            // ============================================
            echo "PARTE 2: Confirm - Cancelar\n";
            echo "-------------------\n";

            // TODO: Buscar botón con id "confirmNoBtn"
            // TODO: Hacer clic
            echo "   1. Botón clickeado\n";

            // TODO: Esperar alerta
            echo "   2. Esperando alerta\n";

            // TODO: Cambiar a alerta
            echo "   3. Alerta obtenida\n";

            // TODO: Usar dismiss() (no accept())
            echo "   4. Alerta cancelada\n";

            // TODO: Esperar un poco
            sleep(1);

            // TODO: Buscar elemento "confirmStatus"
            // TODO: Obtener texto y verificar que contiene "Cancelado"
            // TODO: Si no contiene, lanzar excepción
            echo "   ✓ Resultado verificado: CANCELADO\n\n";

            echo "🎉 ¡EJERCICIO 2 COMPLETADO!\n\n";

        } catch (\Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
        } finally {
            if ($this->driver !== null) {
                $this->driver->quit();
            }
        }
    }
}

try {
    $script = new Ejercicio2_Confirm();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
```

## Pistas
- `accept()` = hacer clic en OK
- `dismiss()` = hacer clic en Cancelar
- `strpos($texto, "palabra")` devuelve posición o FALSE
- Verificación: `if (strpos($status, "Confirmado") === false) { throw... }`

## Resultado Esperado
```
===== EJERCICIO 2: Confirm (Sí/No) =====

[1/2] Conectando driver...
   ✓ Conexión establecida

[2/2] Ejecutando pruebas...

PARTE 1: Confirm - Aceptar
---------------------
   1. Botón clickeado
   2. Esperando alerta
   3. Alerta obtenida
   4. Alerta aceptada
   ✓ Resultado verificado: CONFIRMADO

PARTE 2: Confirm - Cancelar
-------------------
   1. Botón clickeado
   2. Esperando alerta
   3. Alerta obtenida
   4. Alerta cancelada
   ✓ Resultado verificado: CANCELADO

🎉 ¡EJERCICIO 2 COMPLETADO!
```

---

# EJERCICIO 3: Prompt - Entrada de Texto

## Enunciado
Crea un script que:
1. Navegue a `http://localhost:8000/alertas`
2. Haga clic en botón "Solicitar Entrada"
3. Escriba "María González" en el prompt
4. Acepte el prompt
5. Verifique que el texto aparece en la página

## Template a Completar

```php
<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Ejercicio3_Prompt
{
    private $driver;
    private $baseUrl = "http://localhost:8000";

    public function __construct()
    {
        echo "===== EJERCICIO 3: Prompt (Entrada de Texto) =====\n\n";
        $this->conectarDriver();
    }

    private function conectarDriver()
    {
        // TODO: Conectar driver (igual que ejercicios anteriores)
        echo "✓ Conexión establecida\n\n";
    }

    public function ejecutar()
    {
        try {
            // TODO: Navegar a /alertas
            echo "Paso 1: Navegando...\n";

            // TODO: Buscar botón con id "promptBtn"
            // TODO: Hacer clic
            echo "Paso 2: Botón clickeado\n";

            // TODO: Crear WebDriverWait y esperar alert
            echo "Paso 3: Esperando alerta\n";

            // TODO: Obtener alerta con switchTo()->alert()
            echo "Paso 4: Alerta obtenida\n";

            // TODO: Definir el texto a enviar
            $textoAEnviar = "María González";

            // TODO: Usar sendKeys() para escribir en el prompt
            // TODO: Usar accept() para enviar
            echo "Paso 5: Texto enviado: '$textoAEnviar'\n";

            // TODO: Esperar a que el resultado sea visible
            // Pista: usar visibilityOfElementLocated con id "result"
            echo "Paso 6: Esperando resultado...\n";

            // TODO: Buscar elemento con id "result"
            // TODO: Obtener su texto
            // TODO: Verificar que contiene "María González"
            // TODO: Si no contiene, lanzar excepción
            echo "Paso 7: Resultado verificado\n\n";

            echo "🎉 ¡EJERCICIO 3 COMPLETADO!\n\n";

        } catch (\Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n";
        } finally {
            if ($this->driver !== null) {
                $this->driver->quit();
            }
        }
    }
}

try {
    $script = new Ejercicio3_Prompt();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
```

## Pistas
- `$alert->sendKeys("texto")` escribe en el prompt
- `WebDriverExpectedCondition::visibilityOfElementLocated()` espera a que un elemento sea visible
- El div con id "result" comienza con `display: none` en CSS

## Resultado Esperado
```
===== EJERCICIO 3: Prompt (Entrada de Texto) =====

✓ Conexión establecida

Paso 1: Navegando...
Paso 2: Botón clickeado
Paso 3: Esperando alerta
Paso 4: Alerta obtenida
Paso 5: Texto enviado: 'María González'
Paso 6: Esperando resultado...
Paso 7: Resultado verificado

🎉 ¡EJERCICIO 3 COMPLETADO!
```

---

# EJERCICIO 4: iFrame Básico

## Enunciado
Crea un script que:
1. Navegue a `http://localhost:8000/iframes`
2. Cambie al contexto del primer iFrame (id="iframe1")
3. Busque el botón dentro del iFrame
4. Haga clic en el botón
5. Verifique que el mensaje cambió
6. Vuelva al contexto principal

## Template a Completar

```php
<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Ejercicio4_IFrameBasico
{
    private $driver;
    private $baseUrl = "http://localhost:8000";

    public function __construct()
    {
        echo "===== EJERCICIO 4: iFrame Básico =====\n\n";
        $this->conectarDriver();
    }

    private function conectarDriver()
    {
        // TODO: Conectar driver
        echo "✓ Conexión establecida\n\n";
    }

    public function ejecutar()
    {
        try {
            // PASO 1: Navegar a /iframes
            // TODO: Usar $this->driver->get()
            echo "PASO 1: Navegando a página de iFrames...\n";

            // PASO 2: Cambiar al contexto del iFrame
            // TODO: Usar $this->driver->switchTo()->frame("iframe1")
            // Pista: frame() recibe el ID como string
            echo "PASO 2: Cambiado al contexto del iFrame 1\n";

            // PASO 3: Buscar el botón dentro del iFrame
            // TODO: Buscar elemento con id "iframeBtn"
            // Nota: Los selectores funcionan solo dentro del iFrame actual
            echo "PASO 3: Botón encontrado\n";

            // PASO 4: Hacer clic en el botón
            // TODO: Llamar a click()
            echo "PASO 4: Botón clickeado\n";

            // PASO 5: Verificar que el mensaje cambió
            // TODO: Buscar elemento con id "message"
            // TODO: Obtener su texto
            // TODO: Verificar que contiene "clickeado"
            echo "PASO 5: Mensaje verificado\n";

            // PASO 6: Volver al contexto principal
            // TODO: Usar $this->driver->switchTo()->defaultContent()
            echo "PASO 6: Vuelto al contexto principal\n\n";

            echo "🎉 ¡EJERCICIO 4 COMPLETADO!\n\n";

        } catch (\Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
        } finally {
            if ($this->driver !== null) {
                $this->driver->quit();
            }
        }
    }
}

try {
    $script = new Ejercicio4_IFrameBasico();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
```

## Pistas
- Después de `switchTo()->frame("id")`, los selectores funcionan dentro del iFrame
- `defaultContent()` sin parámetros vuelve al documento principal
- Los elementos dentro de iFrame no existen en el contexto principal

## Resultado Esperado
```
===== EJERCICIO 4: iFrame Básico =====

✓ Conexión establecida

PASO 1: Navegando a página de iFrames...
PASO 2: Cambiado al contexto del iFrame 1
PASO 3: Botón encontrado
PASO 4: Botón clickeado
PASO 5: Mensaje verificado
PASO 6: Vuelto al contexto principal

🎉 ¡EJERCICIO 4 COMPLETADO!
```

---

# EJERCICIO 5: Formulario en iFrame

## Enunciado
Crea un script que:
1. Navegue a `http://localhost:8000/iframes`
2. Cambie al contexto del segundo iFrame (id="iframe2")
3. Llene el input "iframeName" con "Pedro López"
4. Llene el textarea "iframeMessage" con "Automatización en iFrame"
5. Haga clic en el botón submit
6. Espere a que el resultado sea visible
7. Verifique que el resultado contiene el nombre y mensaje
8. Vuelva al contexto principal

## Template a Completar

```php
<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Ejercicio5_FormularioEnIFrame
{
    private $driver;
    private $baseUrl = "http://localhost:8000";

    public function __construct()
    {
        echo "===== EJERCICIO 5: Formulario en iFrame =====\n\n";
        $this->conectarDriver();
    }

    private function conectarDriver()
    {
        // TODO: Conectar driver
        echo "✓ Conexión establecida\n\n";
    }

    public function ejecutar()
    {
        try {
            // TODO: Navegar a /iframes
            echo "Paso 1: Navegando a página de iFrames...\n";

            // TODO: Cambiar al contexto del iFrame 2
            // Pista: frame("iframe2")
            echo "Paso 2: Cambiado al contexto del iFrame 2\n";

            // TODO: Buscar input con id "iframeName"
            // TODO: Escribir "Pedro López" con sendKeys()
            echo "Paso 3: Nombre ingresado\n";

            // TODO: Buscar textarea con id "iframeMessage"
            // TODO: Escribir "Automatización en iFrame"
            echo "Paso 4: Mensaje ingresado\n";

            // TODO: Buscar el botón dentro del formulario
            // Pista: puedes usar findElement(WebDriverBy::tagName("button"))
            // TODO: Hacer clic
            echo "Paso 5: Formulario enviado\n";

            // TODO: Esperar a que el elemento "formResult" sea visible
            // Pista: visibilityOfElementLocated(WebDriverBy::id("formResult"))
            echo "Paso 6: Esperando resultado...\n";

            // TODO: Buscar elemento "formResult"
            // TODO: Obtener su texto
            // TODO: Verificar que contiene "Pedro López"
            // TODO: Verificar que contiene "Automatización en iFrame"
            // TODO: Si falta algo, lanzar excepción
            echo "Paso 7: Resultado verificado\n";

            // TODO: Volver al contexto principal
            echo "Paso 8: Vuelto al contexto principal\n\n";

            echo "🎉 ¡EJERCICIO 5 COMPLETADO!\n\n";

        } catch (\Exception $e) {
            echo "✗ ERROR: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
        } finally {
            if ($this->driver !== null) {
                $this->driver->quit();
            }
        }
    }
}

try {
    $script = new Ejercicio5_FormularioEnIFrame();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
```

## Pistas
- Los inputs dentro de iFrame se buscan igual que en el DOM principal
- Debes estar en el contexto del iFrame para encontrar elementos
- El resultado aparece solo después de hacer clic en submit
- Usa dos verificaciones con `strpos()`: una para nombre, otra para mensaje

## Resultado Esperado
```
===== EJERCICIO 5: Formulario en iFrame =====

✓ Conexión establecida

Paso 1: Navegando a página de iFrames...
Paso 2: Cambiado al contexto del iFrame 2
Paso 3: Nombre ingresado
Paso 4: Mensaje ingresado
Paso 5: Formulario enviado
Paso 6: Esperando resultado...
Paso 7: Resultado verificado
Paso 8: Vuelto al contexto principal

🎉 ¡EJERCICIO 5 COMPLETADO!
```

---
