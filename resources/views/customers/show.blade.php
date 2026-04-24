@extends('layouts.app')
@section('title', $customer->full_name)
@section('page-title', $customer->full_name)

@section('content')
<div class="space-y-6">
<div class="flex items-center justify-between">
    <a href="{{ route('customers.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left mr-2"></i>Back to Customers
    </a>
    <div class="flex gap-2">
        <a href="{{ route('payments.create', ['customer_id' => $customer->id]) }}" class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-green-700">
            <i class="fas fa-money-bill mr-1"></i>Collect Payment
        </a>
        <a href="{{ route('invoices.generate') }}" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700">
            <i class="fas fa-file-invoice mr-1"></i>Generate Invoice
        </a>
        <a href="{{ route('customers.edit', $customer) }}" class="bg-yellow-500 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-yellow-600">
            <i class="fas fa-edit mr-1"></i>Edit
        </a>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Customer Info -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">{{ $customer->full_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $customer->customer_code }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    {{ $customer->status === 'active' ? 'bg-green-100 text-green-700' :
                       ($customer->status === 'pending_installation' ? 'bg-yellow-100 text-yellow-700' :
                       'bg-red-100 text-red-700') }}">
                    {{ ucwords(str_replace('_',' ',$customer->status)) }}
                </span>
            </div>

            <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div><dt class="text-gray-500">Phone</dt><dd class="font-medium">{{ $customer->primary_phone }}{{ $customer->secondary_phone ? ' / '.$customer->secondary_phone : '' }}</dd></div>
                <div><dt class="text-gray-500">Email</dt><dd class="font-medium">{{ $customer->email ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Area</dt><dd class="font-medium">{{ $customer->area?->name ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">POP</dt><dd class="font-medium">{{ $customer->pop?->name ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">District</dt><dd class="font-medium">{{ $customer->district ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Address</dt><dd class="font-medium">{{ $customer->address_line ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Connection Date</dt><dd class="font-medium">{{ $customer->connection_date?->format('d M Y') ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Type</dt><dd class="font-medium capitalize">{{ $customer->customer_type }}</dd></div>
            </dl>
        </div>

        <!-- Services -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <h3 class="font-semibold text-gray-700">Services</h3>
                <a href="{{ route('services.create', ['customer_id' => $customer->id]) }}" class="text-sm text-indigo-600 hover:underline">
                    <i class="fas fa-plus mr-1"></i>Add Service
                </a>
            </div>
            @forelse($customer->services as $service)
            <div class="p-4 border-b last:border-0">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $service->package?->package_name }}</p>
                        <p class="text-sm text-gray-500">{{ taka($service->monthly_price) }}/month · {{ $service->package?->speed_label }}</p>
                        @if($service->onu_identifier)
                        <p class="text-xs text-gray-400 mt-1">ONU: {{ $service->onu_identifier }} · Port: {{ $service->pon_port }}</p>
                        @endif
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs {{ $service->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($service->status) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-5 py-6 text-center text-gray-400 text-sm">No services assigned.</div>
            @endforelse
        </div>

        <!-- Recent Invoices -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <h3 class="font-semibold text-gray-700">Invoices</h3>
                <a href="{{ route('invoices.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Invoice</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Month</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Total</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Due</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Status</th>
                </tr></thead>
                <tbody class="divide-y">
                    @forelse($customer->invoices->take(10) as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:underline">{{ $invoice->invoice_number }}</a>
                        </td>
                        <td class="px-4 py-2 text-gray-600">{{ $invoice->billing_month }}</td>
                        <td class="px-4 py-2 text-right font-medium">{{ taka($invoice->total_amount) }}</td>
                        <td class="px-4 py-2 text-right font-medium text-red-600">{{ taka($invoice->due_amount) }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : ($invoice->status === 'unpaid' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst(str_replace('_',' ',$invoice->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-4 text-center text-gray-400">No invoices.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Balance -->
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Account Balance</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Invoiced</span>
                    <span class="font-medium">{{ taka($customer->invoices->sum('total_amount')) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Paid</span>
                    <span class="font-medium text-green-600">{{ taka($customer->payments->where('status','active')->sum('amount')) }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="font-medium text-gray-700">Total Due</span>
                    <span class="font-bold text-red-600">{{ taka($customer->invoices->whereIn('status',['unpaid','partially_paid'])->sum('due_amount')) }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b">
                <h3 class="font-semibold text-gray-700">Recent Payments</h3>
            </div>
            <div class="divide-y">
                @forelse($customer->payments->take(5) as $payment)
                <div class="px-4 py-3 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $payment->payment_number }}</p>
                        <p class="text-xs text-gray-400">{{ $payment->payment_date->format('d M Y') }} · {{ ucfirst($payment->method) }}</p>
                    </div>
                    <span class="text-sm font-bold text-green-600">{{ taka($payment->amount) }}</span>
                </div>
                @empty
                <div class="px-4 py-6 text-center text-gray-400 text-sm">No payments.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</div>
@endsection
