# Product Sense & User Context

## Target Users

**Small and medium ISPs in Bangladesh.**

- Operator size: 50 to 5,000 active customers per tenant
- Staff mix: owners, billing collectors, field technicians, customer support agents
- Device mix: Android phones (field staff), desktop browsers (billing counter), occasionally tablets
- Dominant payment methods: bKash, Nagad, cash at counter — card payments are negligible
- Geographic context: Dhaka, Chittagong, and district-level ISPs; Bengali-speaking staff; BDT currency; Asia/Dhaka timezone

---

## Core User Jobs

These are the five jobs users hire this software to do. Every feature should map to at least one of them.

1. **Generate monthly bills for all customers in one click**
   Staff should not manually create invoices. One action triggers bulk invoice generation for all active services in the billing cycle.

2. **Record a cash or bKash payment quickly at the counter**
   A collector receives money, opens the customer record, enters the amount, and confirms. The flow must take fewer than 30 seconds. No unnecessary fields.

3. **Know instantly who owes what and how much**
   The due list and customer balance must be accurate and immediately visible. Staff should not need to calculate anything manually.

4. **Suspend or reactivate a customer's line**
   Suspending a non-paying customer and reactivating after payment must be a single button click, with the change reflected in both billing status and (eventually) OLT device state.

5. **See today's collections report**
   At end of day, a manager needs total cash collected, bKash collected, and outstanding dues — one screen, no exports required.

---

## Key Metrics

| Metric | Why It Matters |
|---|---|
| **Time-to-collect** | How fast can a collector record a payment end-to-end; directly impacts counter throughput |
| **Invoice generation time** | Bulk generation for 1,000 customers should complete in seconds, not minutes |
| **Accuracy of due amounts** | Errors in due calculation destroy operator trust; zero tolerance for miscalculation |
| **Payment allocation correctness** | Payments must apply to the correct invoices (FIFO oldest-first); misallocation causes disputes |

---

## Pain Points Solved

| Before (status quo) | After (this software) |
|---|---|
| Spreadsheets with manual due calculation | Automated invoice generation and real-time balance |
| WhatsApp screenshots of bKash receipts as payment proof | Structured payment records with transaction references and timestamps |
| Separate billing status and OLT line status, constantly out of sync | Single source of truth; OLT action triggered from the same screen |
| No audit trail for who changed what | Full audit log on all critical actions |
| No way to know MRR or collection efficiency | Reports module with per-day, per-area, per-package breakdowns |

---

## Design Principles

- **Speed over aesthetics for operational staff** — billing counter staff process dozens of payments per hour; every extra click or page load costs real time
- **Bengali-friendly** — BDT (৳) currency symbol via `taka()` helper; Asia/Dhaka timezone for all timestamps; date formats familiar to local operators
- **No complex onboarding** — a new tenant should be able to add their first customer and generate their first invoice within 10 minutes of account creation
- **Every action reversible** — payment reversal, invoice cancellation, and customer reinstatement must always be available; hard deletes of financial records are not permitted in the UI
