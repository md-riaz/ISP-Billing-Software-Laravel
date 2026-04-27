# Quality Score & Checklist

## Code Quality

| Check | Status | Notes |
|---|---|---|
| Service classes for business logic | ✅ | `InvoiceGenerationService`, `PaymentAllocationService`, `CustomerBalanceService` |
| Global scope for tenant isolation | ✅ | `TenantScope` via `BelongsToTenant` trait |
| Soft deletes on critical models | ✅ | Customers, invoices, payments use `SoftDeletes` |
| Consistent naming conventions | ✅ | Laravel conventions followed throughout |
| Unit tests for service classes | ⚠️ | Missing — `InvoiceGenerationService`, `PaymentAllocationService`, `CustomerBalanceService` are untested |
| Feature tests for critical flows | ⚠️ | Missing — no automated tests for invoice generation, payment recording, or tenant isolation |

---

## Security

| Check | Status | Notes |
|---|---|---|
| CSRF on all forms | ✅ | `@csrf` directive enforced; `VerifyCsrfToken` middleware active |
| Auth middleware on all routes | ✅ | All tenant and platform routes behind `auth` middleware |
| Tenant isolation via global scope | ✅ | `TenantScope` prevents cross-tenant data leakage |
| Platform / tenant route separation | ✅ | `PlatformMiddleware` guards `/platform/*`; `TenantMiddleware` guards tenant routes |
| Rate limiting on login | ⚠️ | Not configured — recommended before production |
| Two-factor authentication | ⚠️ | Not implemented — recommended for platform admin accounts |

See [docs/SECURITY.md](SECURITY.md) for detailed security guidance.

---

## Performance

| Check | Status | Notes |
|---|---|---|
| Eager loading in key controllers | ✅ | `with()` used in customer, invoice, and payment index controllers |
| Query caching | ⚠️ | Not implemented — package list and area list are queried on every request |
| Database indexes reviewed | ⚠️ | Only FK indexes exist; `tenant_id`, `status`, `due_date` columns on hot tables lack explicit indexes |
| Bulk invoice generation queued | ⚠️ | Currently synchronous; will timeout for large tenants (>500 customers); see Phase 4 in [docs/PLANS.md](PLANS.md) |

---

## Test Coverage

**Current automated test coverage: 0%**

No PHPUnit or Pest tests exist beyond the Laravel default smoke test. This is the highest technical risk in the codebase.

### Priority test targets (in order)

1. `InvoiceGenerationService` — bulk generation logic, edge cases (existing invoice in period, inactive service)
2. `PaymentAllocationService` — FIFO allocation, over-payment handling, partial payment across multiple invoices
3. `CustomerBalanceService` — balance calculation accuracy after reversals
4. Tenant isolation — assert that a tenant cannot read or write another tenant's records
5. `TenantMiddleware` — suspended tenant redirect; platform user rejection

---

## Related Documents

- [docs/SECURITY.md](SECURITY.md) — authentication, authorization, and security recommendations
- [docs/RELIABILITY.md](RELIABILITY.md) — known failure modes and recovery procedures
