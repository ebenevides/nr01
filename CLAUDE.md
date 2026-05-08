# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

- **Backend:** Laravel 13 (PHP 8.3), Actions pattern
- **Frontend:** Vue 3 + Inertia.js (no separate API — controllers return `Inertia::render()`)
- **DB:** PostgreSQL with Row Level Security (RLS) for multi-tenancy
- **Infra:** Redis (cache/queue/session), S3 (evidências e documentos)

## Commands

```bash
# Backend
php artisan serve          # dev server :8000
php artisan migrate        # run migrations
php artisan migrate:fresh --seed
php artisan test           # PHPUnit
php artisan test --filter=NomeDoTest

# Frontend
npm run dev                # Vite HMR
npm run build

# Queue worker
php artisan queue:work redis

# Combined dev (backend + frontend + queue)
php artisan dev            # runs serve + vite + queue:work + pail
```

## Architecture

### Multi-tenancy (RLS)

Todas as tabelas principais têm `empresa_id uuid`. O middleware `SetTenant` seta `app.tenant_id` no PostgreSQL via `DB::statement("SET app.tenant_id = '...'")`. Cada tabela tem uma RLS policy que filtra por `current_setting('app.tenant_id')::uuid`.

### Camadas Laravel

```
app/Http/Controllers/   → thin, sem lógica de domínio
app/Actions/            → um caso de uso por classe (ex: CriarInventarioRisco)
app/Models/             → Eloquent + scopes globais de tenant
app/Services/           → integrações externas (S3, PDF, etc.)
app/Jobs/               → filas Redis
```

### Frontend

```
resources/js/Pages/       → páginas Inertia (uma por rota)
resources/js/Components/  → componentes reutilizáveis
resources/js/Layouts/     → AuthenticatedLayout, GuestLayout
resources/js/composables/ → lógica Vue reutilizável
```

### Domínio NR-01

Ciclo: `Empresa → Estabelecimento → GHE → InventarioRisco → PlanoAcao → Evidencia`

Documentos gerados: PGR e LTCAT (PDF via job assíncrono, armazenado no S3).

## Configuração local

1. Criar DB: `createdb nr01`
2. Copiar `.env.example` → `.env` e ajustar `DB_PASSWORD`
3. `composer install && npm install`
4. `php artisan migrate --seed`
5. `php artisan dev`

## Testes

Testes usam banco separado. Configurar `DB_DATABASE=nr01_test` em `phpunit.xml` ou `.env.testing`.

## Módulos implementados

| Módulo | Controller | Pages Vue |
|---|---|---|
| Dashboard | `DashboardController` | `Pages/Dashboard.vue` |
| Inventário de Riscos | `InventarioRiscoController` | `Pages/InventarioRisco/` |
| Planos de Ação + Evidências | `PlanoAcaoController` | `Pages/PlanoAcao/` |
| Treinamentos | `TreinamentoController` | `Pages/Treinamento/` |
| Documentos PDF (PGR/LTCAT) | `DocumentoController` | `Pages/Documento/` |

## Auditoria

Trait `App\Models\Concerns\Auditable` — aplicar em qualquer model para logar created/updated/deleted em `audit_logs`. Campos ignorados: `updated_at`, `created_at`, `deleted_at`, `password`, `remember_token`.

## Geração de PDF

Job assíncrono `GerarDocumentoJob` na queue `documentos`. Templates Blade em `resources/views/pdf/`. Resultado salvo no S3 com presigned URL (60 min).

## Enums disponíveis

`GrupoRisco`, `StatusInventario`, `StatusAcao`, `TipoControle`, `TipoTreinamento`, `TipoDocumento` — todos em `App\Enums\`.
