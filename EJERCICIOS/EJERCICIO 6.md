# **EJERCICIO COMPLETO SELENIUM: RESIZABLE + SORTABLE + SELECTABLE**

## 📋**ENUNCIADO DETALLADO**

**Objetivo General:** Automatizar **3 componentes jQuery UI avanzados** usando Selenium con **validaciones estrictas** en HTMLs locales.

**Duración estimada:** 90 minutos  
**Puntuación máxima:** 150 puntos  
**Requisitos previos:** PHP, Selenium, ChromeDriver funcionando

***

## **PARTE 1: RESIZABLE**

### **Descripción:**
Un div de **200x200px** con handle de resize en la esquina inferior-derecha (SE). El usuario debe arrastrar dinámicamente para cambiar tamaño.

### **Tareas:**
```
1. [ ] Abrir resizable-test.html ✓
2. [ ] Localizar div#resizable (div principal) ✓
3. [ ] Localizar handle .ui-resizable-se (esquina roja) ✓
4. [ ] DRAG & DROP: Arrastrar handle 250px hacia abajo, 250px hacia derecha ✓
5. [ ] Validar tamaño FINAL: width ≥ 450px AND height ≥ 450px ✓
6. [ ] Leer #size-status → debe contener "450px" ✓
7. [ ] Tomar screenshot ✓
8. [ ] Validar que div cambió color/tamaño visualmente ✓
```

### **Validaciones:**
```php
// Elemento debe ser resizable
assertTrue($resizable->getAttribute('class') contains 'ui-resizable');

// Tamaño debe haber aumentado
$sizeText = $driver->findElement(WebDriverBy::id('size-status'))->getText();
assertStringContainsString('450px', $sizeText);

// Handle visible
$handle = $driver->findElement(WebDriverBy::className('ui-resizable-se'));
assertTrue($handle->isDisplayed());
```

***

## **PARTE 2: SORTABLE**

### **Descripción:**
Una lista de **5 items** (`Item 1` a `Item 5`) que pueden reordenarse por drag & drop. El orden actualiza automáticamente en el status.

### **Tareas:**
```
1. [ ] Abrir sortable-test.html ✓
2. [ ] Leer orden INICIAL: ["Item 1", "Item 2", "Item 3", "Item 4", "Item 5"] ✓
3. [ ] DRAG Item 4 → drop en posición 1 ✓
4. [ ] DRAG Item 1 → drop en posición 3 ✓
5. [ ] Validar nuevo orden: ["Item 4", "Item 2", "Item 1", "Item 3", "Item 5"] ✓
6. [ ] Leer #order-status → contiene "Item 4, Item 2, Item 1..." ✓
7. [ ] Validar cada <li> tiene class="ui-state-default" ✓
8. [ ] DRAG Item 5 → drop en posición 2 ✓
9. [ ] Validar orden FINAL: ["Item 4", "Item 5", "Item 2", "Item 1", "Item 3"] ✓
10.[ ] Clicar "Reset Order" → orden original ✓
11.[ ] Validar #order-status = "Item 1, Item 2, Item 3, Item 4, Item 5" ✓
12.[ ] Tomar 3 screenshots (inicial, intermedio, final) ✓
```

### **Validaciones:**
```php
// Items tienen clase sortable
$items = $driver->findElements(WebDriverBy::xpath('//ul[@id="sortable"]//li'));
assertEquals(5, count($items));

foreach ($items as $item) {
    assertTrue(strpos($item->getAttribute('class'), 'ui-state-default') !== false);
}

// Orden actualizado en status
$status = $driver->findElement(WebDriverBy::id('order-status'))->getText();
assertEquals('Item 4, Item 2, Item 1, Item 3, Item 5', $status);
```

***

## **PARTE 3: SELECTABLE**

### **Descripción:**
Una lista de **6 items seleccionables** usando Ctrl+Click. Los items seleccionados tienen `class="ui-selected"` con fondo destacado.

