ISP Billing Software Specification (Laravel + Tailwind)

SaaS Multi-Tenant + OLT API Enabled MVP for Bangladesh


---

1. Document Purpose

This is a complete, comprehensive, easy-to-understand product specification for building an ISP Billing Software using Laravel and Tailwind CSS, designed for Bangladesh, using Bangladeshi Taka (BDT / ৳), and planned from scratch to a fully usable MVP.

This version makes the following part of the MVP itself:

SaaS multi-tenant architecture

OLT API support

ISP onboarding and subscription billing

customer billing and collections

service provisioning-ready workflows

staff and operations management

reports and notifications


This document is intended to be usable by founders, product managers, developers, UI/UX designers, QA testers, operations teams, billing teams, and ISP owners.


---

2. Product Summary

2.1 What the Product Is

This product is a SaaS ISP Billing and Operations Platform where multiple internet providers can sign up and use the same platform with full data separation.

Each ISP tenant can manage:

customers

internet packages

invoices

payment collection

due tracking

service areas

staff accounts

SMS notifications

reports

OLT devices and ONU/customer line provisioning workflows


At the platform level, the software owner can manage:

ISP subscriptions

tenant plans

usage limits

global settings

support and monitoring


2.2 Main Problem It Solves

Small and medium ISPs in Bangladesh often run operations using spreadsheets, local desktop software, notebooks, screenshots from bKash/Nagad, and manual line activation/deactivation.

This creates:

billing confusion

missed payments

wrong due amounts

poor reporting

no central customer ledger

weak staff accountability

line status mismatch between billing and network side


2.3 Main Solution

Build a web-based multi-tenant SaaS ISP billing system where each ISP gets a secure workspace and can operate daily billing, collections, service state changes, and OLT-linked workflows from one dashboard.

2.4 MVP Goal

The MVP should be good enough for a real ISP in Bangladesh to use daily for:

customer onboarding

bill generation

collection

due management

line status operations

OLT-linked service workflow preparation or direct action where supported

SaaS tenant subscription and billing



---

3. User Types

3.1 Platform-Level Users

Platform Super Admin

Can manage:

all tenants

subscription plans

global configs

support access

feature flags

usage reports


Platform Finance/Admin Staff

Can manage:

ISP subscription invoices

tenant payment status

plan upgrades and downgrades

trials and renewals


3.2 Tenant-Level Users (Inside Each ISP)

ISP Owner / Tenant Admin

Full control of their own ISP workspace.

Accounts Manager

Handles:

invoices

collections

due reports

waivers and discounts


Billing Officer / Collector

Handles:

payment collection

receipt printing

customer due checking


Support Agent

Handles:

customer lookup

notes

service status view

complaint support later


Technician / Field Staff

Handles:

installation updates

device assignment

ONU/line actions if allowed

service activation workflows


Area Manager

Handles:

area-based customers

collection reports

local staff visibility


Customer Portal User (optional MVP-lite)

Can view:

current due

invoices

payments

package

connection status



---

4. Business Scope

4.1 In Scope for MVP

SaaS / Platform Scope

tenant sign-up or manual tenant creation

subscription plans

trial support

tenant activation and suspension

per-plan feature limits

tenant-specific settings

tenant domain/subdomain support


ISP Operations Scope

customer management

package management

area / zone / POP management

OLT and ONU mapping basics

monthly billing

invoice generation

manual and assisted payment collection

due tracking

line/service status management

SMS notifications

dashboards and reports

activity logs

staff and role permissions


OLT Scope for MVP

OLT registration

OLT API credential management

device connectivity testing

fetch basic device info

fetch PON/ONU data where supported

map customers to ONU/service records

basic activate, suspend, reconnect, disconnect actions through adapter design

command/request logging


4.2 Out of Scope for First MVP but Planned Next

full automatic payment gateway reconciliation

advanced MikroTik PPPoE sync

full network topology map

live bandwidth graphs

auto fault detection

reseller hierarchy

mobile app

advanced accounting ledger

inventory and warehouse system

VAT/tax engine



---

5. Bangladesh-Specific Requirements

5.1 Currency

default currency: BDT

display symbol: ৳

all money should display in local readable format, e.g. ৳1,250.00

recommended storage for MVP: decimal(12,2)


5.2 Local Payment Methods

The system must support:

cash

bKash

Nagad

Rocket

bank transfer

card/manual POS

manual online transfer

adjustment / waiver


5.3 Local Phone Format

Support and normalize:

01XXXXXXXXX

