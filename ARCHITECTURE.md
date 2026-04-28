# ARCHITECTURE.md

## 1. Overview

ISP Billing Software is a **SaaS multi-tenant billing platform** for Internet Service Providers in Bangladesh. A single Laravel application serves two distinct user spaces:

- **Platform Admin** — manages ISP companies (tenants), subscription plans, and platform configuration.
- **Tenant App** — per-ISP operations: customers, packages, OLT devices, invoices, payments, staff, and reports.

All tenants share a single database and a single application codebase; isolation is enforced at the query layer via a `tenant_id` column on every tenant-owned table.

---

## 2. Multi-Tenancy Model

### Strategy: Shared Database, Tenant-Scoped Queries

Every tenant-owned table carries a non-nullable `tenant_id` FK referencing `tenants.id`.

### Key Components

| Component | Location | Responsibility |
|-----------|----------|----------------|
| `TenantScope` | `app/Scopes/TenantScope.php` | Global Eloquent scope; appends `WHERE tenant_id = ?` to all queries on scoped models |
| `BelongsToTenant` | `app/Traits/BelongsToTenant.php` | Trait for tenant-owned models; boots `TenantScope` and auto-sets `tenant_id` on `creating` |
| `ResolveTenant` | `app/Http/Middleware/ResolveTenant.php` | Reads `tenant_id` from the authenticated user; binds the resolved `Tenant` model into the IoC container as `currentTenant` |
| `TenantMiddleware` | `app/Http/Middleware/TenantMiddleware.php` | Guards tenant routes; rejects requests where the user has no `tenant_id` or the tenant is inactive |
| `PlatformMiddleware` | `app/Http/Middleware/PlatformMiddleware.php` | Guards `/platform/*` routes; passes only when `auth()->user()->tenant_id === null` (platform admin) |

### Platform Admin Identity

A user with `tenant_id IS NULL` is a **platform admin**. No special role column is needed; the null FK is the discriminator.

---

## 3. Request Lifecycle

```
Browser
  └─► Laravel HTTP Kernel
        └─► auth middleware  (redirect to /login if unauthenticated)
              ├─► PlatformMiddleware  →  Platform controllers  →  platform Blade views
              └─► TenantMiddleware
                    └─► ResolveTenant  (binds currentTenant)
                          └─► Tenant controllers
                                └─► Eloquent models (TenantScope auto-applied)
                                      └─► Tenant Blade views
```

---

## 4. Module Map

| Module | Controller(s) | Key Models | Route Prefix |
|---|---|---|---|
| Auth | `AuthenticatedSessionController` | `User` | `/login`, `/logout` |
| Platform Dashboard | `Platform\DashboardController` | `Tenant`, `SubscriptionPlan` | `/platform` |
| Tenants | `Platform\TenantController` | `Tenant`, `TenantSubscription` | `/platform/tenants` |
| Plans | `Platform\PlanController` | `SubscriptionPlan` | `/platform/plans` |
| Customers | `CustomerController` | `Customer`, `StatusHistory` | `/customers` |
| Packages | `PackageController` | `Package` | `/packages` |
| Areas & POPs | `AreaController`, `PopController` | `Area`, `Pop` | `/areas`, `/pops` |
| Services | `CustomerServiceController` | `CustomerService` | `/services` |
| OLT Devices | `OltDeviceController` | `OltDevice`, `SyncedOnu`, `OltActionLog` | `/olt-devices` |
| Invoices | `InvoiceController` | `Invoice`, `InvoiceItem` | `/invoices` |
| Payments | `PaymentController` | `Payment`, `PaymentAllocation` | `/payments` |
| Dues | `DueController` | `Invoice`, `Customer` | `/dues` |
| Reports | `ReportController` | *(aggregates)* | `/reports` |
| Staff | `StaffController` | `User` | `/staff` |
| SMS | `SmsController` | `SmsTemplate`, `SmsLog` | `/sms` |
| Settings | `SettingController` | `Setting` | `/settings` |
| Audit Logs | `ActivityLogController` | `ActivityLog` | `/activity-logs` |

---

## 5. Core Services

### `InvoiceGenerationService` (`app/Services/InvoiceGenerationService.php`)
- Generates invoices in bulk (all active customers) or individually.
- Applies package-level discounts.
- Carries forward previous unpaid dues as a line item on the new invoice.

### `PaymentAllocationService` (`app/Services/PaymentAllocationService.php`)
- Allocates incoming payments to invoices using **FIFO** (oldest unpaid invoice first).
- Supports payment reversal with re-allocation.
- Persists allocation records in `payment_allocations`.

