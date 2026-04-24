<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;

class InvoiceGenerationService {

    public function generateForService(CustomerService $service, string $billingMonth, array $options = []): Invoice {
        $customer = $service->customer;
        $tenant = app('currentTenant');
        $issueDate = Carbon::now();
        $dueDate = $issueDate->copy()->addDays(10);

        $monthLabel = Carbon::parse($billingMonth . '-01')->format('F Y');
        $subtotal = $service->monthly_price;

        // Apply discount
        $discountAmount = 0;
        if ($customer->discount_type === 'fixed') {
            $discountAmount = $customer->discount_value;
        } elseif ($customer->discount_type === 'percent') {
            $discountAmount = round($subtotal * $customer->discount_value / 100, 2);
        }

        // Calculate previous due
        $previousDue = $this->getCustomerPreviousDue($customer);

        $adjustmentAmount = $options['adjustment_amount'] ?? 0;
        $totalAmount = $subtotal - $discountAmount + $previousDue + $adjustmentAmount;
        $totalAmount = max(0, $totalAmount);

        $invoiceNumber = $this->generateInvoiceNumber($tenant->id);

        $invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'invoice_number' => $invoiceNumber,
            'customer_id' => $customer->id,
            'customer_service_id' => $service->id,
            'billing_month' => $billingMonth,
            'invoice_type' => 'recurring',
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'subtotal' => $subtotal,
            'previous_due' => $previousDue,
            'discount_amount' => $discountAmount,
            'adjustment_amount' => $adjustmentAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'due_amount' => $totalAmount,
            'status' => 'unpaid',
            'notes' => $options['notes'] ?? null,
            'generated_by' => auth()->id(),
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'tenant_id' => $tenant->id,
            'description' => 'Internet Service - ' . $service->package->package_name . ' (' . $monthLabel . ')',
            'quantity' => 1,
            'unit_price' => $subtotal,
            'amount' => $subtotal,
        ]);

        if ($discountAmount > 0) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'tenant_id' => $tenant->id,
                'description' => 'Discount (' . $customer->discount_type . ')',
                'quantity' => 1,
                'unit_price' => -$discountAmount,
                'amount' => -$discountAmount,
            ]);
        }

        return $invoice;
    }

    public function generateBulk(string $billingMonth): array {
        $generated = [];
        $tenant = app('currentTenant');

        $services = CustomerService::with('customer', 'package')
            ->where('status', 'active')
            ->get();

        foreach ($services as $service) {
            $existing = Invoice::where('customer_service_id', $service->id)
                ->where('billing_month', $billingMonth)
                ->where('invoice_type', 'recurring')
                ->first();

            if (!$existing) {
                try {
                    $generated[] = $this->generateForService($service, $billingMonth);
                } catch (\Exception $e) {
                    // Log and continue
                }
            }
        }

        return $generated;
    }

    private function getCustomerPreviousDue(Customer $customer): float {
        $totalDue = Invoice::where('customer_id', $customer->id)
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->sum('due_amount');

        return (float)$totalDue;
    }

    public function generateInvoiceNumber(int $tenantId): string {
        $prefix = 'INV-' . now()->format('Ym') . '-';
        $last = Invoice::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('invoice_number');

        $next = $last ? (intval(substr($last, -5)) + 1) : 1;
        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
