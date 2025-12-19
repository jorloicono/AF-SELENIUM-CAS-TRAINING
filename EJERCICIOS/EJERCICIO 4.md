# Objetivo General

Aprender a **automatizar la interacción con tablas HTML** usando Selenium PHP. Trabajarás con lectura de datos, búsqueda, conteo, filtrado y cálculos en tablas web.

---

## Contexto

Estás testeando una **aplicación de gestión de ventas** que muestra una tabla con información de transacciones:
- ID de venta
- Nombre del cliente
- Producto
- Cantidad
- Precio unitario
- Total
- Fecha
- Estado

La tabla tiene 10 filas de datos y un campo de búsqueda que filtra por producto.

---

## HTML de la Tabla: `html/sales-table.html`

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ventas - Tabla</title>
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
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #999;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .controls {
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-group {
            flex: 1;
            min-width: 250px;
        }

        .search-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            font-size: 14px;
        }

        #searchInput {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }

        #searchInput:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        thead th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #f9f9f9;
        }

        tbody td {
            padding: 12px 15px;
            font-size: 14px;
            color: #333;
        }

        tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-completado {
            background: #d4edda;
            color: #155724;
        }

        .status-pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .status-cancelado {
            background: #f8d7da;
            color: #721c24;
        }

        .price {
            color: #28a745;
            font-weight: 600;
        }

        .info-box {
            margin-top: 20px;
            padding: 15px;
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            border-radius: 4px;
            font-size: 13px;
            color: #333;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
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

        .row-count {
            margin-top: 15px;
            padding: 10px;
            background: #e8f4f8;
            border-radius: 4px;
            font-size: 14px;
            color: #007bff;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Gestión de Ventas</h1>
        <p class="subtitle">Sistema de control de ventas - Tabla completa de datos</p>

        <!-- Controles de búsqueda -->
        <div class="controls">
            <div class="search-group">
                <label for="searchInput">Buscar por producto:</label>
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Ej: Laptop, Tablet, Mouse..." 
                    autocomplete="off"
                >
            </div>
        </div>

        <!-- Tabla de ventas -->
        <div class="table-wrapper">
            <table id="salesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Las filas se llenarán con JavaScript -->
                </tbody>
            </table>
        </div>

        <div class="row-count">
            Mostrando: <span id="rowCount">0</span> registros
        </div>

        <div class="info-box">
            <strong>ℹ️ Información:</strong>
            <p>Esta tabla contiene datos de ventas completos. Usa el campo de búsqueda para filtrar por producto. Los datos son dinámicos y se filtran automáticamente.</p>
        </div>

        <a href="/" class="back">← Volver al inicio</a>
    </div>

    <script>
        // Datos de ventas
        const ventasData = [
            { id: 1001, cliente: "Juan García", producto: "Laptop", cantidad: 2, precio: 999.99, total: 1999.98, fecha: "2024-01-15", estado: "Completado" },
            { id: 1002, cliente: "Carlos Ruiz", producto: "Mouse", cantidad: 10, precio: 25.50, total: 255.00, fecha: "2024-01-14", estado: "Completado" },
            { id: 1003, cliente: "María López", producto: "Tablet", cantidad: 5, precio: 299.99, total: 1499.95, fecha: "2024-01-17", estado: "Completado" },
            { id: 1004, cliente: "Ana Martínez", producto: "Monitor", cantidad: 3, precio: 349.99, total: 1049.97, fecha: "2024-01-18", estado: "Pendiente" },
            { id: 1005, cliente: "Roberto Díaz", producto: "Laptop", cantidad: 1, precio: 999.99, total: 999.99, fecha: "2024-01-19", estado: "Completado" },
            { id: 1006, cliente: "Sofia Fernández", producto: "Teclado", cantidad: 8, precio: 79.99, total: 639.92, fecha: "2024-01-20", estado: "Completado" },
            { id: 1007, cliente: "Miguel Sánchez", producto: "Webcam", cantidad: 4, precio: 89.99, total: 359.96, fecha: "2024-01-21", estado: "Cancelado" },
            { id: 1008, cliente: "Laura González", producto: "Auriculares", cantidad: 6, precio: 149.99, total: 899.94, fecha: "2024-01-22", estado: "Completado" },
            { id: 1009, cliente: "Fernando López", producto: "Monitor", cantidad: 2, precio: 349.99, total: 699.98, fecha: "2024-01-23", estado: "Pendiente" },
            { id: 1010, cliente: "Patricia Rodríguez", producto: "Cable USB", cantidad: 20, precio: 9.99, total: 199.80, fecha: "2024-01-24", estado: "Cancelado" }
        ];

        function formatearPrecio(numero) {
            return "$" + numero.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function getStatusClass(estado) {
            const estado_lower = estado.toLowerCase();
            if (estado_lower === "completado") return "status-completado";
            if (estado_lower === "pendiente") return "status-pendiente";
            if (estado_lower === "cancelado") return "status-cancelado";
            return "";
        }

        function renderTable(datos) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            datos.forEach(venta => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${venta.id}</strong></td>
                    <td>${venta.cliente}</td>
                    <td>${venta.producto}</td>
                    <td>${venta.cantidad}</td>
                    <td class="price">${formatearPrecio(venta.precio)}</td>
                    <td class="price"><strong>${formatearPrecio(venta.total)}</strong></td>
                    <td>${venta.fecha}</td>
                    <td>
                        <span class="status-badge ${getStatusClass(venta.estado)}">
                            ${venta.estado}
                        </span>
                    </td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('rowCount').textContent = datos.length;
        }

        function filtrarTabla() {
            const searchInput = document.getElementById('searchInput');
            const searchTerm = searchInput.value.toLowerCase().trim();

            if (searchTerm === '') {
                renderTable(ventasData);
            } else {
                const filtered = ventasData.filter(venta => 
                    venta.producto.toLowerCase().includes(searchTerm)
                );
                renderTable(filtered);
            }
        }

        document.getElementById('searchInput').addEventListener('keyup', function() {
            filtrarTabla();
        });

        document.getElementById('searchInput').addEventListener('input', function() {
            filtrarTabla();
        });

        renderTable(ventasData);
    </script>
</body>
</html>
```

---


## Datos de la Tabla

| ID | Cliente | Producto | Cantidad | Precio Unit. | Total | Fecha | Estado |
|---|---|---|---|---|---|---|---|
| 1001 | Juan García | Laptop | 2 | $999.99 | $1,999.98 | 2024-01-15 | Completado |
| 1002 | Carlos Ruiz | Mouse | 10 | $25.50 | $255.00 | 2024-01-14 | Completado |
| 1003 | María López | Tablet | 5 | $299.99 | $1,499.95 | 2024-01-17 | Completado |
| 1004 | Ana Martínez | Monitor | 3 | $349.99 | $1,049.97 | 2024-01-18 | Pendiente |
| 1005 | Roberto Díaz | Laptop | 1 | $999.99 | $999.99 | 2024-01-19 | Completado |
| 1006 | Sofia Fernández | Teclado | 8 | $79.99 | $639.92 | 2024-01-20 | Completado |
| 1007 | Miguel Sánchez | Webcam | 4 | $89.99 | $359.96 | 2024-01-21 | Cancelado |
| 1008 | Laura González | Auriculares | 6 | $149.99 | $899.94 | 2024-01-22 | Completado |
| 1009 | Fernando López | Monitor | 2 | $349.99 | $699.98 | 2024-01-23 | Pendiente |
| 1010 | Patricia Rodríguez | Cable USB | 20 | $9.99 | $199.80 | 2024-01-24 | Cancelado |

**Resumen de datos:**
- Total de registros: 10
- Estados: 6 Completado, 2 Pendiente, 2 Cancelado
- Productos únicos: Laptop (2), Mouse, Tablet, Monitor (2), Teclado, Webcam, Auriculares, Cable USB
- Suma total de ventas: $12,567.43
- Promedio de venta: $1,256.74

---

# EJERCICIO 1: Lectura Básica de Tablas

## Enunciado

Crea un script que:
1. Navegue a `http://localhost:8000/sales-table`
2. Lea la primera fila de la tabla
3. Extraiga todos los valores (ID, Cliente, Producto, Cantidad, Precio, Total, Fecha, Estado)
4. Valide que los datos no estén vacíos
5. Imprima los resultados en consola

## Conceptos Clave

- **`findElements()`** - Devuelve un array de elementos que coinciden con el selector
- **`findElement()`** - Devuelve un solo elemento (error si no existe)
- **`cssSelector("table tbody tr")`** - Selecciona todas las filas del cuerpo de la tabla
- **`getText()`** - Obtiene el texto visible del elemento

## Template

```php
<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Ejercicio1_LecturaBasicaTabla
{
    private $driver;
    private $baseUrl = "http://localhost:8000";

    public function __construct()
    {
        echo "\n===== EJERCICIO 1: Lectura Básica de Tablas =====\n\n";
        $this->conectarDriver();
    }

    private function conectarDriver()
    {
        // TODO: Conectar driver con RemoteWebDriver
        // Pista: DesiredCapabilities::chrome() y RemoteWebDriver::create()
        
        echo "✓ Conexión establecida\n\n";
    }

    public function ejecutar()
    {
        try {
            // PASO 1: Navegar a la página de tabla de ventas
            // TODO: Usar $this->driver->get($this->baseUrl . "/sales-table")
            echo "PASO 1: Navegando a tabla de ventas...\n";

            // PASO 2: Esperar a que la tabla sea visible
            // TODO: Crear WebDriverWait y usar visibilityOfElementLocated
            // TODO: Buscar tabla por id "salesTable"
            // Pista:
            // $wait = new WebDriverWait($this->driver, 10);
            // $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("salesTable")));
            echo "PASO 2: Tabla cargada\n\n";

            // PASO 3: Obtener todas las filas de la tabla
            // TODO: Usar findElements(WebDriverBy::cssSelector("table tbody tr"))
            // TODO: Verificar que hay al menos 1 fila con count()
            // Pista: $rows = $this->driver->findElements(WebDriverBy::cssSelector("table tbody tr"));
            echo "PASO 3: Filas encontradas: " . count($rows) . "\n";

            // PASO 4: Obtener la primera fila
            // TODO: Acceder al índice 0 del array de filas
            $firstRow = $rows[0];
            echo "PASO 4: Primera fila seleccionada\n";

            // PASO 5: Extraer todas las celdas de la primera fila
            // TODO: Usar findElements en $firstRow con cssSelector "td"
            // TODO: Guardar en variable $cells
            // Pista: $cells = $firstRow->findElements(WebDriverBy::cssSelector("td"));
            echo "PASO 5: Celdas extraídas: " . count($cells) . "\n";

            // PASO 6: Obtener el texto de cada celda
            // TODO: Iterar sobre $cells con foreach
            // TODO: Usar $cell->getText() para cada celda
            // TODO: Guardar valores en array $rowData
            // Pista:
            // $rowData = [];
            // foreach ($cells as $cell) {
            //     $rowData[] = $cell->getText();
            // }
            echo "PASO 6: Datos extraídos\n\n";

            // PASO 7: Mostrar los datos
            echo "DATOS DE LA PRIMERA FILA:\n";
            echo "-------------------------\n";
            
            $columnNames = ["ID", "Cliente", "Producto", "Cantidad", "Precio", "Total", "Fecha", "Estado"];

            // TODO: Iterar sobre $rowData y mostrar cada valor
            // Pista: 
            // for ($i = 0; $i < count($rowData); $i++) {
            //     echo $columnNames[$i] . ": " . $rowData[$i] . "\n";
            // }
            
            echo "\n";

            // PASO 8: Validar que los datos no estén vacíos
            // TODO: Iterar sobre $rowData
            // TODO: Si alguno está vacío (empty()), lanzar excepción
            // TODO: Si todo está bien, mostrar "✓ Todos los datos son válidos"
            // Pista:
            // foreach ($rowData as $data) {
            //     if (empty($data)) {
            //         throw new \Exception("Hay datos vacíos en la fila");
            //     }
            // }
            echo "PASO 8: Validación completada\n\n";

            echo "🎉 ¡EJERCICIO 1 COMPLETADO!\n\n";

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
    $script = new Ejercicio1_LecturaBasicaTabla();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
```

## Resultado Esperado

```
===== EJERCICIO 1: Lectura Básica de Tablas =====

✓ Conexión establecida

PASO 1: Navegando a tabla de ventas...
PASO 2: Tabla cargada

PASO 3: Filas encontradas: 10
PASO 4: Primera fila seleccionada
PASO 5: Celdas extraídas: 8
PASO 6: Datos extraídos

DATOS DE LA PRIMERA FILA:
-------------------------
ID: 1001
Cliente: Juan García
Producto: Laptop
Cantidad: 2
Precio: $999.99
Total: $1,999.98
Fecha: 2024-01-15
Estado: Completado

PASO 8: Validación completada

🎉 ¡EJERCICIO 1 COMPLETADO!
```

---

# EJERCICIO 2: Búsqueda en Tabla

## Enunciado

Crea un script que:
1. Navegue a `http://localhost:8000/sales-table`
2. Busque un cliente específico ("María López") en la tabla
3. Si lo encuentra, extraiga toda la fila
4. Valide que el cliente es correcto
5. Imprima los datos de ese cliente

## Conceptos Clave

- **Iteración condicional** - Usar `foreach` para buscar un valor específico
- **Break** - Salir del loop cuando encuentres lo que buscas
- **Comparación de strings** - Usar `===` para comparar exactamente

## Template

```php
<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Ejercicio2_BusquedaEnTabla
{
    private $driver;
    private $baseUrl = "http://localhost:8000";

    public function __construct()
    {
        echo "\n===== EJERCICIO 2: Búsqueda en Tabla =====\n\n";
        $this->conectarDriver();
    }

    private function conectarDriver()
    {
        // TODO: Conectar driver (similar a Ejercicio 1)
        echo "✓ Conexión establecida\n\n";
    }

    public function ejecutar()
    {
        try {
            // TODO: Navegar a /sales-table
            echo "PASO 1: Navegando a tabla de ventas...\n";

            // TODO: Esperar a que tabla sea visible
            echo "PASO 2: Tabla cargada\n\n";

            $clienteBuscado = "María López";
            echo "Buscando cliente: '$clienteBuscado'\n";
            echo "====================================\n\n";

            // PASO 3: Obtener todas las filas
            // TODO: findElements para obtener todas las filas ("table tbody tr")
            echo "PASO 3: Filas cargadas\n";

            // PASO 4: Iterar sobre las filas para buscar el cliente
            // TODO: Usar foreach para iterar sobre $rows
            // TODO: En cada iteración:
            //   - Obtener las celdas con findElements("td")
            //   - Obtener la segunda celda (índice 1) que contiene el cliente
            //   - Usar getText() y comparar con $clienteBuscado
            //   - Si coincide, guardar $filaEncontrada = $row y $indexFila = índice actual
            //   - Usar break; para salir del loop
            // Pista:
            // $filaEncontrada = null;
            // $indexFila = -1;
            // foreach ($rows as $index => $row) {
            //     $cells = $row->findElements(WebDriverBy::cssSelector("td"));
            //     $cliente = $cells[1]->getText();
            //     if ($cliente === $clienteBuscado) {
            //         $filaEncontrada = $row;
            //         $indexFila = $index;
            //         break;
            //     }
            // }
            
            $filaEncontrada = null;
            $indexFila = -1;

            // PASO 5: Verificar si se encontró
            // TODO: Si $filaEncontrada es null, lanzar excepción "Cliente no encontrado"
            echo "PASO 5: Cliente encontrado en fila #" . $indexFila . "\n\n";

            // PASO 6: Extraer datos de la fila encontrada
            // TODO: Obtener todas las celdas de $filaEncontrada
            // TODO: Iterar sobre las celdas y extraer getText()
            // TODO: Guardar en array $datosCliente
            echo "DATOS DEL CLIENTE:\n";
            echo "------------------\n";
            
            $columnNames = ["ID", "Cliente", "Producto", "Cantidad", "Precio", "Total", "Fecha", "Estado"];
            
            // TODO: Mostrar cada dato en formato "Clave: Valor"
            
            echo "\n";

            // PASO 7: Validar que el cliente en posición [1] es correcto
            // TODO: Comparar $datosCliente[1] === $clienteBuscado
            // TODO: Si no coincide, lanzar excepción
            // TODO: Si coincide, mostrar "✓ Cliente verificado"
            echo "PASO 7: Cliente verificado\n\n";

            echo "🎉 ¡EJERCICIO 2 COMPLETADO!\n\n";

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
    $script = new Ejercicio2_BusquedaEnTabla();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
```

## Resultado Esperado

```
===== EJERCICIO 2: Búsqueda en Tabla =====

✓ Conexión establecida

PASO 1: Navegando a tabla de ventas...
PASO 2: Tabla cargada

Buscando cliente: 'María López'
====================================

PASO 3: Filas cargadas
PASO 5: Cliente encontrado en fila #2

DATOS DEL CLIENTE:
------------------
ID: 1003
Cliente: María López
Producto: Tablet
Cantidad: 5
Precio: $299.99
Total: $1,499.95
Fecha: 2024-01-17
Estado: Completado

PASO 7: Cliente verificado

🎉 ¡EJERCICIO 2 COMPLETADO!
```

---

# EJERCICIO 3: Contar Filas y Validar Cantidad

## Enunciado

Crea un script que:
1. Navegue a `http://localhost:8000/sales-table`
2. Cuente el número total de filas en la tabla
3. Valide que hay exactamente 10 filas
4. Cuente filas por estado ("Completado", "Pendiente", "Cancelado")
5. Imprima un resumen con los conteos

## Conceptos Clave

- **`count()`** - Obtiene el número de elementos en un array
- **`array_sum()`** - Suma todos los valores del array
- **Contadores** - Usar un array asociativo para contar por categoría
- **Incremento** - `$contador++` para aumentar el valor en 1

## Template

```php
<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Ejercicio3_ContarFilas
{
    private $driver;
    private $baseUrl = "http://localhost:8000";

    public function __construct()
    {
        echo "\n===== EJERCICIO 3: Contar Filas y Validar =====\n\n";
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
            // TODO: Navegar a /sales-table
            echo "PASO 1: Navegando a tabla de ventas...\n";

            // TODO: Esperar tabla visible
            echo "PASO 2: Tabla cargada\n\n";

            // PASO 3: Contar total de filas
            // TODO: Obtener todas las filas con findElements("table tbody tr")
            // TODO: Contar filas con count()
            // TODO: Guardar en $totalFilas
            echo "PASO 3: Total de filas: " . $totalFilas . "\n";

            // PASO 4: Validar que hay 10 filas
            // TODO: Comparar si $totalFilas === 10
            // TODO: Si no es 10, lanzar excepción
            echo "PASO 4: ✓ Validación de cantidad correcta\n\n";

            // PASO 5: Contar filas por estado
            echo "PASO 5: Contando por estado...\n";
            echo "================================\n";

            // TODO: Crear array para guardar conteos
            $contadores = [
                "Completado" => 0,
                "Pendiente" => 0,
                "Cancelado" => 0
            ];

            // TODO: Iterar sobre todas las filas
            // TODO: En cada fila:
            //   - Obtener las celdas con findElements("td")
            //   - Obtener la última celda (índice 7 = Estado)
            //   - Usar getText() para obtener el estado
            //   - Incrementar el contador correspondiente con $contadores[$estado]++
            // Pista:
            // foreach ($rows as $row) {
            //     $cells = $row->findElements(WebDriverBy::cssSelector("td"));
            //     $estado = $cells[7]->getText();
            //     // Limpiar espacios
            //     $estado = trim($estado);
            //     // Detectar estado
            //     if (strpos($estado, "Completado") !== false) {
            //         $contadores["Completado"]++;
            //     } elseif (strpos($estado, "Pendiente") !== false) {
            //         $contadores["Pendiente"]++;
            //     } elseif (strpos($estado, "Cancelado") !== false) {
            //         $contadores["Cancelado"]++;
            //     }
            // }

            // PASO 6: Mostrar resumen
            echo "\nRESUMEN DE ESTADOS:\n";
            echo "------------------\n";
            
            // TODO: Mostrar cada contador
            echo "Completado: " . $contadores["Completado"] . "\n";
            echo "Pendiente: " . $contadores["Pendiente"] . "\n";
            echo "Cancelado: " . $contadores["Cancelado"] . "\n";
            
            echo "\nTotal: " . array_sum($contadores) . "\n\n";

            // PASO 7: Validar que el total coincide
            // TODO: Comparar array_sum($contadores) === $totalFilas
            // TODO: Si no coincide, lanzar excepción
            echo "PASO 7: ✓ Resumen validado\n\n";

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
    $script = new Ejercicio3_ContarFilas();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
```

## Resultado Esperado

```
===== EJERCICIO 3: Contar Filas y Validar =====

✓ Conexión establecida

PASO 1: Navegando a tabla de ventas...
PASO 2: Tabla cargada

PASO 3: Total de filas: 10
PASO 4: ✓ Validación de cantidad correcta

PASO 5: Contando por estado...
================================

RESUMEN DE ESTADOS:
------------------
Completado: 6
Pendiente: 2
Cancelado: 2

Total: 10

PASO 7: ✓ Resumen validado

🎉 ¡EJERCICIO 3 COMPLETADO!
```

---

# EJERCICIO 4: Filtrado de Tabla

## Enunciado

Crea un script que:
1. Navegue a `http://localhost:8000/sales-table`
2. Use el campo de búsqueda para filtrar por producto "Laptop"
3. Espere a que la tabla se actualice
4. Valide que solo aparecen filas con "Laptop"
5. Cuente cuántas filas quedan después del filtro

## Conceptos Clave

- **`sendKeys()`** - Escribe texto en un input
- **Esperas dinámicas** - Esperar a que el DOM se actualice
- **`strpos()`** - Verifica si un string contiene otro
- **Validación post-filtro** - Verificar que los resultados son correctos

## Template

```php
<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Ejercicio4_FiltradoTabla
{
    private $driver;
    private $baseUrl = "http://localhost:8000";

    public function __construct()
    {
        echo "\n===== EJERCICIO 4: Filtrado de Tabla =====\n\n";
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
            // TODO: Navegar a /sales-table
            echo "PASO 1: Navegando a tabla de ventas...\n";

            // TODO: Esperar tabla visible
            echo "PASO 2: Tabla cargada\n\n";

            // PASO 3: Contar filas ANTES del filtro
            // TODO: Obtener todas las filas con findElements("table tbody tr")
            // TODO: Guardar cantidad en $filasAntes
            // Pista: $filasAntes = count($rows);
            echo "PASO 3: Filas antes del filtro: " . $filasAntes . "\n";

            // PASO 4: Buscar el input de búsqueda
            // TODO: findElement por id "searchInput"
            // Pista: $searchInput = $this->driver->findElement(WebDriverBy::id("searchInput"));
            echo "PASO 4: Campo de búsqueda encontrado\n";

            // PASO 5: Escribir en el campo de búsqueda
            // TODO: Usar sendKeys("Laptop")
            // TODO: Esperar con sleep(1) para que se procese
            // Pista:
            // $searchInput->clear();
            // $searchInput->sendKeys("Laptop");
            // sleep(1);
            echo "PASO 5: Filtro aplicado: 'Laptop'\n";

            // PASO 6: Esperar a que la tabla se actualice
            // TODO: Esperar con sleep(2) para que React/JS actualice la tabla
            echo "PASO 6: Tabla actualizada\n\n";

            // PASO 7: Contar filas DESPUÉS del filtro
            // TODO: Obtener todas las filas nuevamente
            // TODO: Guardar cantidad en $filasDepues
            // Pista: $filasDepues = count($rowsFiltered);
            echo "PASO 7: Filas después del filtro: " . $filasDepues . "\n";

            // PASO 8: Validar que filasDepues < filasAntes
            // TODO: Comparar $filasDepues < $filasAntes
            // TODO: Si no, lanzar excepción
            echo "PASO 8: ✓ Filtro aplicado correctamente\n\n";

            // PASO 9: Validar que solo hay "Laptop" en las filas
            // TODO: Iterar sobre las filas filtradas
            // TODO: En cada fila, obtener la tercera columna (índice 2 = Producto)
            // TODO: Verificar que getText() contiene "Laptop"
            // TODO: Si alguna no contiene "Laptop", lanzar excepción
            // Pista:
            // foreach ($rowsFiltered as $row) {
            //     $cells = $row->findElements(WebDriverBy::cssSelector("td"));
            //     $producto = $cells[2]->getText();
            //     if (strpos($producto, "Laptop") === false) {
            //         throw new \Exception("Fila contiene: $producto (no es Laptop)");
            //     }
            // }
            echo "PASO 9: ✓ Todos los productos son 'Laptop'\n\n";

            // PASO 10: Mostrar resumen
            echo "RESUMEN:\n";
            echo "--------\n";
            echo "Filas iniciales: " . $filasAntes . "\n";
            echo "Filas filtradas: " . $filasDepues . "\n";
            echo "Filas eliminadas: " . ($filasAntes - $filasDepues) . "\n\n";

            echo "🎉 ¡EJERCICIO 4 COMPLETADO!\n\n";

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
    $script = new Ejercicio4_FiltradoTabla();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
```

## Resultado Esperado

```
===== EJERCICIO 4: Filtrado de Tabla =====

✓ Conexión establecida

PASO 1: Navegando a tabla de ventas...
PASO 2: Tabla cargada

PASO 3: Filas antes del filtro: 10
PASO 4: Campo de búsqueda encontrado
PASO 5: Filtro aplicado: 'Laptop'
PASO 6: Tabla actualizada

PASO 7: Filas después del filtro: 2
PASO 8: ✓ Filtro aplicado correctamente

PASO 9: ✓ Todos los productos son 'Laptop'

RESUMEN:
--------
Filas iniciales: 10
Filas filtradas: 2
Filas eliminadas: 8

🎉 ¡EJERCICIO 4 COMPLETADO!
```

---

# EJERCICIO 5: Cálculos en Tabla

## Enunciado

Crea un script que:
1. Navegue a `http://localhost:8000/sales-table`
2. Lea la columna "Total" de todas las filas
3. Calcule la suma total de ventas
4. Calcule el promedio de venta
5. Encuentre la venta máxima y mínima
6. Imprima un reporte completo

## Conceptos Clave

- **`array_sum()`** - Suma todos los valores de un array
- **`max()` y `min()`** - Obtienen valor máximo y mínimo
- **Limpieza de datos** - Remover símbolos de moneda ($, comas)
- **Conversión de tipos** - `(float)` para convertir strings a números
- **Formateo** - `number_format()` para mostrar números con decimales

## Template

```php
<?php
require_once('vendor/autoload.php');
require_once('selenium-config.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Ejercicio5_CalculosEnTabla
{
    private $driver;
    private $baseUrl = "http://localhost:8000";

    public function __construct()
    {
        echo "\n===== EJERCICIO 5: Cálculos en Tabla =====\n\n";
        $this->conectarDriver();
    }

    private function conectarDriver()
    {
        // TODO: Conectar driver
        echo "✓ Conexión establecida\n\n";
    }

    private function limpiarNumero($texto)
    {
        // Función auxiliar para limpiar el formato de moneda
        // Ejemplo: "$1,999.98" => 1999.98
        // TODO: Remover "$" con str_replace()
        // TODO: Remover "," con str_replace()
        // TODO: Convertir a float con (float)
        // TODO: Retornar el número
        // Pista:
        // $numero = str_replace("$", "", $texto);
        // $numero = str_replace(",", "", $numero);
        // return (float)$numero;
    }

    public function ejecutar()
    {
        try {
            // TODO: Navegar a /sales-table
            echo "PASO 1: Navegando a tabla de ventas...\n";

            // TODO: Esperar tabla visible
            echo "PASO 2: Tabla cargada\n\n";

            // PASO 3: Obtener todas las filas
            // TODO: findElements para obtener todas las filas
            echo "PASO 3: Filas obtenidas\n";

            // PASO 4: Crear array para guardar totales
            $totales = [];

            // PASO 5: Iterar sobre filas y extraer columna "Total" (índice 5)
            // TODO: Usar foreach para iterar $rows
            // TODO: En cada fila:
            //   - Obtener celdas con findElements("td")
            //   - Obtener celda índice 5 (Total)
            //   - Usar getText() para extraer el valor
            //   - Guardar en array $totales
            // Pista:
            // foreach ($rows as $row) {
            //     $cells = $row->findElements(WebDriverBy::cssSelector("td"));
            //     $totalText = $cells[5]->getText();
            //     $totales[] = $totalText;
            // }
            echo "PASO 5: Totales extraídos: " . count($totales) . "\n\n";

            // PASO 6: Limpiar y convertir a números
            // TODO: Iterar sobre $totales
            // TODO: Para cada valor, usar $this->limpiarNumero()
            // TODO: Guardar en array $numerosLimpios
            // Pista:
            // $numerosLimpios = [];
            // foreach ($totales as $total) {
            //     $numerosLimpios[] = $this->limpiarNumero($total);
            // }
            echo "PASO 6: Números convertidos\n";

            // PASO 7: Calcular suma total
            // TODO: Usar array_sum($numerosLimpios)
            // TODO: Guardar en $sumaTotal
            // Pista: $sumaTotal = array_sum($numerosLimpios);
            echo "PASO 7: Suma total calculada\n";

            // PASO 8: Calcular promedio
            // TODO: Dividir $sumaTotal entre count($numerosLimpios)
            // TODO: Guardar en $promedio
            // Pista: $promedio = $sumaTotal / count($numerosLimpios);
            echo "PASO 8: Promedio calculado\n";

            // PASO 9: Encontrar máximo y mínimo
            // TODO: Usar max($numerosLimpios)
            // TODO: Usar min($numerosLimpios)
            // Pista:
            // $maximo = max($numerosLimpios);
            // $minimo = min($numerosLimpios);
            echo "PASO 9: Máximo y mínimo encontrados\n\n";

            // PASO 10: Mostrar reporte
            echo "REPORTE DE VENTAS:\n";
            echo "==================\n";
            echo "Total de ventas: " . count($numerosLimpios) . "\n";
            echo "Suma total: $" . number_format($sumaTotal, 2) . "\n";
            echo "Promedio: $" . number_format($promedio, 2) . "\n";
            echo "Venta máxima: $" . number_format($maximo, 2) . "\n";
            echo "Venta mínima: $" . number_format($minimo, 2) . "\n";
            echo "Rango: $" . number_format($maximo - $minimo, 2) . "\n\n";

            // PASO 11: Validaciones
            // TODO: Verificar que $sumaTotal > 0
            // TODO: Verificar que $promedio > 0
            // TODO: Verificar que $maximo >= $promedio
            // TODO: Verificar que $minimo <= $promedio
            // TODO: Si alguna falla, lanzar excepción
            // TODO: Si todo está bien, mostrar "✓ Todas las validaciones correctas"
            // Pista:
            // if ($sumaTotal <= 0 || $promedio <= 0 || $maximo < $promedio || $minimo > $promedio) {
            //     throw new \Exception("Las validaciones no pasaron");
            // }
            echo "PASO 11: ✓ Todas las validaciones correctas\n\n";

            echo "🎉 ¡EJERCICIO 5 COMPLETADO!\n\n";

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
    $script = new Ejercicio5_CalculosEnTabla();
    $script->ejecutar();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
```

## Resultado Esperado

```
===== EJERCICIO 5: Cálculos en Tabla =====

✓ Conexión establecida

PASO 1: Navegando a tabla de ventas...
PASO 2: Tabla cargada

PASO 3: Filas obtenidas
PASO 5: Totales extraídos: 10
PASO 6: Números convertidos
PASO 7: Suma total calculada
PASO 8: Promedio calculado
PASO 9: Máximo y mínimo encontrados

REPORTE DE VENTAS:
==================
Total de ventas: 10
Suma total: $12,567.43
Promedio: $1,256.74
Venta máxima: $1,999.98
Venta mínima: $199.80
Rango: $1,800.18

PASO 11: ✓ Todas las validaciones correctas

🎉 ¡EJERCICIO 5 COMPLETADO!
```

---

## Técnicas de Web Tables Resumen

### Selectores Comunes

```php
// Obtener todas las filas
$rows = $this->driver->findElements(WebDriverBy::cssSelector("table tbody tr"));

// Obtener todas las celdas de una fila
$cells = $row->findElements(WebDriverBy::cssSelector("td"));

// Obtener encabezados
$headers = $this->driver->findElements(WebDriverBy::cssSelector("table thead th"));

// Obtener celda específica por XPath
$cell = $row->findElement(WebDriverBy::xpath(".//td[2]"));

// Por tabla ID
$rows = $this->driver->findElements(WebDriverBy::xpath("//table[@id='salesTable']//tbody//tr"));
```