### `CustomerBalanceService` (`app/Services/CustomerBalanceService.php`)
- Computes per-customer financial summary: `total_invoiced`, `total_paid`, `total_due`.
- Used by due reports, customer detail pages, and invoice generation carry-forward logic.

---

## 6. Database Schema Summary

| Table | Description |
|---|---|
| `users` | Platform admins and tenant staff; `tenant_id` NULL = platform admin |
| `tenants` | ISP companies; name, domain, status, contact info |
| `subscription_plans` | Platform-level SaaS plans (monthly fee, customer limit, etc.) |
| `tenant_subscriptions` | Active/historical plan subscriptions per tenant |
| `tenant_subscription_payments` | Payments made by a tenant for their SaaS subscription |
| `areas` | Geographic service areas belonging to a tenant |
| `pops` | Points of Presence (network nodes) within an area |
| `packages` | Internet packages offered by a tenant (speed, price, type) |
| `customers` | ISP end-customers; status, address, assigned package |
| `customer_services` | Active service assignments linking customer ↔ package ↔ POP |
| `olt_devices` | OLT hardware records (IP, brand, credentials) |
| `synced_onus` | ONUs discovered/synced from OLT devices |
| `olt_action_logs` | Audit trail of OLT provisioning actions |
| `invoices` | Monthly billing invoices per customer; status, amounts |
| `invoice_items` | Line items within an invoice (service charge, discount, due carry-forward) |
| `payments` | Payment receipts; amount, method, collected by |
| `payment_allocations` | FIFO allocation records mapping payments to invoices |
| `status_histories` | Customer status change log (active → suspended → active, etc.) |
| `sms_templates` | Configurable SMS message templates per event type |
| `sms_logs` | Outbound SMS delivery records |
| `settings` | Key-value store for per-tenant configuration |
| `activity_logs` | Audit trail of user actions across the application |
| `permissions` / `roles` / pivot tables | spatie/laravel-permission RBAC tables |

---

## 7. RBAC

Authorization is provided by **spatie/laravel-permission**.

| Role | Space | Typical Capabilities |
|---|---|---|
| *(platform admin)* | Platform | Full platform control; identified by `tenant_id IS NULL`, not a named role |
| `tenant_admin` | Tenant | Full tenant control: staff, settings, billing |
| `accounts_manager` | Tenant | Invoices, payments, due reports |
| `billing_officer` | Tenant | Collect payments, view invoices |
| `support_agent` | Tenant | View customers, log status changes |
| `technician` | Tenant | OLT devices, ONU provisioning |

Permissions are checked via `$this->authorize()` in controllers or `@can` in Blade.

---

## 8. Frontend Architecture

| Concern | Approach |
|---|---|
| CSS framework | Tailwind CSS loaded via CDN (no build step required in dev) |
| JS interactions | Alpine.js loaded via CDN |
| Production build | Vite (`vite.config.js`) bundles and fingerprints assets |
| Tenant layout | `resources/views/layouts/app.blade.php` |
| Platform layout | `resources/views/layouts/platform.blade.php` |
| Content slot | `@yield('content')` |
| Page title slot | `@yield('page-title')` |
| HTML `<title>` slot | `@yield('title')` |

No SPA framework is used; all rendering is server-side Blade with Alpine.js for progressive enhancement (dropdowns, tabs, modals).

---

## 9. Configuration

| Setting | Default | Notes |
|---|---|---|
| Database | SQLite (`database/database.sqlite`) | Set `DATABASE_URL=sqlite:./database/database.sqlite` in `.env` |
| MySQL | Supported | Set `DB_CONNECTION=mysql` and `DB_*` vars in `.env` |
| Timezone | `Asia/Dhaka` | Set in `config/app.php` |
| Currency | BDT (৳) | Formatted via `taka()` helper |
| Locale | `en` | English UI; amounts in Bangladeshi Taka |

---

## 10. Key Conventions

- **Soft deletes** — most models use `SoftDeletes`; records are never hard-deleted by default.
- **`taka($amount)`** — global helper in `app/helpers.php` that formats a number as BDT (e.g., `৳1,200.00`).
- **`setting($key, $default)`** — global helper that reads a value from the `settings` table (tenant-scoped via `TenantScope`).
- **`currentTenant()`** — global helper that resolves the IoC-bound `Tenant` instance set by `ResolveTenant` middleware; returns `null` in the platform space.
- **Service layer** — heavy business logic (invoice generation, payment allocation) lives in `app/Services/`, keeping controllers thin.
- **Activity logging** — user actions are recorded in `activity_logs` for audit purposes; see `ActivityLog` model.