### **Tareas:**
```
1. [ ] Abrir selectable-test.html ✓
2. [ ] Validar que 0 items están seleccionados (class sin "ui-selected") ✓
3. [ ] CTRL+CLICK en Item 2 ✓
4. [ ] Validar Item 2: class contiene "ui-selected" ✓
5. [ ] CTRL+CLICK en Item 4 ✓
6. [ ] CTRL+CLICK en Item 6 ✓
7. [ ] Validar EXACTAMENTE 3 items con "ui-selected" ✓
8. [ ] Leer #selected-status → "3 items selected" ✓
9. [ ] SHIFT+CLICK en Item 5 (rango Item 4 → Item 5) ✓
10.[ ] Validar Items 2,4,5 seleccionados (3 total) ✓
11.[ ] Leer selected status → "3 items selected" ✓
12.[ ] CLICK en área vacía → CLEAR selección ✓
13.[ ] Validar 0 items "ui-selected" ✓
14.[ ] Validar #selected-status = "0 items selected" ✓
15.[ ] Tomar screenshots de cada estado ✓
```

### **Validaciones:**
```php
// Contar seleccionados
$selected = $driver->findElements(WebDriverBy::xpath('//li[contains(@class,"ui-selected")]'));
assertEquals(3, count($selected));

// Verificar status
$status = $driver->findElement(WebDriverBy::id('selected-status'))->getText();
assertStringContainsString('3 items', $status);

// Después de limpiar
$selected = $driver->findElements(WebDriverBy::xpath('//li[contains(@class,"ui-selected")]'));
assertEquals(0, count($selected));
```

***

## **PREGUNTAS TÉCNICAS **

**Pregunta 1 (5 pts):** ¿Cuál es el **método Actions correcto** para drag & drop en Selenium?
```
a) $actions->drag($source)->drop($target);
b) $actions->dragAndDrop($source, $target)->perform(); ✅
c) $source->dragTo($target);
d) $driver->dragAndDrop($source, $target);
```

**Pregunta 2 (5 pts):** Para **seleccionar múltiples items** con Ctrl:
```php
$actions = new WebDriverActions($driver);
$actions->keyDown(Keys::CONTROL)
        ->click($item2)
        ->click($item4)
        ->click($item6)
        ->keyUp(Keys::______);  // ¿?

a) SHIFT
b) CONTROL ✅
c) ALT
d) META
```

**Pregunta 3 (5 pts):** **XPath para validar** que Item 4 está en posición 1:
```xpath
a) //li[1][contains(text(),'Item 4')]
b) //ul[@id='sortable']//li[position()=1][contains(text(),'Item 4')] ✅
c) //li[@data-position='1'][text()='Item 4']
d) //sortable/li[1][='Item 4']
```

**Pregunta 4 (5 pts):** **Esperar a que 3 items** tengan class "ui-selected":
```php
$wait->until(ExpectedCondition::numberOfElementsToBeLocated(
    WebDriverBy::xpath('______'),
    3
));

a) //li[@class='ui-selected']
b) //li[contains(@class,'ui-selected')] ✅
c) //li.ui-selected
d) //li[ui-selected]
```

***



