ISP Billing Software — Developer-Ready PRD + Schema + Laravel Module Plan

SaaS Multi-Tenant + OLT API MVP


---

1. Purpose of This Document

This document converts the approved ISP Billing Software specification into a developer-ready product document for implementation in Laravel + Tailwind CSS.

It includes:

product requirements by module

MVP user stories

database entity design

Laravel module structure

route and controller planning

core service classes

queue/jobs design

implementation phases

engineering backlog


This document is written so a solo developer or small team can start building immediately.


---

2. Product Build Goal

Build a multi-tenant SaaS ISP Billing Software for Bangladesh where:

the platform owner can onboard and bill ISP tenants

each ISP tenant can manage customers, packages, invoices, payments, dues, staff, settings, reports

the tenant can connect supported OLT devices by API

the system can link billing state and service state with controlled OLT actions



---

3. MVP Build Boundary

3.1 Platform MVP

Must support:

create tenant

assign plan

start trial / activate subscription

suspend or reactivate tenant

track subscription payments

enforce feature limits


3.2 Tenant MVP

Must support:

staff login and role permissions

customer CRUD

package CRUD

area / POP CRUD

OLT device CRUD

ONU/service mapping basics

monthly invoice generation

payment collection and allocation

due calculation

service status updates

SMS templates and send logs

reports

settings

audit logs


3.3 OLT MVP

Must support at least one real adapter implementation pattern with:

credential save

connection test

fetch basic device info

fetch ONU list or selected ONU status

enable/disable/reconnect actions where vendor supports it

action logging



---

4. Product Requirement Document (PRD)

4.1 Platform Admin Module

Objective

Allow SaaS owner to manage tenants, plans, subscriptions, platform health, and access control.

Functional Requirements

platform admin can log in

platform admin can create a tenant manually

platform admin can view tenant list with plan and status

platform admin can activate, suspend, cancel, or extend tenant subscription

platform admin can define plans with feature limits

platform admin can record tenant subscription payments

platform admin can see expiring or overdue tenant subscriptions


Key Screens

Platform Dashboard

Tenant List

Tenant Details

Subscription Plans

Tenant Subscription Payment Entry


Acceptance Criteria

tenant status updates immediately affect tenant access rules

feature limits are enforced on tenant operations

plan changes keep tenant data intact



---

4.2 Tenant Authentication and RBAC Module

Objective

Allow tenant staff to securely log in and operate by role.

Functional Requirements

tenant users can log in

tenant admin can create staff users

tenant admin can assign roles and permissions

only authorized users can perform sensitive actions

permissions must protect financial and OLT actions


Core Roles

tenant_admin

accounts_manager

billing_officer

collector

support_agent

technician

area_manager


Acceptance Criteria

unauthorized users cannot access protected routes

permission checks exist for payment reversal, invoice generation, OLT actions, and settings changes



---

4.3 Tenant Profile and Settings Module

Objective

Allow each ISP tenant to configure company-level behavior.

Functional Requirements

set company profile

upload logo

set invoice and receipt footer

set default due date

set numbering prefixes

set payment method options

set billing policy

set SMS provider credentials later or placeholder config now

set OLT action mode (billing-only or billing+OLT)


Acceptance Criteria

tenant-specific settings reflect in invoices, receipts, and billing logic



---

4.4 Customer Management Module

Objective

Manage all subscriber records.

Functional Requirements

create customer

edit customer

soft delete or archive customer

search by name, phone, code, area

assign package, area, collector, technician

store status, discount, opening due, installation charge

store customer notes


Acceptance Criteria

customer search is fast

duplicate phone/code prevention is configurable

customer ledger is accessible from customer profile



---

4.5 Package Module

Objective

Manage internet packages.

Functional Requirements

create package

edit package

activate or deactivate package

store price, speed label, package type

optionally map to service profile labels used in OLT workflows


Acceptance Criteria

historical invoices are not changed after package price edits



---

4.6 Area / POP Module

Objective

Organize customer operations by geography and network point.

Functional Requirements

create area

create POP

assign staff to area/POP

filter customers, bills, dues, and collections by area


Acceptance Criteria

all operational lists can be filtered by area and POP



---

