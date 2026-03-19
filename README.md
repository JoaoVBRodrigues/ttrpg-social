# TTRPG Social Platform

Laravel 12 + Livewire application for organizing TTRPG campaigns, sessions, RSVP, realtime chat, dice rolls, notifications, and campaign compendium content.

## English

### Overview

Local development is designed around Docker. The stack includes:

- `app`: PHP 8.2 FPM running Laravel
- `nginx`: web server on `http://localhost:8080`
- `mysql`: MySQL 8.4 with persistent data volume
- `redis`: cache, queue, and broadcast support
- `queue`: Laravel queue worker
- `reverb`: Laravel Reverb websocket server
- `frontend`: Vite dev server

### Prerequisites

- Docker Desktop with Docker Compose
- Git

Optional:

- A terminal with `docker compose`
- A database client for MySQL inspection

### First-time setup

1. Clone the repository.
2. Copy `.env.example` to `.env`.
3. Build the Docker images.
4. Start the stack.
5. Install Composer and NPM dependencies inside Docker.
6. Generate the Laravel app key.
7. Run migrations and seeders.

```bash
cp .env.example .env
docker compose build
docker compose up -d --force-recreate
docker compose exec app composer install
docker compose exec frontend npm install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Important Docker defaults in `.env`:

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

### Main commands

Run tests:

```bash
docker compose exec app php artisan test
```

Reset the local QA database:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Clear Laravel caches:

```bash
docker compose exec app php artisan optimize:clear
```

Watch Vite logs:

```bash
docker compose logs -f frontend
```

Watch queue logs:

```bash
docker compose logs -f queue
```

Watch Reverb logs:

```bash
docker compose logs -f reverb
```

### URLs

- App: `http://localhost:8080`
- Login: `http://localhost:8080/login`
- Public campaigns: `http://localhost:8080/campaigns`
- My Campaigns: `http://localhost:8080/my-campaigns`
- Create campaign: `http://localhost:8080/campaigns/create`
- Seeded campaign: `http://localhost:8080/campaigns/echoes-below-brightwater`
- Vite dev server: `http://localhost:5173`
- Reverb websocket: `ws://localhost:8081`

### Stable QA users

After `php artisan migrate --seed` or `php artisan migrate:fresh --seed`, these deterministic accounts are recreated:

- GM / Narrator
  - Email: `gm.qa@example.com`
  - Username: `maravale_gm`
  - Password: `password`
- Player
  - Email: `player.qa@example.com`
  - Username: `leo_player`
  - Password: `password`

Stable seeded campaign:

- `Echoes Below Brightwater`
- URL: `http://localhost:8080/campaigns/echoes-below-brightwater`

Seeded state:

- The GM user owns the public seeded campaign.
- The player user does not initially belong to that campaign.
- The player user can browse the campaign and request to join it.

### Manual QA checklist

- Log in as the GM and as the player.
- Switch language between English and Portuguese in the header.
- Switch between light and dark mode in the header.
- Open the homepage and confirm the TTRPG landing page is displayed.
- Browse public campaigns and open the seeded campaign.
- Request to join the seeded campaign as `player.qa@example.com`.
- Log in as `gm.qa@example.com` and approve or deny the pending join request.
- Confirm `My Campaigns` shows owned and active-member campaigns correctly.
- Update the profile page and notification preferences.
- Create another campaign.
- Schedule a session and answer RSVP.
- Send chat messages and confirm they appear without a full-page reload.
- Execute a dice roll.
- Create, update, and delete compendium entries.

### Troubleshooting

Recreate the stack:

```bash
docker compose up -d --force-recreate
```

Full rebuild:

```bash
docker compose down
docker compose build --no-cache
docker compose up -d --force-recreate
```

Recreate everything including volumes:

```bash
docker compose down -v
docker compose build
docker compose up -d --force-recreate
```

If you changed `.env` values:

```bash
docker compose exec app php artisan config:clear
docker compose up -d --force-recreate app queue reverb nginx frontend
```

If Laravel views or cached config look stale:

```bash
docker compose exec app php artisan optimize:clear
```

If the app stops responding through nginx:

```bash
docker compose ps
docker compose logs --tail=100 app
docker compose logs --tail=100 nginx
```

## Português

### Visão geral

O desenvolvimento local foi preparado para Docker. A stack inclui:

