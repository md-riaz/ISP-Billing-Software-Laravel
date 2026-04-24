@extends('layouts.app')
@section('title', 'Company Settings')
@section('page-title', 'Settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

<!-- Settings Nav Tabs -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="flex border-b">
        @foreach([
            ['route' => 'settings.company', 'label' => 'Company', 'icon' => 'fas fa-building'],
        ] as $tab)
        <a href="{{ route($tab['route']) }}"
           class="flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 {{ request()->routeIs($tab['route']) ? 'border-indigo-600 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800' }}">
            <i class="{{ $tab['icon'] }}"></i>{{ $tab['label'] }}
        </a>
        @endforeach
    </div>

    <div class="p-6">
        <form method="POST" action="{{ route('settings.company.update') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name', setting('company_name', $tenant->name)) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">{{ old('address', setting('address')) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', setting('phone', $tenant->phone)) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', setting('email', $tenant->email)) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                </div>
            </div>

            <hr>
            <h3 class="font-medium text-gray-700">Billing Settings</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Prefix</label>
                    <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', setting('invoice_prefix', 'INV')) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Due Days</label>
                    <input type="number" name="invoice_due_days" value="{{ old('invoice_due_days', setting('invoice_due_days', 10)) }}" min="1"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                </div>
            </div>

            <hr>
            <h3 class="font-medium text-gray-700">SMS Settings</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMS API Key</label>
                    <input type="text" name="sms_api_key" value="{{ old('sms_api_key', setting('sms_api_key')) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                           placeholder="Your SMS provider API key">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMS Sender ID</label>
                    <input type="text" name="sms_sender_id" value="{{ old('sms_sender_id', setting('sms_sender_id')) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                           placeholder="Sender name / number">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-indigo-700">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<!-- Tenant Info Card -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="font-semibold text-gray-800 mb-3">Account Information</h3>
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div><span class="text-gray-500">Tenant Slug:</span> <span class="font-medium">{{ $tenant->slug }}</span></div>
        <div><span class="text-gray-500">Status:</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ ucfirst($tenant->status) }}
            </span>
        </div>
        <div><span class="text-gray-500">Timezone:</span> <span class="font-medium">{{ $tenant->timezone }}</span></div>
        <div><span class="text-gray-500">Account Since:</span> <span class="font-medium">{{ $tenant->created_at?->format('d M Y') }}</span></div>
    </div>
</div>
</div>
@endsection