4.7 Customer Service / ONU Mapping Module

Objective

Represent actual subscribed service separately from the customer profile.

Functional Requirements

create service record for customer

assign package and monthly price snapshot

assign OLT device and ONU mapping if available

view service status

change service status

store line/service profile labels


Acceptance Criteria

a customer can have one active service in MVP, but structure supports more later



---

4.8 OLT Device Module

Objective

Allow tenant to connect and manage supported OLT devices.

Functional Requirements

add OLT device

edit OLT config

securely store credentials

test connection

fetch basic device info

sync ONU list or selected ONU info

map customer service to synced ONU

execute supported OLT actions

log action request and result


Acceptance Criteria

secrets are encrypted

unsupported actions are hidden or disabled

operator can review action result and error



---

4.9 Billing / Invoice Module

Objective

Generate and manage recurring bills.

Functional Requirements

generate monthly invoices by month

generate by all customers, by area, by package, or selected customers

prevent duplicate recurring invoices per billing period

allow one-time invoice items

show invoice list with status

print invoice or export PDF


Acceptance Criteria

recurring invoice generation is idempotent for same service + month

invoice totals calculate correctly with due carry-forward and discounts



---

4.10 Payment Collection Module

Objective

Record customer payments accurately in BDT.

Functional Requirements

collect payment against customer

allocate payment to invoices

support partial payment

support payment methods: cash, bKash, Nagad, Rocket, bank, card

generate receipt

reverse payment by permission


Acceptance Criteria

payment save updates invoice balances atomically

reversed payments remain in audit history



---

4.11 Due Management Module

Objective

Give clear visibility of dues and overdue accounts.

Functional Requirements

customer due list

overdue list

aging bucket report

area-wise due report

suspension candidate list


Acceptance Criteria

due totals match invoice and payment records exactly



---

4.12 Service Status Module

Objective

Manage connection lifecycle.

Functional Requirements

activate

suspend for due

suspend manually

reconnect

disconnect

terminate

store status history with reason

optionally call OLT action where configured


Acceptance Criteria

all changes are logged

OLT-linked actions clearly show success or failure



---

4.13 SMS / Notification Module

Objective

Notify customers and tenants of important billing events.

Functional Requirements

configure templates

send invoice SMS

send payment confirmation SMS

send due reminders

log send status


Acceptance Criteria

message variables render correctly

failed sends are visible in logs



---

4.14 Reports Module

Objective

Provide operational and financial insight.

Functional Requirements

collection reports

due reports

billing reports

customer reports

OLT/service reports

tenant subscription report (platform side)


Acceptance Criteria

date range filter works

exported/printed reports reflect current filters



---

4.15 Audit Log Module

Objective

Track important system actions.

Functional Requirements

log user, action, entity, old values, new values, timestamp

cover financial actions, subscription actions, OLT actions, settings changes


Acceptance Criteria

critical actions are traceable without reading raw DB tables



---

5. MVP User Stories

5.1 Platform User Stories

As a platform admin, I want to create a new ISP tenant so they can start using the system.

As a platform admin, I want to assign a plan to a tenant so feature limits are controlled.

As a platform admin, I want to suspend a tenant subscription so unpaid accounts can be restricted.

As a platform finance user, I want to record subscription payment so the tenant remains active.


5.2 Tenant Admin User Stories

As a tenant admin, I want to add staff users so work can be distributed.

As a tenant admin, I want to create packages so customers can be assigned plans.

As a tenant admin, I want to configure OLT devices so services can be mapped and controlled.

As a tenant admin, I want to set invoice numbering and due date rules so billing follows our office policy.


5.3 Accounts/Billing User Stories

As an accounts manager, I want to generate monthly bills by area so billing can be done in batches.

As a billing officer, I want to collect partial payments so customers can pay what they can.

As a billing officer, I want automatic due recalculation after payment so I see the correct remaining balance.

As an accounts manager, I want payment reversal with audit logs so mistakes can be corrected safely.


5.4 Collector User Stories

As a collector, I want to search by customer phone or code so I can quickly collect payment in the field.

As a collector, I want to print or share a receipt so the customer gets confirmation.


5.5 Support/Technician User Stories

As a technician, I want to link a customer service to an ONU so network and billing stay aligned.