+8801XXXXXXXXX

8801XXXXXXXXX


5.4 Bangla-Friendly Language

UI should be written in simple English terms, with future-ready support for Bangla labels and SMS templates.

5.5 Common ISP Billing Habits in Bangladesh

Must support:

monthly fixed package billing

due carry forward

partial payment

manual discount

reconnection fee

installation fee

suspend on due

local collector cash collection



---

6. Product Principles

The system must be:

simple for local office staff

fast on average office hardware

mobile usable for collectors and technicians

accurate in money and due calculation

secure in multi-tenant isolation

easy to extend for different OLT vendors

practical and not over-engineered



---

7. High-Level Product Architecture

7.1 Architecture Style

Use a modular monolith with multi-tenant SaaS support.

This is best for MVP because it is:

faster to build

easier to deploy

easier to maintain

still scalable enough if designed cleanly


7.2 Main Layers

platform admin layer

tenant application layer

billing domain layer

device integration layer

notification layer

reporting layer


7.3 Suggested Tech Stack

Backend: Laravel 12+ or current stable

Frontend: Blade + Tailwind CSS

Light JS: Alpine.js

Database: MySQL / MariaDB

Queue: database queue for MVP, Redis later

Auth: Laravel Breeze

Permissions: Spatie Laravel Permission

PDF: DomPDF or Snappy

Secret handling: Laravel encrypted casts / secure config pattern



---

8. SaaS Multi-Tenant Design

8.1 Multi-Tenant Model

Each ISP is a tenant.

Each tenant has:

its own customers

its own packages

its own invoices

its own payments

its own staff

its own OLT devices

its own settings

its own reports


8.2 Recommended MVP Tenancy Approach

For MVP, use:

single database with tenant_id column isolation

application-level scoping for tenant-owned records

central/platform tables for platform-level records


This is simpler than separate database per tenant for early MVP.

8.3 Tenant Isolation Rules

Every tenant-owned table must have tenant_id.

Queries must always be scoped by tenant.

No tenant user should ever access another tenant’s:

customer data

payments

invoices

OLT credentials

staff

reports


8.4 Tenant Access Modes

Support:

subdomain access like ispname.yourapp.com

optional custom domain later


8.5 Tenant Lifecycle States

trial

active

suspended

past_due

cancelled


If tenant subscription is suspended:

tenant login may be blocked

read-only grace mode can be optional



---

9. SaaS Subscription Module

9.1 Why It Is Needed in MVP

Since this is a SaaS product, the MVP must include the ability for the platform owner to charge ISPs for using the software.

9.2 Tenant Subscription Plans

Example plans:

Starter

Growth

Pro

Enterprise


9.3 Plan Features

Plans can limit:

max customers

max staff users

max OLT devices

max SMS usage

advanced reporting access

API access

branding features


9.4 SaaS Billing Cycle

Support:

monthly subscription

yearly subscription

trial period

manual activation by platform admin

manual payment verification for MVP


9.5 Tenant Subscription Fields

plan name

billing cycle

price in BDT

start date

expiry date

subscription status

payment status

feature limits snapshot


9.6 SaaS Admin Actions

create tenant

activate tenant

suspend tenant

change plan

extend expiry

record payment

add discount

add note



---

10. Core Domain Modules

The ISP-side MVP should include these modules:

1. authentication and RBAC


2. tenant profile and settings


3. staff management


4. customer management


5. package management


6. area / zone / POP management


7. OLT device management


8. customer service / ONU mapping


9. billing and invoices


10. payment collection


11. due management


12. service status management


13. SMS notifications


14. reports


15. audit logs


16. optional customer portal




---

11. Authentication and Authorization

11.1 Platform-Level Auth

For SaaS owner admins.

11.2 Tenant-Level Auth

For ISP staff inside the tenant workspace.

11.3 Security Rules

password hashing

remember me

password reset

optional 2FA later

tenant-aware session scoping

permission checks for all sensitive actions


11.4 Role Types Inside Tenant

tenant_admin

accounts_manager

billing_officer

collector

support_agent

technician

area_manager

read_only_auditor (optional)


11.5 Permission Examples

view_customers

create_customer

edit_customer

generate_invoices

collect_payment

reverse_payment

view_reports

manage_olt

execute_olt_actions

manage_settings



---

12. Customer Management

12.1 Customer Purpose

Represents the subscriber receiving internet service.

12.2 Customer Fields

Identity

customer_code

full_name

company_name (optional)

primary_phone

secondary_phone (optional)

email (optional)

