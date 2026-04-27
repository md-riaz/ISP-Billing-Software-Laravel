@extends('layouts.app')
@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page-title', 'Invoice Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
<div class="flex items-center justify-between">
    <a href="{{ route('invoices.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center gap-1">
        <i class="fas fa-arrow-left"></i> Back to Invoices
    </a>
    <div class="flex gap-2">
        @if(in_array($invoice->status, ['unpaid','partially_paid']))
        <a href="{{ route('payments.create', ['customer_id' => $invoice->customer_id]) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
            <i class="fas fa-money-bill mr-1"></i>Collect Payment
        </a>
        @endif
    </div>
</div>

<!-- Invoice Card -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden" id="invoice-print">
    <!-- Header -->
    <div class="bg-indigo-700 text-white p-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold">{{ setting('company_name', currentTenant()?->name) }}</h2>
                <p class="text-indigo-200 text-sm mt-1">{{ setting('address', '') }}</p>
                <p class="text-indigo-200 text-sm">{{ setting('phone', '') }}</p>
            </div>
            <div class="text-right">
                <div class="text-lg font-bold uppercase tracking-widest text-indigo-200">Invoice</div>
                <div class="text-2xl font-bold mt-1">{{ $invoice->invoice_number }}</div>
                @php $colors=['paid'=>'green','partially_paid'=>'yellow','unpaid'=>'red','draft'=>'gray','waived'=>'purple','cancelled'=>'gray'] @endphp
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold bg-white text-{{ $colors[$invoice->status] ?? 'gray' }}-700">
                    {{ strtoupper(str_replace('_',' ',$invoice->status)) }}
                </span>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6">
        <!-- Customer & Dates -->
        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-xs text-gray-500 uppercase font-medium mb-1">Bill To</p>
                <p class="font-semibold text-gray-800">{{ $invoice->customer?->full_name }}</p>
                <p class="text-sm text-gray-600">{{ $invoice->customer?->customer_code }}</p>
                <p class="text-sm text-gray-600">{{ $invoice->customer?->primary_phone }}</p>
                <p class="text-sm text-gray-600">{{ $invoice->customer?->address_line }}</p>
            </div>
            <div class="text-right">
                <div class="space-y-1 text-sm">
                    <div class="flex justify-end gap-6">
                        <span class="text-gray-500">Issue Date:</span>
                        <span class="font-medium">{{ $invoice->issue_date?->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-end gap-6">
                        <span class="text-gray-500">Due Date:</span>
                        <span class="font-medium {{ $invoice->due_date < now() && $invoice->due_amount > 0 ? 'text-red-600' : '' }}">{{ $invoice->due_date?->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-end gap-6">
                        <span class="text-gray-500">Billing Month:</span>
                        <span class="font-medium">{{ $invoice->billing_month }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->description }}</td>
                        <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">{{ taka($item->unit_price) }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ taka($item->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end">
            <div class="w-64 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-600">Subtotal</span><span>{{ taka($invoice->subtotal) }}</span></div>
                @if($invoice->discount_amount > 0)
                <div class="flex justify-between text-green-700"><span>Discount</span><span>-{{ taka($invoice->discount_amount) }}</span></div>
                @endif
                @if($invoice->previous_due > 0)
                <div class="flex justify-between text-red-600"><span>Previous Due</span><span>+{{ taka($invoice->previous_due) }}</span></div>
                @endif
                @if($invoice->adjustment_amount != 0)
                <div class="flex justify-between"><span>Adjustment</span><span>{{ taka($invoice->adjustment_amount) }}</span></div>
                @endif
                <div class="flex justify-between font-bold text-base border-t pt-2"><span>Total</span><span>{{ taka($invoice->total_amount) }}</span></div>
                <div class="flex justify-between text-green-700"><span>Paid</span><span>{{ taka($invoice->paid_amount) }}</span></div>
                <div class="flex justify-between font-bold text-red-600 text-base border-t pt-2"><span>Due Amount</span><span>{{ taka($invoice->due_amount) }}</span></div>
            </div>
        </div>

        @if($invoice->notes)
        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
            <strong>Notes:</strong> {{ $invoice->notes }}
        </div>
        @endif
    </div>
</div>

<!-- Payment History -->
@if($invoice->allocations->count() > 0)
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="font-semibold text-gray-800 mb-3">Payment History</h3>
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr>
            <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Payment #</th>
            <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Date</th>
            <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Method</th>
            <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Allocated</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($invoice->allocations as $alloc)
            <tr>
                <td class="px-4 py-2"><a href="{{ route('payments.show', $alloc->payment) }}" class="text-indigo-600 hover:underline">{{ $alloc->payment?->payment_number }}</a></td>
                <td class="px-4 py-2">{{ $alloc->payment?->payment_date?->format('d M Y') }}</td>
                <td class="px-4 py-2">{{ strtoupper($alloc->payment?->method) }}</td>
                <td class="px-4 py-2 text-right font-medium text-green-700">{{ taka($alloc->allocated_amount) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
</div>
@endsection
