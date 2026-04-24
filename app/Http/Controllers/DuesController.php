<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DuesController extends Controller {
    public function index(Request $request) {
        $query = Customer::with('activeService.package')
            ->whereHas('invoices', fn($q) => $q->whereIn('status',['unpaid','partially_paid']));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name','like',"%$search%")
                  ->orWhere('customer_code','like',"%$search%")
                  ->orWhere('primary_phone','like',"%$search%");
            });
        }

        $customers = $query->orderBy('full_name')->paginate(20)->withQueryString();

        // Get due amounts for each customer
        $dueMap = Invoice::whereIn('customer_id', $customers->pluck('id'))
            ->whereIn('status', ['unpaid','partially_paid'])
            ->selectRaw('customer_id, SUM(due_amount) as total_due, COUNT(*) as invoice_count')
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        $totalDue = Invoice::whereIn('status', ['unpaid','partially_paid'])->sum('due_amount');

        return view('dues.index', compact('customers','dueMap','totalDue'));
    }
}
