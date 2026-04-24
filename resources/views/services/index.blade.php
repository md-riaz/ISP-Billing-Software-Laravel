@extends('layouts.app')
@section('title','Customer Services')
@section('page-title','Customer Services')
@section('content')
<div class="flex justify-between items-center mb-4">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by customer/ONU..." class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        <select name="status" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            <option value="">All Status</option>
            @foreach(['active','suspended','terminated'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm">Filter</button>
    </form>
    <a href="{{ route('services.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
        <i class="fas fa-plus mr-1"></i>New Service
    </a>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
<table class="w-full text-sm">
<thead class="bg-gray-50"><tr>
    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Customer</th>
    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Package</th>
    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">ONU / Port</th>
    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Monthly Price</th>
    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Status</th>
    <th class="px-4 py-3"></th>
</tr></thead>
<tbody class="divide-y">
@forelse($services as $service)
<tr class="hover:bg-gray-50">
    <td class="px-4 py-3">
        <a href="{{ route('customers.show', $service->customer) }}" class="font-medium text-indigo-600 hover:underline">{{ $service->customer?->full_name }}</a>
        <p class="text-xs text-gray-400">{{ $service->customer?->customer_code }}</p>
    </td>
    <td class="px-4 py-3">
        <p class="font-medium">{{ $service->package?->package_name }}</p>
        <p class="text-xs text-gray-400">{{ $service->package?->speed_label }}</p>
    </td>
    <td class="px-4 py-3 text-gray-600">{{ $service->onu_identifier ?? '—' }}{{ $service->pon_port ? ' · '.$service->pon_port : '' }}</td>
    <td class="px-4 py-3 text-right font-medium">{{ taka($service->monthly_price) }}</td>
    <td class="px-4 py-3">
        <span class="px-2 py-0.5 rounded-full text-xs {{ $service->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
            {{ ucfirst($service->status) }}
        </span>
    </td>
    <td class="px-4 py-3 text-right">
        <a href="{{ route('services.show', $service) }}" class="text-indigo-600 hover:text-indigo-800 text-xs mr-2"><i class="fas fa-eye"></i></a>
    </td>
</tr>
@empty
<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No services found.</td></tr>
@endforelse
</tbody>
</table>
</div>
<div class="mt-4">{{ $services->links() }}</div>
@endsection
