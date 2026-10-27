# Asistente de Cartera

Aplicación web en Laravel que permite consultar una cartera de cobranzas en lenguaje natural. El usuario escribe una pregunta en castellano ("¿qué deudores de Catamarca tienen más de 200 días de mora?") y la aplicación la resuelve consultando la base de datos mediante **tool calling** con la API de Claude.

Proyecto de práctica: trabaja sobre datos ficticios generados con Faker.

## Cómo funciona

El modelo no accede a la base de datos. La aplicación le describe las herramientas disponibles y él decide cuál usar y con qué parámetros; el código ejecuta la consulta y le devuelve el resultado.

1. Se envía la pregunta del usuario junto con la definición de las herramientas (`tools`).
2. Si el modelo necesita datos, responde con `stop_reason: "tool_use"` indicando la herramienta y los parámetros.
3. La aplicación ejecuta la consulta y agrega el resultado al historial como `tool_result`.
4. Se repite la llamada con el historial completo hasta que el modelo responde en lenguaje natural (`end_turn`).

El ciclo tiene un tope de vueltas para evitar bucles infinitos.

### Herramientas disponibles

| Herramienta | Descripción |
|---|---|
| `buscar_deudores` | Filtra deudores por provincia, días de mora mínimos y estado de la cuenta. Todos los filtros son opcionales. |

## Stack

- PHP 8.4 · Laravel 13
- MySQL 8.4
- Redis
- Docker / Laravel Sail
- API de Claude (modelo Haiku)

## Estructura relevante

```
app/
├── Services/
│   ├── ClaudeClient.php     Cliente HTTP de la API y definición de herramientas
│   └── AgenteCartera.php    Ciclo de tool calling
├── Http/Controllers/
│   └── ConsultarBaseController.php
└── Models/
    ├── Deudor.php
    └── Pago.php
```

## Entorno de desarrollo

Requiere Docker.

```bash
git clone https://github.com/dleon14g/asistente-cartera.git
cd asistente-cartera
cp .env.example .env
```

Completar en `.env`:

```
ANTHROPIC_API_KEY=
ANTHROPIC_MODEL=claude-haiku-4-5-20251001
APP_PORT=8080
FORWARD_DB_PORT=3307
```

Los puertos 8080 y 3307 evitan conflictos con un Apache o MySQL ya instalados en la máquina.

```bash
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

Disponible en `http://localhost:8080`.

## Rutas

| Ruta | Descripción |
|---|---|
| `/preguntar?pregunta=...` | Consulta en lenguaje natural |
| `/consultar-base?provincia=...&moraMinima=...&estado=...` | Consulta directa con filtros |
| `/consultar-base/formulario` | Formulario de consulta directa |
| `/prueba-claude` | Llamada simple a la API, sin herramientas |
| `/prueba-tools` | Devuelve el JSON crudo con la herramienta que elige el modelo |

## Imagen Docker

El `Dockerfile` construye una imagen con el código y las dependencias de producción incluidas. No contiene el archivo `.env`: la configuración se inyecta al ejecutar el contenedor.

```bash
docker build -t asistente-cartera .
docker run --rm --name cartera-app \
  --network asistente-cartera_sail \
  -p 8001:8000 \
  --env-file .env.docker \
  asistente-cartera
```

El `.env.docker` no se versiona. Necesita al menos:

```
APP_KEY=
ANTHROPIC_API_KEY=
ANTHROPIC_MODEL=
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

`DB_HOST=mysql` es el nombre del servicio dentro de la red de Docker, y el puerto es el interno (3306), no el que Sail expone hacia el host.

El `COPY` del código va después del `composer install` para aprovechar el caché de capas: mientras no cambien `composer.json` ni `composer.lock`, las dependencias no se reinstalan en cada build.

## Pendientes

- Parámetros `deuda_minima` y `deuda_maxima` en `buscar_deudores`.
- Herramienta de agregados (totales y promedios por provincia).
- Persistir el historial de la conversación entre requests.
- Suite de evaluación para verificar la selección de parámetros del modelo.
- El contenedor usa `php artisan serve`, que es un servidor de desarrollo. Un despliegue real debería usar nginx con PHP-FPM.
