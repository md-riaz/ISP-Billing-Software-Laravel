# Billing Engine Design

---

## Invoice Generation — `InvoiceGenerationService`

### Single Invoice

Generates one invoice for a given `CustomerService` and billing month. Throws if an invoice already exists for that combination.

### Bulk Generation

Iterates all active `CustomerService` records for the current tenant and calls the single-invoice path for each. Already-existing invoices for the target month are silently skipped (idempotent).

### Calculation

| Field | Formula |
|---|---|
| `subtotal` | `customer_service.monthly_price` (the package price at time of billing) |
| `discount` | Fixed amount **or** percentage of subtotal, sourced from the customer record |
| `previous_due` | Sum of `due_amount` on existing unpaid invoices for this customer, carried forward |
| `adjustment` | Manual positive/negative adjustment entered at generation time (default 0) |
| `total` | `subtotal − discount + previous_due + adjustment` |

### Invoice Number Format

```
INV-YYYYMM-XXXXX
```

- `YYYYMM` — billing year and month
- `XXXXX` — zero-padded sequential counter scoped per tenant (e.g. `INV-202507-00042`)

### Due Date

`issue_date + 10 days` (the offset may be overridden via the `setting()` helper in future).

---

## Payment Allocation — `PaymentAllocationService`

### Allocation Strategy: FIFO

When a payment is recorded, the service pays off the oldest unpaid or partially-paid invoices first until the payment amount is exhausted.

### What Gets Created

For each invoice touched by the payment, a `PaymentAllocation` record is created linking:

- `payment_id`
- `invoice_id`
- `allocated_amount`

### Invoice Status Transitions

| Condition | Status |
|---|---|
| `due_amount > 0`, no payment yet | `unpaid` |
| `paid_amount > 0` and `due_amount > 0` | `partially_paid` |
| `due_amount = 0` | `paid` |

### Reversal

Reversing a payment:

1. Deletes all `PaymentAllocation` records for that payment (soft delete).
2. Recalculates `paid_amount` and `due_amount` on every affected invoice.
3. Restores invoice statuses to `unpaid` or `partially_paid` as appropriate.
4. Marks the payment as reversed (soft delete + `reversed_at` timestamp).

The original payment record is never hard-deleted.

---

## Balance Calculation — `CustomerBalanceService`

| Metric | Calculation |
|---|---|
| `total_due` | `SUM(due_amount)` on invoices with status `unpaid` or `partially_paid` |
| `total_paid` | `SUM(amount)` on active (non-reversed) payments |
| `total_invoiced` | `SUM(total_amount)` on all non-cancelled invoices |

---

## Invoice Statuses

| Status | Meaning |
|---|---|
| `draft` | Generated but not yet issued to the customer |
| `unpaid` | Issued; no payment received |
| `partially_paid` | Payment received but `due_amount > 0` |
| `paid` | Fully settled (`due_amount = 0`) |
| `waived` | Debt forgiven; no payment expected |
| `cancelled` | Invoice voided; excluded from balance calculations |

State transitions flow forward. An invoice cannot move from `paid` back to `unpaid` directly — a payment reversal achieves this through the `PaymentAllocationService`.

---

## Future Work

- **Queued bulk generation** — for tenants with large customer counts, bulk invoice generation should be dispatched as a queued job (`GenerateBulkInvoicesJob`) to avoid HTTP timeout. The job must set the tenant context via `WithTenant` job middleware before processing.
