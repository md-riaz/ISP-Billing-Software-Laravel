@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

<!-- Stats Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Customers</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_customers']) }}</p>
                <p class="text-xs text-green-600 mt-1">{{ $stats['active_customers'] }} active</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-indigo-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Monthly Revenue</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ taka($stats['monthly_revenue']) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ now()->format('M Y') }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Dues</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ taka($stats['total_due']) }}</p>
                <p class="text-xs text-gray-400 mt-1">Outstanding</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium">Active Services</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($stats['active_services']) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['total_services'] }} total</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-network-wired text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-xl shadow-sm p-5">
    <h3 class="text-base font-semibold text-gray-700 mb-4">Quick Actions</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('customers.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
            <i class="fas fa-user-plus mr-2"></i>New Customer
        </a>
        <a href="{{ route('payments.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
            <i class="fas fa-money-bill mr-2"></i>Collect Payment
        </a>
        <a href="{{ route('invoices.generate') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
            <i class="fas fa-file-invoice mr-2"></i>Generate Invoices
        </a>
        <a href="{{ route('dues.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">
            <i class="fas fa-list mr-2"></i>View Dues
        </a>
    </div>
</div>

<!-- Tables -->
<div class="grid lg:grid-cols-2 gap-6">

    <!-- Recent Payments -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <h3 class="font-semibold text-gray-700">Recent Payments</h3>
            <a href="{{ route('payments.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
        </div>
        <div class="divide-y">
            @forelse($recentPayments as $payment)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="font-medium text-sm text-gray-800">{{ $payment->customer?->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $payment->payment_number }} · {{ $payment->payment_date->format('d M Y') }}</p>
                </div>
                <span class="text-sm font-semibold text-green-600">{{ taka($payment->amount) }}</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-sm">No payments yet</div>
            @endforelse
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <h3 class="font-semibold text-gray-700">Recent Invoices</h3>
            <a href="{{ route('invoices.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
        </div>
        <div class="divide-y">
            @forelse($recentInvoices as $invoice)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="font-medium text-sm text-gray-800">{{ $invoice->customer?->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $invoice->invoice_number }} · {{ $invoice->billing_month }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">{{ taka($invoice->total_amount) }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : ($invoice->status === 'unpaid' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-gray-400 text-sm">No invoices yet</div>
            @endforelse
        </div>
    </div>
</div>

</div>
@endsection