- `app`: PHP 8.2 FPM executando o Laravel
- `nginx`: servidor web em `http://localhost:8080`
- `mysql`: MySQL 8.4 com volume persistente
- `redis`: suporte a cache, filas e broadcast
- `queue`: worker de filas do Laravel
- `reverb`: servidor websocket Laravel Reverb
- `frontend`: servidor do Vite

### Pré-requisitos

- Docker Desktop com Docker Compose
- Git

Opcional:

- Um terminal com `docker compose`
- Um cliente de banco para inspecionar o MySQL

### Primeira configuração

1. Clone o repositório.
2. Copie `.env.example` para `.env`.
3. Faça o build das imagens Docker.
4. Suba a stack.
5. Instale as dependências do Composer e do NPM dentro do Docker.
6. Gere a chave da aplicação Laravel.
7. Execute migrations e seeders.

```bash
cp .env.example .env
docker compose build
docker compose up -d --force-recreate
docker compose exec app composer install
docker compose exec frontend npm install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Principais valores Docker no `.env`:

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

### Comandos principais

Rodar os testes:

```bash
docker compose exec app php artisan test
```

Resetar o banco local de QA:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Limpar caches do Laravel:

```bash
docker compose exec app php artisan optimize:clear
```

Ver logs do Vite:

```bash
docker compose logs -f frontend
```

Ver logs da fila:

```bash
docker compose logs -f queue
```

Ver logs do Reverb:

```bash
docker compose logs -f reverb
```

### URLs

- App: `http://localhost:8080`
- Login: `http://localhost:8080/login`
- Campanhas públicas: `http://localhost:8080/campaigns`
- Minhas Campanhas: `http://localhost:8080/my-campaigns`
- Criar campanha: `http://localhost:8080/campaigns/create`
- Campanha semeada: `http://localhost:8080/campaigns/echoes-below-brightwater`
- Vite: `http://localhost:5173`
- Reverb websocket: `ws://localhost:8081`

### Usuários estáveis para QA

Depois de `php artisan migrate --seed` ou `php artisan migrate:fresh --seed`, estas contas determinísticas são recriadas:

- GM / Narrador
  - E-mail: `gm.qa@example.com`
  - Username: `maravale_gm`
  - Senha: `password`
- Jogador
  - E-mail: `player.qa@example.com`
  - Username: `leo_player`
  - Senha: `password`

Campanha semeada:

- `Echoes Below Brightwater`
- URL: `http://localhost:8080/campaigns/echoes-below-brightwater`

Estado inicial semeado:

- O usuário GM já é dono da campanha pública semeada.
- O usuário jogador não pertence inicialmente a essa campanha.
- O jogador pode navegar até a campanha e solicitar entrada.

### Checklist manual de QA

- Fazer login com o GM e com o jogador.
- Alternar o idioma entre inglês e português no cabeçalho.
- Alternar entre modo claro e escuro no cabeçalho.
- Abrir a homepage e confirmar a landing page temática de TTRPG.
- Navegar pelas campanhas públicas e abrir a campanha semeada.
- Solicitar entrada na campanha com `player.qa@example.com`.
- Entrar com `gm.qa@example.com` e aprovar ou negar a solicitação pendente.
- Confirmar que `My Campaigns` mostra corretamente campanhas próprias e campanhas nas quais o usuário é membro ativo.
- Atualizar perfil e preferências de notificação.
- Criar outra campanha.
- Agendar uma sessão e responder o RSVP.
- Enviar mensagens no chat e confirmar que aparecem sem recarregar a página inteira.
- Fazer uma rolagem de dados.
- Criar, editar e excluir entradas do compêndio.

### Solução de problemas

Recriar a stack:

```bash
docker compose up -d --force-recreate
```

Rebuild completo:

```bash
docker compose down
docker compose build --no-cache
docker compose up -d --force-recreate
```

Recriar tudo, incluindo volumes:

```bash
docker compose down -v
docker compose build
docker compose up -d --force-recreate
```

Se você alterou valores no `.env`:

```bash
docker compose exec app php artisan config:clear
docker compose up -d --force-recreate app queue reverb nginx frontend
```

Se o cache do Laravel ou as views parecerem desatualizados:

```bash
docker compose exec app php artisan optimize:clear
```

Se a aplicação parar de responder pelo nginx:

```bash
docker compose ps
docker compose logs --tail=100 app
docker compose logs --tail=100 nginx
```
