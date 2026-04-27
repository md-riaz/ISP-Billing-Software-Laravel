# AGENTS.md — AI Agent Orientation Guide

## What Is This Repo?

This is **ISP Billing Software**, a multi-tenant SaaS platform built with Laravel for Internet Service Providers (ISPs) in Bangladesh. A single deployment hosts multiple ISP companies (tenants); each ISP manages its own customers, packages, invoices, payments, OLT devices, and staff. A superordinate **Platform Admin** space handles tenant onboarding, subscription plans, and platform-wide settings.

---

## Tech Stack

| Layer         | Technology                                      |
|---------------|-------------------------------------------------|
| Framework     | Laravel 12, PHP 8.3                             |
| Database      | SQLite (default/dev) · MySQL (production)       |
| CSS           | Tailwind CSS via CDN (no build step in dev)     |
| JS            | Alpine.js via CDN                               |
| Build (prod)  | Vite                                            |
| Auth & RBAC   | Laravel Breeze + spatie/laravel-permission      |
| Currency      | BDT (৳), timezone Asia/Dhaka                   |

---

## Key Concepts

- **Multi-tenancy**: shared database; every tenant-owned table carries a `tenant_id` FK to `tenants`.
- **`TenantScope`** (`app/Scopes/TenantScope.php`): global Eloquent scope that automatically appends `WHERE tenant_id = ?` to all queries for tenant-owned models.
- **`BelongsToTenant`** (`app/Traits/BelongsToTenant.php`): trait applied to tenant-owned models; bootstraps `TenantScope` and auto-sets `tenant_id` on `creating`.
- **`ResolveTenant`** middleware: resolves the current tenant from the authenticated user and binds it into the IoC container as `currentTenant`.
- **`PlatformMiddleware`**: guards `/platform/*` routes; passes only when the authenticated user has `tenant_id IS NULL` (platform admin).
- **`TenantMiddleware`**: guards all tenant routes; passes only when `tenant_id IS NOT NULL` and the tenant is active.

---

## Route Overview

| Prefix        | Space          | Guarded By           |
|---------------|----------------|----------------------|
| `/login`      | Auth           | guest                |
| `/platform/*` | Platform Admin | `PlatformMiddleware` |
| `/*`          | Tenant App     | `TenantMiddleware`   |

---

## Key Files Map

| What                    | Where                                    |
|-------------------------|------------------------------------------|
| Web routes              | `routes/web.php`                         |
| Middleware              | `app/Http/Middleware/`                   |
| Tenant isolation scope  | `app/Scopes/TenantScope.php`             |
| Tenant trait            | `app/Traits/BelongsToTenant.php`         |
| Business services       | `app/Services/`                          |
| Eloquent models         | `app/Models/`                            |
| Global helpers          | `app/helpers.php`                        |
| Blade layouts           | `resources/views/layouts/`              |
| Database migrations     | `database/migrations/`                  |
| Seeders                 | `database/seeders/`                      |

---

## Documentation Pointer Table

| Topic                  | File                                          |
|------------------------|-----------------------------------------------|
| Architecture           | `ARCHITECTURE.md`                             |
| Design decisions       | `docs/DESIGN.md`                              |
| Frontend patterns      | `docs/FRONTEND.md`                            |
| Product specs          | `docs/product-specs/index.md`                 |
| DB schema              | `docs/generated/db-schema.md`                 |
| Execution plans        | `docs/exec-plans/`                            |
| Design docs index      | `docs/design-docs/index.md`                   |
| Security               | `docs/SECURITY.md`                            |
| Reliability            | `docs/RELIABILITY.md`                         |
| Quality score          | `docs/QUALITY_SCORE.md`                       |
| Tech debt tracker      | `docs/exec-plans/tech-debt-tracker.md`        |

---

## Demo Credentials

| Role            | Email                  | Password   |
|-----------------|------------------------|------------|
| Platform Admin  | admin@platform.com     | password   |
| Tenant Admin    | admin@demo.com         | password   |
| Collector       | collector@demo.com     | password   |

---

## Local Setup

```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --force
php artisan serve
```

App runs at <http://localhost:8000>.
