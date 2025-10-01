# Validador de Captcha con Control de Intentos

Este script PHP valida un **captcha generado previamente** y controla los intentos de envío de un formulario, protegiendo contra bots y accesos automatizados.

Se tiene 2 versiones de captcha en diferentes ramas
  -uno
  -dos

---

## Especificaciones

- **Lenguaje:** PHP  
- **Almacenamiento temporal:** Sesiones (`$_SESSION`)  
- **Propósito:** Asegurar que solo usuarios humanos puedan enviar el formulario y que los captchas sean válidos y de un solo uso.

---

## Funcionalidades principales

1. **Validación de campos**
   - Verifica que el usuario haya llenado los campos obligatorios (`nombre` y `codigo`). 
   - Si faltan datos, muestra un mensaje de error y redirige al formulario.

2. **Fingerprint del usuario**
   - Genera un identificador único por sesión y navegador (`fingerprint`) para controlar intentos por usuario.  

3. **Control de intentos y bloqueo**
   - Permite un máximo de **3 intentos fallidos** por captcha.  
   - Si se supera, bloquea temporalmente **5 minutos** antes de permitir nuevos intentos.  
   - Reinicia el contador si el bloqueo expira.

4. **Recuperación y verificación del captcha**
   - Obtiene el captcha almacenado en la sesión usando el `form_id`.  
   - Verifica que **exista y no haya expirado** (2 minutos de validez).  
   - Cada captcha es de **un solo uso**, eliminándose de la sesión después de validar.

5. **Comparación segura**
   - Compara el código ingresado con el original usando `hash('sha256')` y `hash_equals()` para prevenir ataques de timing.  
   - Si el código es incorrecto:
     - Se incrementa el contador de intentos.  
     - Se elimina el captcha de la sesión.  
     - Se aplica un **retardo de 2 segundos** como medida anti-bots.  
     - Se muestra mensaje de error con el número de intento.

6. **Éxito en validación**
   - Si el captcha es correcto:
     - Se elimina de la sesión para **un solo uso**.  
     - Se reinicia el contador de intentos.  
     - Se muestra un mensaje de bienvenida al usuario.

---

## Beneficios y seguridad

- Previene **envíos automáticos** de formularios por bots.  
- Garantiza que **cada captcha tenga validez temporal** y sea de **un solo uso**.  
- Controla la **frecuencia de intentos** para aumentar la seguridad.  
- Implementa **retardo anti-bots** y manejo seguro de comparaciones para evitar vulnerabilidades.

