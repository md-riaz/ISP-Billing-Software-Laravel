@extends('layouts.app')
@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="space-y-4">
<div class="flex flex-wrap items-center gap-3 justify-between">
    <form class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Payment #, customer..."
               class="border rounded-lg px-3 py-2 text-sm w-44 focus:ring-2 focus:ring-indigo-400 outline-none">
        <select name="method" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            <option value="">All Methods</option>
            @foreach(['cash','bkash','nagad','rocket','bank','card','online','adjustment'] as $m)
            <option value="{{ $m }}" {{ request('method') == $m ? 'selected' : '' }}>{{ strtoupper($m) }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Filter</button>
        @if(request()->anyFilled(['search','method','date_from','date_to']))
        <a href="{{ route('payments.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Clear</a>
        @endif
    </form>
    <a href="{{ route('payments.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 flex items-center gap-2">
        <i class="fas fa-plus"></i>Collect Payment
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Collected By</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                <tr class="hover:bg-gray-50 {{ $payment->status === 'reversed' ? 'opacity-60' : '' }}">
                    <td class="px-4 py-3">
                        <a href="{{ route('payments.show', $payment) }}" class="text-indigo-600 font-medium hover:underline">{{ $payment->payment_number }}</a>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $payment->customer?->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $payment->customer?->customer_code }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $payment->payment_date?->format('d M Y') }}</td>
                    <td class="px-4 py-3 font-bold text-green-700">{{ taka($payment->amount) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium uppercase">{{ $payment->method }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $payment->collector?->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @if($payment->status === 'reversed')
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Reversed</span>
                        @else
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Active</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('payments.show', $payment) }}" class="text-indigo-600 hover:text-indigo-800 mr-2"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400"><i class="fas fa-money-bill-wave text-3xl mb-2 block"></i>No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="px-4 py-3 border-t">{{ $payments->withQueryString()->links() }}</div>
    @endif
</div>
</div>
@endsection
