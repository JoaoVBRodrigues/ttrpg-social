# TTRPG Social Platform

Laravel 12 + Livewire application for organizing TTRPG campaigns, sessions, RSVP, realtime chat, dice rolls, notifications, and campaign compendium content.

## Prerequisites

- Docker Desktop with Docker Compose
- Git

Optional but useful:

- A terminal with `docker compose`
- A database client if you want to inspect MySQL manually

## Local Architecture

The local Docker stack includes:

- `app`: PHP 8.2 FPM container running Laravel
- `nginx`: web server serving the app on `http://localhost:8080`
- `mysql`: MySQL 8.4 with persistent data volume
- `redis`: Redis for cache, queue, and broadcast support
- `queue`: Laravel queue worker
- `reverb`: Laravel Reverb websocket server
- `frontend`: Vite dev server for Livewire/Blade frontend assets

## First-Time Setup

1. Clone the repository.
2. Copy the environment file.
3. Review the Docker-oriented defaults in `.env`.
4. Build the containers.
5. Start the stack.
6. Install PHP and Node dependencies inside Docker.
7. Generate the Laravel app key.
8. Run migrations and seeders.

### Copy `.env.example` to `.env`

```bash
cp .env.example .env
```

Important defaults for Docker in `.env`:

- `APP_URL=http://localhost:8080`
- `DB_HOST=mysql`
- `DB_PORT=3306`
- `DB_DATABASE=ttrpg_social`
- `DB_USERNAME=ttrpg`
- `DB_PASSWORD=ttrpg`
- `REDIS_HOST=redis`
- `QUEUE_CONNECTION=redis`
- `CACHE_STORE=redis`
- `BROADCAST_CONNECTION=reverb`
- `REVERB_HOST=reverb`
- `REVERB_PORT=8080`
- `VITE_REVERB_HOST=localhost`
- `VITE_REVERB_PORT=8081`

## Build and Start

### Build images

```bash
docker compose build
```

### Start the full stack

```bash
docker compose up -d
```

The app container and frontend container will auto-install dependencies on first boot if needed, but the explicit install commands below are still the recommended setup flow.

Docker containers inject Docker-safe database, Redis, queue, cache, and Reverb hosts automatically. That means your normal local `.env` can stay local-development oriented if you want, while Docker still talks to `mysql`, `redis`, and `reverb` correctly.

## Dependency Installation

### Composer install

```bash
docker compose exec app composer install
```

### NPM install

```bash
docker compose exec frontend npm install
```

## Laravel App Initialization

### Generate the application key

```bash
docker compose exec app php artisan key:generate
```

### Run migrations and seeders

```bash
docker compose exec app php artisan migrate --seed
```

If you want a clean reset:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

## Running the Main Services

### Application

The Laravel app is served through nginx at:

- `http://localhost:8080`

### Queue worker

The queue worker runs in the `queue` service automatically when the stack is up.

Useful commands:

```bash
docker compose logs -f queue
docker compose restart queue
docker compose exec app php artisan queue:work --verbose --tries=1 --timeout=90
```

### Websocket / Reverb

The Reverb websocket server runs in the `reverb` service automatically when the stack is up.

Endpoints:

- App HTTP: `http://localhost:8080`
- Reverb websocket port: `ws://localhost:8081`

Useful commands:

```bash
docker compose logs -f reverb
docker compose restart reverb
docker compose exec app php artisan reverb:start --host=0.0.0.0 --port=8080
```

### Vite / frontend watch

The `frontend` service runs the Vite dev server automatically.

Useful commands:

```bash
docker compose logs -f frontend
docker compose restart frontend
docker compose exec frontend npm run dev -- --host=0.0.0.0 --port=5173
docker compose exec frontend npm run build
```

Frontend dev server:

- `http://localhost:5173`

## Running Tests

Run the full Laravel test suite inside Docker:

```bash
docker compose exec app php artisan test
```

Run a specific test file:

```bash
docker compose exec app php artisan test --filter=CampaignNotificationTest
```

## Useful Laravel Commands Inside Docker

```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:list
docker compose exec app php artisan db:seed
docker compose exec app php artisan tinker
```

## Accessing the Application

After setup, open:

- `http://localhost:8080`

Supporting services:

- MySQL host port: `localhost:33060`
- Redis host port: `localhost:63790`
- Reverb websocket port: `localhost:8081`
- Vite dev server: `localhost:5173`

## Validating Main Modules

Use the seeded app with locally created users to validate the main flows:

- register and verify login/logout flow
- edit a user profile and preferences
- create a campaign
- invite or request campaign membership
- schedule a session and answer RSVP
- send chat messages
- execute dice rolls
- confirm notifications/jobs are being created
- create and edit campaign compendium entries

You can inspect running notifications and jobs with:

```bash
docker compose logs -f queue
docker compose logs -f reverb
docker compose exec app php artisan pail --timeout=0
```

## Manual QA Checklist

- Authentication works: register, log in, log out, reset password flow loads.
- Profile update works: change profile fields and notification preferences.
- Campaign creation works: create a campaign and confirm owner membership is created.
- Session RSVP works: create a session as GM and respond as an active member.
- Chat works: send a campaign message and see it persist.
- Dice roller works: submit a valid roll and confirm a dice message plus persisted roll history.
- Notifications/jobs work: invite a member, schedule/update a session, mark a message as important, and watch the queue logs.
- Compendium pages work: create, update, and delete campaign reference entries.

## Troubleshooting

### Rebuild everything

```bash
docker compose down
docker compose build --no-cache
docker compose up -d
```

### Remove containers and volumes

```bash
docker compose down -v
```

### If environment values change

```bash
docker compose exec app php artisan config:clear
docker compose up -d --force-recreate app queue reverb nginx frontend
```

### If you generated a new `APP_KEY`

Because Docker injects env vars when containers are created, changing `APP_KEY` in `.env` requires container recreation:

```bash
docker compose up -d --force-recreate app queue reverb nginx
```

### If Docker is still using localhost-style database or Redis hosts

Check the live container env:

```bash
docker compose exec app env
```

The app container should resolve:

- `DB_HOST=mysql`
- `REDIS_HOST=redis`
- `QUEUE_CONNECTION=redis`
- `CACHE_STORE=redis`
- `BROADCAST_CONNECTION=reverb`

## Exact Runbook

Run these commands in order from the repository root:

```bash
cp .env.example .env
docker compose build
docker compose up -d
docker compose exec app composer install
docker compose exec frontend npm install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan test
```

Then open:

```text
http://localhost:8080
```
