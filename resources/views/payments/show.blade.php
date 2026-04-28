@extends('layouts.app')
@section('title', 'Receipt - ' . $payment->payment_number)
@section('page-title', 'Payment Receipt')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
<div class="flex items-center justify-between">
    <a href="{{ route('payments.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center gap-1">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <div class="flex gap-2">
        <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-print mr-1"></i>Print Receipt
        </button>
        @if($payment->status === 'active')
        <form method="POST" action="{{ route('payments.reverse', $payment) }}" x-data="{ show: false }" @submit.prevent="show=true">
            @csrf
            <button type="button" @click="show=true" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm hover:bg-red-200">
                <i class="fas fa-undo mr-1"></i>Reverse
            </button>
            <!-- Reverse Modal -->
            <div x-show="show" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl p-6 w-96">
                    <h3 class="font-semibold text-gray-800 mb-3">Reverse Payment</h3>
                    <p class="text-sm text-gray-600 mb-4">This will reverse {{ taka($payment->amount) }} payment and update invoice balances.</p>
                    <form method="POST" action="{{ route('payments.reverse', $payment) }}">
                        @csrf
                        <textarea name="reversal_reason" placeholder="Reason for reversal..." required rows="2"
                                  class="w-full border rounded-lg px-3 py-2 text-sm mb-3 focus:ring-2 focus:ring-red-400 outline-none"></textarea>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded-lg text-sm hover:bg-red-700">Confirm Reverse</button>
                            <button type="button" @click="show=false" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg text-sm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </form>
        @endif
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden" id="receipt">
    <!-- Receipt Header -->
    <div class="bg-green-700 text-white p-6 text-center">
        <div class="text-sm uppercase tracking-widest text-green-200 mb-1">Payment Receipt</div>
        <div class="text-3xl font-bold">{{ setting('company_name', currentTenant()?->name) }}</div>
        <div class="text-green-200 text-sm mt-1">{{ setting('address', '') }} | {{ setting('phone', '') }}</div>
    </div>

    <div class="p-6 space-y-6">
        @if($payment->status === 'reversed')
        <div class="bg-red-50 border border-red-200 text-red-700 text-center py-3 rounded-lg font-bold uppercase tracking-widest">
            ⚠ REVERSED
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Receipt No:</span> <span class="font-bold">{{ $payment->payment_number }}</span></div>
            <div class="text-right"><span class="text-gray-500">Date:</span> <span class="font-bold">{{ $payment->payment_date?->format('d M Y') }}</span></div>
            <div><span class="text-gray-500">Customer:</span> <span class="font-bold">{{ $payment->customer?->full_name }}</span></div>
            <div class="text-right"><span class="text-gray-500">Code:</span> <span class="font-bold">{{ $payment->customer?->customer_code }}</span></div>
            <div><span class="text-gray-500">Phone:</span> <span class="font-bold">{{ $payment->customer?->primary_phone }}</span></div>
            <div class="text-right"><span class="text-gray-500">Collected By:</span> <span class="font-bold">{{ $payment->collector?->name ?? 'System' }}</span></div>
        </div>

        <div class="border-2 border-green-200 rounded-xl p-6 text-center bg-green-50">
            <p class="text-sm text-green-700 mb-1">Amount Received</p>
            <p class="text-4xl font-bold text-green-800">{{ taka($payment->amount) }}</p>
            <p class="text-sm text-green-700 mt-1 uppercase">{{ str_replace('_',' ',$payment->method) }}</p>
            @if($payment->transaction_reference)
            <p class="text-xs text-green-600 mt-1">Ref: {{ $payment->transaction_reference }}</p>
            @endif
        </div>

        @php
            $currentDue = \App\Models\Invoice::where('customer_id', $payment->customer_id)
                ->whereIn('status', ['unpaid','partially_paid'])
                ->sum('due_amount');
        @endphp
        <div class="flex justify-between items-center text-sm bg-yellow-50 p-3 rounded-lg">
            <span class="text-gray-600">Remaining Due After This Payment:</span>
            <span class="font-bold text-yellow-800 text-lg">{{ taka($currentDue) }}</span>
        </div>

        @if($payment->note)
        <div class="text-sm text-gray-600"><strong>Note:</strong> {{ $payment->note }}</div>
        @endif

        @if($payment->status === 'reversed')
        <div class="text-sm text-red-600"><strong>Reversal Reason:</strong> {{ $payment->reversal_reason }}</div>
        @endif
    </div>

    <div class="px-6 pb-6 text-center text-xs text-gray-400 border-t pt-4">
        Thank you for your payment. Please keep this receipt for your records.
    </div>
</div>

<!-- Allocated Invoices -->
@if($payment->allocations->count() > 0)
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="font-semibold text-gray-800 mb-3">Allocated to Invoices</h3>
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr>
            <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Invoice #</th>
            <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Month</th>
            <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Allocated</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($payment->allocations as $alloc)
            <tr>
                <td class="px-4 py-2"><a href="{{ route('invoices.show', $alloc->invoice) }}" class="text-indigo-600 hover:underline">{{ $alloc->invoice?->invoice_number }}</a></td>
                <td class="px-4 py-2">{{ $alloc->invoice?->billing_month }}</td>
                <td class="px-4 py-2 text-right font-medium text-green-700">{{ taka($alloc->allocated_amount) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
</div>
@endsection