As a technician, I want to test OLT connectivity so I know the device integration is working.

As a technician, I want to suspend or reconnect service from the app if supported by the device.


5.6 Customer-Facing User Stories

As a customer, I want to receive due reminder SMS so I do not miss payment.

As a customer, I want to receive payment confirmation SMS so I know the office recorded my payment.



---

6. Suggested Laravel Project Structure

6.1 App Modules / Domains

Use a modular domain-based structure inside Laravel:

app/Domain/Platform

app/Domain/Tenant

app/Domain/Auth

app/Domain/Customers

app/Domain/Packages

app/Domain/Areas

app/Domain/Services

app/Domain/Olt

app/Domain/Billing

app/Domain/Payments

app/Domain/Notifications

app/Domain/Reports

app/Domain/Audit


6.2 Common Folder Pattern Per Domain

Each domain may contain:

Models

Actions

Services

DTOs

Policies

Queries

Jobs

Enums

Controllers (or in Http namespace if preferred)


Example:

app/Domain/Billing/Models/Invoice.php

app/Domain/Billing/Services/InvoiceGenerationService.php

app/Domain/Olt/Services/Adapters/HuaweiOltAdapter.php



---

7. Database Entity Relationship Design

7.1 Platform Entities

Tenant

TenantDomain

SubscriptionPlan

TenantSubscription

TenantSubscriptionPayment

PlatformUser


7.2 Tenant Entities

User

Role

Permission

Area

Pop

Package

Customer

CustomerService

OltDevice

SyncedOnu

Invoice

InvoiceItem

Payment

PaymentAllocation

StatusHistory

SmsLog

Setting

ActivityLog


7.3 Important Relationships

one tenant has many users

one tenant has many customers

one tenant has many packages

one tenant has many OLT devices

one customer has many invoices

one customer has many payments

one customer has many services over time

one service belongs to one customer

one OLT device has many synced ONUs

one service may link to one synced ONU

one payment has many allocations

one invoice has many payment allocations

one tenant has one current subscription record at a time in MVP, but history is preserved



---

8. Practical ERD Description

8.1 Core Financial Chain

Tenant -> Customer -> CustomerService -> Invoice -> PaymentAllocation <- Payment

8.2 Service Chain

Tenant -> OltDevice -> SyncedOnu -> CustomerService -> Customer

8.3 SaaS Chain

SubscriptionPlan -> TenantSubscription -> TenantSubscriptionPayment -> Tenant


---

9. Key Migrations Plan

9.1 Migration Order

1. tenants


2. tenant_domains


3. subscription_plans


4. tenant_subscriptions


5. tenant_subscription_payments


6. users


7. roles / permissions


8. areas


9. pops


10. packages


11. customers


12. customer_services


13. olt_devices


14. synced_onus


15. invoices


16. invoice_items


17. payments


18. payment_allocations


19. status_histories


20. sms_logs


21. settings


22. activity_logs


23. olt_action_logs



9.2 Important Indexes

Add indexes for:

tenant_id

customer_code

primary_phone

status

billing_month

invoice_number

payment_number

olt_device_id

onu_identifier


9.3 Important Unique Constraints

tenant + customer_code unique

tenant + package_code unique

tenant + invoice_number unique

tenant + payment_number unique

tenant + service + billing_month unique for recurring invoice logic



---

10. Recommended Eloquent Models

Platform Models

Tenant

TenantDomain

SubscriptionPlan

TenantSubscription

TenantSubscriptionPayment

PlatformUser


Tenant Models

User

Area

Pop

Package

Customer

CustomerService

OltDevice

SyncedOnu

Invoice

InvoiceItem

Payment

PaymentAllocation

StatusHistory

SmsLog

Setting

ActivityLog

OltActionLog



---

11. Laravel Route Plan

Use route groups for:

platform routes

tenant routes

auth routes

API/device actions if needed


11.1 Platform Routes (Web)

Prefix suggestion: /platform

Example Routes

GET /platform/dashboard

GET /platform/tenants

GET /platform/tenants/create

POST /platform/tenants

GET /platform/tenants/{tenant}

PATCH /platform/tenants/{tenant}/status

GET /platform/plans

