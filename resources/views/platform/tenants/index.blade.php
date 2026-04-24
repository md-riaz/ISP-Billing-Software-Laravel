@extends('layouts.platform')
@section('title', 'Tenants')
@section('page-title', 'Tenants (ISPs)')

@section('content')
<div class="space-y-4">
<div class="flex items-center justify-between">
    <form class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
               class="border rounded-lg px-3 py-2 text-sm w-64 focus:ring-2 focus:ring-indigo-400 outline-none">
        <select name="status" class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            <option value="">All Status</option>
            @foreach(['trial','active','suspended','past_due','cancelled'] as $s)
            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Filter</button>
    </form>
    <a href="{{ route('platform.tenants.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 flex items-center gap-2">
        <i class="fas fa-plus"></i>Add Tenant
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenant</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trial Ends</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $tenant->name }}</div>
                        <div class="text-xs text-gray-400">{{ $tenant->email }} · {{ $tenant->slug }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $tenant->subscription?->plan?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' :
                               ($tenant->status === 'trial' ? 'bg-yellow-100 text-yellow-700' :
                               ($tenant->status === 'suspended' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $tenant->trial_ends_at?->format('d M Y') ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('platform.tenants.show', $tenant) }}" class="text-indigo-600 hover:underline">View</a>
                            <a href="{{ route('platform.tenants.edit', $tenant) }}" class="text-yellow-600 hover:underline">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No tenants found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t">{{ $tenants->links() }}</div>
</div>
</div>
@endsection
