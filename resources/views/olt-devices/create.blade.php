@extends('layouts.app')
@section('title', 'Add OLT Device')
@section('page-title', 'Add OLT Device')

@section('content')
<div class="max-w-2xl mx-auto">
<a href="{{ route('olt-devices.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center gap-1 mb-4">
    <i class="fas fa-arrow-left"></i> Back to OLT Devices
</a>
<div class="bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('olt-devices.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Device Name <span class="text-red-500">*</span></label>
                <input type="text" name="device_name" value="{{ old('device_name') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                       placeholder="e.g. Main OLT - Dhaka">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                <select name="vendor" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    <option value="">Select vendor...</option>
                    @foreach(['Huawei','ZTE','VSOL','C-Data','BDCOM','Other'] as $v)
                    <option value="{{ $v }}" {{ old('vendor') == $v ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                <input type="text" name="model" value="{{ old('model') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                <input type="text" name="ip_address" value="{{ old('ip_address') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                       placeholder="192.168.1.1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                <input type="number" name="port" value="{{ old('port', 80) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">API Base URL</label>
                <input type="text" name="base_url" value="{{ old('base_url') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                       placeholder="http://192.168.1.1:8080/api">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Auth Type <span class="text-red-500">*</span></label>
                <select name="auth_type" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    <option value="basic" {{ old('auth_type','basic') == 'basic' ? 'selected' : '' }}>Basic (Username/Password)</option>
                    <option value="api_key" {{ old('auth_type') == 'api_key' ? 'selected' : '' }}>API Key</option>
                    <option value="token" {{ old('auth_type') == 'token' ? 'selected' : '' }}>Token</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">API Token</label>
                <input type="text" name="api_token" value="{{ old('api_token') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Area</label>
                <select name="area_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    <option value="">Select area...</option>
                    @foreach($areas as $area)
                    <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">POP</label>
                <select name="pop_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    <option value="">Select POP...</option>
                    @foreach($pops as $pop)
                    <option value="{{ $pop->id }}" {{ old('pop_id') == $pop->id ? 'selected' : '' }}>{{ $pop->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-indigo-700">Save Device</button>
            <a href="{{ route('olt-devices.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>
</div>
@endsection