NID/passport (optional)

customer_type: home / business / corporate


Address

address_line

village/road/house/flat

area_id

thana/upazila

district

postal_code


Service Basics

connection_date

activation_date

package_id

monthly_bill_amount

status

assigned_collector_id

assigned_technician_id

pop_id


Financial

discount_type

discount_value

opening_due

installation_charge

reconnection_fee_policy

billing_note


Technical / OLT Related

olt_id (via service or mapping table)

pon_port

onu_id / onu_sn

onu_name/label

service_profile_name

line_profile_name

vlan/profile notes


12.3 Customer Status Values

pending_installation

active

temporary_hold

suspended_due

suspended_manual

disconnected

terminated


12.4 Customer Actions

create customer

edit customer

assign package

assign area

map to OLT/ONU

change status

collect payment

view ledger

view invoice list

view device mapping

add note



---

13. Package Management

13.1 Purpose

Defines the internet plans that can be assigned to customers.

13.2 Fields

package_code

package_name

speed_label

package_type

monthly_price

description

active_flag

optional vendor/service profile mapping fields


13.3 Rules

old invoices keep their own amount snapshot

package price changes should not modify old bills

package can be inactive without deleting history



---

14. Area / Zone / POP Management

14.1 Purpose

Organize customers and operations geographically or operationally.

14.2 Entities

zone/area

sub-area

POP

assigned collector

assigned technician


14.3 Usage

customer assignment

collection routing

due reports by area

technician operations

OLT/POP service grouping



---

15. OLT API Support (MVP)

This is a required MVP module.

15.1 Goal

Allow tenant ISPs to connect supported OLT devices/APIs to the platform so they can:

register devices

test connection

read ONU/device data

map customer services

send basic service actions

keep billing and line status more aligned


15.2 Important Reality

Different OLT vendors provide different APIs, authentication methods, and command patterns.

So MVP should use an adapter-based integration design.

15.3 Supported OLT MVP Capabilities

For each supported vendor, aim to support:

save API/base URL

save credentials securely

test connection

fetch device identity info

fetch PON list if available

fetch ONU list if available

get ONU status by identifier

activate/enable ONU or subscriber profile if API supports it

suspend/disable ONU or subscriber profile if API supports it

reconnect/re-enable service

sync selected ONU info manually

log every request and response summary


15.4 OLT Vendors

Vendor support should be designed as pluggable.

Examples of future vendor categories:

Huawei

ZTE

VSOL

C-Data

BDCOM

custom local gateway/bridge adapters


The MVP does not need to fully support all vendors equally, but the system design must support adding multiple vendors cleanly.

15.5 OLT Module Entities

OLT device

OLT credential config

OLT port/PON data

ONU records / synced devices

customer-to-ONU mapping

OLT action logs

sync history


15.6 OLT Device Fields

device_name

vendor

model

base_url / endpoint

ip_address

port

auth_type

username

encrypted password/token

area_id / pop_id

status active/inactive

notes


15.7 OLT Action Types

test_connection

sync_device_info

sync_onu_list

enable_subscriber

disable_subscriber

reconnect_subscriber

fetch_onu_status

custom command bridge (only if safe)


15.8 OLT Integration Rules

credentials must be encrypted at rest

only allowed users can execute device actions

device actions should be queued if they may take time

store request metadata and success/fail result

never expose raw secrets in UI or logs

keep vendor-specific logic in adapters/services


15.9 Recommended OLT Workflow in MVP

1. tenant admin adds OLT


2. system tests connection


3. tenant syncs ONU list or selected ONU info


4. staff maps customer service to ONU


5. customer billing status and service status become operationally linked


6. when customer becomes overdue, authorized user may suspend through app


7. when paid, authorized user may reconnect through app



15.10 MVP Limitation Handling

Because not all vendors expose the same actions:

app should show capability flags per adapter

unsupported actions should be disabled in UI

all failures must be clear and logged



---

16. Customer Service and ONU Mapping

16.1 Why Separate Service Records Are Needed

A customer may later have:

more than one connection

a replaced ONU

changed package

changed port mapping


So use a customer_services concept even in MVP.

16.2 Service Fields

customer_id

package_id

monthly_price

status

start_date

end_date nullable

olt_device_id nullable

pon_port nullable

onu_identifier nullable

onu_serial nullable

service_profile nullable

line_profile nullable

remote_reference nullable


16.3 Service Status

pending

active

suspended

disconnected

terminated


16.4 Mapping Actions

link service to OLT

link service to ONU

update line profile labels

