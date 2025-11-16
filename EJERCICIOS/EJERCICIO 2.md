# **EXPLICACIÓN COMPLETA DEL EJERCICIO**

La página usada: [https://www.selenium.dev/selenium/web/dynamic.html](https://www.selenium.dev/selenium/web/dynamic.html)

Esta web fue creada específicamente por Selenium para simular **comportamientos dinámicos reales**, como:

* AJAX
* habilitar/deshabilitar botones
* cambios de estilo
* creación de frames en tiempo real
* elementos que aparecen y desaparecen

Esto la hace perfecta para practicar sincronización.


# **OBJETIVO GENERAL**

Ejercitar los 3 tipos de espera en Selenium, aplicados a situaciones reales:

| Situación                 | Espera usada                        |
| ------------------------- | ----------------------------------- |
| Botón que se habilita     | Explicit Wait                       |
| Carga AJAX                | Explicit Wait                       |
| Frame que aparece después | Explicit Wait (frameToBeAvailable…) |
| Cambio de color animado   | Fluent Wait + cond. personalizada   |

La idea no es solo usar esperas, sino **aprender cuándo y por qué usarlas**.

---

# **EXPLICACIÓN DETALLADA DEL CÓDIGO (PASO A PASO)**

---

## **1. Configuración inicial**

```java
WebDriver driver = new ChromeDriver();
driver.manage().window().maximize();
driver.get("https://www.selenium.dev/selenium/web/dynamic.html");
```

Se abre Chrome, se maximiza la ventana y se abre la web dinámica.

---

## **2. Esperar a que un botón se habilite dinámicamente**

```java
WebElement enableBtn = driver.findElement(By.id("enable-button"));

wait.until(ExpectedConditions.elementToBeClickable(enableBtn));
enableBtn.click();
```

### ¿Qué pasa aquí?

1. El botón empieza **deshabilitado** (`disabled`).
2. Después de un tiempo, la página lo **habilita**.
3. `elementToBeClickable` asegura que:

   * el elemento sea **visible**
   * el elemento sea **enabled**
   * el elemento no esté cubierto por nada

Si no usas este wait → Selenium falla porque intenta clic antes de tiempo.

---

## **3. Forzar una carga AJAX**

```java
WebElement addBtn = driver.findElement(By.id("adder"));
addBtn.click();
```

El botón dispara un **request AJAX**, que crea un nuevo elemento dinámico.

---

## **4. Esperar a que aparezca un elemento generado vía AJAX**

```java
WebElement newElement = wait.until(
    ExpectedConditions.visibilityOfElementLocated(By.className("redbox"))
);
```

Esto evita errores típicos:

* `NoSuchElementException`
* `ElementNotVisibleException`
* `StaleElementReferenceException`

La espera explícita no continúa hasta que:

✔ El elemento existe en el DOM
✔ Es visible
✔ Tiene dimensiones (>0 px)

---

## **5. Crear un iframe dinámico y esperar a que exista**

```java
WebElement frameBtn = driver.findElement(By.id("frame-button"));
frameBtn.click();

wait.until(ExpectedConditions.frameToBeAvailableAndSwitchToIt(By.tagName("iframe")));
```

### ¿Qué está pasando aquí?

1. El frame **NO está en el DOM** al principio.
2. Cuando presionas el botón, el frame se crea.
3. La espera:

   * Espera a que el `iframe` aparezca
   * Cambia automáticamente el driver **dentro del frame**

Si no haces esto → Selenium no verá el contenido interno.

---

## **6. Leer algo dentro del frame**

```java
WebElement frameText = wait.until(
    ExpectedConditions.visibilityOfElementLocated(By.id("inside-frame"))
);
```

Dentro del frame aparece un texto dinámico.
Necesitas una espera **porque aparece con retraso**.

---

## **7. Volver al DOM principal**

```java
driver.switchTo().defaultContent();
```

Muy importante:
Si no vuelves al contexto principal, Selenium sigue "encerrado" en el frame y fallará todo lo demás.

---

## **8. Fluent Wait para esperar un cambio de color**

```java
WebElement colorChanging = driver.findElement(By.id("colorbox"));

Wait<WebDriver> fluentWait = new FluentWait<>(driver)
        .withTimeout(Duration.ofSeconds(20))
        .pollingEvery(Duration.ofSeconds(2))
        .ignoring(NoSuchElementException.class);
```

### 🔍 Fluent Wait permite:

* Definir tiempo máximo
* Definir intervalos de consulta
* Ignorar excepciones
* Usar funciones personalizadas

### Condición personalizada:

```java
Boolean colorChanged = fluentWait.until(driver -> {
    String color = colorChanging.getCssValue("background-color");
    System.out.println("Esperando cambio de color... actual: " + color);
    return color.contains("0, 128, 0"); // verde
});
```

Esperamos hasta que el CSS cambie a verde.

Esto NO se puede lograr con un ExpectedCondition normal.

---

## **9. Validar texto final**

```java
WebElement finish = wait.until(
        ExpectedConditions.visibilityOfElementLocated(By.id("finish"))
);
System.out.println("Texto final: " + finish.getText());
```

Un texto aparece cuando todos los procesos dinámicos terminan.

---


¿Quieres ese nivel?
