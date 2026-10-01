# Santa Rita Conectada

## Plataforma de Gestión Comunitaria

Sistema integral de gestión comunitaria diseñado para optimizar la comunicación, administración de socios y postulación a fondos públicos de una Junta de Vecinos.

Este repositorio contiene el **Backend (API REST)** y el **Portal Web Administrativo**.

El proyecto forma parte de una arquitectura distribuida que incluye una **Aplicación Móvil desarrollada en Kotlin**.

---

## Arquitectura y Tecnologías

El sistema sigue un modelo cliente-servidor desacoplado, orquestado bajo contenedores Docker para asegurar consistencia entre desarrollo y producción.

| Componente | Tecnología |
|---|---|
| Backend Framework | Laravel 12 (PHP 8.3) |
| Base de Datos | MySQL 8 |
| Contenerización | Docker & Laravel Sail |
| Notificaciones Asíncronas | Firebase Cloud Messaging (FCM) |
| Autenticación | JWT / Laravel Sanctum |
| Control de acceso | RBAC (Role-Based Access Control) |

---

## Funcionalidades Principales

### API RESTful

Endpoints centralizados para el consumo desde la aplicación móvil y el frontend web.

### Sistema de Roles (RBAC)

Jerarquía estricta entre:

- Directiva
- Socios
- Vecinos

El sistema protege las rutas y vistas según los permisos correspondientes a cada rol.

### Módulo de Notificaciones

Sistema de alertas push en tiempo real mediante **Firebase Cloud Messaging (FCM)** para el envío de anuncios y comunicaciones urgentes.

### Gestión Documental y Subsidios

Administración de archivos y seguimiento de estados para las postulaciones a proyectos y fondos públicos.

---

## Despliegue Local

El proyecto está contenerizado con **Laravel Sail**. Para levantarlo en tu entorno local no es necesario instalar las dependencias de PHP directamente en el sistema.

### 1. Clonar el repositorio

```bash
git clone https://github.com/ghernandezsotodev/santa-rita-conectada.git
cd santa-rita-conectada
```

### 2. Configurar las variables de entorno

Copia el archivo de configuración de ejemplo:

```bash
cp .env.example .env
```

### 3. Instalar las dependencias

Las dependencias se instalan utilizando un contenedor temporal:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

### 4. Levantar la infraestructura

Inicia los contenedores correspondientes a la API y la base de datos:

```bash
./vendor/bin/sail up -d
```

### 5. Preparar la base de datos

Genera la clave de aplicación y ejecuta las migraciones junto con los datos iniciales:

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

---

## Componentes del Sistema

La plataforma forma parte de una arquitectura distribuida compuesta por:

- **Backend:** API REST desarrollada con Laravel 12.
- **Portal Web Administrativo:** interfaz para la gestión de la Junta de Vecinos.
- **Aplicación Móvil:** aplicación desarrollada en Kotlin.
- **Base de Datos:** MySQL 8.
- **Servicio de Notificaciones:** Firebase Cloud Messaging (FCM).
- **Infraestructura:** Docker y Laravel Sail.

---

## Infraestructura

La aplicación utiliza **Docker** y **Laravel Sail** para proporcionar un entorno de desarrollo consistente y facilitar la ejecución de los distintos componentes del sistema.

La infraestructura contempla principalmente:

- API Laravel.
- Base de datos MySQL.
- Servicios necesarios para la ejecución del backend.

---

## Seguridad y Control de Acceso

El sistema implementa autenticación mediante **JWT / Laravel Sanctum** y un esquema de **control de acceso basado en roles (RBAC)**.

Los permisos se organizan de acuerdo con los distintos perfiles de usuario de la Junta de Vecinos, permitiendo proteger rutas y vistas según el rol correspondiente.

---

## Arquitectura Distribuida

El backend funciona como punto central de comunicación para los distintos clientes de la plataforma.

| Componente | Comunicación | Dependencias |
|---|---|---|
| Aplicación Móvil | API REST | Backend Laravel 12 |
| Portal Web Administrativo | API REST | Backend Laravel 12 |
| Backend | API REST | MySQL 8, FCM, Gestión Documental y Subsidios |
| Base de Datos | Conexión interna | MySQL 8 |
| Notificaciones | Firebase Cloud Messaging | FCM |
| Gestión Documental y Subsidios | Gestión interna | Backend Laravel 12 |

### Flujo de comunicación

```text
Aplicación Móvil (Kotlin)
          |
          | API REST
          v
Portal Web Administrativo -----> Backend Laravel 12
                                      |
                 +--------------------+--------------------+
                 |                    |                    |
                 v                    v                    v
              MySQL 8               FCM          Gestión Documental
           Base de Datos      Notificaciones        y Subsidios
```

---

## Requisitos

Para ejecutar el proyecto localmente se requiere:

- Docker
- Git
- Un sistema compatible con Docker y Laravel Sail

No es necesario instalar PHP o Composer directamente en el entorno local, ya que las dependencias PHP se pueden instalar mediante el contenedor:

```text
laravelsail/php83-composer
```

---

## Resumen

Santa Rita Conectada proporciona una plataforma centralizada para la gestión de una Junta de Vecinos, integrando:

- Comunicación comunitaria.
- Administración de socios y vecinos.
- Control de acceso basado en roles.
- Notificaciones push.
- Gestión documental.
- Seguimiento de postulaciones a proyectos y subsidios.
- API REST para integración con clientes web y móviles.
- Infraestructura contenerizada mediante Docker.

