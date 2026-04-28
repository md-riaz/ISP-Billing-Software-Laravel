# Multi-Tenancy Design

---

## Approach Chosen

**Shared database, shared schema** with a `tenant_id` column on every tenant-owned table.

Each ISP operator (tenant) shares the same database and the same set of tables as every other tenant. Rows are isolated by `tenant_id`. The application layer enforces isolation automatically via a global Eloquent scope.

---

## Why Not Schema-Per-Tenant

| Concern | Detail |
|---|---|
| Operational complexity | Running migrations across hundreds of schemas requires tooling that adds maintenance burden with no user-visible benefit. |
| Harder migrations | Additive schema changes must be applied to every schema individually; a failed migration mid-run leaves schemas out of sync. |
| Overkill for target market | The MVP target is ISPs with 50–5,000 customers. At this scale, row-level isolation is performant and operationally simple. Schema-per-tenant can be revisited if a tenant reaches millions of rows. |

---

## How It Works

| Component | Role |
|---|---|
| `TenantScope` | A global Eloquent scope that automatically appends `WHERE tenant_id = ?` to every query on tenant-owned models. |
| `BelongsToTenant` trait | Applied to every tenant-owned model. Registers `TenantScope`, sets `tenant_id` on creation, and provides `forTenant()` scopes. |
| `ResolveTenant` middleware | Runs early in the HTTP pipeline. Identifies the current tenant from the authenticated user and binds it to the IoC container as `currentTenant`. |
| `TenantMiddleware` | Enforces that a valid, active tenant is bound before the request reaches the controller. Returns 403 if the tenant is suspended. |
| `currentTenant()` helper | Resolves the `currentTenant` binding from the IoC container. Used by `TenantScope` and anywhere the tenant context is needed. |

---

## Platform Admin Special Case

Users with `tenant_id IS NULL` are platform (super) admins. They manage tenants, subscriptions, and platform-level configuration.

- `PlatformMiddleware` enforces that the authenticated user is a platform admin; any other user is rejected.
- Platform admin controllers resolve models **without** `TenantScope` so they can view and manage all tenants' data.
- Platform admins never appear in any tenant's user list.

---

## Data Isolation Guarantees

| Layer | Mechanism |
|---|---|
| Database | Foreign key constraints on `tenant_id` referencing the `tenants` table. |
| Eloquent | `TenantScope` global scope applied via `BelongsToTenant` on every tenant-owned model. |
| Cascade delete | `ON DELETE CASCADE` on `tenant_id` FK — deleting a tenant row removes all its data automatically. |

---

## Limitations

`TenantScope` only fires when `currentTenant` is bound to the IoC container. Two cases where this does **not** happen automatically:

1. **Queued jobs** — jobs must call `setCurrentTenant($tenant)` (or use the `WithTenant` job middleware) before performing any Eloquent queries.
2. **Console commands / Artisan** — commands that operate on tenant data must resolve and set the tenant explicitly at the start of the command.

Forgetting this in a queued job is the single most common source of cross-tenant data leakage. Code review must check for it.

---

## Tables with Tenant Isolation

Every row in the following tables carries a `tenant_id` column and is covered by `TenantScope`:

- `areas`
- `pops`
- `packages`
- `customers`
- `customer_services`
- `olt_devices`
- `synced_onus`
- `invoices`
- `invoice_items`
- `payments`
- `payment_allocations`
- `status_histories`
- `sms_templates`
- `sms_logs`
- `settings`
- `activity_logs`
