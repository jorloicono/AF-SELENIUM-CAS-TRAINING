# 🧪 Laboratorios

Esta carpeta contiene laboratorios completos que integran múltiples conceptos y técnicas de automatización web.

## 📋 Lista de Laboratorios

### Laboratorio 5

Laboratorio completo con arquitectura modular que incluye:

**Estructura:**
```
Laboratorio 5/
├── lab5.php              # Script principal del laboratorio
├── pages/                # Páginas HTML de prueba
│   ├── alert-demo.html
│   ├── checkbox-demo.html
│   ├── drag-drop-demo.html
│   ├── dynamic-data-loading-demo.html
│   ├── iframe-demo.html
│   ├── simple-form-demo.html
│   └── table-search-demo.html
└── src/
    ├── Helpers/          # Clases auxiliares
    │   └── WebDriverHelper.php
    ├── Server/           # Servidor de archivos estáticos
    │   └── StaticFileServer.php
    └── Tests/            # Tests del laboratorio
        └── ExpectedConditionsTests.php
```

**Conceptos Cubiertos:**
- ✅ Arquitectura modular de tests
- ✅ Servidor local de archivos HTML
- ✅ Helpers reutilizables
- ✅ Expected Conditions avanzadas
- ✅ Manejo de formularios
- ✅ Interacción con checkboxes
- ✅ Drag & Drop
- ✅ Carga dinámica de datos
- ✅ Iframes
- ✅ Alertas
- ✅ Búsqueda en tablas

## 🚀 Cómo Ejecutar

### Laboratorio 5

```bash
php labs/Laboratorio\ 5/lab5.php
```

Este laboratorio incluye su propio servidor de archivos estáticos, por lo que no necesitas un servidor web externo.

## 🏗️ Arquitectura de los Laboratorios

### Helpers
Clases auxiliares que proporcionan funcionalidad reutilizable:
- Configuración de WebDriver
- Funciones de espera
- Utilidades comunes

### Server
Servidor simple para servir páginas HTML de prueba localmente:
- No requiere Apache/Nginx
- Puerto configurable
- Ideal para desarrollo y testing

### Tests
Clases de test organizadas por funcionalidad:
- Tests de Expected Conditions
- Tests de interacciones
- Tests de componentes UI

### Pages
Páginas HTML específicas para cada tipo de test:
- Formularios simples
- Elementos dinámicos
- Componentes interactivos

## 💡 Patrones y Mejores Prácticas

### 1. Separación de Responsabilidades
```php
// ❌ Malo: Todo en un archivo
$driver = RemoteWebDriver::create(...);
$element = $driver->findElement(...);

// ✅ Bueno: Usando helpers
$helper = new WebDriverHelper();
$driver = $helper->initDriver();
```

### 2. Reutilización de Código
```php
// Los helpers permiten reutilizar lógica común
$helper->waitForElement(By::id('myElement'));
$helper->fillForm($formData);
```

### 3. Tests Independientes
- Cada test debe poder ejecutarse de forma independiente
- No hay dependencias entre tests
- Setup y teardown claros

### 4. Manejo de Recursos
```php
try {
    // Test logic
} finally {
    $driver->quit(); // Siempre cerrar el driver
}
```

## 📚 Conceptos Avanzados

### Expected Conditions
Los laboratorios demuestran el uso de:
- `elementToBeClickable()`
- `presenceOfElementLocated()`
- `visibilityOfElementLocated()`
- `alertIsPresent()`
- `textToBePresentInElement()`

### Servidor Local
El StaticFileServer permite:
- Servir archivos HTML sin Apache/Nginx
- Control total del entorno de test
- Pruebas reproducibles

### Arquitectura Modular
Beneficios:
- Código más mantenible
- Fácil de extender
- Mejor organización
- Reutilización de componentes

## 🎯 Objetivos de Aprendizaje

Al completar estos laboratorios, serás capaz de:

1. **Estructurar proyectos** de automatización de manera profesional
2. **Crear helpers** y utilidades reutilizables
3. **Implementar patrones** de diseño para tests
4. **Manejar recursos** correctamente (drivers, archivos, etc.)
5. **Trabajar con arquitecturas modulares** complejas
6. **Integrar múltiples conceptos** en un solo proyecto

## 🔧 Personalización

### Modificar el Puerto del Servidor
En `StaticFileServer.php`:
```php
$server = new StaticFileServer(8080); // Cambiar puerto
```

### Agregar Nuevas Páginas de Prueba
1. Crear HTML en `pages/`
2. Actualizar las rutas en los tests
3. Agregar tests específicos en `src/Tests/`

### Extender los Helpers
Agregar métodos útiles en `WebDriverHelper.php`:
```php
public function customWait($condition, $timeout = 10) {
    // Tu lógica aquí
}
```

## ❓ Solución de Problemas

### El servidor no inicia
- Verificar que el puerto no esté en uso
- Comprobar permisos de firewall
- Revisar logs de error

### Tests fallan intermitentemente
- Aumentar los timeouts
- Usar esperas más específicas
- Verificar la estabilidad de los elementos

### ChromeDriver no responde
- Verificar versión de Chrome
- Actualizar ChromeDriver
- Revisar que no haya procesos zombies

## 📖 Recursos Adicionales

- **Ejemplos relacionados**: `../examples/`
- **Ejercicios previos**: `../exercises/`
- **POM avanzado**: `../pom-examples/`

---

Los laboratorios representan el nivel más avanzado del curso. ¡Tómate tu tiempo para entenderlos completamente! 🎓
