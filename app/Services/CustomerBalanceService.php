<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;

class CustomerBalanceService {

    public function getTotalDue(Customer $customer): float {
        return (float)Invoice::where('customer_id', $customer->id)
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->sum('due_amount');
    }

    public function getTotalPaid(Customer $customer): float {
        return (float)Payment::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->sum('amount');
    }

    public function getBalance(Customer $customer): array {
        $totalInvoiced = (float)Invoice::where('customer_id', $customer->id)->sum('total_amount');
        $totalPaid = $this->getTotalPaid($customer);
        $totalDue = $this->getTotalDue($customer);

        return [
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'opening_due' => (float)$customer->opening_due,
        ];
    }
}
