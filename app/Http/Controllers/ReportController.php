<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller {
    public function collections(Request $request) {
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : now()->startOfMonth();
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : now()->endOfMonth();

        $payments = Payment::with('customer','collector')
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->where('status','active')
            ->orderByDesc('payment_date')
            ->get();

        $summary = [
            'total' => $payments->sum('amount'),
            'count' => $payments->count(),
            'by_method' => $payments->groupBy('method')->map(fn($g) => $g->sum('amount')),
            'by_collector' => $payments->groupBy('collector_id')->map(fn($g) => [
                'name' => $g->first()->collector?->name ?? 'Unknown',
                'count' => $g->count(),
                'total' => $g->sum('amount'),
            ]),
        ];

        return view('reports.collections', compact('payments','summary','fromDate','toDate'));
    }

    public function billing(Request $request) {
        $month = $request->month ?? now()->format('Y-m');

        $invoices = Invoice::with('customer')
            ->where('billing_month', $month)
            ->orderByDesc('created_at')
            ->get();

        $summary = [
            'total_invoiced' => $invoices->sum('total_amount'),
            'total_paid' => $invoices->sum('paid_amount'),
            'total_due' => $invoices->sum('due_amount'),
            'count' => $invoices->count(),
            'paid_count' => $invoices->where('status','paid')->count(),
            'unpaid_count' => $invoices->whereIn('status',['unpaid','partially_paid'])->count(),
        ];

        return view('reports.billing', compact('invoices','summary','month'));
    }
}
