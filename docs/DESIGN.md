# Design Philosophy & Architectural Decisions

## Philosophy

- **Pragmatic over perfect** — ship working software for real ISP operators; avoid over-engineering for hypothetical scale
- **Convention over configuration** — follow Laravel conventions wherever possible; only deviate with explicit justification
- **Readable Blade over JS-heavy SPAs** — server-rendered HTML with Alpine.js for lightweight interactivity; no React/Vue complexity
- **Tenant isolation as a first-class concern** — every model, query, and route must respect tenant boundaries; isolation is not an afterthought

---

## Key Decisions

### Shared Database Multi-Tenancy
**Decision:** All tenants share a single database; every tenant-owned table has a `tenant_id` foreign key.

**Why:** Schema-per-tenant or database-per-tenant is operationally complex for small ISPs. A shared schema with a global scope provides sufficient isolation while keeping deployment, migrations, and backups simple. Most ISP tenants have fewer than 5,000 customers — no sharding is needed.

### Global Eloquent Scope for Tenant Isolation
**Decision:** A `TenantScope` global scope is applied automatically to all tenant-owned models via the `BelongsToTenant` trait.

**Why:** Controller-level filtering is error-prone — a developer can forget it. A global scope makes isolation automatic and impossible to accidentally skip. Explicit `withoutGlobalScopes()` must be called intentionally, making bypasses visible in code review.

### SQLite as Default
**Decision:** `DB_CONNECTION=sqlite` ships as the default; MySQL is supported for production.

**Why:** Zero configuration for local development — no Docker, no MySQL installation, no credentials. Developers clone and run. Switching to MySQL for production requires a single `.env` change.

### Tailwind CSS via CDN
**Decision:** Tailwind is loaded from CDN in development; Vite is used in production.

**Why:** Eliminates the build step during development. Developers can edit Blade templates and see changes immediately without a `npm run dev` watcher. CDN Tailwind loads the full utility set, which is acceptable for development.

### Alpine.js via CDN
**Decision:** Alpine.js is loaded from CDN.

**Why:** Provides just enough reactive interactivity (dropdowns, sidebar toggles, auto-dismiss alerts) without the overhead of a full SPA framework. Keeps JavaScript minimal and readable inline in Blade templates.

---

## Patterns Used

### Service Classes for Complex Business Logic
Business logic that spans multiple models lives in service classes, not controllers or models.

| Service | Responsibility |
|---|---|
| `InvoiceGenerationService` | Bulk invoice creation per billing cycle; per-customer service-to-invoice mapping |
| `PaymentAllocationService` | FIFO allocation of payments to oldest outstanding invoices |
| `CustomerBalanceService` | Calculates total due, advance balance, and payment history for a customer |

Controllers call services; services own transactions and business rules.

### Trait-Based Scope Injection (`BelongsToTenant`)
Models that belong to a tenant use the `BelongsToTenant` trait, which:
1. Registers `TenantScope` as a global Eloquent scope
2. Automatically sets `tenant_id` on `creating` model events
3. Ensures every query is implicitly scoped to the current tenant

### IoC Container Binding for Current Tenant
The current tenant is resolved once in `TenantMiddleware` and bound into the container:

```php
app()->instance('currentTenant', $tenant);
```

Any service class can then resolve the tenant with `app('currentTenant')` or via constructor injection, without passing it through every method signature.

---

## Anti-Patterns to Avoid

- **Don't put tenant filtering in controllers.** Never write `->where('tenant_id', $tenant->id)` in a controller. Use the global scope via `BelongsToTenant`.
- **Don't bypass `TenantScope` without explicit intent.** Only use `withoutGlobalScopes()` in platform-admin contexts or cross-tenant reporting, and document why.
- **Don't hard-code currency symbols.** Never write `'৳' . $amount` or `'BDT ' . $amount` inline. Always use the `taka()` helper — it ensures consistent formatting and makes future locale changes a one-line fix.
- **Don't put business logic in Blade views.** Views display data; services compute it.
