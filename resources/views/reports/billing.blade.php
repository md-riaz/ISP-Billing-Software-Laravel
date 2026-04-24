@extends('layouts.app')
@section('title', 'Billing Report')
@section('page-title', 'Billing Report')

@section('content')
<div class="space-y-4">

<!-- Filter -->
<div class="bg-white rounded-xl shadow-sm p-4">
    <form class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Billing Month</label>
            <input type="month" name="billing_month" value="{{ request('billing_month', now()->format('Y-m')) }}"
                   class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 self-end">
            <i class="fas fa-filter mr-1"></i>Filter
        </button>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-indigo-500">
        <p class="text-sm text-gray-500">Total Billed</p>
        <p class="text-2xl font-bold text-indigo-700 mt-1">{{ taka($summary['total_billed']) }}</p>
        <p class="text-xs text-gray-400">{{ $summary['count'] }} invoices</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
        <p class="text-sm text-gray-500">Total Paid</p>
        <p class="text-2xl font-bold text-green-700 mt-1">{{ taka($summary['total_paid']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Total Due</p>
        <p class="text-2xl font-bold text-red-700 mt-1">{{ taka($summary['total_due']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500">Unpaid Invoices</p>
        <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $summary['unpaid_count'] }}</p>
    </div>
</div>

<!-- Invoice Status Breakdown -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="font-semibold text-gray-800 mb-4">Invoice Status Breakdown</h3>
    <div class="flex flex-wrap gap-4">
        @foreach($summary['by_status'] as $status => $data)
        @php $colors=['paid'=>'green','partially_paid'=>'blue','unpaid'=>'red','draft'=>'gray','waived'=>'purple','cancelled'=>'gray']; $c=$colors[$status]??'gray'; @endphp
        <div class="flex-1 min-w-32 bg-{{ $c }}-50 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-{{ $c }}-700">{{ $data['count'] }}</div>
            <div class="text-xs text-{{ $c }}-600 font-medium uppercase mt-1">{{ str_replace('_',' ',$status) }}</div>
            <div class="text-sm font-bold text-{{ $c }}-800 mt-1">{{ taka($data['amount']) }}</div>
        </div>
        @endforeach
    </div>
</div>

<!-- Invoices Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-3 border-b flex justify-between items-center">
        <h3 class="font-semibold text-gray-800">Invoice Details - {{ request('billing_month', now()->format('Y-m')) }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Invoice #</th>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Customer</th>
                <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Total</th>
                <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Paid</th>
                <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Due</th>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2"><a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:underline">{{ $invoice->invoice_number }}</a></td>
                    <td class="px-4 py-2">
                        <div class="font-medium">{{ $invoice->customer?->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $invoice->customer?->customer_code }}</div>
                    </td>
                    <td class="px-4 py-2 text-right font-medium">{{ taka($invoice->total_amount) }}</td>
                    <td class="px-4 py-2 text-right text-green-700">{{ taka($invoice->paid_amount) }}</td>
                    <td class="px-4 py-2 text-right {{ $invoice->due_amount > 0 ? 'text-red-700 font-bold' : 'text-gray-400' }}">{{ taka($invoice->due_amount) }}</td>
                    <td class="px-4 py-2">
                        @php $colors=['paid'=>'green','partially_paid'=>'blue','unpaid'=>'red','draft'=>'gray','waived'=>'purple','cancelled'=>'gray']; $c=$colors[$invoice->status]??'gray'; @endphp
                        <span class="px-2 py-0.5 bg-{{ $c }}-100 text-{{ $c }}-700 rounded-full text-xs">{{ str_replace('_',' ',$invoice->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">No invoices for this month.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection
