@extends('layouts.app')
@section('title', 'Collection Report')
@section('page-title', 'Collection Report')

@section('content')
<div class="space-y-4">

<!-- Filter -->
<div class="bg-white rounded-xl shadow-sm p-4">
    <form class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
                   class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date', now()->format('Y-m-d')) }}"
                   class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 self-end">
            <i class="fas fa-filter mr-1"></i>Filter
        </button>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
        <p class="text-sm text-gray-500">Total Collected</p>
        <p class="text-2xl font-bold text-green-700 mt-1">{{ taka($summary['total']) }}</p>
        <p class="text-xs text-gray-400">{{ $summary['count'] }} payments</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500 col-span-2 lg:col-span-2">
        <p class="text-sm text-gray-500 mb-2">By Payment Method</p>
        <div class="flex flex-wrap gap-4">
            @foreach($summary['by_method'] as $method => $amount)
            <div class="text-center">
                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium uppercase block mb-1">{{ $method }}</span>
                <span class="font-bold text-gray-800">{{ taka($amount) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- By Collector -->
@if(count($summary['by_collector']) > 0)
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="font-semibold text-gray-800 mb-3">Collection by Staff</h3>
    <table class="w-full text-sm">
        <thead class="bg-gray-50"><tr>
            <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Collector</th>
            <th class="px-4 py-2 text-center text-xs text-gray-500 uppercase">Payments</th>
            <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Total</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($summary['by_collector'] as $collectorId => $data)
            <tr>
                <td class="px-4 py-2 font-medium">{{ $data['name'] }}</td>
                <td class="px-4 py-2 text-center">{{ $data['count'] }}</td>
                <td class="px-4 py-2 text-right font-bold text-green-700">{{ taka($data['total']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- Payments Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-3 border-b flex justify-between items-center">
        <h3 class="font-semibold text-gray-800">Payment Details</h3>
        <span class="text-sm text-gray-500">{{ $payments->count() }} records</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Receipt #</th>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Customer</th>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Date</th>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Method</th>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Collector</th>
                <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Amount</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2"><a href="{{ route('payments.show', $payment) }}" class="text-indigo-600 hover:underline">{{ $payment->payment_number }}</a></td>
                    <td class="px-4 py-2">
                        <div class="font-medium">{{ $payment->customer?->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $payment->customer?->customer_code }}</div>
                    </td>
                    <td class="px-4 py-2">{{ $payment->payment_date?->format('d M Y') }}</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs uppercase">{{ $payment->method }}</span></td>
                    <td class="px-4 py-2">{{ $payment->collector?->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-right font-bold text-green-700">{{ taka($payment->amount) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">No payments in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Other Reports Nav -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="{{ route('reports.billing') }}" class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-indigo-500 hover:shadow-md transition">
        <div class="flex items-center gap-3">
            <i class="fas fa-file-invoice text-indigo-500 text-xl"></i>
            <div><p class="font-medium text-gray-800">Billing Report</p><p class="text-xs text-gray-400">Invoice summaries</p></div>
        </div>
    </a>
    <a href="{{ route('dues.index') }}" class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500 hover:shadow-md transition">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
            <div><p class="font-medium text-gray-800">Due Report</p><p class="text-xs text-gray-400">Outstanding balances</p></div>
        </div>
    </a>
    <a href="{{ route('customers.index') }}" class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500 hover:shadow-md transition">
        <div class="flex items-center gap-3">
            <i class="fas fa-users text-green-500 text-xl"></i>
            <div><p class="font-medium text-gray-800">Customer Report</p><p class="text-xs text-gray-400">Customer list</p></div>
        </div>
    </a>
    <a href="{{ route('audit-logs.index') }}" class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-gray-500 hover:shadow-md transition">
        <div class="flex items-center gap-3">
            <i class="fas fa-history text-gray-500 text-xl"></i>
            <div><p class="font-medium text-gray-800">Audit Logs</p><p class="text-xs text-gray-400">System activity</p></div>
        </div>
    </a>
</div>
</div>
@endsection
