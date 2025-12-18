# Ejercicio: Automatización de Formulario Dinámico con Implicit y Explicit Waits en Java

## Objetivo

Desarrollar un **script de automatización con Selenium y Java** que valide un **formulario con carga dinámica de elementos**, utilizando **Implicit Waits** y **Explicit Waits** (sin Expected Conditions) para sincronizar la ejecución con la renderización del DOM.

---

## Contexto del Ejercicio

El formulario simula un flujo real donde:
- **Algunos elementos aparecen después de 2-3 segundos** de carga inicial
- **Campos se habilitan dinámicamente** al completar otros
- **Botones aparecen tras validaciones** automáticas
- **La redirección ocurre tras procesamiento backend** simulado

---

## Pasos del Ejercicio

### 1. Descarga y configuración inicial

1. Descarga `form-index.html` y `form-success.html` en la **misma carpeta**.
2. Abre `form-index.html` en tu navegador (doble clic) para entender el flujo.
3. Rellena manualmente el formulario para ver:
   - Qué elementos aparecen/desaparecen
   - Cuándo se habilitan los campos
   - El tiempo de respuesta
4. Esta será tu **URL local para los tests**: `file:///C:/ruta/form-index.html`

---

### 2. Configuración de Implicit Wait

**Tarea:**
- Define un **Implicit Wait de 10 segundos** al inicializar el WebDriver
- Este wait será el **timeout global** para todas las búsquedas de elementos
- Documenta en tu código **por qué** elegiste este tiempo

**Validación:**
- Captura con screenshots en qué momentos el Implicit Wait **evita fallos**
- Nota qué sucede si un elemento **NO aparece en ese tiempo**

---

### 3. Interacción con campos de texto (con Implicit Wait)

**Tarea:**
- Localiza el campo "Nombre Completo"
- Escribe un nombre con **Implicit Wait activo** (no debes agregar esperas adicionales aquí)
- Localiza el campo "Email"
- Escribe un email válido
- Verifica que ambos campos tienen el texto correcto

**Validación:**
- Obtén el texto con `.getAttribute("value")` o `.getText()`
- Compara con lo que escribiste
- Si algún campo NO contiene el texto, guarda un screenshot de error

---

### 4. Manejo de elementos que aparecen con delay (Explicit Wait sin Expected Conditions)

**Tarea:**
- Después de llenar Email, el campo **"Teléfono"** aparece con un delay de 2 segundos
- Usa un **Explicit Wait de 5 segundos** para localizar este elemento:

```java
WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
WebElement phoneField = wait.until(driver -> driver.findElement(By.id("phone")));
```

**Validación:**
- Mide el tiempo real que tardó en aparecer
- Escribe el teléfono: `"34 600 123 456"`
- Verifica que el campo está visible y habilitado

**Nota:**
- NO uses `ExpectedConditions`
- Usa solo lambdas `(driver) -> driver.findElement(...)`

---

### 5. Selección en desplegables con sincronización

**Tarea:**
- Localiza el elemento `<select id="country">`
- Selecciona la opción **"España"** (o la segunda opción disponible)
- Al seleccionar, un nuevo campo **"Provincia"** aparece tras 1.5 segundos

**Implementación:**
```java
WebElement countrySelect = driver.findElement(By.id("country"));
Select select = new Select(countrySelect);
select.selectByValue("ES");

// Ahora usa Explicit Wait para el campo Provincia
WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
WebElement provinceField = wait.until(driver -> driver.findElement(By.id("province")));
```

**Validación:**
- Verifica que "Provincia" NO estaba en el DOM antes de seleccionar país
- Verifica que SÍ está después (captura ambas pantallas)
- Selecciona una provincia

---

### 6. Carga de archivos con validación

**Tarea:**
- Localiza el input `<input type="file" id="avatar">`
- Carga una imagen o archivo de texto desde tu equipo
- El sistema validará el archivo y mostrará un **mensaje de éxito tras 2 segundos**

**Implementación:**
```java
WebElement fileInput = driver.findElement(By.id("avatar"));
fileInput.sendKeys("C:\\ruta\\completa\\archivo.png"); // Ruta absoluta

// Espera a que aparezca el mensaje de validación
WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
WebElement successMessage = wait.until(driver -> 
    driver.findElement(By.id("file-validation-message"))
);
```

