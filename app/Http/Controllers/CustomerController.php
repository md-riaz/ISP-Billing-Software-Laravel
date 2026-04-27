<?php
namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Customer;
use App\Models\Pop;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller {

    public function index(Request $request) {
        $query = Customer::with('area','pop','activeService.package');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('customer_code', 'like', "%$search%")
                  ->orWhere('primary_phone', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $customers = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $areas = Area::orderBy('name')->get();

        return view('customers.index', compact('customers', 'areas'));
    }

    public function create() {
        $areas = Area::orderBy('name')->get();
        $pops = Pop::orderBy('name')->get();
        $collectors = User::where('tenant_id', app('currentTenant')->id)->where('status','active')->get();
        return view('customers.create', compact('areas', 'pops', 'collectors'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'customer_type' => 'required|in:home,business,corporate',
            'primary_phone' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'nid' => 'nullable|string|max:50',
            'address_line' => 'nullable|string|max:500',
            'area_id' => 'nullable|exists:areas,id',
            'pop_id' => 'nullable|exists:pops,id',
            'thana' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'connection_date' => 'nullable|date',
            'discount_type' => 'required|in:none,fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'opening_due' => 'nullable|numeric|min:0',
            'installation_charge' => 'nullable|numeric|min:0',
            'billing_note' => 'nullable|string',
        ]);

        $tenant = app('currentTenant');
        $customerCode = $this->generateCustomerCode($tenant);

        $customer = Customer::create(array_merge($validated, [
            'tenant_id' => $tenant->id,
            'customer_code' => $customerCode,
            'status' => 'pending_installation',
            'discount_value' => $validated['discount_value'] ?? 0,
            'opening_due' => $validated['opening_due'] ?? 0,
            'installation_charge' => $validated['installation_charge'] ?? 0,
        ]));

        logActivity('customer_created', 'Customer', $customer->id, null, $customer->toArray());

        return redirect()->route('customers.show', $customer)->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer) {
        $customer->load('area','pop','services.package','invoices','payments','collector','technician');
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer) {
        $areas = Area::orderBy('name')->get();
        $pops = Pop::orderBy('name')->get();
        $collectors = User::where('tenant_id', app('currentTenant')->id)->where('status','active')->get();
        return view('customers.edit', compact('customer', 'areas', 'pops', 'collectors'));
    }

    public function update(Request $request, Customer $customer) {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'customer_type' => 'required|in:home,business,corporate',
            'primary_phone' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'nid' => 'nullable|string|max:50',
            'address_line' => 'nullable|string|max:500',
            'area_id' => 'nullable|exists:areas,id',
            'pop_id' => 'nullable|exists:pops,id',
            'thana' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'connection_date' => 'nullable|date',
            'discount_type' => 'required|in:none,fixed,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'opening_due' => 'nullable|numeric|min:0',
            'installation_charge' => 'nullable|numeric|min:0',
            'billing_note' => 'nullable|string',
            'status' => 'required|in:pending_installation,active,temporary_hold,suspended_due,suspended_manual,disconnected,terminated',
        ]);

        $old = $customer->toArray();
        $customer->update($validated);
        logActivity('customer_updated', 'Customer', $customer->id, $old, $customer->toArray());

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer) {
        $customer->delete();
        logActivity('customer_deleted', 'Customer', $customer->id);
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }

    private function generateCustomerCode($tenant): string {
        $slug = strtoupper(substr($tenant->slug, 0, 4));
        $last = Customer::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('customer_code', 'like', "CST-{$slug}-%")
            ->count();
        return 'CST-' . $slug . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
