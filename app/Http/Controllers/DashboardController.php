<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CustomerService;
use Carbon\Carbon;

class DashboardController extends Controller {
    public function index() {
        $tenant = app('currentTenant');
        $thisMonth = now()->format('Y-m');

        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('status', 'active')->count(),
            'total_services' => CustomerService::count(),
            'active_services' => CustomerService::where('status', 'active')->count(),
            'monthly_revenue' => Payment::whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->where('status', 'active')
                ->sum('amount'),
            'total_due' => Invoice::whereIn('status', ['unpaid','partially_paid'])->sum('due_amount'),
            'invoices_this_month' => Invoice::where('billing_month', $thisMonth)->count(),
            'payments_this_month' => Payment::whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->count(),
        ];

        $recentPayments = Payment::with('customer')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentInvoices = Invoice::with('customer')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $overdueCustomers = Customer::whereHas('invoices', function($q) {
                $q->whereIn('status', ['unpaid','partially_paid'])
                  ->where('due_date', '<', now());
            })
            ->where('status', 'active')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recentPayments', 'recentInvoices', 'overdueCustomers'));
    }
}
