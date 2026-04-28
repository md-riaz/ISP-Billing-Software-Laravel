# References & External Links Index

A curated index of documentation, APIs, and standards referenced across this project.

---

## Framework & Libraries

### Core Framework

| Resource | URL | Notes |
|---|---|---|
| Laravel 12 Documentation | https://laravel.com/docs/12.x | Primary framework |
| Laravel Eloquent ORM | https://laravel.com/docs/12.x/eloquent | Database layer |
| Laravel Queues | https://laravel.com/docs/12.x/queues | Async jobs (SMS dispatch, OLT actions) |
| Laravel Sanctum | https://laravel.com/docs/12.x/sanctum | API token auth (if API tier enabled) |
| Laravel Telescope | https://laravel.com/docs/12.x/telescope | Debug assistant (dev only) |

### PHP Packages

| Package | Packagist | Documentation | Usage |
|---|---|---|---|
| `spatie/laravel-permission` | https://packagist.org/packages/spatie/laravel-permission | https://spatie.be/docs/laravel-permission | Role & permission management |
| `barryvdh/laravel-dompdf` | https://packagist.org/packages/barryvdh/laravel-dompdf | https://github.com/barryvdh/laravel-dompdf | PDF invoice generation *(planned)* |
| `laravel/tinker` | https://packagist.org/packages/laravel/tinker | | REPL for development |

### Frontend

| Resource | URL | Notes |
|---|---|---|
| Alpine.js Documentation | https://alpinejs.dev/start-here | Lightweight JS for interactivity |
| Alpine.js Component Reference | https://alpinejs.dev/directives/data | Directives: `x-data`, `x-bind`, `x-on` |
| Tailwind CSS Documentation | https://tailwindcss.com/docs | Utility-first CSS framework |
| Tailwind CSS Configuration | https://tailwindcss.com/docs/configuration | `tailwind.config.js` reference |
| Vite Documentation | https://vitejs.dev/guide/ | Asset bundler (`vite.config.js`) |

---

## Internal References

| Document | Path | Description |
|---|---|---|
| Architecture Overview | [`ARCHITECTURE.md`](../../ARCHITECTURE.md) | System architecture, module structure, multi-tenancy design |
| Design Decisions | [`docs/DESIGN.md`](../DESIGN.md) | Core product and technical design rationale |
| Database Schema | [`docs/generated/db-schema.md`](../generated/db-schema.md) | Full table/column reference with ERD (auto-generated) |
| Frontend Guide | [`docs/FRONTEND.md`](../FRONTEND.md) | Blade, Alpine.js, and Tailwind conventions |
| Security Practices | [`docs/SECURITY.md`](../SECURITY.md) | Authentication, authorization, data protection |
| Reliability Notes | [`docs/RELIABILITY.md`](../RELIABILITY.md) | Uptime, queue resilience, error handling |
| Design Docs Index | [`docs/design-docs/index.md`](../design-docs/index.md) | Deep-dive design documents |
| Billing Engine Design | [`docs/design-docs/billing-engine.md`](../design-docs/billing-engine.md) | Invoice generation, payment allocation logic |
| Multi-Tenancy Design | [`docs/design-docs/multi-tenancy.md`](../design-docs/multi-tenancy.md) | Tenant isolation strategy |
| MVP Launch Checklist | [`docs/exec-plans/active/mvp-launch-checklist.md`](../exec-plans/active/mvp-launch-checklist.md) | Current execution plan |

---

## Bangladesh-Specific

### Mobile Financial Services (MFS) — Future Payment Gateways

| Provider | Developer Docs | Notes |
|---|---|---|
| **bKash** | https://developer.bka.sh/ | Most widely used MFS in Bangladesh; Checkout, Payment Request, Disbursement APIs available. Integration planned for online bill payment. |
| **Nagad** | https://nagad.com.bd/merchant/ | Second-largest MFS; merchant payment API. Integration planned. |
| **Rocket (DBBL)** | https://rocket.dutchbanglabank.com/ | Dutch-Bangla Bank mobile banking. Integration considered. |

> **Note:** MFS payment collection (`bkash`, `nagad`, `rocket`) are already represented as `method` enum values in the `payments` table, anticipating future gateway integration. Manual reference entry is supported now.

### SMS Gateway — Future Provider

| Provider | URL | Notes |
|---|---|---|
| **BulkSMSBD** | https://bulksmsbd.net/api/ | Common Bangladeshi SMS API provider. HTTP GET/POST API. Integration planned for the SMS dispatch system (see `sms_logs`, `sms_templates` tables). |
| **SSL Wireless** | https://www.sslwireless.com/sms-service/ | Alternative SMS provider option. |

### Regulatory

| Body | URL | Notes |
|---|---|---|
| **BTRC** (Bangladesh Telecommunication Regulatory Commission) | https://www.btrc.gov.bd/ | Governs ISP licensing, broadband service standards, and subscriber registration requirements. `nid_number` on the `customers` table reflects BTRC subscriber identity verification requirements. |

---

## OLT Vendors

OLT (Optical Line Terminal) device integration is a core feature. The system stores credentials and API type in `olt_devices` and logs all actions in `olt_action_logs`.

### ZTE ZXAN Series *(Primary Target)*

| Resource | URL | Notes |
|---|---|---|
| ZTE ZXAN Product Page | https://www.zte.com.cn/global/products/access/olt/ | C300, C320, C650 series |
| ZXAN NETCONF/RESTCONF API | *(vendor-distributed; contact ZTE support)* | Preferred programmatic interface for newer firmware |
| ZXAN Telnet CLI Reference | *(vendor NDA documentation)* | Legacy CLI used for Telnet/SSH `api_type` |
| ZTE OLT ONU Management Guide | *(hardware-specific; firmware version dependent)* | Covers ONU registration, profile assignment, port admin |

**Integration approach:** Telnet/SSH CLI scraping for legacy firmware; NETCONF/RESTCONF where available. ONU enable/disable maps to `olt_action_logs.action` values.

### Huawei SmartAX MA5600/MA5800 Series *(Secondary Target)*

| Resource | URL | Notes |
|---|---|---|
| Huawei iMaster NCE | https://e.huawei.com/en/products/optical-access/imaster-nce-access | Centralized OLT management platform |
| MA5608T/MA5683T Product Page | https://e.huawei.com/en/products/optical-access/smartax-ma5600t | Common models in Bangladeshi ISP deployments |
| Huawei CLI Reference (SSH/Telnet) | *(Huawei Support portal — requires account)* | CLI command reference for ONU management |
| NETCONF YANG Models | *(Huawei iMaster SDK — requires NDA)* | For REST-style integration on modern firmware |

**Integration approach:** SSH CLI for initial support; NETCONF/YANG for future automation. Brand/model stored in `olt_devices.brand` and `olt_devices.model`.

### General OLT Integration Notes

- ONU serial numbers are stored in both `synced_onus.onu_serial` (discovered) and `customer_services.onu_serial` (assigned).
- The `olt_devices.api_type` column (`telnet`, `ssh`, `rest`) drives the connection driver selection at runtime.
- Passwords in `olt_devices.password` are encrypted at the application layer before persistence.
- All OLT commands are logged to `olt_action_logs` with `request_payload` and `response_payload` for full auditability.
