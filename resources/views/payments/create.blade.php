@extends('layouts.app')
@section('title', 'Collect Payment')
@section('page-title', 'Collect Payment')

@section('content')
<div class="max-w-2xl mx-auto">
<a href="{{ route('payments.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center gap-1 mb-4">
    <i class="fas fa-arrow-left"></i> Back to Payments
</a>

<div class="bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('payments.store') }}" x-data="paymentForm()" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Customer <span class="text-red-500">*</span></label>
            <select name="customer_id" x-model="customerId" @change="loadCustomerDue()" required
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                <option value="">Select customer...</option>
                @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ (old('customer_id', $selectedCustomer?->id) == $c->id) ? 'selected' : '' }}>
                    {{ $c->customer_code }} - {{ $c->full_name }} ({{ $c->primary_phone }})
                </option>
                @endforeach
            </select>
        </div>

        @if($selectedCustomer && $unpaidInvoices->count() > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm">
            <p class="font-medium text-yellow-800 mb-2">Outstanding Invoices:</p>
            <div class="space-y-1">
                @foreach($unpaidInvoices as $inv)
                <div class="flex justify-between text-yellow-700">
                    <span>{{ $inv->invoice_number }} ({{ $inv->billing_month }})</span>
                    <span class="font-bold">{{ taka($inv->due_amount) }}</span>
                </div>
                @endforeach
            </div>
            <div class="border-t border-yellow-300 mt-2 pt-2 flex justify-between font-bold text-yellow-900">
                <span>Total Due</span>
                <span>{{ taka($unpaidInvoices->sum('due_amount')) }}</span>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date <span class="text-red-500">*</span></label>
                <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (৳) <span class="text-red-500">*</span></label>
                <input type="number" name="amount" value="{{ old('amount') }}" required step="0.01" min="1"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                       placeholder="0.00">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-4 gap-2">
                @foreach(['cash','bkash','nagad','rocket','bank','card','online','adjustment'] as $m)
                <label class="flex items-center justify-center border rounded-lg px-3 py-2 cursor-pointer text-sm font-medium
                       {{ old('method') == $m ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                       :class="method === '{{ $m }}' ? 'bg-indigo-600 text-white border-indigo-600' : ''">
                    <input type="radio" name="method" value="{{ $m }}" x-model="method" class="sr-only">
                    {{ strtoupper($m) }}
                </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Reference</label>
            <input type="text" name="transaction_reference" value="{{ old('transaction_reference') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                   placeholder="TrxID, cheque no, etc.">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
            <textarea name="note" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">{{ old('note') }}</textarea>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700">
            <i class="fas fa-check mr-2"></i>Save Payment
        </button>
    </form>
</div>
</div>

<script>
function paymentForm() {
    return {
        customerId: '{{ $selectedCustomer?->id ?? "" }}',
        method: '{{ old('method', 'cash') }}',
        loadCustomerDue() {
            // Could load via AJAX - for now just trigger change
        }
    }
}
</script>
@endsection
