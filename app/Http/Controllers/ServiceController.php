<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerService;
use App\Models\OltDevice;
use App\Models\Package;
use App\Models\StatusHistory;
use Illuminate\Http\Request;

class ServiceController extends Controller {
    public function index(Request $request) {
        $query = CustomerService::with('customer','package','oltDevice');
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', fn($q) => $q->where('full_name','like',"%$search%")->orWhere('customer_code','like',"%$search%"));
        }
        $services = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('services.index', compact('services'));
    }

    public function create(Request $request) {
        $customers = Customer::where('status','active')->orWhere('status','pending_installation')->orderBy('full_name')->get();
        $packages = Package::where('is_active', true)->orderBy('package_name')->get();
        $oltDevices = OltDevice::where('status','active')->orderBy('device_name')->get();
        $selectedCustomer = $request->filled('customer_id') ? Customer::find($request->customer_id) : null;
        return view('services.create', compact('customers','packages','oltDevices','selectedCustomer'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'package_id' => 'required|exists:packages,id',
            'monthly_price' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'olt_device_id' => 'nullable|exists:olt_devices,id',
            'pon_port' => 'nullable|string|max:50',
            'onu_identifier' => 'nullable|string|max:100',
            'onu_serial' => 'nullable|string|max:100',
            'onu_name' => 'nullable|string|max:100',
            'service_profile' => 'nullable|string|max:100',
            'line_profile' => 'nullable|string|max:100',
        ]);

        $service = CustomerService::create(array_merge($validated, ['status' => 'pending']));

        return redirect()->route('services.show', $service)->with('success', 'Service created.');
    }

    public function show(CustomerService $service) {
        $service->load('customer','package','oltDevice','invoices','statusHistories.changer','actionLogs');
        return view('services.show', compact('service'));
    }

    public function updateStatus(Request $request, CustomerService $service) {
        $validated = $request->validate([
            'status' => 'required|in:pending,active,suspended,disconnected,terminated',
            'reason' => 'nullable|string',
        ]);

        $old = $service->status;
        $service->update(['status' => $validated['status']]);

        StatusHistory::create([
            'tenant_id' => app('currentTenant')->id,
            'customer_service_id' => $service->id,
            'old_status' => $old,
            'new_status' => $validated['status'],
            'reason' => $validated['reason'] ?? null,
            'changed_by' => auth()->id(),
        ]);

        // Sync customer status with service status
        if ($validated['status'] === 'active') {
            $service->customer->update(['status' => 'active', 'activation_date' => now()]);
        } elseif (in_array($validated['status'], ['suspended'])) {
            $service->customer->update(['status' => 'suspended_manual']);
        }

        return back()->with('success', 'Service status updated to ' . $validated['status']);
    }
}
