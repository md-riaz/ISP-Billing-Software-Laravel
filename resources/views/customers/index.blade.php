@extends('layouts.app')
@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
<div class="space-y-4">
<div class="flex flex-wrap items-center gap-3 justify-between">
    <form class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, code, phone..."
               class="border rounded-lg px-3 py-2 text-sm w-48 focus:ring-2 focus:ring-indigo-400 outline-none">
        <select name="status" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            <option value="">All Status</option>
            @foreach(['pending_installation','active','temporary_hold','suspended_due','suspended_manual','disconnected','terminated'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
        <select name="area_id" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            <option value="">All Areas</option>
            @foreach($areas as $area)
            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Filter</button>
    </form>
    <a href="{{ route('customers.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 flex items-center gap-2 whitespace-nowrap">
        <i class="fas fa-plus"></i>Add Customer
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Area</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Package</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('customers.show', $customer) }}" class="font-medium text-indigo-600 hover:underline">{{ $customer->full_name }}</a>
                        <p class="text-xs text-gray-400">{{ $customer->customer_code }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $customer->primary_phone }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $customer->area?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $customer->activeService?->package?->package_name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-700',
                                'pending_installation' => 'bg-yellow-100 text-yellow-700',
                                'suspended_due' => 'bg-red-100 text-red-700',
                                'suspended_manual' => 'bg-red-100 text-red-700',
                                'disconnected' => 'bg-gray-100 text-gray-700',
                                'terminated' => 'bg-gray-100 text-gray-700',
                                'temporary_hold' => 'bg-orange-100 text-orange-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$customer->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucwords(str_replace('_',' ',$customer->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('customers.show', $customer) }}" class="text-indigo-600 hover:underline">View</a>
                            <a href="{{ route('customers.edit', $customer) }}" class="text-yellow-600 hover:underline">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t">{{ $customers->links() }}</div>
</div>
</div>
@endsection
