@extends('layouts.app')
@section('title', 'Generate Invoices')
@section('page-title', 'Generate Invoices')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
<a href="{{ route('invoices.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center gap-1 mb-2">
    <i class="fas fa-arrow-left"></i> Back to Invoices
</a>

<div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Bulk Invoice Generation</h2>
    <p class="text-sm text-gray-600 mb-4">Generate monthly invoices for all active customer services for a billing month.</p>
    <form method="POST" action="{{ route('invoices.generate-bulk') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Billing Month</label>
            <input type="month" name="billing_month" value="{{ $thisMonth }}" required
                   class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-indigo-700"
                onclick="return confirm('Generate invoices for all active services for this month?')">
            <i class="fas fa-bolt mr-1"></i>Generate All Active Invoices
        </button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Single Customer Invoice</h2>
    <form method="POST" action="{{ route('invoices.generate-single') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Service</label>
            <select name="customer_service_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                <option value="">Select a customer service...</option>
                @foreach($customers as $customer)
                    @if($customer->activeService)
                    <option value="{{ $customer->activeService->id }}">
                        {{ $customer->customer_code }} - {{ $customer->full_name }}
                        ({{ $customer->activeService->package?->package_name ?? 'No Package' }} - {{ taka($customer->activeService->monthly_price) }})
                    </option>
                    @endif
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Billing Month</label>
            <input type="month" name="billing_month" value="{{ $thisMonth }}" required
                   class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-green-700">
            <i class="fas fa-file-invoice mr-1"></i>Generate Single Invoice
        </button>
    </form>
</div>
</div>
@endsection