**Validación:**
- Verifica que el archivo se subió
- Captura el mensaje de éxito
- Intenta cargar un archivo inválido y captura el error

---

### 7. Interacción con checkboxes y radio buttons

**Tarea:**
- Marca el checkbox **"Acepto términos y condiciones"**
- Al marcarlo, se habilita el checkbox **"Newsletter"** (estaba deshabilitado)
- Espera con **Explicit Wait** a que se habilite

**Implementación:**
```java
WebElement termsCheckbox = driver.findElement(By.id("terms"));
termsCheckbox.click();

// Newsletter se habilita tras 1 segundo
WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
WebElement newsCheckbox = wait.until(driver -> {
    WebElement elem = driver.findElement(By.id("newsletter"));
    return elem.isEnabled() ? elem : null;
});

newsCheckbox.click();
```

**Validación:**
- Captura antes/después de marcar "Términos"
- Verifica que Newsletter estaba deshabilitado
- Verifica que Newsletter está habilitado después

---

### 8. Validación de radio buttons con dependencias

**Tarea:**
- Selecciona la opción de radio button **"Empresa"** (estaba "Personal" seleccionado)
- Al seleccionar, aparece un nuevo campo **"Nombre de Empresa"** tras 2 segundos
- Rellena el campo con: `"Mi Empresa S.A."`

**Validación:**
- Verifica que al seleccionar, el radio anterior se deselecciona automáticamente
- Captura cuando el campo de empresa NO existe
- Captura cuando el campo de empresa aparece
- Verifica el contenido del campo

---

### 9. Manejo de DatePicker con escritura manual

**Tarea:**
- Localiza el campo de fecha `<input type="date" id="birth-date">`
- Escribe manualmente la fecha: `"15/01/1990"` o en formato ISO `"1990-01-15"`
- Valida que la fecha se escribió correctamente

**Validación:**
- Obtén el valor con `.getAttribute("value")`
- Compara con la fecha escrita
- Si el navegador abre un datepicker, documenta el comportamiento

---

### 10. Validación del formulario con Explicit Wait

**Tarea:**
- Después de completar TODOS los campos, aparece el botón **"Enviar"** (estaba oculto con `display:none`)
- Usa **Explicit Wait** para esperar a que sea visible y clickeable

**Implementación:**
```java
WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
WebElement submitBtn = wait.until(driver -> {
    WebElement btn = driver.findElement(By.id("submit-btn"));
    // No uses ExpectedConditions, valida manualmente
    if (btn.isDisplayed() && btn.isEnabled()) {
        return btn;
    }
    return null;
});

submitBtn.click();
```

**Validación:**
- Captura el botón oculto ANTES de completar el formulario
- Captura el botón visible DESPUÉS
- Mide cuánto tiempo tardó en aparecer

---

### 11. Navegación y redirección con sincronización

**Tarea:**
- Tras hacer clic en "Enviar", el navegador procesa datos (tarda 3 segundos)
- Se redirige a `form-success.html`
- Usa **Explicit Wait** para verificar que la URL cambió

**Implementación:**
```java
// Espera a que la URL contenga "success"
WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(10));
wait.until(driver -> driver.getCurrentUrl().contains("form-success"));

String currentUrl = driver.getCurrentUrl();
System.out.println("Redirección exitosa: " + currentUrl);
```

**Validación:**
- Captura la URL antes del clic
- Captura la URL después de la redirección
- Verifica que los datos aparecen en la página de éxito

---

### 12. Verificación de datos en página de confirmación

**Tarea:**
- En `form-success.html` se muestran los datos enviados
- Valida que el **nombre, email, teléfono** y otros campos coinciden con lo que escribiste

**Implementación:**
```java
WebElement confirmName = driver.findElement(By.id("confirm-name"));
String displayedName = confirmName.getText();

if (displayedName.equals(nombreEscrito)) {
    System.out.println("✓ Nombre verificado: " + displayedName);
} else {
    System.out.println("✗ Error: Nombre no coincide");
    // Captura screenshot de error
}
```

**Validación:**
- Compara cada campo de la página original con la página de confirmación
- Guarda en un log qué campos coincidieron y cuáles no

---

