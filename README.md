# Prueba Técnica - Gestión de Productos

## Aspirante
Luis Alberto Mejia López

## Descripción
Aplicación web que permite cargar un archivo Excel con un listado de productos, 
validar su información, visualizarlos en tarjetas y editarlos en tiempo real.

- **Backend:** Laravel 11 (PHP)
- **Frontend:** React (con Vite)
- **Base de datos:** MySQL

## Requisitos previos

Antes de instalar el proyecto, asegúrate de tener:

- PHP 8.2 o superior
- Composer
- Node.js 18 o superior (incluye npm)
- MySQL (se puede usar XAMPP, que incluye PHP + MySQL)
- Git

## Estructura del proyecto
Desarrollo/
├── prueba-productos/ # Backend (Laravel)
├── prueba-productos-frontend/ # Frontend (React)
└── productos_prueba.xlsx # Excel de muestra para pruebas


## Configuración del Backend (Laravel)

1. Entra a la carpeta del backend:
cd prueba-productos


2. Instala las dependencias de PHP:
composer install


3. Copia el archivo de entorno de ejemplo y genera la clave de la aplicación:
copy .env.example .env
php artisan key:generate


4. Crea una base de datos vacía en MySQL llamada `prueba_productos` 

5. Edita el archivo `.env` y configura tus credenciales de base de datos:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prueba_productos
DB_USERNAME=root
DB_PASSWORD=


6. Ejecuta las migraciones para crear las tablas:
php artisan migrate


7. Levanta el servidor del backend:
php artisan serve

   El backend quedará disponible en: `http://127.0.0.1:8000`

## Configuración del Frontend (React)

1. En otra terminal, entra a la carpeta del frontend:
cd prueba-productos-frontend


2. Instala las dependencias:
npm install


3. Levanta el servidor de desarrollo:
npm run dev

   El frontend quedará disponible en: `http://localhost:5173`

## Cómo probar la solución

1. Con ambos servidores corriendo (backend y frontend), abre `http://localhost:5173` en el navegador.
2. Usa el archivo `productos_prueba.xlsx` (incluido en la raíz de este repositorio) 
   para probar la carga de productos. Este archivo incluye filas válidas y filas 
   con errores intencionales (caracteres no permitidos, precio no numérico, 
   cantidad no entera) para comprobar las validaciones.
3. Después de subir el archivo, los productos válidos aparecerán como tarjetas 
   y las filas con errores se listarán con su número de fila y motivo del error.
4. Haz clic en "Editar" en cualquier tarjeta para modificar sus datos. Los cambios 
   se validan nuevamente en el backend y se reflejan de inmediato sin recargar la página.

## Notas adicionales

- La validación de datos se realiza tanto en el frontend como en el backend, 
  cumpliendo con el requerimiento de no depender únicamente de la validación del cliente.
- Se restringen caracteres como comillas simples ('), comillas dobles ("), 
  y acentos graves (`, ¨) en los campos de texto.
- Si una URL de imagen es inválida o no carga, la tarjeta muestra un mensaje 
  de "Imagen no disponible" en lugar de un ícono roto.
