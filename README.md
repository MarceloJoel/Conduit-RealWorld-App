# Conduit — RealWorld App (PHP + Angular + Docker)

Implementación del proyecto **RealWorld** ("la madre de todas las apps de demostración") usando un backend en **PHP (Slim Framework)** y un frontend en **Angular**, ambos orquestados con **Docker Compose**. Incluye una funcionalidad extendida de **Habilidades (Skills)** en el perfil de usuario como ejercicio de relaciones 1-a-muchos con Eloquent ORM.

## 🏗️ Arquitectura

```
┌─────────────────────┐        HTTP/JSON        ┌──────────────────────┐
│   Frontend Angular   │ ───────────────────────▶│   Backend PHP (Slim)  │
│   (puerto 4200)       │◀─────────────────────── │   (puerto 8000)        │
└─────────────────────┘                          └──────────┬───────────┘
                                                              │
                                                              ▼
                                                   ┌──────────────────────┐
                                                   │   MySQL 8.0            │
                                                   │   (puerto 33066)       │
                                                   └──────────────────────┘
```

- **Frontend:** [angular-realworld-example-app](https://github.com/gothinkster/angular-realworld-example-app) — SPA en Angular que consume la API REST.
- **Backend:** [slim-php-realworld-example-app](https://github.com/gothinkster/slim-php-realworld-example-app) — API REST en PHP (Slim 3), autenticación JWT, migraciones con Phinx, ORM Eloquent.
- **Base de datos:** MySQL 8.0, corriendo en un contenedor separado.

## 📋 Requisitos previos

- [Docker](https://www.docker.com/) y Docker Compose instalados.
- Puertos libres: `4200` (Angular), `8000` (API PHP), `33066` (MySQL).
- Aplicaciones utilizadas: VS code, docker desktop, postman, DBeaver.

## 🚀 Instalación y despliegue

### 1. Backend (PHP)

```bash
cd slim-php-realworld-example-app
cp .env.example .env   # o crea el .env manualmente (ver sección Variables de Entorno)
docker-compose up -d --build
docker-compose run --rm --entrypoint sh app -c "composer install --no-interaction --optimize-autoloader --ignore-platform-reqs"
docker-compose up -d app
```

Verifica que ambos servicios estén arriba:

```bash
docker-compose ps
```

Deberías ver `app` (puerto 8000) y `db` (puerto 33066) en estado `Up`.

### 2. Frontend (Angular)

```bash
cd angular-realworld-example-app
docker-compose up -d
```

Verifica en `docker-compose ps` que `angular-app` esté `Up` en el puerto `4200`.

### 3. Verificación

Abre [http://localhost:4200](http://localhost:4200) — deberías ver la landing de Conduit. Registra un usuario o inicia sesión para probar el flujo completo (Angular → PHP → MySQL).

## 🔑 Variables de Entorno (.env del backend)

```env
APP_NAME=conduit
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
JWT_SECRET=supersecretkeyyoushouldnotcommittogithub

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=conduit
DB_USERNAME=conduit_user
DB_PASSWORD=secret

CORS_ALLOWED_ORIGINS=http://localhost:4200
UNSPLASH=tu_clave_de_unsplash   # opcional
```

> ⚠️ `DB_HOST` debe ser `db` (el nombre del servicio en `docker-compose.yml`), **no** `localhost`, porque los contenedores se comunican entre sí por nombre de servicio.

## ✨ Funcionalidad añadida: Habilidades (Skills)

Se extendió el modelo de perfil de usuario con una relación **1 usuario → muchas habilidades**, como ejercicio de desarrollo full-stack:

| Capa | Cambio |
|---|---|
| **Base de datos** | Migración `CreateSkillsTable` (Phinx) — tabla `skills` con `name`, `user_id` (FK a `users`, `ON DELETE CASCADE`). |
| **Modelo (ORM)** | Nuevo modelo `Skill.php` (Eloquent) y relación `hasMany` en `User.php`. |
| **Backend** | `ProfileController` incluye `'skills' => $user->skills->pluck('name')` en la respuesta JSON del perfil. |
| **Frontend** | `Profile` model extendido con `skills?: string[]`; `profile.component.html` renderiza las habilidades como tags con `@for`. |

### Cómo probarlo

1. Conecta [DBeaver](https://dbeaver.io/) (o tu cliente MySQL favorito) a `localhost:33066`, base de datos `conduit`.
2. Inserta filas en la tabla `skills`, usando el `id` real de tu usuario:

   | name | user_id |
   |------|---------|
   | docker | 1 |
   | angular | 1 |

3. Visita `http://localhost:4200/profile/<tu_usuario>` — verás las habilidades como etiquetas debajo de la biografía.

## 🛠️ Comandos útiles

```bash
# Ver logs del backend
docker-compose logs -f app

# Ejecutar una migración nueva
docker-compose exec app vendor/bin/phinx create NombreDeLaMigracion
docker-compose exec app vendor/bin/phinx migrate

# Reiniciar solo el backend (por ejemplo tras un "Connection refused" al arrancar)
docker-compose restart app
```

## 📚 Créditos

- Basado en el proyecto oficial [RealWorld](https://github.com/gothinkster/realworld) (licencia MIT).
- Adaptado como proyecto de curso para prácticas de arquitectura full-stack (PHP + Angular + Docker).

## 📄 Licencia

MIT — mismo licenciamiento que el proyecto RealWorld original.