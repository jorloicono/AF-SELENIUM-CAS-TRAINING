# 📚 Ejemplos de Selenium + PHP

Esta carpeta contiene 28 ejemplos progresivos que cubren desde los conceptos básicos hasta técnicas avanzadas de automatización web.

## 📋 Índice de Ejemplos

### 🌟 Fundamentos (1-7)

| # | Archivo | Descripción |
|---|---------|-------------|
| 1 | `1.CrearInstanciayObtenerTitulo.php` | Primera instancia de WebDriver y obtención del título de página |
| 2 | `2.TestConAnotaciones.php` | Uso de anotaciones en tests |
| 3 | `3.BuscarId.php` | Localización de elementos por ID |
| 4 | `4.BuscarLink.php` | Localización de elementos por texto de enlace |
| 5 | `5.BuscarTag.php` | Localización de elementos por etiqueta HTML |
| 6 | `6.BuscarXPath.php` | Localización de elementos usando XPath |
| 7 | `7.RellenarFormulario.php` | Rellenado automático de formularios |

### ⏰ Esperas y Sincronización (8-12)

| # | Archivo | Descripción |
|---|---------|-------------|
| 8 | `8.DemoErrorNoUsoEsperas.php` | Demostración de problemas sin usar esperas |
| 9 | `9.EjemploImplicitWait.php` | Implementación de esperas implícitas |
| 10 | `10.EjemploImplicitIneficienteNonClick.php` | Ejemplo de uso ineficiente de esperas |
| 11 | `11.FluenttWait.php` | Esperas fluidas con polling personalizado |
| 12 | `12.ExpectedConditions.php` | Uso de condiciones esperadas |

### ✅ Tests de Condiciones (13-16)

| # | Archivo | Descripción |
|---|---------|-------------|
| 13 | `13.PresenceTest.php` | Test de presencia de elementos |
| 14 | `14.ClickableTest.php` | Test de elementos clickeables |
| 15 | `15.TextToBePresentTest.php` | Test de presencia de texto |
| 16 | `16.AlertTest.php` | Manejo de alertas JavaScript |

### 🖱️ Acciones e Interacciones (17-23)

| # | Archivo | Descripción |
|---|---------|-------------|
| 17 | `17.EjemploAccionClick.php` | Acciones de click avanzadas |
| 18 | `18.EjemploMoveToElement.php` | Movimiento del cursor sobre elementos |
| 19 | `19.Iframes.php` | Trabajo con iframes |
| 20 | `20.IframesAnidados.php` | Manejo de iframes anidados |
| 21 | `21.WebTables.php` | Manipulación de tablas web |
| 22 | `22.Acciones.php` | Acciones complejas (drag & drop, etc.) |
| 23 | `23.Ventanas.php` | Manejo de múltiples ventanas |

### 🎨 Componentes UI (24-28)

| # | Archivo | Descripción |
|---|---------|-------------|
| 24 | `24.Accordion.php` | Interacción con accordions |
| 25 | `25.Tabs.php` | Manejo de tabs/pestañas |
| 26 | `26.Sortable.php` | Elementos ordenables (sortable) |
| 27 | `27.Selectable.php` | Elementos seleccionables |
| 28 | `28.Resizable.php` | Elementos redimensionables |

## 🚀 Cómo Usar

Ejecuta cualquier ejemplo desde la raíz del proyecto:

```bash
php examples/1.CrearInstanciayObtenerTitulo.php
```

## 💡 Recomendaciones

1. **Seguir el orden numérico**: Los ejemplos están diseñados para aprendizaje progresivo
2. **Modificar y experimentar**: Cambia valores, prueba diferentes elementos
3. **Leer los comentarios**: Cada archivo tiene explicaciones detalladas
4. **Ejecutar múltiples veces**: Algunos ejemplos demuestran comportamientos dinámicos

## 📝 Notas

- Todos los ejemplos usan ChromeDriver (ubicado en `../drivers/`)
- Las páginas de prueba están en `../test-pages/`
- La configuración se carga desde `../config/selenium-config.php`
