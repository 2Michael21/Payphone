# Formulario de Transacciones - Proyecto CI/CD

Este repositorio contiene una aplicación web PHP para la gestión de transacciones de pago, contenerizada con Docker y automatizada mediante GitHub Actions.

## Estructura del Proyecto

- `index.html`: Página principal de la interfaz de usuario.
- `crear_transaccion.php`: Script backend para procesar transacciones.
- `Dockerfile`: Definición de la imagen Docker (basada en `php:8.2-apache`).
- `.github/workflows/docker-publish.yml`: Pipeline de CI/CD que construye y publica la imagen en GitHub Container Registry (GHCR).

## Automatización (CI/CD)

El proyecto utiliza **GitHub Actions** para integrar y desplegar cambios automáticamente.
Cada vez que se realiza un `push` a las ramas `main` o `master`:
1.  Se activa el workflow `Docker Build and Publish`.
2.  Se construye la imagen Docker.
3.  Se publica la nueva imagen en: `ghcr.io/2michael21/payphone:main` (o el tag correspondiente).

## Cómo Desplegar y Probar (Manual de Usuario)

Para probar la aplicación localmente o en un servidor utilizando la imagen generada automáticamente:

### Prerrequisitos
- Tener [Docker instalado](https://docs.docker.com/get-docker/).

### Pasos
1.  **Ejecutar el contenedor:**
    Corre el siguiente comando en tu terminal para descargar la imagen y arrancar el servidor:

    ```bash
    docker run -d -p 8080:80 --name mi-app-pagos ghcr.io/2michael21/payphone:main
    ```

    *Nota: Si el repositorio es privado, asegúrate de haber hecho login antes con `echo $CR_PAT | docker login ghcr.io -u TU_USUARIO --password-stdin`.*

2.  **Acceder a la aplicación:**
    Abre tu navegador web y visita:
    [http://localhost:8080](http://localhost:8080)

3.  **Detener la prueba:**
    ```bash
    docker stop mi-app-pagos
    docker rm mi-app-pagos
    ```

## Tecnologías
- **PHP 8.2**
- **Apache**
- **Docker**
- **GitHub Actions**
