# Proyecto Base Laravel Multi-Tenancy con FilamentPHP

Este es un proyecto base en Laravel que implementa Multi-Tenancy utilizando [FilamentPHP](https://filamentphp.com/). Incluye las configuraciones esenciales para gestionar múltiples inquilinos (tenants) y el paquete [Shield](https://filamentphp.com/plugins/shield) para la gestión de roles y permisos.

## Características

-   **Laravel**: Framework PHP moderno y robusto.
-   **FilamentPHP**: Panel de administración flexible y potente.
-   **Multi-Tenancy**: Soporte para múltiples inquilinos en una sola instancia de la aplicación.
-   **Filament Shield**: Control de acceso basado en roles y permisos.

## Requisitos previos

Asegúrate de tener instalado lo siguiente antes de comenzar:

-   PHP 8.3+
-   Composer
-   MySQL o PostgreSQL
-   Node.js y npm (opcional, para compilación de assets)

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/tu-repositorio.git
cd tu-repositorio
```

### 2. Instalar dependencias

```bash
composer install
npm install && npm run build
```

### 3. Configurar el entorno

Copia el archivo de entorno y configura las variables necesarias:

```bash
cp .env.example .env
```

Edita el archivo `.env` y actualiza las credenciales de la base de datos.

### 4. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 5. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

## Contribuciones

Si deseas contribuir a este proyecto, por favor, crea un fork y envía un pull request con tus mejoras.

## Licencia

Este proyecto está bajo la licencia MIT.