check current ONU status

run supported line action



---

17. Billing and Invoice Management

17.1 Scope

The billing engine must support monthly internet billing for each tenant.

17.2 Invoice Types

recurring monthly invoice

one-time installation invoice

reconnection invoice

manual adjustment invoice item


17.3 Invoice Fields

invoice_number

customer_id

customer_service_id

billing_month

issue_date

due_date

subtotal

previous_due

discount_amount

adjustment_amount

total_amount

paid_amount

due_amount

status

notes


17.4 Invoice Status

draft

unpaid

partially_paid

paid

waived

cancelled


17.5 Monthly Bill Generation Rules

generate only for eligible active services

do not generate duplicate invoice for same tenant + service + month

allow generation by all/area/package/selected list

preview before confirming

show skipped records with reason


17.6 Calculation Formula

Invoice Total = Previous Due + Current Charge + Extra Charges - Discount ± Adjustments

17.7 Due Formula

Due = Total Amount - Paid Amount

17.8 Billing Settings

due date default

grace days

carry forward previous due on invoice display

pro-rata first month optional

suspend policy settings



---

18. Payment Collection

18.1 Payment Methods

cash

bKash

Nagad

Rocket

bank transfer

card

online/manual

adjustment


18.2 Payment Fields

payment_number

customer_id

payment_date

amount

method

transaction_reference

collector_id

note

status


18.3 Payment Use Cases

full payment

partial payment

multiple invoice payment

oldest due first allocation

exact invoice allocation

reversal by permission


18.4 Receipt Output

Must include:

ISP logo/name

receipt number

customer details

amount paid

payment method

transaction ID if any

date/time

collected by

remaining due


18.5 Reversal Rules

never hard delete payments

mark reversed with reason

update allocations and invoice balances

keep audit log



---

19. Due Management

19.1 Must-Have Views

total due list

overdue customer list

area-wise due report

due aging buckets

suspended due list


19.2 Aging Buckets

current due

1 month overdue

2 months overdue

3+ months overdue


19.3 Business Rules

due view must clearly show current bill vs old due

suspension candidate list should use settings thresholds

collector should quickly see payable amount and overdue months



---

20. Service Status Management

20.1 Status Actions

pending installation

activate

suspend for due

suspend manual

reconnect

disconnect

terminate


20.2 Status Change Tracking

Every status change must store:

old status

new status

reason

user

timestamp

optional related OLT action result


20.3 OLT-Linked Status Behavior

Where supported and authorized:

suspended_due can trigger OLT disable action

active/reconnected can trigger enable action


For safety, MVP should allow configuration:

billing-only status change

billing + OLT action together



---

21. SMS and Notifications

21.1 Notification Events

invoice generated

due reminder

payment received

suspension warning

reconnection success

tenant subscription expiry reminder (platform side)


21.2 SMS Requirements

template management

placeholder variables

send log

failed log

queued sending

tenant-level SMS provider config


21.3 Template Examples

Invoice SMS

প্রিয় {name}, আপনার {month} মাসের বিল ৳{amount}। অনুগ্রহ করে {due_date} এর মধ্যে পরিশোধ করুন। - {company}

Payment SMS

ধন্যবাদ {name}, ৳{amount} গ্রহণ করা হয়েছে। বর্তমান বকেয়া ৳{due}। রশিদ নং: {receipt_no}

Due SMS

প্রিয় {name}, আপনার মোট বকেয়া ৳{due}। সংযোগ সচল রাখতে দ্রুত পরিশোধ করুন।


---

22. Reports

22.1 Tenant Operational Reports

Collections

today collection

date range collection

collector-wise collection

area-wise collection

payment-method-wise collection


Billing

billed amount by month

invoice summary

unpaid invoices

partially paid invoices


Due

total due report

due aging

area-wise due

suspended customers due list


Customer

active customers

new customers

disconnected customers

package-wise customer count

area-wise customer list


OLT / Service

OLT-wise mapped customer count

ONU status summary

service status summary

failed OLT action log report


22.2 SaaS Platform Reports

total tenants

active tenants

trial tenants

expiring subscriptions

plan-wise tenant count

subscription collection report



---

23. Settings

23.1 Platform Settings

platform name

support contact

default currency

default trial days

tenant creation policy

platform SMS/email settings if needed


23.2 Tenant Settings

company name

logo

address

hotline

receipt footer

invoice footer

default due date

numbering prefixes

payment method settings

billing policy settings

OLT action mode settings


23.3 OLT Settings

device credentials

