> **Auto-generated from migration inspection. Do not edit manually — regenerate from migrations.**

# Database Schema Documentation

---

## Table of Contents

1. [users](#users)
2. [tenants](#tenants)
3. [subscription_plans](#subscription_plans)
4. [tenant_subscriptions](#tenant_subscriptions)
5. [tenant_subscription_payments](#tenant_subscription_payments)
6. [areas](#areas)
7. [pops](#pops)
8. [packages](#packages)
9. [customers](#customers)
10. [customer_services](#customer_services)
11. [olt_devices](#olt_devices)
12. [synced_onus](#synced_onus)
13. [olt_action_logs](#olt_action_logs)
14. [invoices](#invoices)
15. [invoice_items](#invoice_items)
16. [payments](#payments)
17. [payment_allocations](#payment_allocations)
18. [status_histories](#status_histories)
19. [sms_templates](#sms_templates)
20. [sms_logs](#sms_logs)
21. [settings](#settings)
22. [activity_logs](#activity_logs)
23. [Spatie Permission Tables](#spatie-permission-tables)
24. [Entity Relationship Summary](#entity-relationship-summary)

---

## users

Stores all authenticated users within the system, scoped by tenant (multi-tenant SaaS).

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | Yes | NULL | References `tenants.id`; NULL = super-admin |
| name | varchar(255) | No | | |
| email | varchar(255) | No | | Unique |
| phone | varchar(255) | Yes | NULL | |
| email_verified_at | timestamp | Yes | NULL | |
| password | varchar(255) | No | | Bcrypt hash |
| status | enum | No | `active` | Values: `active`, `inactive` |
| last_login_at | timestamp | Yes | NULL | |
| remember_token | varchar(100) | Yes | NULL | |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |
| deleted_at | timestamp | Yes | NULL | Soft delete |

**Relationships:**
- Belongs to `tenants` via `tenant_id`
- Referenced by `invoices.generated_by`, `payments.collector_id`, `payments.reversed_by`, `olt_action_logs.performed_by`, `status_histories.changed_by`, `tenant_subscription_payments.recorded_by`, `activity_logs.user_id`

---

## tenants

Top-level tenant (ISP company) accounts in the multi-tenant architecture.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| name | varchar(255) | No | | Company/ISP name |
| slug | varchar(255) | No | | Unique; used in URLs |
| email | varchar(255) | No | | Primary contact email |
| phone | varchar(255) | Yes | NULL | |
| logo | varchar(255) | Yes | NULL | File path to logo |
| status | enum | No | `trial` | Values: `trial`, `active`, `suspended`, `past_due`, `cancelled` |
| trial_ends_at | timestamp | Yes | NULL | |
| timezone | varchar(255) | No | `Asia/Dhaka` | |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |
| deleted_at | timestamp | Yes | NULL | Soft delete |

**Relationships:**
- Has many `users`, `areas`, `pops`, `packages`, `customers`, `customer_services`, `olt_devices`, `invoices`, `payments`, `sms_templates`, `sms_logs`, `settings`, `activity_logs`
- Has many `tenant_subscriptions`

---

## subscription_plans

SaaS subscription plan definitions (monthly/yearly tiers).

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| name | varchar(255) | No | | Plan display name |
| slug | varchar(255) | No | | Unique identifier |
| price_monthly | decimal(12,2) | No | | |
| price_yearly | decimal(12,2) | No | | |
| max_customers | int | No | `100` | Per-tenant customer limit |
| max_staff | int | No | `5` | Per-tenant staff user limit |
| max_olt_devices | int | No | `1` | |
| max_sms_monthly | int | No | `100` | Monthly SMS quota |
| has_reports | tinyint(1) | No | `1` | Boolean feature flag |
| has_api | tinyint(1) | No | `0` | Boolean feature flag |
| has_branding | tinyint(1) | No | `0` | White-label branding flag |
| is_active | tinyint(1) | No | `1` | Whether plan is purchasable |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Has many `tenant_subscriptions`

---

## tenant_subscriptions

Records each subscription period for a tenant (current and historical).

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| plan_id | bigint unsigned (FK) | No | | References `subscription_plans.id` |
| billing_cycle | enum | No | | Values: `monthly`, `yearly`, `trial` |
| price | decimal(12,2) | No | | Locked price at time of subscription |
| starts_at | timestamp | Yes | NULL | |
| expires_at | timestamp | Yes | NULL | |
| status | enum | No | | Values: `trial`, `active`, `past_due`, `suspended`, `cancelled` |
| notes | text | Yes | NULL | |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `tenants`
- Belongs to `subscription_plans`
- Has many `tenant_subscription_payments`

---

## tenant_subscription_payments

Payment records for SaaS subscription fees (platform-level billing).

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| subscription_id | bigint unsigned (FK) | No | | References `tenant_subscriptions.id` |
| amount | decimal(12,2) | No | | |
| payment_date | date | No | | |
| method | varchar(255) | Yes | NULL | Payment method description |
| reference | varchar(255) | Yes | NULL | Transaction reference number |
| note | text | Yes | NULL | |
| recorded_by | bigint unsigned (FK) | Yes | NULL | References `users.id` |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `tenants`
- Belongs to `tenant_subscriptions`
- Belongs to `users` (via `recorded_by`)

---

## areas

Geographic service areas within a tenant's ISP coverage zone.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| name | varchar(255) | No | | Area name |
| description | text | Yes | NULL | |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |
| deleted_at | timestamp | Yes | NULL | Soft delete |

**Relationships:**
- Belongs to `tenants`
- Has many `pops`
- Has many `customers`

---

## pops

Points of Presence — physical network distribution nodes.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| area_id | bigint unsigned (FK) | No | | References `areas.id` |
| name | varchar(255) | No | | POP name/identifier |
| location | varchar(255) | Yes | NULL | Physical address or GPS note |
| description | text | Yes | NULL | |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |
| deleted_at | timestamp | Yes | NULL | Soft delete |

**Relationships:**
- Belongs to `tenants`
- Belongs to `areas`
- Has many `customers` (optional association)
- Has many `customer_services` (optional association)

---

## packages

Internet service packages (speed tiers) offered by a tenant.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| package_code | varchar(255) | No | | Short identifier (e.g. `H-10M`) |
| package_name | varchar(255) | No | | Display name |
| speed_label | varchar(255) | No | | e.g. `10 Mbps / 5 Mbps` |
| package_type | enum | No | `home` | Values: `home`, `business`, `corporate` |
| monthly_price | decimal(12,2) | No | | |
| description | text | Yes | NULL | |
| is_active | tinyint(1) | No | `1` | |
| service_profile_label | varchar(255) | Yes | NULL | OLT service profile name |
| line_profile_label | varchar(255) | Yes | NULL | OLT line profile name |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |
| deleted_at | timestamp | Yes | NULL | Soft delete |

**Relationships:**
- Belongs to `tenants`
- Has many `customer_services`

---

## customers

End-customers (subscribers) of a tenant ISP.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| customer_code | varchar(255) | No | | Unique per tenant |
| first_name | varchar(255) | No | | |
| last_name | varchar(255) | No | | |
| email | varchar(255) | Yes | NULL | |
| phone | varchar(255) | No | | |
| address | text | Yes | NULL | |
| area_id | bigint unsigned (FK) | Yes | NULL | References `areas.id` |
| pop_id | bigint unsigned (FK) | Yes | NULL | References `pops.id` |
| nid_number | varchar(255) | Yes | NULL | National ID (Bangladesh) |
| connection_date | date | Yes | NULL | |
| status | enum | No | `pending` | Values: `active`, `suspended`, `terminated`, `pending` |
| discount_type | enum | No | `none` | Values: `none`, `fixed`, `percent` |
| discount_value | decimal(10,2) | No | `0.00` | |
| opening_due | decimal(12,2) | No | `0.00` | Carried-over balance at onboarding |
| notes | text | Yes | NULL | |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |
| deleted_at | timestamp | Yes | NULL | Soft delete |

**Relationships:**
- Belongs to `tenants`
- Belongs to `areas` (optional)
- Belongs to `pops` (optional)
- Has many `customer_services`
- Has many `invoices`
- Has many `payments`
- Has many `sms_logs`

---

## customer_services

A specific active or historical service subscription line for a customer (one customer may have multiple service lines).

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| customer_id | bigint unsigned (FK) | No | | References `customers.id` |
| package_id | bigint unsigned (FK) | No | | References `packages.id` |
| olt_device_id | bigint unsigned (FK) | Yes | NULL | References `olt_devices.id` |
| pop_id | bigint unsigned (FK) | Yes | NULL | References `pops.id` |
| onu_serial | varchar(255) | Yes | NULL | ONU hardware serial number |
| onu_mac | varchar(255) | Yes | NULL | ONU MAC address |
| ip_address | varchar(255) | Yes | NULL | Assigned static IP (if any) |
| monthly_price | decimal(12,2) | No | | Locked price at time of service creation |
| status | enum | No | `pending` | Values: `active`, `suspended`, `terminated`, `pending` |
| activation_date | date | Yes | NULL | |
| termination_date | date | Yes | NULL | |
| notes | text | Yes | NULL | |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |
| deleted_at | timestamp | Yes | NULL | Soft delete |

**Relationships:**
- Belongs to `tenants`
- Belongs to `customers`
- Belongs to `packages`
- Belongs to `olt_devices` (optional)
- Belongs to `pops` (optional)
- Has many `invoices`
- Has many `status_histories`
- Has many `olt_action_logs`

---

## olt_devices

OLT (Optical Line Terminal) devices managed by a tenant.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| device_name | varchar(255) | No | | Friendly name |
| device_type | varchar(255) | Yes | NULL | e.g. `GPON`, `EPON` |
| brand | varchar(255) | Yes | NULL | e.g. `ZTE`, `Huawei` |
| model | varchar(255) | Yes | NULL | e.g. `ZXAN C300`, `MA5608T` |
| ip_address | varchar(255) | Yes | NULL | Management IP |
| port | int | Yes | NULL | Management port (Telnet/SSH/API) |
| username | varchar(255) | Yes | NULL | |
| password | text | Yes | NULL | Encrypted at application layer |
| api_type | varchar(255) | Yes | NULL | e.g. `telnet`, `ssh`, `rest` |
| location | varchar(255) | Yes | NULL | Physical location note |
| status | enum | No | `active` | Values: `active`, `inactive`, `unreachable` |
| last_synced_at | timestamp | Yes | NULL | Last successful ONU sync |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |
| deleted_at | timestamp | Yes | NULL | Soft delete |

**Relationships:**
- Belongs to `tenants`
- Has many `synced_onus`
- Has many `customer_services`
- Has many `olt_action_logs`

---

## synced_onus

Snapshot of ONUs discovered from OLT devices during sync operations.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| olt_device_id | bigint unsigned (FK) | No | | References `olt_devices.id` |
| onu_serial | varchar(255) | No | | Hardware serial number |
| onu_mac | varchar(255) | Yes | NULL | MAC address |
| status | varchar(255) | Yes | NULL | ONU operational status |
| slot | varchar(255) | Yes | NULL | OLT slot number |
| port | varchar(255) | Yes | NULL | OLT port number |
| raw_data | json | Yes | NULL | Full raw response from OLT |
| synced_at | timestamp | No | | Timestamp of this sync record |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `tenants`
- Belongs to `olt_devices`

---

## olt_action_logs

Audit log of every action performed against an OLT device (enable/disable ONU, change profile, etc.).

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| olt_device_id | bigint unsigned (FK) | Yes | NULL | References `olt_devices.id` |
| customer_service_id | bigint unsigned (FK) | Yes | NULL | References `customer_services.id` |
| action | varchar(255) | No | | e.g. `enable_onu`, `disable_onu`, `change_profile` |
| status | enum | No | | Values: `success`, `failed`, `pending` |
| request_payload | json | Yes | NULL | Parameters sent to OLT |
| response_payload | json | Yes | NULL | Raw response from OLT |
| error_message | text | Yes | NULL | |
| performed_by | bigint unsigned (FK) | Yes | NULL | References `users.id` |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `tenants`
- Belongs to `olt_devices` (optional)
- Belongs to `customer_services` (optional)
- Belongs to `users` via `performed_by` (optional)
- Referenced by `status_histories.olt_action_log_id`

---

## invoices

Monthly or one-time billing invoices issued to customers.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| invoice_number | varchar(255) | No | | Unique per tenant |
| customer_id | bigint unsigned (FK) | No | | References `customers.id` |
| customer_service_id | bigint unsigned (FK) | Yes | NULL | References `customer_services.id` |
| billing_month | varchar(7) | No | | Format: `YYYY-MM` |
| invoice_type | enum | No | `recurring` | Values: `recurring`, `one_time`, `correction` |
| issue_date | date | No | | |
| due_date | date | Yes | NULL | |
| subtotal | decimal(12,2) | No | | Line items total before adjustments |
| previous_due | decimal(12,2) | No | `0.00` | Carried-forward balance |
| discount_amount | decimal(12,2) | No | `0.00` | |
| adjustment_amount | decimal(12,2) | No | `0.00` | Manual positive/negative adjustment |
| total_amount | decimal(12,2) | No | | Final billable amount |
| paid_amount | decimal(12,2) | No | `0.00` | Running total allocated |
| due_amount | decimal(12,2) | No | `0.00` | `total_amount - paid_amount` |
| status | enum | No | | Values: `draft`, `unpaid`, `partially_paid`, `paid`, `waived`, `cancelled` |
| notes | text | Yes | NULL | |
| generated_by | bigint unsigned (FK) | Yes | NULL | References `users.id` |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `tenants`
- Belongs to `customers`
- Belongs to `customer_services` (optional)
- Belongs to `users` via `generated_by` (optional)
- Has many `invoice_items` (cascade delete)
- Has many `payment_allocations`

---

## invoice_items

Line items belonging to an invoice.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| invoice_id | bigint unsigned (FK) | No | | References `invoices.id`; cascade delete |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| description | varchar(255) | No | | Line item label |
| quantity | int | No | `1` | |
| unit_price | decimal(12,2) | No | | |
| amount | decimal(12,2) | No | | `quantity × unit_price` |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `invoices` (cascade on delete)
- Belongs to `tenants`

---

## payments

Cash or digital payments received from customers.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| payment_number | varchar(255) | No | | Unique per tenant |
| customer_id | bigint unsigned (FK) | No | | References `customers.id` |
| payment_date | date | No | | |
| amount | decimal(12,2) | No | | |
| method | enum | No | | Values: `cash`, `bkash`, `nagad`, `rocket`, `bank`, `card`, `online`, `adjustment` |
| transaction_reference | varchar(255) | Yes | NULL | MFS/bank transaction ID |
| collector_id | bigint unsigned (FK) | Yes | NULL | References `users.id` |
| note | text | Yes | NULL | |
| status | enum | No | `active` | Values: `active`, `reversed` |
| reversed_at | timestamp | Yes | NULL | |
| reversed_by | bigint unsigned (FK) | Yes | NULL | References `users.id` |
| reversal_reason | text | Yes | NULL | |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `tenants`
- Belongs to `customers`
- Belongs to `users` via `collector_id` (optional)
- Belongs to `users` via `reversed_by` (optional)
- Has many `payment_allocations`

---

## payment_allocations

Maps a payment to the specific invoices it covers (supports partial payments across multiple invoices).

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| payment_id | bigint unsigned (FK) | No | | References `payments.id` |
| invoice_id | bigint unsigned (FK) | No | | References `invoices.id` |
| allocated_amount | decimal(12,2) | No | | Portion of payment applied to this invoice |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `tenants`
- Belongs to `payments`
- Belongs to `invoices`

---

## status_histories

Audit trail of every service status change (active → suspended, etc.).

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| customer_service_id | bigint unsigned (FK) | No | | References `customer_services.id` |
| old_status | varchar(255) | Yes | NULL | Previous status value |
| new_status | varchar(255) | No | | New status value |
| reason | text | Yes | NULL | |
| changed_by | bigint unsigned (FK) | Yes | NULL | References `users.id` |
| olt_action_log_id | bigint unsigned (FK) | Yes | NULL | References `olt_action_logs.id` |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `tenants`
- Belongs to `customer_services`
- Belongs to `users` via `changed_by` (optional)
- Belongs to `olt_action_logs` (optional — links status change to the OLT action that caused it)

---

## sms_templates

Configurable SMS message templates for automated notifications.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| event_type | varchar(255) | No | | e.g. `invoice_generated`, `payment_received`, `service_suspended` |
| template_name | varchar(255) | No | | |
| message_body | text | No | | Supports `{{variable}}` placeholders |
| variables | json | Yes | NULL | Declared variable names for this template |
| is_active | tinyint(1) | No | `1` | |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `tenants`

---

## sms_logs

Delivery log for every SMS message dispatched by the system.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| customer_id | bigint unsigned (FK) | Yes | NULL | References `customers.id` |
| phone | varchar(255) | No | | Destination phone number |
| message | text | No | | Rendered message body |
| status | enum | No | `queued` | Values: `sent`, `failed`, `queued` |
| provider_response | text | Yes | NULL | Raw API response from SMS gateway |
| sent_at | timestamp | Yes | NULL | |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `tenants`
- Belongs to `customers` (optional — may be sent to non-customer numbers)

---

## settings

Key-value configuration store scoped per tenant.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | No | | References `tenants.id` |
| key | varchar(255) | No | | Setting key name |
| value | text | Yes | NULL | |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Constraints:**
- Unique composite: `(tenant_id, key)`

**Relationships:**
- Belongs to `tenants`

---

## activity_logs

General-purpose audit log for significant user actions across the application.

| Column | Type | Nullable | Default | Notes |
|---|---|---|---|---|
| id | bigint unsigned (PK) | No | auto_increment | |
| tenant_id | bigint unsigned (FK) | Yes | NULL | References `tenants.id`; NULL for super-admin actions |
| user_id | bigint unsigned (FK) | Yes | NULL | References `users.id` |
| action | varchar(255) | No | | Action performed (e.g. `customer.created`) |
| entity_type | varchar(255) | Yes | NULL | Model class name (e.g. `App\Models\Customer`) |
| entity_id | bigint unsigned | Yes | NULL | Primary key of the affected entity |
| old_values | json | Yes | NULL | State before the action |
| new_values | json | Yes | NULL | State after the action |
| ip_address | varchar(45) | Yes | NULL | IPv4 or IPv6 address |
| created_at | timestamp | Yes | NULL | |
| updated_at | timestamp | Yes | NULL | |

**Relationships:**
- Belongs to `tenants` (optional)
- Belongs to `users` (optional)

---

## Spatie Permission Tables

These tables are managed by the [`spatie/laravel-permission`](https://spatie.be/docs/laravel-permission) package. Refer to the official documentation for full schema details.

| Table | Purpose |
|---|---|
| `permissions` | Defines individual permission abilities (e.g. `invoice.create`) |
| `roles` | Named role groups (e.g. `admin`, `billing_staff`, `readonly`) |
| `model_has_permissions` | Direct permission assignments to any model (typically `User`) |
| `model_has_roles` | Role assignments to any model (typically `User`) |
| `role_has_permissions` | Many-to-many mapping of permissions to roles |

All permission tables include a `guard_name` column for multi-guard support. Role and permission records are scoped to the application's `web` guard by default.

---

## Entity Relationship Summary

```
┌─────────────────────────────────────────────────────────────────────┐
│                          PLATFORM LAYER                             │
│                                                                     │
│  subscription_plans ──< tenant_subscriptions >── tenants            │
│                                  │                   │              │
│                     tenant_subscription_payments    ...             │
└─────────────────────────────────────────────────────────────────────┘
                                   │ (1 tenant has many of everything below)
┌─────────────────────────────────────────────────────────────────────┐
│                           TENANT LAYER                              │
│                                                                     │
│  tenants ──< users                                                  │
│           │                                                         │
│           ├──< areas ──< pops                                       │
│           │                                                         │
│           ├──< packages                                             │
│           │                                                         │
│           ├──< olt_devices ──< synced_onus                          │
│           │         │                                               │
│           │         └──< olt_action_logs >── customer_services      │
│           │                                        │                │
│           └──< customers ──────────────────────────┤                │
│                   │          (via area_id, pop_id) │                │
│                   │                                │                │
│                   ├──< invoices ──< invoice_items  │                │
│                   │        │                       │                │
│                   │        └──< payment_allocations│                │
│                   │                   │            │                │
│                   ├──< payments ──────┘            │                │
│                   │                                │                │
│                   └──< sms_logs         status_histories            │
│                                                                     │
│  settings (tenant-scoped key-value store)                           │
│  sms_templates (tenant-scoped message templates)                    │
│  activity_logs (tenant-scoped audit trail)                          │
└─────────────────────────────────────────────────────────────────────┘

Key:
  ──<   one-to-many (parent ──< children)
  >──<  many-to-many (via junction table)
  >──   many-to-one (FK reference)
```

### Core Domain Relationships (summary)

| From | Relationship | To | Via |
|---|---|---|---|
| `tenants` | has many | `users` | `users.tenant_id` |
| `tenants` | has many | `tenant_subscriptions` | `tenant_subscriptions.tenant_id` |
| `subscription_plans` | has many | `tenant_subscriptions` | `tenant_subscriptions.plan_id` |
| `tenants` | has many | `areas` | `areas.tenant_id` |
| `areas` | has many | `pops` | `pops.area_id` |
| `tenants` | has many | `packages` | `packages.tenant_id` |
| `tenants` | has many | `olt_devices` | `olt_devices.tenant_id` |
| `olt_devices` | has many | `synced_onus` | `synced_onus.olt_device_id` |
| `tenants` | has many | `customers` | `customers.tenant_id` |
| `customers` | has many | `customer_services` | `customer_services.customer_id` |
| `packages` | has many | `customer_services` | `customer_services.package_id` |
| `olt_devices` | has many | `customer_services` | `customer_services.olt_device_id` |
| `customer_services` | has many | `status_histories` | `status_histories.customer_service_id` |
| `customer_services` | has many | `olt_action_logs` | `olt_action_logs.customer_service_id` |
| `customers` | has many | `invoices` | `invoices.customer_id` |
| `invoices` | has many | `invoice_items` | `invoice_items.invoice_id` (cascade) |
| `customers` | has many | `payments` | `payments.customer_id` |
| `payments` | has many | `payment_allocations` | `payment_allocations.payment_id` |
| `invoices` | has many | `payment_allocations` | `payment_allocations.invoice_id` |
| `tenants` | has many | `settings` | `settings.tenant_id` |
| `tenants` | has many | `sms_templates` | `sms_templates.tenant_id` |
| `customers` | has many | `sms_logs` | `sms_logs.customer_id` |
