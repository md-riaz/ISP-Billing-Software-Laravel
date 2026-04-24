<?php
namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DuesController extends Controller {
    public function index(Request $request) {
        $query = Customer::with('activeService.package','area')
            ->whereHas('invoices', fn($q) => $q->whereIn('status',['unpaid','partially_paid']));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name','like',"%$search%")
                  ->orWhere('customer_code','like',"%$search%")
                  ->orWhere('primary_phone','like',"%$search%");
            });
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
        $overdueDue = Invoice::whereIn('status', ['unpaid','partially_paid'])
            ->where('due_date', '<', now())->sum('due_amount');
        $overdueCount = Customer::whereHas('invoices', fn($q) =>
            $q->whereIn('status',['unpaid','partially_paid'])->where('due_date','<',now())
        )->count();
        $suspendedCount = Customer::where('status','suspended_due')->count();
        $currentMonthBilled = Invoice::where('billing_month', now()->format('Y-m'))->sum('total_amount');
        $areas = Area::orderBy('name')->get();

        return view('dues.index', compact('customers','dueMap','totalDue','overdueDue','overdueCount','suspendedCount','currentMonthBilled','areas'));
    }
}