vendor adapter settings

timeout/retry settings

manual/automatic action toggles



---

24. Audit Logs

24.1 Must Log

tenant creation/update/suspension

customer create/update/status change

invoice generation/cancellation

payment collection/reversal

plan change

OLT config change

OLT action execution

settings updates

permission changes


24.2 Fields

tenant_id if applicable

user_id

action

entity_type

entity_id

old_values

new_values

ip_address

timestamp



---

25. Non-Functional Requirements

25.1 Performance

customer search must be quick

payment save should feel instant

invoice generation should use batching/jobs if large

OLT actions should support async jobs where needed


25.2 Security

encrypted OLT credentials

tenant data isolation

CSRF protection

authorization checks

secure secret handling

no hard delete of financial records


25.3 Reliability

payment and invoice actions must use DB transactions

retries for OLT/API calls where safe

queue for notifications and longer tasks

failure logging mandatory


25.4 Scalability

all tenant tables indexed by tenant_id

indexes on customer_code, phone, billing_month, status

pagination for big lists



---

26. Database Design

26.1 Platform-Level Tables

tenants

tenant_domains

subscription_plans

tenant_subscriptions

tenant_subscription_payments

platform_users

feature_flags (optional)


26.2 Tenant-Owned Tables

users

roles / permissions tables

customers

customer_addresses

areas

pops

packages

olt_devices

olt_action_logs

synced_onus

customer_services

invoices

invoice_items

payments

payment_allocations

status_histories

notes

sms_logs

settings

activity_logs


26.3 Key Table Suggestions

tenants

id

name

slug

company_name

owner_name

owner_phone

owner_email

status

trial_ends_at nullable

activated_at nullable

suspended_at nullable

timestamps


subscription_plans

id

name

code

price_monthly decimal(12,2)

price_yearly decimal(12,2)

max_customers nullable

max_users nullable

max_olt_devices nullable

max_sms_per_month nullable

feature_config json

is_active boolean

timestamps


tenant_subscriptions

id

tenant_id

subscription_plan_id

billing_cycle

start_date

end_date

status

amount decimal(12,2)

feature_snapshot json

timestamps


olt_devices

id

tenant_id

name

vendor

model nullable

base_url nullable

ip_address nullable

port nullable

auth_type

username nullable

secret_encrypted nullable

area_id nullable

pop_id nullable

capability_flags json nullable

is_active boolean

last_connected_at nullable

last_sync_at nullable

notes nullable

timestamps


synced_onus

id

tenant_id

olt_device_id

pon_port nullable

onu_identifier nullable

serial_number nullable

name nullable

status nullable

raw_payload json nullable

last_seen_at nullable

timestamps


customer_services

id

tenant_id

customer_id

package_id

monthly_price decimal(12,2)

status

start_date

end_date nullable

olt_device_id nullable

synced_onu_id nullable

pon_port nullable

onu_identifier nullable

service_profile nullable

line_profile nullable

remote_reference nullable

timestamps


olt_action_logs

id

tenant_id

olt_device_id

customer_service_id nullable

action_type

request_summary json nullable

response_summary json nullable

status

executed_by nullable

executed_at datetime

error_message nullable

timestamps


customers

id

tenant_id

customer_code

full_name

company_name nullable

primary_phone

secondary_phone nullable

email nullable

customer_type

area_id nullable

pop_id nullable

assigned_collector_id nullable

assigned_technician_id nullable

connection_date nullable

activation_date nullable

status

discount_type nullable

discount_value decimal(12,2) default 0

opening_due decimal(12,2) default 0

installation_charge decimal(12,2) default 0

notes nullable

deleted_at nullable

timestamps


packages

id

tenant_id

package_code

name

speed_label

package_type

price decimal(12,2)

description nullable

is_active boolean

timestamps


invoices

id

tenant_id

invoice_number

customer_id

customer_service_id nullable

billing_month

issue_date

due_date nullable

subtotal decimal(12,2)

previous_due decimal(12,2) default 0

discount_amount decimal(12,2) default 0

adjustment_amount decimal(12,2) default 0

total_amount decimal(12,2)

paid_amount decimal(12,2) default 0

due_amount decimal(12,2) default 0

status

generated_batch_id nullable

notes nullable

timestamps


payments

id

tenant_id

payment_number

customer_id

payment_date datetime

amount decimal(12,2)

method

transaction_reference nullable

collector_id nullable

note nullable

status

reversed_at nullable

reversed_by nullable

timestamps


payment_allocations

id

tenant_id

payment_id