POST /platform/plans

PATCH /platform/plans/{plan}

GET /platform/subscriptions

POST /platform/subscriptions/{subscription}/payments


11.2 Tenant Routes (Web)

Prefix suggestion: tenant-scoped web routes after middleware resolves tenant.

Dashboard

GET /dashboard


Customers

GET /customers

GET /customers/create

POST /customers

GET /customers/{customer}

GET /customers/{customer}/edit

PUT /customers/{customer}

PATCH /customers/{customer}/status

GET /customers/{customer}/ledger


Packages

GET /packages

POST /packages

PUT /packages/{package}


Areas / POPs

GET /areas

POST /areas

GET /pops

POST /pops


Services

GET /services

POST /services

GET /services/{service}

PATCH /services/{service}/status

POST /services/{service}/map-onu


OLT

GET /olt-devices

GET /olt-devices/create

POST /olt-devices

GET /olt-devices/{device}

PUT /olt-devices/{device}

POST /olt-devices/{device}/test-connection

POST /olt-devices/{device}/sync-onus

GET /olt-devices/{device}/logs


Billing / Invoices

GET /invoices

POST /invoices/generate

GET /invoices/{invoice}

GET /invoices/{invoice}/print

PATCH /invoices/{invoice}/cancel


Payments

GET /payments

GET /payments/create

POST /payments

GET /payments/{payment}

POST /payments/{payment}/reverse

GET /payments/{payment}/receipt


Due Management

GET /dues

GET /dues/aging

GET /dues/suspension-candidates


Reports

GET /reports/collections

GET /reports/billing

GET /reports/due

GET /reports/customers

GET /reports/services

GET /reports/olt


Staff / Roles

GET /staff

POST /staff

PUT /staff/{user}

GET /roles

POST /roles


Settings

GET /settings/company

PUT /settings/company

GET /settings/billing

PUT /settings/billing

GET /settings/notifications

PUT /settings/notifications

GET /settings/olt

PUT /settings/olt



---

12. Controller Plan

Platform Controllers

PlatformDashboardController

TenantController

SubscriptionPlanController

TenantSubscriptionController

TenantSubscriptionPaymentController


Tenant Controllers

DashboardController

CustomerController

CustomerLedgerController

PackageController

AreaController

PopController

CustomerServiceController

OltDeviceController

OltDeviceActionController

InvoiceController

InvoiceGenerationController

PaymentController

PaymentReversalController

DueController

ReportController

StaffController

RoleController

SettingsController

SmsTemplateController

AuditLogController



---

13. Core Service Classes

13.1 Platform Services

TenantProvisioningService

TenantPlanLimitService

TenantSubscriptionService

TenantAccessGateService


13.2 Billing Services

InvoiceGenerationService

InvoiceNumberService

CustomerBalanceService

PaymentAllocationService

PaymentReversalService


13.3 OLT Services

OltAdapterManager

OltCredentialService

OltDiscoveryService

OltActionService

OnuSyncService

ServiceProvisioningService


13.4 Support Services

SmsDispatchService

ReportQueryService

AuditLoggerService

SettingsService



---

14. OLT Adapter Contract

14.1 Interface Shape

Each adapter should follow one standard contract.

Example methods:

testConnection(): AdapterResult

fetchDeviceInfo(): AdapterResult

fetchPonPorts(): AdapterResult

fetchOnuList(array $filters = []): AdapterResult

fetchOnuStatus(string $identifier): AdapterResult

enableSubscriber(CustomerService $service): AdapterResult

disableSubscriber(CustomerService $service): AdapterResult

reconnectSubscriber(CustomerService $service): AdapterResult

capabilities(): array


14.2 Adapter Result Format

Return a standard result object with:

success boolean

message

normalized data array

raw payload optional

error code optional


14.3 Why This Matters

This avoids mixing vendor-specific logic into controllers and billing services.


---

15. Queue / Job Design

Use queued jobs for operations that may take time.

Recommended Jobs

GenerateMonthlyInvoicesJob

SendSmsJob

SyncOnuListJob

ExecuteOltActionJob

GenerateReportExportJob later


Rules

log job failure

retry only where safe

long device actions should not block UI response



---

16. Middleware Plan

