# Product Spec: New User (ISP Tenant) Onboarding Flow

## Overview

A new ISP signs up and gets an operational billing system within 15 minutes.

## Actors

- **Platform Admin** — manages all tenants on the SaaS platform
- **ISP Owner (Tenant Admin)** — the ISP operator setting up their own instance

## Preconditions

- Platform admin has access to `/platform/`

## Happy Path

1. Platform admin creates tenant via `/platform/tenants/create` (name, slug, email, phone, plan).
2. Platform admin creates tenant admin user with `tenant_id` set (or seeder creates it).
3. Tenant admin logs in at `/login` with their credentials.
4. Tenant admin goes to **Settings → Company** to configure company name, address, logo.
5. Tenant admin creates **Areas** (coverage zones) and **POPs** (network points).
6. Tenant admin creates **Packages** (speed tiers and pricing).
7. Tenant admin creates first **Customer** and assigns a **Service** (links customer to package).
8. Tenant admin generates first invoice via **Invoices → Generate**.
9. Collector logs in, goes to **Payments → Collect Payment**, records payment.
10. Dashboard shows updated stats.

## Acceptance Criteria

- New tenant cannot see any data from other tenants.
- First invoice generates correctly with the correct amount.
- Payment allocates to invoice; invoice status becomes **paid**.

## Edge Cases

- Tenant with expired subscription gets `403` from `TenantMiddleware`.
- Creating a customer without a package is allowed; service is added separately.
