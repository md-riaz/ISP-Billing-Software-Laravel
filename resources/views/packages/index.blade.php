@extends('layouts.app')
@section('title','Packages')
@section('page-title','Packages')
@section('content')
<div class="flex justify-between items-center mb-4">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search packages..." class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        <button class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">Search</button>
    </form>
    <a href="{{ route('packages.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
        <i class="fas fa-plus mr-1"></i>New Package
    </a>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
<table class="w-full text-sm">
<thead class="bg-gray-50"><tr>
    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Package</th>
    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Speed</th>
    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Monthly Price</th>
    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Type</th>
    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Status</th>
    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Customers</th>
    <th class="px-4 py-3"></th>
</tr></thead>
<tbody class="divide-y">
@forelse($packages as $pkg)
<tr class="hover:bg-gray-50">
    <td class="px-4 py-3 font-medium text-gray-800">{{ $pkg->package_name }}</td>
    <td class="px-4 py-3 text-gray-600">{{ $pkg->speed_label }}</td>
    <td class="px-4 py-3 text-right font-medium">{{ taka($pkg->monthly_price) }}</td>
    <td class="px-4 py-3"><span class="capitalize text-gray-600">{{ $pkg->package_type }}</span></td>
    <td class="px-4 py-3">
        <span class="px-2 py-0.5 rounded-full text-xs {{ $pkg->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
            {{ $pkg->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td class="px-4 py-3 text-right text-gray-600">{{ $pkg->services_count ?? 0 }}</td>
    <td class="px-4 py-3 text-right">
        <div class="flex justify-end gap-2">
            <a href="{{ route('packages.edit', $pkg) }}" class="text-yellow-600 hover:text-yellow-800 text-xs"><i class="fas fa-edit"></i></a>
            <form method="POST" action="{{ route('packages.destroy', $pkg) }}" onsubmit="return confirm('Delete this package?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No packages found.</td></tr>
@endforelse
</tbody>
</table>
</div>
<div class="mt-4">{{ $packages->links() }}</div>
@endsection
