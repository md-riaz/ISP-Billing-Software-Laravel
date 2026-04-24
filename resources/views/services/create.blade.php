@extends('layouts.app')
@section('title','New Service')
@section('page-title','New Service')
@section('content')
<div class="max-w-2xl">
<a href="{{ route('services.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="fas fa-arrow-left mr-2"></i>Back
</a>
<form method="POST" action="{{ route('services.store') }}" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
    @csrf
    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
            <select name="customer_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                <option value="">Select Customer</option>
                @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ (old('customer_id', request('customer_id')) == $c->id) ? 'selected' : '' }}>
                    {{ $c->full_name }} ({{ $c->customer_code }})
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Package *</label>
            <select name="package_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                <option value="">Select Package</option>
                @foreach($packages as $pkg)
                <option value="{{ $pkg->id }}" {{ old('package_id') == $pkg->id ? 'selected' : '' }} data-price="{{ $pkg->monthly_price }}">
                    {{ $pkg->package_name }} — {{ taka($pkg->monthly_price) }}/mo
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Price *</label>
            <input type="number" name="monthly_price" id="monthly_price" value="{{ old('monthly_price') }}" step="0.01" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">OLT Device</label>
            <select name="olt_device_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                <option value="">None</option>
                @foreach($oltDevices as $olt)
                <option value="{{ $olt->id }}" {{ old('olt_device_id') == $olt->id ? 'selected' : '' }}>{{ $olt->device_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ONU Identifier</label>
            <input type="text" name="onu_identifier" value="{{ old('onu_identifier') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" placeholder="e.g. HWTC12345678">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">PON Port</label>
            <input type="text" name="pon_port" value="{{ old('pon_port') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none" placeholder="e.g. 0/1/4">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">ONU Index</label>
            <input type="number" name="onu_index" value="{{ old('onu_index') }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Billing Start Date</label>
            <input type="date" name="billing_start_date" value="{{ old('billing_start_date', date('Y-m-d')) }}" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
    </div>
    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('services.index') }}" class="px-5 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Create Service</button>
    </div>
</form>
</div>
<script>
document.querySelector('[name=package_id]').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('monthly_price').value = opt.dataset.price || '';
});
</script>
@endsection
