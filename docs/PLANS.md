# Roadmap & Planning

## Current Status

**MVP is complete.** All 16 core modules are implemented and manually tested.

| Module | Status |
|---|---|
| Customers | ✅ Complete |
| Packages | ✅ Complete |
| Areas / POPs | ✅ Complete |
| Services | ✅ Complete |
| OLT Devices | ✅ Complete (UI scaffold; no live API) |
| Invoices | ✅ Complete |
| Payments | ✅ Complete |
| Dues | ✅ Complete |
| Reports | ✅ Complete |
| Staff management | ✅ Complete |
| SMS logs | ✅ Complete (log-only; no provider) |
| Settings | ✅ Complete |
| Audit logs | ✅ Complete |
| Platform admin | ✅ Complete |
| Tenant isolation | ✅ Complete |
| Role & permissions | ✅ Complete (spatie/laravel-permission) |

---

## Phase 2 — Near-term

Improvements that directly unblock real ISP operations:

- **OLT API live integration** — implement ZTE and Huawei adapter classes behind an `OltDriverInterface`; wire to device actions (provision, suspend, reactivate) already scaffolded in the UI
- **PDF invoice generation** — integrate `barryvdh/laravel-dompdf`; generate branded PDF invoice from existing invoice view; add download button
- **Email notifications** — send invoice, payment confirmation, and suspension notices via Laravel Mail (SMTP configurable per tenant)
- **bKash / Nagad payment gateway integration** — implement payment gateway callbacks; auto-record payments; reconcile with existing `PaymentAllocationService`

---

## Phase 3 — Growth

Features that extend reach and usability:

- **Customer self-service portal** — read-only portal at a subdomain or tenant slug where customers can view invoices, payment history, and current balance
- **Mobile-responsive improvements** — audit and improve all critical flows (record payment, view due, customer search) for small-screen field staff
- **Advanced analytics dashboard** — MRR trend, churn rate, collection efficiency, area-wise performance charts
- **API for third-party integrations** — REST API with API token auth; expose customers, invoices, and payments for integration with NOC tools or custom mobile apps

---

## Phase 4 — Scale

Infrastructure improvements for larger tenants:

- **Separate database per tenant option** — introduce a `TenantDatabaseResolver` that can route Eloquent connections per tenant; migration tooling for per-tenant schema management
- **Background job queues for bulk invoice generation** — dispatch `GenerateInvoiceJob` per customer; track progress with a job batch; remove the synchronous bulk generation bottleneck
- **Real-time dashboard updates** — use Laravel Echo + Pusher (or Reverb) to push collection totals and due counts to the dashboard without page refresh

---

## Related Documents

- Active execution plans: `exec-plans/active/`
- Tech debt tracker: `exec-plans/tech-debt-tracker.md`
