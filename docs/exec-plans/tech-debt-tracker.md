# Tech Debt Tracker

> Last updated: 2025-07

## Summary

| Priority | Count |
|----------|-------|
| High     | 2     |
| Medium   | 4     |
| Low      | 2     |
| **Total**| **8** |

---

## Items

| ID | Priority | Description | Impact | Suggested Fix |
|----|----------|-------------|--------|---------------|
| **[TD-001]** | High | Bulk invoice generation is synchronous — locks the HTTP request for large tenants | Response timeout for 500+ customers | Move to queued jobs with progress polling |
| **[TD-002]** | High | Zero automated test coverage | Regressions go undetected | Start with unit tests for `InvoiceGenerationService` and `PaymentAllocationService` |
| **[TD-003]** | Med | No rate limiting on `/login` route | Brute-force vulnerability | Add `throttle:5,1` middleware to login POST route |
| **[TD-004]** | Med | `TenantScope` requires IoC binding — artisan commands and jobs don't auto-scope | Potential cross-tenant data access in background tasks | Establish a `setCurrentTenant()` helper and call it explicitly in jobs/commands |
| **[TD-005]** | Med | SMS sending is stubbed — templates and logs UI exist but no provider integration | Feature appears complete but does nothing | Integrate an SMS gateway (e.g. BulkSMSBD or Twilio) behind a `SmsProvider` interface |
| **[TD-006]** | Med | OLT device API actions are scaffolded (UI exists) but no live API calls | OLT features are non-functional | Implement ZTE/Huawei adapter using the planned adapter pattern |
| **[TD-007]** | Low | No database query optimisation review | Potential N+1 queries in reports and customer lists | Add eager loading audit; add missing indexes |
| **[TD-008]** | Low | No HTTPS enforcement in `AppServiceProvider` | Production deployments may serve over HTTP | Add `URL::forceScheme('https')` behind environment check |