invoice_id

allocated_amount decimal(12,2)

timestamps



---

27. Main Business Rules

27.1 Tenant Rules

every tenant-owned record must include tenant_id

all queries must scope to current tenant

plan limits must be enforced before create actions


27.2 Invoice Rules

one recurring invoice per tenant + service + month

no duplicate recurring billing

old invoices stay unchanged after package price edits


27.3 Payment Rules

amount must be positive

partial payment allowed

reversal only by permission

allocations update invoice status automatically


27.4 OLT Rules

only supported actions shown in UI

OLT failures must not silently change billing state

action result must be visible to operator

app should support “billing only” and “billing + OLT action” modes


27.5 Subscription Rules

expired tenant may be blocked or grace-limited

subscription payment updates tenant status

plan change should not destroy tenant data



---

28. UI / UX Structure

28.1 Tenant App Main Navigation

Dashboard

Customers

Packages

Areas / POPs

OLT Devices

Services / ONU Mapping

Billing / Invoices

Payments

Due List

Reports

SMS Logs

Staff & Roles

Settings


28.2 Platform Admin Navigation

Dashboard

Tenants

Subscription Plans

Tenant Subscriptions

Collections

Support / Logs

Settings


28.3 Design Style

Use Tailwind with:

clean cards

strong table filters

large readable amounts

color-coded statuses

mobile-responsive collector screens

simple forms with grouped sections



---

29. API / Service Layer Design

29.1 Internal Service Classes

Recommended services:

TenantProvisioningService

SubscriptionService

InvoiceGenerationService

PaymentAllocationService

CustomerBalanceService

OltAdapterManager

OltActionService

SmsDispatchService

ReportQueryService


29.2 OLT Adapter Interface Responsibilities

Each vendor adapter should implement methods like:

testConnection()

fetchDeviceInfo()

fetchPonPorts()

fetchOnuList()

fetchOnuStatus(identifier)

enableSubscriber(service)

disableSubscriber(service)

reconnectSubscriber(service)


This keeps vendor-specific code separate from billing logic.


---

30. MVP Release Plan

Phase 1: SaaS Foundation

tenant model

subscription plans

platform admin panel

tenant onboarding

tenant-scoped auth


Phase 2: ISP Core Operations

customer module

packages

areas/POPs

invoices

payments

due tracking

reports


Phase 3: OLT MVP Integration

OLT device registry

vendor adapter structure

connection test

ONU sync basics

customer-to-ONU mapping

suspend/reconnect actions where supported


Phase 4: Polish and Launch

SMS templates and logs

better dashboards

audit logs

print/PDF outputs

permissions hardening

QA and bug fixing



---

31. MVP Acceptance Criteria

The MVP is usable when:

a new ISP tenant can be created and activated

tenant staff can log in securely

customer records can be managed

monthly invoices can be generated

payments can be collected and reversed safely

due reports are accurate

tenant subscription plans and payments work

at least one OLT adapter flow can:

save credentials

test connection

sync basic device/ONU data

perform at least one service action like suspend/reconnect if vendor supports it


audit logs and error handling are present



---

32. Risks and Practical Notes

32.1 OLT Integration Risk

OLT APIs vary a lot.

So the real MVP success depends on:

choosing one or two real vendor targets first

building a stable adapter pattern

not assuming all vendors work the same way


32.2 SaaS Complexity Risk

Multi-tenant SaaS adds complexity.

To keep MVP manageable:

use single DB with tenant_id

use modular monolith

keep platform admin separate from tenant panels


32.3 Payment Reality in Bangladesh

Most collections may still be manual.

So MVP should prioritize:

strong manual collection flow

transaction reference storage

collector-wise reporting



---

33. Final Recommended MVP Definition

A usable first MVP should include these exact business outcomes:

Platform Side

create and manage ISP tenants

create subscription plans

activate/suspend tenants

track tenant subscription payments


Tenant Side

manage customers

manage packages and areas

manage OLT devices

map customers to services/ONU

generate monthly bills

collect payments in BDT

track dues and partial payments

suspend/reconnect service status

optionally trigger OLT action where supported

send SMS reminders and confirmations

view reports and logs


This version would be strong enough to demo, pilot, and onboard real ISP clients in Bangladesh.


---

34. Best Next Deliverables

After approving this spec, the best next documents to produce are:

1. database ERD


2. module-by-module user stories


3. route list and controller map


4. Laravel migration plan


5. Tailwind admin UI wireframe


6. API contract for OLT adapters


7. phased development backlog