Key Middleware

ResolveTenantFromDomainOrSession

EnsureTenantIsActive

EnforceTenantPlanLimits

CheckPermission

LogSensitiveAction optional wrapper



---

17. Validation Rules Summary

Customer

full_name required

primary_phone required and normalized

customer_code required and unique per tenant


Package

name required

price numeric and non-negative


Invoice Generation

billing_month required

generation scope required


Payment

customer required

amount > 0

payment_date required

method required


OLT Device

name required

vendor required

auth fields required based on auth_type



---

18. Reporting Query Strategy

For MVP, use query services instead of heavy analytics tooling.

Example classes:

CollectionReportQuery

DueReportQuery

BillingSummaryQuery

OltActionLogQuery


All queries must filter by:

tenant

date range

optional area/package/collector/status/device



---

19. Tailwind UI Planning

19.1 Layout Pattern

left sidebar

top bar with tenant/company identity

filter row

summary cards

table/list content

drawer or modal for quick actions


19.2 Key Reusable Components

stat cards

status badges

searchable tables

money blocks with ৳ format

filter panels

form sections

timeline/activity list

invoice and receipt print templates


19.3 Important Pages to Design First

1. Login


2. Tenant Dashboard


3. Customer List


4. Customer Profile + Ledger


5. Invoice Generation Screen


6. Payment Collection Screen


7. Due List


8. OLT Device Detail Screen


9. OLT Action Log Screen


10. Platform Tenant List




---

20. Engineering Backlog by Phase

Phase 1 — Foundation

bootstrap Laravel app

auth setup

platform admin auth

tenant resolver

tenant table and plan tables

RBAC setup

settings framework


Phase 2 — Core Data Modules

areas and POPs

packages

customers

customer services

staff management


Phase 3 — Billing Core

invoices

invoice generation service

payments

payment allocations

receipts

due queries


Phase 4 — OLT Integration

olt devices table and UI

adapter contract

one vendor adapter starter

connection test action

ONU sync action

service mapping

suspend/reconnect action flow


Phase 5 — Reporting and Notifications

SMS templates/logs

collection reports

due reports

billing reports

OLT reports

audit logs


Phase 6 — Hardening

policy checks

transactional safety review

indexing review

UI polish

QA scenarios

pilot deployment prep



---

21. Testing Checklist

Unit Tests

invoice calculation

due calculation

payment allocation

payment reversal

tenant scope enforcement

plan limit enforcement


Feature Tests

tenant user cannot see another tenant data

invoice generation does not duplicate same month

payment updates invoice balances correctly

suspension changes service status

OLT action request is logged


Manual QA

search flows

collection workflow

reverse payment workflow

OLT connection test flow

invoice print and receipt print

plan suspension behavior



---

22. Deployment Notes for MVP

Recommended MVP Deployment

single Laravel app

MySQL database

supervisor for queues

file storage for logos and PDFs

HTTPS mandatory

daily DB backup


Environment Configuration

tenant domain config

queue config

mail config if needed later

SMS provider config if used

encrypted app key storage



---

23. Final Build Order Recommendation

If building this with limited time, use this exact order:

1. platform + tenant auth + RBAC


2. tenant settings


3. packages + areas + customers


4. customer services


5. invoice generation


6. payment collection + receipt


7. due reports


8. OLT device registry + adapter contract


9. ONU sync + service mapping


10. OLT suspend/reconnect actions


11. SMS logs/templates


12. reports + audit logs



This order gives a usable business product early, then adds network integration.


---

24. Best Next Output After This Document

The next strongest artifacts to produce are:

1. database ERD diagram


2. Laravel migration files list with columns and datatypes


3. route file skeleton


4. controller method skeleton list


5. service class skeletons


6. Tailwind wireframes for dashboard, customer, billing, payment, OLT screens


7. 8-week sprint plan




---

25. Final Note

This PRD is scoped carefully so the MVP remains realistic.

The product becomes valuable very early because even before deep OLT automation, it already solves the most painful daily ISP tasks:

customer management

billing

due control

payment collection

service state control

tenant SaaS monetization


And because OLT support is built into the architecture from day one, the product can grow into a stronger ISP operations platform without needing a major rewrite.
