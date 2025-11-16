# Ejercicio: Automatización Completa de un Formulario Dinámico con Selenium en Java

## Objetivo

Desarrollar un **script de automatización de pruebas con Selenium y Java** para validar un **formulario web con elementos dinámicos y múltiples interacciones**.
El flujo debe simular una experiencia de usuario realista y contemplar diferentes tipos de elementos HTML y validaciones automáticas.

---

## Pasos del Ejercicio

### 1. Acceso y verificación de la página

1. Descarga `index.html` y `submitted-form.html` y guárdalos en la **misma carpeta**.
2. Abre `index.html` en tu navegador (doble clic).
3. Rellena el formulario y pulsa **Submit** — la página redirigirá a `submitted-form.html` donde verás los datos enviados.
4. Usa este formulario para ejecutar tus tests de Selenium (la URL local será `file:///.../index.html`).

---

### 2. Interacción con los campos de texto

* Rellena el campo de texto con un **nombre**.
* Ingresa una **contraseña segura** (mínimo 8 caracteres, con números y letras).
* Escribe un **mensaje de prueba** en el área de texto.

---

### 3. Selección en listas desplegables

* Selecciona la **segunda opción** en el elemento `<select>`.
* Escribe en el campo **datalist** y elige la opción `"Seattle"`.

---

### 4. Carga de archivos

* Carga un **archivo de imagen o texto** desde tu equipo local.
* Verifica que el archivo **ha sido subido correctamente**.

---

### 5. Interacción con checkboxes y radio buttons

* **Desmarca** el checkbox que estaba marcado por defecto y **marca el otro**.
* **Selecciona** el segundo radio button.
* Verifica que **solo un radio button** puede estar seleccionado a la vez.

---

### 6. Manejo de fechas con DatePicker

* Escribe manualmente la fecha **"12/31/2024"** en el campo de fecha.
* Si el datepicker es interactivo, selecciona la fecha desde el **calendario emergente**.

---

### 7. Simulación de envíos y navegación

* Haz clic en el botón **Submit** y valida que la URL cambia a `"submitted-form.html"`.
* Verifica que los **datos enviados** aparecen en la nueva página.
* Navega de regreso a la página principal con el enlace **"Return to index"**.

---

### 8. Captura de pantalla y generación de logs

* Si ocurre un **error en cualquier paso**, captura una **captura de pantalla** y guarda un **log con el nombre del error**.

---

### 9. Cierre del navegador

* Al finalizar la ejecución de los tests, **cierra el navegador correctamente**.
