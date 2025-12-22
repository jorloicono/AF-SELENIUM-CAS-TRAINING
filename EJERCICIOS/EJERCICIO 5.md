# 🏆 **EJERCICIO SELENIUM: TABS & ACCORDION CHALLENGE**

## 📋 **ENUNCIADO**

**Objetivo:** Automatizar pruebas completas en tu propio HTML con **Tabs + Accordion**.

**Tareas requeridas:**
1. ✅ Abrir tu HTML local
2. ✅ Interactuar con **3 pestañas** (Home, Products, Contact)
3. ✅ Expandir/colapsar **3 panels** del accordion en Products
4. ✅ **Validar textos específicos** en cada sección
5. ✅ **Tomar screenshots** de estados clave
6. ✅ **Verificar estados** (tabs activas, panels expandidos)
7. **PREGUNTAS** al final con validaciones

***

## 📁 **1. HTML COMPLETO (tabs-accordion-test.html)**

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabs & Accordion - Selenium Practice</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; }
        .tabs { overflow: hidden; border: 1px solid #ccc; background: #f1f1f1; }
        .tab-button { 
            background: #ddd; float: left; border: none; outline: none; 
            cursor: pointer; padding: 14px 16px; transition: 0.3s; 
        }
        .tab-button:hover { background: #ccc; }
        .tab-button.active { background: #007cba; color: white; }
        .tab-content { display: none; padding: 20px; border: 1px solid #ccc; }
        .tab-content.active { display: block; }
        
        .accordion { margin: 20px 0; }
        .accordion-item { border: 1px solid #ddd; margin-bottom: 5px; }
        .accordion-header { 
            background: #f8f9fa; padding: 15px; cursor: pointer; 
            font-weight: bold; user-select: none;
        }
        .accordion-header:hover { background: #e9ecef; }
        .accordion-header.active { background: #28a745; color: white; }
        .accordion-body { 
            padding: 15px; background: #f8f9fa; display: none; 
        }
        .accordion-body.active { display: block; }
        
        .status { background: #e7f3ff; padding: 15px; margin: 20px 0; border-radius: 5px; }
        button.test-btn { background: #dc3545; color: white; padding: 10px; margin: 5px; }
    </style>
</head>
<body>
    <h1>🧪 Tabs & Accordion - Selenium Practice</h1>
    
    <!-- TABS -->
    <div class="tabs">
        <button class="tab-button active" onclick="openTab(event, 'home')">🏠 Home</button>
        <button class="tab-button" onclick="openTab(event, 'products')">🛒 Products</button>
        <button class="tab-button" onclick="openTab(event, 'contact')">📧 Contact</button>
    </div>
    
    <div id="home" class="tab-content active">
        <h2>Bienvenido a la práctica</h2>
        <p>Este es un <strong>HTML de prueba</strong> con tabs y accordion para Selenium.</p>
        <p>Objetivo: Automatizar navegación entre tabs y expansión de panels.</p>
    </div>
    
    <div id="products" class="tab-content">
        <h2>Productos</h2>
        <p>Interactúa con el <strong>accordion de productos</strong> debajo:</p>
        
        <!-- ACCORDION -->
        <div class="accordion">
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(1)">📱 Smartphone Pro</div>
                <div class="accordion-body" id="panel1">
                    <p>Smartphone con <strong>6.5" AMOLED</strong>, 128GB, cámara 108MP. Precio: €599</p>
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(2)">💻 Laptop Ultra</div>
                <div class="accordion-body" id="panel2">
                    <p>Laptop <strong>Intel i7, 16GB RAM</strong>, SSD 1TB, pantalla 15.6" 4K. Precio: €1299</p>
                </div>
            </div>
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(3)">🎧 Wireless Pro</div>
                <div class="accordion-body" id="panel3">
                    <p>Auriculares <strong>ANC noise cancelling</strong>, 30h batería, Bluetooth 5.2. Precio: €199</p>
                </div>
            </div>
        </div>
    </div>
    
    <div id="contact" class="tab-content">
        <h2>Contacto</h2>
        <p>Envíanos un email a <strong>info@testing.com</strong></p>
        <p>Teléfono: <strong>+34 123 456 789</strong></p>
    </div>

    <div class="status">
        <h3>📊 Estado Actual</h3>
        <div id="tab-status">Tab activa: Home</div>
        <div id="accordion-status">Panels expandidos: 0/3</div>
        <button class="test-btn" onclick="resetAll()">🔄 Reset Todo</button>
    </div>

    <script>
        function openTab(evt, tabName) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            evt.currentTarget.classList.add('active');
            document.getElementById('tab-status').textContent = 'Tab activa: ' + tabName.charAt(0).toUpperCase() + tabName.slice(1);
        }
        
        let expandedPanels = 0;
        function toggleAccordion(panelId) {
            const body = document.getElementById('panel' + panelId);
            const header = body.previousElementSibling;
            
            if (body.classList.contains('active')) {
                body.classList.remove('active');
                header.classList.remove('active');
                expandedPanels--;
            } else {
                body.classList.add('active');
                header.classList.add('active');
                expandedPanels++;
            }
            document.getElementById('accordion-status').textContent = 'Panels expandidos: ' + expandedPanels + '/3';
        }
        
        function resetAll() {
            document.querySelectorAll('.accordion-body').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.accordion-header').forEach(h => h.classList.remove('active'));
            document.querySelectorAll('.tab-button')[0].click();
            expandedPanels = 0;
            document.getElementById('accordion-status').textContent = 'Panels expandidos: 0/3';
        }
    </script>
</body>
</html>
```

***

## 📝 **2. EJERCICIO EN MD**

## 🎯 OBJETIVO
Automatizar navegación completa entre tabs y accordion con **validaciones estrictas**.

## 📋 REQUISITOS 

### PASO 1: Navegación Tabs 
```
[ ] 1.1 Abrir HTML local ✓
[ ] 1.2 Clicar tab "Products" → Validar class="active" ✓  
[ ] 1.3 Validar texto "Interactúa con el accordion" ✓
[ ] 1.4 Volver a "Home" → Validar "Bienvenido a la práctica" ✓
[ ] 1.5 Ir a "Contact" → Validar email "info@testing.com" ✓
```

### PASO 2: Accordion Products 
```
[ ] 2.1 En tab Products, expandir "Smartphone Pro" ✓
[ ] 2.2 Validar "6.5" AMOLED" aparece ✓
[ ] 2.3 Expandir "Laptop Ultra" ✓
[ ] 2.4 NO expandir "Wireless Pro" ✓
[ ] 2.5 Validar status: "Panels expandidos: 2/3" ✓
```

### PASO 3: Validaciones Avanzadas 
```
[ ] 3.1 Screenshot tab Products con 2 panels ✓
[ ] 3.2 Clic "Reset Todo" ✓
[ ] 3.3 Validar "Panels expandidos: 0/3" ✓
[ ] 3.4 Screenshot final ✓
```

## ❓ PREGUNTAS 

**Pregunta 1 (10 pts):** ¿Cuál es el **XPath exacto** del header "Laptop Ultra"?
```
a) //div[contains(text(),'Laptop')]
b) //div[@onclick='toggleAccordion(2)']
c) .accordion-header[1]
d) //h2[contains(@class,'active')]
```

**Pregunta 2 (15 pts):** Escribe el código para **esperar** que el status muestre "2/3":
```
$wait->until(ExpectedCondition::______(WebDriverBy::id("accordion-status"), "2/3"));
```

**Pregunta 3 (15 pts):** ¿Cómo validar que **exactamente 2 headers** tienen class="active"?
```
$activeHeaders = $driver->findElements(WebDriverBy::______);
assertEquals(____, count($activeHeaders));
```

