# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Synerise Sylius Integration Plugin (`synerise/sylius-integration-plugin`) — a Sylius 2.x plugin that integrates the Synerise marketing platform. It provides event tracking (cart, customer, order, review events), data synchronization (customers, orders, products), and admin configuration UI.

**Stack:** PHP 8.2+, Sylius 2.0, Symfony 6.4/7.1, Doctrine ORM, Symfony Messenger, Stimulus (JS), Webpack Encore. Uses `synerise/php-sdk` for API communication.

## Development Environment

All commands run inside Docker containers via `make` targets. First-time setup:

```bash
make init          # Copies compose.override, installs composer + node deps, starts containers
make database-init # Creates DB and runs migrations
make load-fixtures # Loads Sylius test fixtures
```

Container management: `make run` / `make up` / `make down` / `make clean` (down -v)

Shell access: `make php-shell` / `make node-shell`

## Build & Quality Commands

```bash
make phpstan   # Static analysis: vendor/bin/phpstan analyse -c phpstan.neon -l max src/
make ecs       # Coding standard: vendor/bin/ecs check src (sylius-labs/coding-standard)
make phpunit   # Unit/integration tests: vendor/bin/phpunit
make behat     # Acceptance tests: vendor/bin/behat
```

To run a single PHPUnit test inside the container:
```bash
docker compose exec php vendor/bin/phpunit tests/Unit/Path/To/TestFile.php
```

To run a single Behat scenario:
```bash
docker compose exec php vendor/bin/behat features/path/to/file.feature
```

PHPSpec is also configured (`phpspec.yml.dist`, namespace `Synerise\SyliusIntegrationPlugin`).

Assets: `make node-watch` for live rebuilding during development.

## Architecture

### Namespace & Autoloading

- `Synerise\SyliusIntegrationPlugin\` → `src/`
- `Tests\Synerise\SyliusIntegrationPlugin\` → `tests/`
- Test application kernel: `tests/Application/`

### Core Domain Entities (`src/Entity/`)

- **Workspace** — Synerise API credentials (API key, GUID, environment, auth method)
- **ChannelConfiguration** — Per-Sylius-channel settings (tracking code, enabled events, cookie config)
- **SynchronizationConfiguration** — Catalog sync mapping per channel
- **Synchronization** — Tracks sync job progress with status per entity type

All are registered as Sylius Resources in `config/config.yaml` with grids, forms, and repositories.

### Event Pipeline (`src/Event/`)

Three-layer design for Sylius domain events:

1. **Listeners** (`Event/Listener/`) — catch Sylius/Symfony events (cart add/remove, customer login/register/logout, order create/workflow, product update, review create)
2. **Processors** (`Event/Processor/`) — map the Sylius entity to an API request using request mappers, then delegate to a handler
3. **Handlers** (`Event/Handler/`) — `LiveHandler` (immediate API call) or `MessageQueueHandler` (async via Messenger). `EventHandlerResolver` picks the handler dynamically.

### API Layer (`src/Api/`)

- **Request Handlers** — concrete API call implementations (e.g., `AddedToCartRequestHandler`, `ProfileRequestHandler`, `TransactionRequestHandler`)
- **Request Mappers** — transform Sylius entities to Synerise API format (separate mappers for events vs resources)
- **`EventRequestHandlerFactory`** — resolves the correct handler by event type

### Synchronization System (`src/Synchronization/`)

Bulk sync of customers, orders, and products to Synerise:

1. `CreateSynchronizationCommand` or admin UI triggers sync
2. `SyncStartMessage` → `SyncStartMessageHandler` → `SynchronizationProcessor`
3. Processor queries a **DataProvider** for entity IDs in a date range
4. Creates batches of 20 entities, dispatches `SyncMessage` per batch
5. `SyncMessageHandler` loads entities, maps via request mappers, sends via API

Message routing configured in `config/app/messenger.yaml`.

### Service Configuration

XML-based DI in `config/services/`: `api.xml`, `event.xml`, `listener.xml`, `message_queue.xml`, `synchronization.xml`, `form.xml`, `twig.xml`, `ui.xml`. Doctrine mappings in `config/doctrine/*.orm.xml`.

### Frontend

- Stimulus controllers in `assets/` for admin/shop interactive UI
- Twig UX components (`src/Twig/`): `TrackingScriptComponent`, `OpenGraphComponent`, `RecommendationsComponent`, `SynchronizationsList`, etc.
- Admin UI hooks configured in `config/app/twig_hooks/**/*.yaml`

### Logging

Dedicated `synerise` Monolog channel logging to `synerise.log`. HTTP request/response logging via `anik/loguzz` integration (`src/Loguzz/`).

## Key Patterns

- **Sylius Resource CRUD** for all plugin entities (grids, forms, routing in `config/admin_routing.yaml`)
- **Symfony Messenger** with separate buses for sync vs events
- **Factory pattern** for handler/processor resolution (tagged service iterators)
- **Context services** provide current workspace/channel configuration
- **Coding standard:** `sylius-labs/coding-standard` via ECS — applies to `src/` and `tests/Behat/`
- **PHPStan level max** — excludes `src/DependencyInjection/Configuration.php` (causes crash)
