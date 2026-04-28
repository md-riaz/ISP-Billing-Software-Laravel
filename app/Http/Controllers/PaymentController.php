<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentAllocationService;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function __construct(private PaymentAllocationService $allocationService) {}

    public function index(Request $request) {
        $query = Payment::with('customer','collector');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_number','like',"%$search%")
                  ->orWhereHas('customer', fn($cq) => $cq->where('full_name','like',"%$search%"));
            });
        }
        if ($request->filled('method')) $query->where('method', $request->method);
        if ($request->filled('date_from')) $query->whereDate('payment_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('payment_date', '<=', $request->date_to);
        $payments = $query->orderByDesc('payment_date')->paginate(20)->withQueryString();
        return view('payments.index', compact('payments'));
    }

    public function create(Request $request) {
        $customers = Customer::where('status','active')->orderBy('full_name')->get();
        $selectedCustomer = $request->filled('customer_id') ? Customer::with('invoices')->find($request->customer_id) : null;
        $unpaidInvoices = $selectedCustomer ? Invoice::where('customer_id', $selectedCustomer->id)
            ->whereIn('status', ['unpaid','partially_paid'])->orderBy('issue_date')->get() : collect();
        return view('payments.create', compact('customers','selectedCustomer','unpaidInvoices'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,bkash,nagad,rocket,bank,card,online,adjustment',
            'transaction_reference' => 'nullable|string|max:100',
            'note' => 'nullable|string',
        ]);

        $tenant = app('currentTenant');
        $paymentNumber = $this->generatePaymentNumber($tenant->id);

        $payment = Payment::create(array_merge($validated, [
            'tenant_id' => $tenant->id,
            'payment_number' => $paymentNumber,
            'collector_id' => auth()->id(),
            'status' => 'active',
        ]));

        $this->allocationService->allocate($payment);
        logActivity('payment_collected', 'Payment', $payment->id, null, ['amount' => $payment->amount]);

        return redirect()->route('payments.show', $payment)->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment) {
        $payment->load('customer','collector','reverser','allocations.invoice');
        return view('payments.show', compact('payment'));
    }

    public function reverse(Request $request, Payment $payment) {
        $validated = $request->validate(['reversal_reason' => 'required|string|max:500']);
        if ($payment->status === 'reversed') {
            return back()->with('error', 'Payment already reversed.');
        }
        $this->allocationService->reverseAllocation($payment);
        $payment->update([
            'status' => 'reversed',
            'reversed_at' => now(),
            'reversed_by' => auth()->id(),
            'reversal_reason' => $validated['reversal_reason'],
        ]);
        logActivity('payment_reversed', 'Payment', $payment->id);
        return redirect()->route('payments.show', $payment)->with('success', 'Payment reversed.');
    }

    private function generatePaymentNumber(int $tenantId): string {
        $prefix = 'PAY-' . now()->format('Ym') . '-';
        $last = Payment::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('payment_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('payment_number');
        $next = $last ? (intval(substr($last, -5)) + 1) : 1;
        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