## **HTML RESIZABLE COMPLETO (resizable-test.html)**

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resizable - Selenium Practice</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f0f2f5; }
        h1 { text-align: center; color: #333; }
        .demo-container { text-align: center; margin: 50px 0; }
        .status { background: #e8f4fd; padding: 20px; border-radius: 10px; margin: 30px 0; }
        #resizable { 
            width: 200px; height: 200px; 
            background: linear-gradient(45deg, #007cba, #28a745);
            margin: 0 auto; 
            border: 3px solid #333;
            position: relative;
        }
        .ui-resizable-handle { 
            background: #ff6b6b !important; 
            border: 2px solid #fff; 
            width: 20px !important; 
            height: 20px !important;
        }
        .ui-resizable-se { bottom: -10px; right: -10px; }
        #size-status { font-size: 24px; font-weight: bold; color: #007cba; }
    </style>
</head>
<body>
    <h1>🔄 Resizable - Selenium Practice</h1>
    
    <div class="demo-container">
        <div id="resizable" class="ui-resizable"></div>
        <div class="status">
            <h3>📏 Current Size</h3>
            <div id="size-status">200px x 200px</div>
            <p><strong>Goal:</strong> Drag red handle (SE corner) to 450px x 450px</p>
        </div>
    </div>

    <script>
        $( "#resizable" ).resizable({
            handles: "se",
            resize: function(event, ui) {
                const width = Math.round(ui.size.width);
                const height = Math.round(ui.size.height);
                $('#size-status').text(width + 'px x ' + height + 'px');
            }
        });
    </script>
</body>
</html>
```

***

## **HTML SORTABLE COMPLETO (sortable-test.html)**

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sortable - Selenium Practice</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f8f9fa; }
        h1 { text-align: center; color: #495057; }
        .demo-container { max-width: 600px; margin: 0 auto; }
        #sortable { 
            list-style: none; margin: 0; padding: 0; 
            background: white; border: 2px solid #dee2e6;
            border-radius: 8px; min-height: 300px;
        }
        #sortable li { 
            margin: 5px; padding: 10px; font-size: 18px; 
            height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; cursor: move; border-radius: 6px;
            display: flex; align-items: center;
        }
        .status { background: #d4edda; padding: 20px; border-radius: 10px; margin: 30px 0; text-align: center; }
        .reset-btn { background: #dc3545; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>🔀 Sortable - Selenium Practice</h1>
    
    <div class="demo-container">
        <ul id="sortable">
            <li class="ui-state-default">Item 1</li>
            <li class="ui-state-default">Item 2</li>
            <li class="ui-state-default">Item 3</li>
            <li class="ui-state-default">Item 4</li>
            <li class="ui-state-default">Item 5</li>
        </ul>
        
        <div class="status">
            <h3>📋 Current Order</h3>
            <div id="order-status">Item 1, Item 2, Item 3, Item 4, Item 5</div>
            <button class="reset-btn" onclick="resetOrder()">🔄 Reset Order</button>
        </div>
    </div>

    <script>
        $( "#sortable" ).sortable({
            placeholder: "ui-state-highlight",
            update: function(event, ui) {
                const items = $(this).sortable('toArray', {attribute: 'innerText'});
                const order = items.map(t => t.trim()).join(', ');
                $('#order-status').text(order);
            }
        });

        function resetOrder() {
            location.reload();
        }
    </script>
</body>
</html>
```

***

## **HTML SELECTABLE COMPLETO (selectable-test.html)**

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Selectable - Selenium Practice</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
        h1 { text-align: center; color: #333; }
        .demo-container { max-width: 600px; margin: 0 auto; }
        #selectable { 
            list-style: none; margin: 0; padding: 0;
            background: white; border: 2px solid #ddd; border-radius: 8px;
            min-height: 300px;
        }
        #selectable li { 
            margin: 5px; padding: 15px; font-size: 18px; 
            background: #e3f2fd; border: 2px solid #1976d2;
            cursor: pointer; border-radius: 6px; transition: all 0.3s;
        }
        #selectable li.ui-selected { 
            background: #1976d2; color: white; 
            border-color: #0d47a1;
        }
        .status { background: #fff3cd; padding: 20px; border-radius: 10px; margin: 30px 0; text-align: center; }
    </style>
</head>
<body>
    <h1>✅ Selectable - Selenium Practice</h1>
    
    <div class="demo-container">
        <ul id="selectable">
            <li class="ui-selectee">Item 1</li>
            <li class="ui-selectee">Item 2</li>
            <li class="ui-selectee">Item 3</li>
            <li class="ui-selectee">Item 4</li>
            <li class="ui-selectee">Item 5</li>
            <li class="ui-selectee">Item 6</li>
        </ul>
        
        <div class="status">
            <h3>📊 Selection Status</h3>
            <div id="selected-status">0 items selected</div>
            <p><strong>Instructions:</strong> CTRL+Click to select, SHIFT+Click for range, Click empty area to clear</p>
        </div>
    </div>

    <script>
        $( "#selectable" ).selectable({
            selected: function() {
                updateStatus();
            },
            unselected: function() {
                updateStatus();
            }
        });

        function updateStatus() {
            const count = $('#selectable .ui-selected').length;
            const text = count === 1 ? '1 item selected' : count + ' items selected';
            $('#selected-status').text(text);
        }

        // Click en área vacía limpia
        $('#selectable').click(function(e) {
            if (e.target === this) {
                $('#selectable li').removeClass('ui-selected');
                updateStatus();
            }
        });
    </script>
</body>
</html>
```

***
