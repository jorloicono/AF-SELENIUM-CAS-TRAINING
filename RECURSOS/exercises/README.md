# 📝 Ejercicios Prácticos

Esta carpeta contiene ejercicios para poner en práctica los conceptos aprendidos en los ejemplos.

## 📋 Lista de Ejercicios

| Ejercicio | Descripción | Conceptos Clave |
|-----------|-------------|-----------------|
| **Ejercicio 1** | Ejercicio introductorio básico | Inicialización, navegación básica |
| **Ejercicio 3** | Ejercicio de localización de elementos | Selectores, interacción con elementos |
| **Ejercicio 4** | Ejercicio de formularios | Input, submit, validación |
| **Ejercicio 5** | Ejercicio de esperas y sincronización | Implicit/Explicit waits |
| **Ejercicio 6** | Ejercicio avanzado | Múltiples conceptos integrados |

## 🎯 Cómo Abordar los Ejercicios

### Antes de Empezar
1. Revisa los ejemplos relacionados en la carpeta `examples/`
2. Lee el enunciado completo del ejercicio
3. Identifica qué conceptos necesitas aplicar

### Durante el Desarrollo
1. Escribe tu código paso a paso
2. Prueba frecuentemente
3. Usa las herramientas de debugging (print, var_dump)
4. Consulta la documentación si es necesario

### Después de Completar
1. Verifica que el ejercicio funcione correctamente
2. Optimiza tu código
3. Compara con las mejores prácticas
4. Experimenta con variaciones

## 🚀 Ejecución

```bash
# Ejecutar un ejercicio específico
php exercises/Ejercicio\ 1.php
php exercises/Ejercicio\ 6.php
```

## 💡 Tips

- **No te saltes ejercicios**: Cada uno refuerza conceptos importantes
- **Experimenta**: Prueba diferentes enfoques
- **Documenta tu código**: Añade comentarios explicativos
- **Maneja errores**: Implementa try-catch donde sea necesario
- **Usa el patrón POM**: Para ejercicios más complejos, considera usar Page Object Model

## 📚 Recursos Relacionados

- **Ejemplos básicos**: `../examples/1-7.*.php`
- **Ejemplos de esperas**: `../examples/8-12.*.php`
- **Ejemplos avanzados**: `../examples/13-28.*.php`
- **POM Examples**: `../pom-examples/`

## ❓ Solución de Problemas

### El ejercicio no encuentra elementos
- Verifica que estés usando los selectores correctos
- Asegúrate de que la página se haya cargado completamente
- Usa esperas explícitas cuando sea necesario

### Errores de ChromeDriver
- Verifica que ChromeDriver esté en `../drivers/`
- Comprueba que la versión coincida con tu Chrome
- Revisa que el puerto no esté en uso

### Timeouts
- Aumenta los tiempos de espera si la página tarda en cargar
- Verifica tu conexión de internet (si usas URLs externas)
- Usa esperas más específicas en lugar de sleep()

## 🎓 Niveles de Dificultad

- ⭐ **Ejercicio 1**: Básico - Primeros pasos
- ⭐⭐ **Ejercicios 3-4**: Intermedio - Aplicación de conceptos
- ⭐⭐⭐ **Ejercicios 5-6**: Avanzado - Integración de múltiples técnicas

---

¡Buena suerte con los ejercicios! 🚀
