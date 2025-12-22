# Proyecto de Automatización con Selenium + PHP

Este proyecto contiene una colección completa de ejemplos, ejercicios y laboratorios para aprender automatización web con Selenium WebDriver y PHP.

## 📁 Estructura del Proyecto

```
RECURSOS/
│
├── 📂 config/              # Archivos de configuración
│   └── selenium-config.php  # Configuración centralizada del proyecto
│
├── 📂 drivers/             # WebDrivers para navegadores
│   └── chromedriver.exe     # ChromeDriver para Chrome
│
├── 📂 examples/            # Ejemplos numerados (1-28)
│   ├── 1.CrearInstanciayObtenerTitulo.php
│   ├── 2.TestConAnotaciones.php
│   ├── 3-7.Buscar*.php      # Búsqueda de elementos (ID, Link, Tag, XPath)
│   ├── 8-12.*.php          # Esperas (Implicit, Fluent, Expected Conditions)
│   ├── 13-16.*.php         # Tests de condiciones (Presence, Clickable, Alerts)
│   ├── 17-18.*.php         # Acciones (Click, MoveToElement)
│   ├── 19-20.Iframes*.php  # Manejo de iframes
│   ├── 21.WebTables.php    # Manejo de tablas
│   ├── 22.Acciones.php     # Acciones complejas
│   ├── 23.Ventanas.php     # Manejo de ventanas
│   └── 24-28.*.php         # Componentes UI (Accordion, Tabs, Sortable, etc.)
│
├── 📂 exercises/           # Ejercicios prácticos
│   ├── Ejercicio 1.php
│   ├── Ejercicio 3.php
│   ├── Ejercicio 4.php
│   ├── Ejercicio 5.php
│   └── Ejercicio 6.php
│
├── 📂 labs/                # Laboratorios completos
│   ├── Laboratorio 5/
│   │   ├── lab5.php
│   │   ├── pages/          # Páginas HTML de prueba del lab
│   │   └── src/            # Código fuente del lab
│   │       ├── Helpers/
│   │       ├── Server/
│   │       └── Tests/
│
├── 📂 pom-examples/        # Ejemplos de Page Object Model
│   ├── POMExample/
│   │   ├── Pages/          # Clases Page Object
│   │   │   ├── LoginPage.php
│   │   │   └── ProductsPage.php
│   │   └── Tests/
│   │       ├── ParentTest.php
│   │       └── Login/
│   │           ├── LoginWithOutPOMTest.php
│   │           └── LoginWithPOMTest.php
│   │
│   └── POMEjercicio/
│       └── Pages/
│           ├── Guru99HomePage.php
│           └── Guru99Login.php
│
├── 📂 test-pages/          # Páginas HTML para testing
│   ├── index.html
│   ├── index_completo.html
│   ├── form-inside-iframe.html
│   ├── iframes-page.html
│   ├── nested-iframe.html
│   ├── alertas-page.html
│   ├── sales-table.html
│   ├── tabs-accordion-test.html
│   ├── sortable-test.html
│   ├── selectable-test.html
│   ├── resizable-test.html
│   └── otros archivos HTML
│
├── 📂 src/                 # Código fuente base
│   └── BaseTest.php         # Clase base para tests
│
├── 📂 assets/              # Recursos adicionales
│   ├── resizable_final.png
│   └── sortable_inicial.png
│
├── 📂 vendor/              # Dependencias de Composer
│   └── ...
│
├── composer.json           # Configuración de dependencias
├── composer.lock
└── README.md              # Este archivo

```

## 🚀 Requisitos Previos

- **PHP** >= 7.4
- **Composer** instalado
- **ChromeDriver** (incluido en `drivers/`)
- **Google Chrome** instalado

## 📦 Instalación

1. Instalar las dependencias con Composer:
```bash
composer install
```

2. Verificar que ChromeDriver esté en la carpeta `drivers/`

## 🎯 Cómo Usar Este Proyecto

### 1. Ejemplos Básicos (Carpeta `examples/`)

Los ejemplos están numerados del 1 al 28 y cubren temas progresivos:

**Fundamentos (1-7):**
```bash
php examples/1.CrearInstanciayObtenerTitulo.php  # Primer contacto con Selenium
php examples/3.BuscarId.php                       # Búsqueda por ID
php examples/7.RellenarFormulario.php             # Rellenar formularios
```

**Esperas y Sincronización (8-12):**
```bash
php examples/8.DemoErrorNoUsoEsperas.php          # Problema sin esperas
php examples/9.EjemploImplicitWait.php            # Esperas implícitas
php examples/11.FluenttWait.php                   # Esperas fluidas
php examples/12.ExpectedConditions.php            # Condiciones esperadas
```

