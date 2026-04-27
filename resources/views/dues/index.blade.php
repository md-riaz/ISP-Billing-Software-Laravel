@extends('layouts.app')
@section('title', 'Due Management')
@section('page-title', 'Due Management')

@section('content')
<div class="space-y-4">

<!-- Summary Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Total Due</p>
        <p class="text-2xl font-bold text-red-700 mt-1">{{ taka($totalDue) }}</p>
        <p class="text-xs text-gray-400">{{ $customers->total() }} customers</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500">
        <p class="text-sm text-gray-500">Overdue (Past Due Date)</p>
        <p class="text-2xl font-bold text-orange-700 mt-1">{{ taka($overdueDue) }}</p>
        <p class="text-xs text-gray-400">{{ $overdueCount }} customers</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500">Suspended - Due</p>
        <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $suspendedCount }}</p>
        <p class="text-xs text-gray-400">customers</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-indigo-500">
        <p class="text-sm text-gray-500">Current Month Bills</p>
        <p class="text-2xl font-bold text-indigo-700 mt-1">{{ taka($currentMonthBilled) }}</p>
        <p class="text-xs text-gray-400">{{ now()->format('M Y') }}</p>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-sm p-4">
    <form class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer..."
               class="border rounded-lg px-3 py-2 text-sm w-48 focus:ring-2 focus:ring-indigo-400 outline-none">
        <select name="area_id" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            <option value="">All Areas</option>
            @foreach($areas as $area)
            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
            @endforeach
        </select>
        <select name="status" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            <option value="">All Status</option>
            @foreach(['active','suspended_due','suspended_manual'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Filter</button>
        @if(request()->anyFilled(['search','area_id','status']))
        <a href="{{ route('dues.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Clear</a>
        @endif
    </form>
</div>

<!-- Due List Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Area</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Package</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Invoices</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Due</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($customers as $customer)
                @php $dueData = $dueMap[$customer->id] ?? null; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">
                            <a href="{{ route('customers.show', $customer) }}" class="hover:text-indigo-600">{{ $customer->full_name }}</a>
                        </div>
                        <div class="text-xs text-gray-400">{{ $customer->customer_code }} · {{ $customer->primary_phone }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $customer->area?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $customer->activeService?->package?->package_name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @php $sc = ['active'=>'green','suspended_due'=>'red','suspended_manual'=>'orange','temporary_hold'=>'yellow']; $sc2 = $sc[$customer->status] ?? 'gray'; @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-{{ $sc2 }}-100 text-{{ $sc2 }}-700">
                            {{ ucwords(str_replace('_',' ',$customer->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">{{ $dueData?->invoice_count ?? 0 }}</td>
                    <td class="px-4 py-3 text-right font-bold text-red-700 text-base">{{ taka($dueData?->total_due ?? 0) }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('payments.create', ['customer_id' => $customer->id]) }}"
                           class="bg-green-100 text-green-700 px-3 py-1 rounded text-xs hover:bg-green-200 font-medium">
                            <i class="fas fa-money-bill-wave mr-1"></i>Collect
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400"><i class="fas fa-check-circle text-3xl mb-2 block text-green-400"></i>No overdue customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="px-4 py-3 border-t">{{ $customers->withQueryString()->links() }}</div>
    @endif
</div>
</div>
@endsection
