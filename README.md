# Santa Rita Conectada – Backend

Plataforma web desarrollada en Laravel para la gestión de comunicados y subsidios comunitarios. Forma parte de una solución mixta compuesta por una API REST y una aplicación móvil Android.

## Stack Tecnológico

- Laravel 12
- PHP 8.3
- MySQL
- Laravel Sail (Docker)
- Firebase Cloud Messaging (FCM)

## Funcionalidades

- Gestión de usuarios con roles
- Publicación de comunicados
- Envío de notificaciones push
- Gestión de subsidios
- Panel administrativo

## Instalación

1. Clonar el repositorio
2. Copiar el archivo `.env.example` a `.env`
3. Ejecutar:

```bash
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
