@extends('layouts.platform')
@section('title', 'Create Plan')
@section('page-title', 'Create Subscription Plan')

@section('content')
<div class="max-w-2xl">
<a href="{{ route('platform.plans.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="fas fa-arrow-left mr-2"></i>Back
</a>
<form method="POST" action="{{ route('platform.plans.store') }}" class="space-y-6">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Plan Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" placeholder="e.g. Starter, Professional">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Price (৳) *</label>
                <input type="number" name="price_monthly" value="{{ old('price_monthly', 0) }}" min="0" step="0.01" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Yearly Price (৳) *</label>
                <input type="number" name="price_yearly" value="{{ old('price_yearly', 0) }}" min="0" step="0.01" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Customers</label>
                <input type="number" name="max_customers" value="{{ old('max_customers', 100) }}" min="1" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Staff</label>
                <input type="number" name="max_staff" value="{{ old('max_staff', 5) }}" min="1" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max OLT Devices</label>
                <input type="number" name="max_olt_devices" value="{{ old('max_olt_devices', 1) }}" min="0" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SMS/Month</label>
                <input type="number" name="max_sms_monthly" value="{{ old('max_sms_monthly', 100) }}" min="0" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
        </div>
        <div class="flex gap-6">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="has_reports" value="1" {{ old('has_reports', 1) ? 'checked' : '' }} class="rounded"> Reports
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="has_api" value="1" {{ old('has_api') ? 'checked' : '' }} class="rounded"> API Access
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="has_branding" value="1" {{ old('has_branding') ? 'checked' : '' }} class="rounded"> White Label
            </label>
        </div>
    </div>
    <div class="flex justify-end gap-3">
        <a href="{{ route('platform.plans.index') }}" class="px-5 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Create Plan</button>
    </div>
</form>
</div>
@endsection
