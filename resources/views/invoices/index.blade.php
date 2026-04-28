@extends('layouts.app')
@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
<div class="space-y-4">
<div class="flex flex-wrap items-center gap-3 justify-between">
    <form class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice #, customer..."
               class="border rounded-lg px-3 py-2 text-sm w-44 focus:ring-2 focus:ring-indigo-400 outline-none">
        <select name="status" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            <option value="">All Status</option>
            @foreach(['unpaid','partially_paid','paid','draft','waived','cancelled'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <input type="month" name="month" value="{{ request('month') }}" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Filter</button>
        @if(request()->anyFilled(['search','status','month']))
        <a href="{{ route('invoices.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Clear</a>
        @endif
    </form>
    <a href="{{ route('invoices.generate') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 flex items-center gap-2">
        <i class="fas fa-plus"></i>Generate Invoice
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paid</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 font-medium hover:underline">{{ $invoice->invoice_number }}</a>
                        <div class="text-xs text-gray-400">{{ ucwords(str_replace('_',' ',$invoice->invoice_type)) }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $invoice->customer?->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $invoice->customer?->customer_code }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $invoice->billing_month }}</td>
                    <td class="px-4 py-3 font-medium">{{ taka($invoice->total_amount) }}</td>
                    <td class="px-4 py-3 text-green-700">{{ taka($invoice->paid_amount) }}</td>
                    <td class="px-4 py-3 font-bold {{ $invoice->due_amount > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ taka($invoice->due_amount) }}</td>
                    <td class="px-4 py-3">
                        @php $colors=['paid'=>'green','partially_paid'=>'blue','unpaid'=>'red','draft'=>'gray','waived'=>'purple','cancelled'=>'gray'] @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $colors[$invoice->status] ?? 'gray' }}-100 text-{{ $colors[$invoice->status] ?? 'gray' }}-700">
                            {{ ucwords(str_replace('_',' ',$invoice->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        <span class="{{ $invoice->due_date < now() && $invoice->due_amount > 0 ? 'text-red-600 font-medium' : '' }}">
                            {{ $invoice->due_date?->format('d M Y') }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-800 mr-2"><i class="fas fa-eye"></i></a>
                        @if($invoice->status === 'draft')
                        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirm('Delete this invoice?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400"><i class="fas fa-file-invoice text-3xl mb-2 block"></i>No invoices found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div class="px-4 py-3 border-t">{{ $invoices->withQueryString()->links() }}</div>
    @endif
</div>
</div>
@endsection
