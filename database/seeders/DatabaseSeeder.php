<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\OltDevice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Pop;
use App\Models\SmsTemplate;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create Roles ─────────────────────────────────────────────────
        $roles = ['tenant_admin','accounts_manager','billing_officer','collector','support_agent','technician','area_manager'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ── 2. Create Platform Admin ─────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@platform.com'],
            [
                'tenant_id' => null,
                'name' => 'Platform Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        // ── 3. Create Subscription Plans ────────────────────────────────────
        $starter = SubscriptionPlan::firstOrCreate(['slug' => 'starter'], [
            'name' => 'Starter', 'price_monthly' => 999, 'price_yearly' => 9990,
            'max_customers' => 100, 'max_staff' => 3, 'max_olt_devices' => 1,
            'max_sms_monthly' => 500, 'has_reports' => true, 'has_api' => false, 'has_branding' => false, 'is_active' => true,
        ]);

        $growth = SubscriptionPlan::firstOrCreate(['slug' => 'growth'], [
            'name' => 'Growth', 'price_monthly' => 1999, 'price_yearly' => 19990,
            'max_customers' => 500, 'max_staff' => 10, 'max_olt_devices' => 5,
            'max_sms_monthly' => 2000, 'has_reports' => true, 'has_api' => true, 'has_branding' => false, 'is_active' => true,
        ]);

        SubscriptionPlan::firstOrCreate(['slug' => 'pro'], [
            'name' => 'Pro', 'price_monthly' => 3999, 'price_yearly' => 39990,
            'max_customers' => 9999, 'max_staff' => 50, 'max_olt_devices' => 20,
            'max_sms_monthly' => 10000, 'has_reports' => true, 'has_api' => true, 'has_branding' => true, 'is_active' => true,
        ]);

        // ── 4. Create Demo Tenant ────────────────────────────────────────────
        $tenant = Tenant::firstOrCreate(['slug' => 'demo-isp'], [
            'name' => 'Demo ISP Bangladesh',
            'email' => 'admin@demo-isp.com',
            'phone' => '01700000001',
            'status' => 'active',
            'trial_ends_at' => now()->addDays(30),
            'timezone' => 'Asia/Dhaka',
        ]);

        TenantSubscription::firstOrCreate(['tenant_id' => $tenant->id], [
            'plan_id' => $growth->id,
            'billing_cycle' => 'monthly',
            'price' => $growth->price_monthly,
            'starts_at' => now()->subMonth(),
            'expires_at' => now()->addMonth(),
            'status' => 'active',
        ]);

        // ── 5. Create Tenant Users ───────────────────────────────────────────
        $tenantAdmin = User::firstOrCreate(['email' => 'admin@demo.com'], [
            'tenant_id' => $tenant->id,
            'name' => 'ISP Admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $tenantAdmin->syncRoles(['tenant_admin']);

        $collector1 = User::firstOrCreate(['email' => 'collector@demo.com'], [
            'tenant_id' => $tenant->id,
            'name' => 'Rahim Collector',
            'phone' => '01711000001',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $collector1->syncRoles(['collector']);

        $tech1 = User::firstOrCreate(['email' => 'tech@demo.com'], [
            'tenant_id' => $tenant->id,
            'name' => 'Karim Technician',
            'phone' => '01711000002',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $tech1->syncRoles(['technician']);

        // ── 6. Set tenant context ────────────────────────────────────────────
        app()->instance('currentTenant', $tenant);

        // ── 7. Create Areas and POPs ─────────────────────────────────────────
        $area1 = Area::firstOrCreate(['tenant_id' => $tenant->id, 'name' => 'Mirpur'], ['description' => 'Mirpur Area']);
        $area2 = Area::firstOrCreate(['tenant_id' => $tenant->id, 'name' => 'Uttara'], ['description' => 'Uttara Area']);
        $area3 = Area::firstOrCreate(['tenant_id' => $tenant->id, 'name' => 'Dhanmondi'], ['description' => 'Dhanmondi Area']);

        $pop1 = Pop::firstOrCreate(['tenant_id' => $tenant->id, 'name' => 'Mirpur-10 POP'], ['area_id' => $area1->id, 'location' => 'Mirpur 10 Road']);
        $pop2 = Pop::firstOrCreate(['tenant_id' => $tenant->id, 'name' => 'Uttara Sector-7 POP'], ['area_id' => $area2->id, 'location' => 'Sector 7']);
        $pop3 = Pop::firstOrCreate(['tenant_id' => $tenant->id, 'name' => 'Dhanmondi-27 POP'], ['area_id' => $area3->id, 'location' => 'Road 27']);

        // ── 8. Create Packages ───────────────────────────────────────────────
        $packages = [
            ['code' => 'PKG-10', 'name' => '10 Mbps Basic', 'speed' => '10 Mbps', 'price' => 500],
            ['code' => 'PKG-20', 'name' => '20 Mbps Standard', 'speed' => '20 Mbps', 'price' => 800],
            ['code' => 'PKG-30', 'name' => '30 Mbps Plus', 'speed' => '30 Mbps', 'price' => 1200],
            ['code' => 'PKG-50', 'name' => '50 Mbps Premium', 'speed' => '50 Mbps', 'price' => 1800],
            ['code' => 'PKG-100', 'name' => '100 Mbps Ultra', 'speed' => '100 Mbps', 'price' => 3000],
        ];

        $createdPackages = [];
        foreach ($packages as $pkg) {
            $createdPackages[] = Package::firstOrCreate(
                ['tenant_id' => $tenant->id, 'package_code' => $pkg['code']],
                ['package_name' => $pkg['name'], 'speed_label' => $pkg['speed'], 'package_type' => 'home', 'monthly_price' => $pkg['price'], 'is_active' => true]
            );
        }

        // ── 9. Create OLT Device ─────────────────────────────────────────────
        $olt = OltDevice::firstOrCreate(
            ['tenant_id' => $tenant->id, 'device_name' => 'Main OLT - Mirpur'],
            [
                'vendor' => 'Huawei', 'model' => 'MA5608T', 'ip_address' => '192.168.1.1',
                'port' => 80, 'auth_type' => 'basic', 'username' => 'admin',
                'password' => 'admin123', 'area_id' => $area1->id, 'pop_id' => $pop1->id,
                'status' => 'active', 'notes' => 'Primary OLT for Mirpur area',
            ]
        );

        // ── 10. Create SMS Templates ──────────────────────────────────────────
        $smsTemplates = [
            ['event' => 'invoice_generated', 'name' => 'Invoice SMS', 'body' => 'প্রিয় {name}, আপনার {month} মাসের বিল ৳{amount}। অনুগ্রহ করে {due_date} এর মধ্যে পরিশোধ করুন। - {company}'],
            ['event' => 'payment_received', 'name' => 'Payment SMS', 'body' => 'ধন্যবাদ {name}, ৳{amount} গ্রহণ করা হয়েছে। বর্তমান বকেয়া ৳{due}। রশিদ নং: {receipt_no}'],
            ['event' => 'due_reminder', 'name' => 'Due Reminder', 'body' => 'প্রিয় {name}, আপনার মোট বকেয়া ৳{due}। সংযোগ সচল রাখতে দ্রুত পরিশোধ করুন। - {company}'],
        ];
        foreach ($smsTemplates as $tmpl) {
            SmsTemplate::firstOrCreate(
                ['tenant_id' => $tenant->id, 'event_type' => $tmpl['event']],
                ['template_name' => $tmpl['name'], 'message_body' => $tmpl['body'], 'is_active' => true]
            );
        }

        // ── 11. Create Customers and Services ────────────────────────────────
        $customerData = [
            ['name' => 'Mohammad Kamal', 'phone' => '01711111001', 'area' => $area1, 'pop' => $pop1, 'pkg' => $createdPackages[1], 'status' => 'active'],
            ['name' => 'Farida Begum', 'phone' => '01711111002', 'area' => $area1, 'pop' => $pop1, 'pkg' => $createdPackages[0], 'status' => 'active'],
            ['name' => 'Raihan Hossain', 'phone' => '01711111003', 'area' => $area2, 'pop' => $pop2, 'pkg' => $createdPackages[2], 'status' => 'active'],
            ['name' => 'Nasreen Akter', 'phone' => '01711111004', 'area' => $area2, 'pop' => $pop2, 'pkg' => $createdPackages[1], 'status' => 'suspended_due'],
            ['name' => 'Tariqul Islam', 'phone' => '01711111005', 'area' => $area3, 'pop' => $pop3, 'pkg' => $createdPackages[3], 'status' => 'active'],
            ['name' => 'Shahnaz Parvin', 'phone' => '01711111006', 'area' => $area1, 'pop' => $pop1, 'pkg' => $createdPackages[0], 'status' => 'active'],
            ['name' => 'Abdul Malek', 'phone' => '01711111007', 'area' => $area2, 'pop' => $pop2, 'pkg' => $createdPackages[4], 'status' => 'active'],
            ['name' => 'Roksana Khatun', 'phone' => '01711111008', 'area' => $area3, 'pop' => $pop3, 'pkg' => $createdPackages[1], 'status' => 'active'],
            ['name' => 'Jahangir Alam', 'phone' => '01711111009', 'area' => $area1, 'pop' => $pop1, 'pkg' => $createdPackages[2], 'status' => 'temporary_hold'],
            ['name' => 'Moriam Khanam', 'phone' => '01711111010', 'area' => $area2, 'pop' => $pop2, 'pkg' => $createdPackages[0], 'status' => 'active'],
            ['name' => 'Sabbir Ahmed', 'phone' => '01711111011', 'area' => $area3, 'pop' => $pop3, 'pkg' => $createdPackages[1], 'status' => 'active'],
            ['name' => 'Laila Arjuman', 'phone' => '01711111012', 'area' => $area1, 'pop' => $pop1, 'pkg' => $createdPackages[3], 'status' => 'active'],
            ['name' => 'Enamul Huq', 'phone' => '01711111013', 'area' => $area2, 'pop' => $pop2, 'pkg' => $createdPackages[2], 'status' => 'active'],
            ['name' => 'Farzana Yasmin', 'phone' => '01711111014', 'area' => $area3, 'pop' => $pop3, 'pkg' => $createdPackages[0], 'status' => 'disconnected'],
            ['name' => 'Mizanur Rahman', 'phone' => '01711111015', 'area' => $area1, 'pop' => $pop1, 'pkg' => $createdPackages[1], 'status' => 'active'],
            ['name' => 'Kamrunnahar', 'phone' => '01711111016', 'area' => $area2, 'pop' => $pop2, 'pkg' => $createdPackages[4], 'status' => 'active'],
            ['name' => 'Shakil Hossain', 'phone' => '01711111017', 'area' => $area3, 'pop' => $pop3, 'pkg' => $createdPackages[2], 'status' => 'suspended_due'],
            ['name' => 'Nasima Sultana', 'phone' => '01711111018', 'area' => $area1, 'pop' => $pop1, 'pkg' => $createdPackages[1], 'status' => 'active'],
            ['name' => 'Tanvir Khan', 'phone' => '01711111019', 'area' => $area2, 'pop' => $pop2, 'pkg' => $createdPackages[3], 'status' => 'active'],
            ['name' => 'Hosne Ara', 'phone' => '01711111020', 'area' => $area3, 'pop' => $pop3, 'pkg' => $createdPackages[0], 'status' => 'pending_installation'],
        ];

        $slug = strtoupper(substr($tenant->slug, 0, 4));
        $idx = Customer::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count();

        foreach ($customerData as $cd) {
            $idx++;
            $code = 'CST-' . $slug . '-' . str_pad($idx, 5, '0', STR_PAD_LEFT);
            $customer = Customer::withoutGlobalScopes()->firstOrCreate(
                ['tenant_id' => $tenant->id, 'primary_phone' => $cd['phone']],
                [
                    'customer_code' => $code, 'full_name' => $cd['name'],
                    'customer_type' => 'home', 'area_id' => $cd['area']->id, 'pop_id' => $cd['pop']->id,
                    'district' => 'Dhaka', 'thana' => $cd['area']->name,
                    'address_line' => 'House-' . rand(1, 99) . ', Road-' . rand(1, 20) . ', ' . $cd['area']->name . ', Dhaka',
                    'connection_date' => now()->subMonths(rand(3, 18)),
                    'activation_date' => now()->subMonths(rand(1, 12)),
                    'status' => $cd['status'],
                    'discount_type' => 'none', 'discount_value' => 0,
                    'opening_due' => 0, 'installation_charge' => 500,
                    'assigned_collector_id' => $collector1->id,
                    'assigned_technician_id' => $tech1->id,
                ]
            );

            // Create service for non-disconnected/terminated customers
            if (!in_array($cd['status'], ['disconnected', 'terminated', 'pending_installation'])) {
                $svcStatus = $cd['status'] === 'active' ? 'active' : ($cd['status'] === 'suspended_due' ? 'suspended' : 'pending');
                CustomerService::withoutGlobalScopes()->firstOrCreate(
                    ['tenant_id' => $tenant->id, 'customer_id' => $customer->id],
                    [
                        'package_id' => $cd['pkg']->id,
                        'monthly_price' => $cd['pkg']->monthly_price,
                        'status' => $svcStatus,
                        'start_date' => now()->subMonths(rand(1, 12)),
                        'olt_device_id' => $olt->id,
                        'pon_port' => 'PON-0/' . rand(1, 8),
                        'onu_identifier' => 'ONU-' . str_pad(rand(1, 64), 3, '0', STR_PAD_LEFT),
                        'onu_serial' => 'HWTC' . strtoupper(substr(md5($cd['phone']), 0, 8)),
                        'service_profile' => 'HSI_' . $cd['pkg']->speed_label,
                        'line_profile' => 'DEFAULT',
                    ]
                );
            }
        }

        // ── 12. Generate Invoices and Payments ───────────────────────────────
        $invoiceIdx = Invoice::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count();
        $paymentIdx = Payment::withoutGlobalScopes()->where('tenant_id', $tenant->id)->count();

        $activeServices = CustomerService::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('customer', 'package')
            ->get();

        foreach ($activeServices as $service) {
            // Generate last 3 months of invoices
            for ($m = 3; $m >= 1; $m--) {
                $billingMonth = now()->subMonths($m)->format('Y-m');
                $issueDate = Carbon::parse($billingMonth . '-01');
                $dueDate = $issueDate->copy()->addDays(10);
                $invoiceIdx++;
                $invNum = 'INV-' . $issueDate->format('Ym') . '-' . str_pad($invoiceIdx, 5, '0', STR_PAD_LEFT);

                $existing = Invoice::withoutGlobalScopes()
                    ->where('tenant_id', $tenant->id)
                    ->where('customer_service_id', $service->id)
                    ->where('billing_month', $billingMonth)
                    ->first();

                if ($existing) continue;

                $amount = $service->monthly_price;
                $invoice = Invoice::create([
                    'tenant_id' => $tenant->id,
                    'invoice_number' => $invNum,
                    'customer_id' => $service->customer_id,
                    'customer_service_id' => $service->id,
                    'billing_month' => $billingMonth,
                    'invoice_type' => 'recurring',
                    'issue_date' => $issueDate,
                    'due_date' => $dueDate,
                    'subtotal' => $amount,
                    'previous_due' => 0,
                    'discount_amount' => 0,
                    'adjustment_amount' => 0,
                    'total_amount' => $amount,
                    'paid_amount' => 0,
                    'due_amount' => $amount,
                    'status' => 'unpaid',
                    'generated_by' => $tenantAdmin->id,
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'tenant_id' => $tenant->id,
                    'description' => 'Internet Service - ' . $service->package->package_name . ' (' . $issueDate->format('F Y') . ')',
                    'quantity' => 1,
                    'unit_price' => $amount,
                    'amount' => $amount,
                ]);

                // Pay invoices for months 3 and 2 (leave month 1 unpaid for demo)
                if ($m >= 2) {
                    $paymentIdx++;
                    $payNum = 'PAY-' . $issueDate->format('Ym') . '-' . str_pad($paymentIdx, 5, '0', STR_PAD_LEFT);
                    $payment = Payment::create([
                        'tenant_id' => $tenant->id,
                        'payment_number' => $payNum,
                        'customer_id' => $service->customer_id,
                        'payment_date' => $dueDate->copy()->subDays(rand(1, 5)),
                        'amount' => $amount,
                        'method' => ['cash','bkash','nagad','rocket'][rand(0,3)],
                        'transaction_reference' => $m % 2 === 0 ? null : 'TRX' . strtoupper(substr(md5($payNum), 0, 8)),
                        'collector_id' => $collector1->id,
                        'status' => 'active',
                    ]);

                    PaymentAllocation::create([
                        'tenant_id' => $tenant->id,
                        'payment_id' => $payment->id,
                        'invoice_id' => $invoice->id,
                        'allocated_amount' => $amount,
                    ]);

                    $invoice->update(['paid_amount' => $amount, 'due_amount' => 0, 'status' => 'paid']);
                }
            }
        }

        $this->command->info('Demo seeder completed!');
        $this->command->info('Platform Admin: admin@platform.com / password');
        $this->command->info('Tenant Admin:   admin@demo.com / password');
        $this->command->info('Collector:      collector@demo.com / password');
    }
}
