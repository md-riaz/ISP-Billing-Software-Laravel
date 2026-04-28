@extends('layouts.app')
@section('title','Areas & POPs')
@section('page-title','Areas & POPs')
@section('content')
<div class="grid lg:grid-cols-2 gap-6">

<!-- Areas -->
<div>
<div class="flex justify-between items-center mb-3">
    <h3 class="font-semibold text-gray-700">Areas</h3>
    <button onclick="document.getElementById('areaForm').classList.toggle('hidden')" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-indigo-700">
        <i class="fas fa-plus mr-1"></i>Add Area
    </button>
</div>
<div id="areaForm" class="hidden bg-indigo-50 rounded-xl p-4 mb-3">
    <form method="POST" action="{{ route('areas.store') }}" class="space-y-3">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Area Name *</label>
            <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
            <input type="text" name="district" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-sm">Save Area</button>
    </form>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
    <thead class="bg-gray-50"><tr>
        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Name</th>
        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">District</th>
        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Customers</th>
        <th class="px-4 py-2"></th>
    </tr></thead>
    <tbody class="divide-y">
    @forelse($areas as $area)
    <tr class="hover:bg-gray-50">
        <td class="px-4 py-2 font-medium">{{ $area->name }}</td>
        <td class="px-4 py-2 text-gray-500">{{ $area->district ?? '—' }}</td>
        <td class="px-4 py-2 text-right">{{ $area->customers_count ?? 0 }}</td>
        <td class="px-4 py-2 text-right">
            <form method="POST" action="{{ route('areas.destroy', $area) }}" onsubmit="return confirm('Delete area?')">
                @csrf @method('DELETE')
                <button class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No areas yet.</td></tr>
    @endforelse
    </tbody>
    </table>
</div>
</div>

<!-- POPs -->
<div>
<div class="flex justify-between items-center mb-3">
    <h3 class="font-semibold text-gray-700">POPs (Points of Presence)</h3>
    <button onclick="document.getElementById('popForm').classList.toggle('hidden')" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-indigo-700">
        <i class="fas fa-plus mr-1"></i>Add POP
    </button>
</div>
<div id="popForm" class="hidden bg-indigo-50 rounded-xl p-4 mb-3">
    <form method="POST" action="{{ route('pops.store') }}" class="space-y-3">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">POP Name *</label>
            <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <input type="text" name="address" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-sm">Save POP</button>
    </form>
</div>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
    <thead class="bg-gray-50"><tr>
        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Name</th>
        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Address</th>
        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Customers</th>
        <th class="px-4 py-2"></th>
    </tr></thead>
    <tbody class="divide-y">
    @forelse($pops as $pop)
    <tr class="hover:bg-gray-50">
        <td class="px-4 py-2 font-medium">{{ $pop->name }}</td>
        <td class="px-4 py-2 text-gray-500">{{ $pop->address ?? '—' }}</td>
        <td class="px-4 py-2 text-right">{{ $pop->customers_count ?? 0 }}</td>
        <td class="px-4 py-2 text-right">
            <form method="POST" action="{{ route('pops.destroy', $pop) }}" onsubmit="return confirm('Delete POP?')">
                @csrf @method('DELETE')
                <button class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No POPs yet.</td></tr>
    @endforelse
    </tbody>
    </table>
</div>
</div>

</div>
@endsection
