<?php
namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAllocation;

class PaymentAllocationService {

    public function allocate(Payment $payment): void {
        $remaining = $payment->amount;

        $unpaidInvoices = Invoice::where('customer_id', $payment->customer_id)
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->orderBy('issue_date')
            ->get();

        foreach ($unpaidInvoices as $invoice) {
            if ($remaining <= 0) break;

            $dueAmount = $invoice->due_amount;
            $allocate = min($remaining, $dueAmount);

            PaymentAllocation::create([
                'tenant_id' => $payment->tenant_id,
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'allocated_amount' => $allocate,
            ]);

            $newPaid = $invoice->paid_amount + $allocate;
            $newDue = $invoice->total_amount - $newPaid;
            $newStatus = $newDue <= 0 ? 'paid' : 'partially_paid';

            $invoice->update([
                'paid_amount' => $newPaid,
                'due_amount' => max(0, $newDue),
                'status' => $newStatus,
            ]);

            $remaining -= $allocate;
        }
    }

    public function reverseAllocation(Payment $payment): void {
        foreach ($payment->allocations as $allocation) {
            $invoice = $allocation->invoice;
            if ($invoice) {
                $newPaid = $invoice->paid_amount - $allocation->allocated_amount;
                $newDue = $invoice->total_amount - max(0, $newPaid);
                $status = $newPaid <= 0 ? 'unpaid' : 'partially_paid';

                $invoice->update([
                    'paid_amount' => max(0, $newPaid),
                    'due_amount' => $newDue,
                    'status' => $status,
                ]);
            }
            $allocation->delete();
        }
    }
}
