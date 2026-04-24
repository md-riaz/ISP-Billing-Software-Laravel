@extends('layouts.platform')
@section('title', 'Create Tenant')
@section('page-title', 'Create New Tenant')

@section('content')
<div class="max-w-2xl">
<a href="{{ route('platform.tenants.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="fas fa-arrow-left mr-2"></i>Back to Tenants
</a>

<form method="POST" action="{{ route('platform.tenants.store') }}" class="space-y-6">
    @csrf

    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-700 border-b pb-2">ISP Information</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-700 border-b pb-2">Subscription</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plan *</label>
                <select name="plan_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    <option value="">Select Plan</option>
                    @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                        {{ $plan->name }} ({{ taka($plan->price_monthly) }}/mo)
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Billing Cycle *</label>
                <select name="billing_cycle" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    <option value="trial">Trial</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trial Days</label>
                <input type="number" name="trial_days" value="{{ old('trial_days', 14) }}" min="1" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-700 border-b pb-2">Admin Account</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Name *</label>
                <input type="text" name="admin_name" value="{{ old('admin_name') }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Email *</label>
                <input type="email" name="admin_email" value="{{ old('admin_email') }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Password *</label>
                <input type="password" name="admin_password" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('platform.tenants.index') }}" class="px-5 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Create Tenant</button>
    </div>
</form>
</div>
@endsection
