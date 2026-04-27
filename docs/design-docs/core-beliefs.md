# Core Beliefs

Foundational engineering and product beliefs that guide every decision in this codebase.

---

## Engineering Beliefs

### Tenant isolation is non-negotiable
Every model that belongs to a tenant **must** use the `BelongsToTenant` trait. No exceptions. A query that leaks data across tenants is a critical bug regardless of how unlikely it seems in production.

### Business logic lives in Services, not Controllers
Controllers are thin HTTP adapters. All domain logic lives in the service layer. `InvoiceGenerationService`, `PaymentAllocationService`, and `CustomerBalanceService` are the authoritative source of truth for their respective domains. A controller should call a service method and return a response — nothing more.

### Reversibility over deletion
Payments can be reversed. Invoices can be cancelled. Every financial model uses soft deletes. Hard-deleting a payment, invoice, or allocation record is never acceptable. The audit trail must be intact forever.

### Explicit over implicit for money
All monetary calculations go through the service layer. Due amounts, discounts, and balances are never computed in views, Blade templates, or controllers. If a number representing money appears on screen, it was produced by a service and formatted by the `taka()` helper.

### One source of truth for settings
Company-level configuration is read via the `setting()` helper from the `settings` table. Values such as company name, SMS gateway credentials, or due-date offset are never hardcoded anywhere in the application.

---

## Product Beliefs

### Speed at the counter beats visual polish
A billing officer must be able to record a payment in under 10 seconds. Keyboard-navigable forms, sensible defaults, and fast server responses matter more than animations or elaborate layouts.

### BDT first
All currency display goes through the `taka()` helper. The application timezone is always `Asia/Dhaka`. No multi-currency abstraction is needed at this stage; adding one prematurely would complicate every financial calculation.

### ISP operators are not technical users
The UI must be self-explanatory to someone with no software training. Error messages must not say "An error occurred" — they must say what went wrong and what the user should do next. Validation messages are part of the product.

### Every action must be auditable
The `activity_logs` table records every significant action: who did it, which entity was affected, and what the old and new values were. This is non-negotiable for a billing product where disputes over payments and invoices are routine.
