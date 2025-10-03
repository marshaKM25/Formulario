# CAPTCHA Animado y Seguro con PHP 

Esta es la implementación de un sistema de CAPTCHA de alta seguridad que genera **GIFs animados** para una máxima resistencia contra bots. El proyecto utiliza PHP, la extensión Imagick y está completamente configurado para ejecutarse al instante con Docker.

---

## Características Principales 

* **Alta Seguridad:** Diseñado desde cero para ser resistente a bots y ataques comunes.
* **Protección contra Fuerza Bruta:** Limita los intentos fallidos por usuario y aplica un bloqueo temporal para detener ataques.
* **Comparación Segura:** Usa `hash_equals()` para prevenir ataques de temporización al validar el código.
* **Entorno Dockerizado:** Configuración de un solo comando para un entorno de desarrollo y pruebas 100% reproducible.
* **Código de Un Solo Uso y con Expiración:** Cada CAPTCHA es válido para un único intento y expira después de un tiempo definido.
* **Generación de GIF Animado:** Utiliza **Imagick** para crear imágenes complejas y dinámicas que son extremadamente difíciles de procesar por software OCR.

---

## Prueba  Rápida con Docker

La forma más sencilla de probar esta versión es usando Docker. No necesitas instalar PHP ni un servidor web en tu máquina.

### **Requisitos Previos:**
* [Docker](https://www.docker.com/get-started) y [Docker Compose](https://docs.docker.com/compose/install/) instalados.

### **Pasos para Ejecutar:**

1.  **Clona el repositorio y entra al directorio:**
    ```bash
    git clone https://github.com/marshaKM25/Formulario
    ```
    *(Si ya lo clonaste, asegúrate de estar en la rama correcta).*

2.  **Construye y levanta el contenedor:**
    Este comando leerá el `docker-compose.yml`, construirá la imagen de PHP con todas las dependencias (incluyendo Imagick) y ejecutará el servidor en segundo plano.
    ```bash
    docker-compose up -d --build
    ```

3.  **¡Listo!**
    Abre tu navegador y visita **[http://localhost:8081](http://localhost:8081)**. Deberías ver el formulario con el CAPTCHA animado funcionando.

---

## Contribuciones 🤝

¡Las contribuciones son bienvenidas! Si deseas reportar un bug o sugerir una mejora, por favor, lee nuestra [guía para contribuir](CONTRIBUTING.md).

## Licencia 📄

Este proyecto está distribuido bajo la Licencia MIT. Consulta el archivo [LICENSE](LICENSE) para más detalles.

---
