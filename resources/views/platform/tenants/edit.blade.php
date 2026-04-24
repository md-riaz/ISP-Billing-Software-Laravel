@extends('layouts.platform')
@section('title', 'Edit Tenant')
@section('page-title', 'Edit Tenant')

@section('content')
<div class="max-w-2xl">
<a href="{{ route('platform.tenants.show', $tenant) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="fas fa-arrow-left mr-2"></i>Back
</a>
<form method="POST" action="{{ route('platform.tenants.update', $tenant) }}" class="space-y-6">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-700 border-b pb-2">Tenant Details</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name *</label>
                <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $tenant->email) }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    @foreach(['trial','active','suspended','past_due','cancelled'] as $s)
                    <option value="{{ $s }}" {{ old('status', $tenant->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="flex justify-end gap-3">
        <a href="{{ route('platform.tenants.show', $tenant) }}" class="px-5 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Update Tenant</button>
    </div>
</form>
</div>
@endsection
