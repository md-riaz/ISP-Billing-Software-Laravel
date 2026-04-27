# 🌐 ISP Billing Software

![PHP](https://img.shields.io/badge/PHP-8.3-blue?logo=php)
![Laravel](https://img.shields.io/badge/Laravel-12-red?logo=laravel)
![License](https://img.shields.io/badge/License-MIT-green)

A **SaaS multi-tenant ISP Billing & Operations Platform** built for internet service providers in Bangladesh. Each ISP gets a secure, isolated workspace to manage customers, billing, collections, and network devices — all in one dashboard.

---

## ✨ Features

- **Multi-tenant SaaS** — platform owner manages ISP tenants with subscription plans and usage limits
- **Customer Management** — full CRUD with service status tracking (active, suspended, terminated)
- **Internet Packages** — create and manage speed-tiered packages with BDT (৳) pricing
- **Invoice Generation** — bulk and single invoice generation with line-item detail
- **Payment Collection** — record payments, allocate to invoices, reverse transactions
- **Dues Tracking** — real-time outstanding balance view per customer
- **OLT Device Management** — register OLT devices; link ONU/service mappings for provisioning workflows
- **Areas & POPs** — organise coverage zones and Points of Presence
- **Staff & Role Management** — role-based access control via `spatie/laravel-permission`
- **SMS Templates & Logs** — configure notification templates, track send history
- **Reports** — billing reports and collection summaries with date range filters
- **Audit Logs** — full activity trail per tenant
- **Platform Admin Panel** — manage tenants, subscription plans, and platform-level payments

---

## 🏗️ Tech Stack

| Layer        | Technology                               |
|--------------|------------------------------------------|
| Backend      | PHP 8.3 · Laravel 12                     |
| Frontend     | Tailwind CSS · Alpine.js · Vite          |
| Database     | SQLite (swappable to MySQL / PostgreSQL) |
| Auth & RBAC  | Laravel Auth · spatie/laravel-permission |
| Currency     | Bangladeshi Taka (BDT / ৳)              |

---

## 🚀 Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/md-riaz/ISP-Billing-Software-Laravel.git
cd ISP-Billing-Software-Laravel

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies and build assets
npm install && npm run build

# 4. Set up environment
cp .env.example .env
php artisan key:generate

# 5. Create the SQLite database file
touch database/database.sqlite

# 6. Run migrations
php artisan migrate

# 7. Seed demo data
php artisan db:seed

# 8. Start the development server
php artisan serve
```

Visit `http://localhost:8000` in your browser.

---

## 🔐 Demo Credentials

| Role           | Email                 | Password   |
|----------------|-----------------------|------------|
| Platform Admin | `admin@platform.com`  | `password` |
| Tenant Admin   | `admin@demo.com`      | `password` |
| Collector      | `collector@demo.com`  | `password` |
| Technician     | `tech@demo.com`       | `password` |

> **Platform Admin** logs in at `/login` and is redirected to `/platform/dashboard`.  
> All tenant users log in at `/login` and land on the tenant dashboard.

---

## 📋 Module Overview

| Module             | Description                                                           |
|--------------------|-----------------------------------------------------------------------|
| Platform Dashboard | Overview of all tenants, subscriptions, and platform health           |
| Tenant Management  | Create, suspend, and manage ISP tenants and their subscriptions       |
| Subscription Plans | Define Starter / Growth / Pro plans with feature and usage limits     |
| Customer Management| Add, edit, and track customer profiles and service connections        |
| Packages           | Manage internet packages with speed tiers and BDT pricing            |
| Services           | Map customers to packages; update service activation status           |
| Areas & POPs       | Define geographic areas and Points of Presence                        |
| OLT Devices        | Register OLT hardware; link customer ONUs for provisioning workflows  |
| Invoices           | Generate monthly invoices in bulk or individually; view invoice detail|
| Payments           | Collect and allocate payments; reverse erroneous entries              |
| Dues               | View per-customer outstanding balances at a glance                    |
| Staff              | Manage staff accounts with scoped role assignments                    |
| Reports            | Billing and collection reports with date range filters                |
| SMS                | Configure reusable message templates and review delivery logs         |
| Audit Logs         | Immutable activity trail scoped per tenant                            |
| Settings           | Company profile and tenant-level configuration                        |

---

## 🏢 SaaS Architecture

The platform uses a **single-database multi-tenancy** model:

- Every data table carries a `tenant_id` foreign key; a custom `ResolveTenant` middleware injects the active tenant into every authenticated request.
- **Platform users** (`tenant_id = null`) access `/platform/*` routes protected by the `platform` middleware.
- **Tenant users** are scoped to their ISP workspace via the `tenant` + `resolve.tenant` middleware stack.
- Subscription plans enforce hard limits on the number of customers, staff members, OLT devices, and monthly SMS messages per tenant.
- Roles (`tenant_admin`, `accounts_manager`, `billing_officer`, `collector`, `support_agent`, `technician`, `area_manager`) are managed via `spatie/laravel-permission`.

---

## 📁 Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Tenant-level feature controllers
│   │   │   └── Platform/         # Platform-level controllers
│   │   └── Middleware/           # Auth, tenant resolution middleware
│   ├── Models/                   # Eloquent models (Tenant, Customer, Invoice …)
│   └── Services/                 # Business logic service classes
├── database/
│   ├── migrations/               # All schema migrations
│   └── seeders/                  # Demo data seeder with sample ISP
├── resources/
│   └── views/                    # Blade templates (Tailwind CSS + Alpine.js)
├── routes/
│   └── web.php                   # All application routes
└── tests/                        # Feature and unit tests
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit your changes with clear, descriptive messages
4. Open a Pull Request describing what you changed and why

Please ensure existing tests pass and add tests for new functionality where applicable.

---

## 📄 License

This project is open-sourced software licensed under the [MIT License](LICENSE).