**Interacciones Avanzadas (13-23):**
```bash
php examples/16.AlertTest.php                     # Manejo de alertas
php examples/19.Iframes.php                       # Trabajo con iframes
php examples/21.WebTables.php                     # Manipulación de tablas
php examples/23.Ventanas.php                      # Manejo de ventanas
```

**Componentes UI (24-28):**
```bash
php examples/24.Accordion.php                     # Accordions
php examples/26.Sortable.php                      # Elementos ordenables
```

### 2. Ejercicios Prácticos (Carpeta `exercises/`)

Ejercicios para practicar lo aprendido:
```bash
php exercises/Ejercicio\ 1.php
php exercises/Ejercicio\ 6.php
```

### 3. Laboratorios (Carpeta `labs/`)

Proyectos más completos con múltiples componentes:
```bash
php labs/Laboratorio\ 5/lab5.php
```

### 4. Page Object Model (Carpeta `pom-examples/`)

Ejemplos de implementación del patrón POM:
```bash
# Sin POM
php pom-examples/POMExample/Tests/Login/LoginWithOutPOMTest.php

# Con POM (recomendado)
php pom-examples/POMExample/Tests/Login/LoginWithPOMTest.php
```

## 🔧 Configuración

El archivo `config/selenium-config.php` contiene la configuración centralizada:

```php
require_once __DIR__ . '/config/selenium-config.php';

// Ahora tienes acceso a:
// - CHROMEDRIVER_PATH: Ruta al driver
// - CHROMEDRIVER_HOST: Host del driver
// - TEST_PAGES_PATH: Ruta a las páginas de prueba
```

## 📚 Temas Cubiertos

### Fundamentos
- ✅ Inicialización de WebDriver
- ✅ Navegación básica
- ✅ Localización de elementos (ID, CSS, XPath, Link Text)
- ✅ Interacción con formularios

### Esperas y Sincronización
- ✅ Implicit Wait
- ✅ Explicit Wait
- ✅ Fluent Wait
- ✅ Expected Conditions

### Interacciones Avanzadas
- ✅ Acciones del ratón (click, hover)
- ✅ Manejo de alertas
- ✅ Trabajo con iframes
- ✅ Múltiples ventanas y pestañas
- ✅ Tablas dinámicas

### Componentes UI
- ✅ Accordions
- ✅ Tabs
- ✅ Drag & Drop
- ✅ Elementos sortables
- ✅ Elementos seleccionables
- ✅ Elementos redimensionables

### Patrones de Diseño
- ✅ Page Object Model (POM)
- ✅ Test base classes
- ✅ Helpers y utilidades

## 🎓 Rutas de Aprendizaje Sugeridas

### Principiante
1. Ejemplos 1-7 (Fundamentos)
2. Ejercicio 1
3. Ejemplo 8 (Problemas sin esperas)
4. Ejemplo 9 (Solución con esperas)

### Intermedio
1. Ejemplos 10-16 (Esperas y condiciones)
2. Ejercicios 3-5
3. Ejemplos 17-23 (Interacciones avanzadas)
4. Laboratorio 5

### Avanzado
1. Ejemplos 24-28 (Componentes UI)
2. Ejercicio 6
3. POM Examples (Patrón Page Object Model)
4. Crear tus propios tests usando POM

## 🐛 Solución de Problemas

### ChromeDriver no encontrado
```bash
# Verificar que existe en drivers/
ls drivers/chromedriver.exe

# Si no existe, descargar de:
# https://chromedriver.chromium.org/
```

### Error de dependencias
```bash
composer update
```

### Puerto ya en uso
Modificar `CHROMEDRIVER_PORT` en `config/selenium-config.php`

## 📖 Recursos Adicionales

- [Documentación PHP WebDriver](https://github.com/php-webdriver/php-webdriver)
- [Selenium Documentation](https://www.selenium.dev/documentation/)
- [Page Object Model Pattern](https://www.selenium.dev/documentation/test_practices/encouraged/page_object_models/)

## 🤝 Contribuciones

Este es un proyecto educativo. Siéntete libre de:
- Agregar nuevos ejemplos
- Mejorar la documentación
- Reportar issues
- Sugerir mejoras

## 📝 Notas

- Todos los ejemplos están en español para facilitar el aprendizaje
- Los archivos HTML de prueba están en `test-pages/`
- La configuración centralizada evita rutas hardcodeadas
- Usa el patrón POM para proyectos reales

---

**Autor:** Curso de Automatización con Selenium + PHP  
**Fecha:** Diciembre 2025
