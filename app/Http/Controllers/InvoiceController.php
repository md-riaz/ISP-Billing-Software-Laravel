<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\Invoice;
use App\Services\InvoiceGenerationService;
use Illuminate\Http\Request;

class InvoiceController extends Controller {
    public function __construct(private InvoiceGenerationService $invoiceService) {}

    public function index(Request $request) {
        $query = Invoice::with('customer');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number','like',"%$search%")
                  ->orWhereHas('customer', fn($cq) => $cq->where('full_name','like',"%$search%"));
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('month')) $query->where('billing_month', $request->month);

        $invoices = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice) {
        $invoice->load('customer','items','allocations.payment','customerService.package','generator');
        return view('invoices.show', compact('invoice'));
    }

    public function generate(Request $request) {
        $customers = Customer::where('status','active')->with('activeService.package')->get();
        $thisMonth = now()->format('Y-m');
        return view('invoices.generate', compact('customers', 'thisMonth'));
    }

    public function generateBulk(Request $request) {
        $validated = $request->validate(['billing_month' => 'required|date_format:Y-m']);
        $generated = $this->invoiceService->generateBulk($validated['billing_month']);
        return redirect()->route('invoices.index')->with('success', count($generated) . ' invoices generated for ' . $validated['billing_month']);
    }

    public function generateSingle(Request $request) {
        $validated = $request->validate([
            'customer_service_id' => 'required|exists:customer_services,id',
            'billing_month' => 'required|date_format:Y-m',
        ]);
        $service = CustomerService::findOrFail($validated['customer_service_id']);
        $invoice = $this->invoiceService->generateForService($service, $validated['billing_month']);
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice generated.');
    }

    public function destroy(Invoice $invoice) {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Only draft invoices can be deleted.');
        }
        $invoice->items()->delete();
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }
}
