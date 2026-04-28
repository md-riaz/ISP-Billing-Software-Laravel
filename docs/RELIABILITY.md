# Reliability & Operational Guidance

## Known Failure Modes

### Bulk Invoice Generation — Silent Per-Service Skip
When generating invoices in bulk, exceptions thrown for an individual service (e.g., missing package price, data integrity issue) are caught and skipped. The job continues for other services. The skipped service does not generate an invoice, and the failure is only visible in the application log.

**Mitigation:** Review `storage/logs/laravel.log` after every bulk generation run. A future improvement is to return a per-customer result list to the UI.

### SMS Sending — Not Yet Implemented
The SMS module creates log records and displays them in the UI, but no SMS provider is wired up. Messages are not delivered to customers.

**Mitigation:** This is known scope for Phase 2. Do not promise SMS delivery to ISP operators until a provider (e.g., SSL Wireless, Infobip) is integrated.

### OLT Device Actions — UI Scaffold Only
The OLT device management UI (provision, suspend, reactivate) is fully built, but no live API calls are made to physical OLT hardware in the MVP.

**Mitigation:** Line status changes in the billing system do not automatically propagate to the OLT. Field technicians must make device changes manually until Phase 2 OLT API integration is complete.

---

## Data Integrity

### Payment Allocation (FIFO)
`PaymentAllocationService` applies payments to invoices in oldest-first order (FIFO). Partial payments are tracked per invoice. A payment cannot be allocated to a future invoice while an older one remains unpaid.

### Payment Reversal
Reversing a payment cascades correctly:
1. Payment allocation records for that payment are deleted
2. Affected invoices have their status recalculated (paid → partial or unpaid)
3. Customer balance is updated

No payment reversal creates orphaned allocation records.

### Tenant ID Enforcement
`tenant_id` is enforced at two levels:
1. **Application layer:** `BelongsToTenant` trait sets `tenant_id` automatically on `creating` events
2. **Database layer:** FK constraint on `tenant_id` references the `tenants` table; rows without a valid tenant cannot be inserted

---

## Backup Recommendations

### SQLite (development / small deployments)
- Back up `database/database.sqlite` daily
- Copy the file while the application is idle, or use SQLite's `.backup` command for a consistent snapshot
- Retain at least 7 daily backups

### MySQL (production)
```bash
mysqldump --single-transaction -u USER -p DATABASE > backup_$(date +%Y%m%d).sql
```
- Run daily via cron
- Retain at least 14 daily backups
- Test restore from backup monthly

### File Storage
Back up `storage/app/` for any uploaded files (logos, documents). This directory is not included in a database backup.

---

## Monitoring Checklist

Monitor these areas in production:

| Area | What to Watch |
|---|---|
| Invoice generation | 500 errors; generation taking > 30 seconds |
| Payment recording and reversal | 500 errors; duplicate payment submissions |
| Tenant middleware | 403/500 errors indicating scope or auth issues |
| Queue worker (Phase 4+) | Failed jobs in the `failed_jobs` table |
| Log file size | `storage/logs/laravel.log` rotation; alert if > 100 MB |

Primary log location: `storage/logs/laravel.log`

---

## Recovery Procedures

| Scenario | Recovery Action |
|---|---|
| Payment recorded in error | Use payment reversal in the UI — this correctly unwinds allocations and invoice statuses |
| Invoice generated in error | Cancel the invoice via the UI (soft delete); do not hard-delete from the database |
| Tenant accidentally suspended | Re-activate via platform admin panel (`/platform/tenants/{id}`) |
| Corrupt SQLite file | Restore from most recent backup; re-apply any transactions from manual records |
| Wrong package assigned to service | Edit the service to correct the package; regenerate the affected invoice for that period |
