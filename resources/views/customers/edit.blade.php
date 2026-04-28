@extends('layouts.app')
@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer: ' . $customer->full_name)

@section('content')
<div class="max-w-3xl">
<a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="fas fa-arrow-left mr-2"></i>Back
</a>
<form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6">
    @csrf @method('PUT')

    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-700 border-b pb-2">Personal Information</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="full_name" value="{{ old('full_name', $customer->full_name) }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer Type</label>
                <select name="customer_type" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    @foreach(['home','business','corporate'] as $t)
                    <option value="{{ $t }}" {{ old('customer_type', $customer->customer_type) == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    @foreach(['pending_installation','active','temporary_hold','suspended_due','suspended_manual','disconnected','terminated'] as $s)
                    <option value="{{ $s }}" {{ old('status', $customer->status) == $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Primary Phone *</label>
                <input type="text" name="primary_phone" value="{{ old('primary_phone', $customer->primary_phone) }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Phone</label>
                <input type="text" name="secondary_phone" value="{{ old('secondary_phone', $customer->secondary_phone) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-700 border-b pb-2">Location</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input type="text" name="address_line" value="{{ old('address_line', $customer->address_line) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Area</label>
                <select name="area_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    <option value="">Select Area</option>
                    @foreach($areas as $area)
                    <option value="{{ $area->id }}" {{ old('area_id', $customer->area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">POP</label>
                <select name="pop_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    <option value="">Select POP</option>
                    @foreach($pops as $pop)
                    <option value="{{ $pop->id }}" {{ old('pop_id', $customer->pop_id) == $pop->id ? 'selected' : '' }}>{{ $pop->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                <input type="text" name="district" value="{{ old('district', $customer->district) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-gray-700 border-b pb-2">Billing</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Discount Type</label>
                <select name="discount_type" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    @foreach(['none','fixed','percent'] as $dt)
                    <option value="{{ $dt }}" {{ old('discount_type', $customer->discount_type) == $dt ? 'selected' : '' }}>{{ ucfirst($dt) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Discount Value</label>
                <input type="number" name="discount_value" value="{{ old('discount_value', $customer->discount_value) }}" min="0" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Billing Note</label>
                <textarea name="billing_note" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">{{ old('billing_note', $customer->billing_note) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('customers.show', $customer) }}" class="px-5 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Update Customer</button>
    </div>
</form>
</div>
@endsection