### 13. Navegación de regreso con sincronización

**Tarea:**
- En `form-success.html` hay un link **"Volver al formulario"**
- Haz clic en él y valida que regresas a `form-index.html`
- Verifica que los campos están vacíos (nuevo formulario)

**Implementación:**
```java
WebElement backLink = driver.findElement(By.id("back-to-form"));
backLink.click();

// Espera a que la URL vuelva al index
WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
wait.until(driver -> driver.getCurrentUrl().contains("form-index"));

// Verifica que el campo nombre está vacío
WebElement nameInput = driver.findElement(By.id("name"));
String nameValue = nameInput.getAttribute("value");
if (nameValue.isEmpty()) {
    System.out.println("✓ Formulario limpiado correctamente");
}
```

**Validación:**
- Captura la URL de ambas páginas
- Verifica que los campos están vacíos

---

### 14. Captura de pantallas y logging

**Tarea:**
- En **cada paso importante**, toma un screenshot
- Guarda logs con timestamps de:
  - Tiempo inicial y final de cada operación
  - Elementos localizados/no localizados
  - Errores y excepciones

**Estructura de archivos:**
```
/screenshots
  ├─ paso-01-carga-pagina.png
  ├─ paso-02-relleno-nombre.png
  ├─ paso-03-teléfono-apareció.png
  └─ ... (uno por cada paso)

/logs
  ├─ test-execution.log
  └─ test-errors.log
```

**Ejemplo de logging:**
```java
SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
String timestamp = sdf.format(new Date());
System.out.println("[" + timestamp + "] Buscando elemento: phone");
System.out.println("[" + timestamp + "] Elemento encontrado en 1.2 segundos");
```

---

### 15. Cierre del navegador

**Tarea:**
- Al finalizar todos los tests, cierra el navegador correctamente con `driver.quit()`
- Verifica que el proceso se cerró sin excepciones

**Validación:**
- Captura una pantalla final o mensaje en consola: `"✓ Navegador cerrado correctamente"`

---

## Requisitos Técnicos

### Implicit Wait
- Debe estar **configurado al inicializar WebDriver**
- Se aplica a **TODOS los `findElement()` globalmente**
- **NO lo cambies durante la ejecución**

### Explicit Wait
- Úsalo **solo cuando necesites sincronización extra** (elementos que aparecen con delay)
- **Implementación sin `ExpectedConditions`**: usa lambdas personalizadas
- Timeout: 5-10 segundos según el elemento

### Patrón de Implementación
```java
// Implicit Wait (global)
driver.manage().timeouts().implicitlyWait(Duration.ofSeconds(10));

// Explicit Wait (puntual)
WebDriverWait wait = new WebDriverWait(driver, Duration.ofSeconds(5));
WebElement element = wait.until(driver -> driver.findElement(By.id("elemento")));
```

---

## Validaciones Finales

- [ ] **Implicit Wait** está configurado y funciona
- [ ] **Explicit Waits** se usan sin `ExpectedConditions`
- [ ] Todos los campos se rellenan correctamente
- [ ] Los elementos dinámicos se localizan tras su aparición
- [ ] La redirección se valida con Explicit Wait
- [ ] Los datos se verifican en la página de confirmación
- [ ] Screenshots capturan cada paso importante
- [ ] Logs registran tiempos y eventos
- [ ] El navegador se cierra sin excepciones
- [ ] **NO hay esperas hardcodeadas** (`Thread.sleep()`) sin justificación

---

## Notas Importantes

1. **No uses `Thread.sleep()`** a menos que sea absolutamente necesario (documentar por qué)
2. **No uses `ExpectedConditions`** - implementa tu lógica con lambdas
3. **Captura screenshots en cada paso importante**
4. **Anota tiempos reales vs tiempos de wait** - esto te ayuda a optimizar
5. **Documenta en comentarios** por qué usaste Implicit o Explicit en cada caso

---

## Recursos

- **Selenium Documentation**: https://www.selenium.dev/documentation/webdriver/waits/
- **Java Duration API**: https://docs.oracle.com/javase/8/docs/api/java/time/Duration.html
- **WebDriverWait**: https://www.selenium.dev/selenium/docs/api/java/org/openqa/selenium/support/ui/WebDriverWait.html

