@extends('layouts.app')
@section('title','Edit Package')
@section('page-title','Edit Package')
@section('content')
<div class="max-w-2xl">
<a href="{{ route('packages.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="fas fa-arrow-left mr-2"></i>Back
</a>
<form method="POST" action="{{ route('packages.update', $package) }}" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
    @csrf @method('PUT')
    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Package Name *</label>
            <input type="text" name="package_name" value="{{ old('package_name', $package->package_name) }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Download Speed (Mbps) *</label>
            <input type="number" name="download_speed_mbps" value="{{ old('download_speed_mbps', $package->download_speed_mbps) }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Speed (Mbps) *</label>
            <input type="number" name="upload_speed_mbps" value="{{ old('upload_speed_mbps', $package->upload_speed_mbps) }}" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Price (BDT) *</label>
            <input type="number" name="monthly_price" value="{{ old('monthly_price', $package->monthly_price) }}" step="0.01" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Package Type</label>
            <select name="package_type" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                @foreach(['shared','dedicated','ftth','wireless'] as $t)
                <option value="{{ $t }}" {{ old('package_type', $package->package_type) == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">{{ old('description', $package->description) }}</textarea>
        </div>
        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }} class="rounded">
                <span class="text-sm text-gray-700">Active</span>
            </label>
        </div>
    </div>
    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('packages.index') }}" class="px-5 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancel</a>
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Update Package</button>
    </div>
</form>
</div>
@endsection
