@extends('layouts.app')
@section('title','Service Details')
@section('page-title','Service Details')
@section('content')
<div class="max-w-2xl">
<a href="{{ route('services.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="fas fa-arrow-left mr-2"></i>Back
</a>
<div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
    <div class="flex justify-between items-start">
        <div>
            <h3 class="font-bold text-gray-800">{{ $service->customer?->full_name }}</h3>
            <p class="text-sm text-gray-500">{{ $service->customer?->customer_code }}</p>
        </div>
        <span class="px-3 py-1 rounded-full text-sm {{ $service->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
            {{ ucfirst($service->status) }}
        </span>
    </div>
    <dl class="grid grid-cols-2 gap-3 text-sm border-t pt-4">
        <div><dt class="text-gray-500">Package</dt><dd class="font-medium">{{ $service->package?->package_name }}</dd></div>
        <div><dt class="text-gray-500">Speed</dt><dd class="font-medium">{{ $service->package?->speed_label }}</dd></div>
        <div><dt class="text-gray-500">Monthly Price</dt><dd class="font-medium">{{ taka($service->monthly_price) }}</dd></div>
        <div><dt class="text-gray-500">OLT Device</dt><dd class="font-medium">{{ $service->oltDevice?->device_name ?? '—' }}</dd></div>
        <div><dt class="text-gray-500">ONU Identifier</dt><dd class="font-medium">{{ $service->onu_identifier ?? '—' }}</dd></div>
        <div><dt class="text-gray-500">PON Port</dt><dd class="font-medium">{{ $service->pon_port ?? '—' }}</dd></div>
        <div><dt class="text-gray-500">ONU Index</dt><dd class="font-medium">{{ $service->onu_index ?? '—' }}</dd></div>
        <div><dt class="text-gray-500">Billing Start</dt><dd class="font-medium">{{ $service->billing_start_date?->format('d M Y') ?? '—' }}</dd></div>
    </dl>
    <div class="flex gap-2 pt-2">
        @if($service->status !== 'active')
        <form method="POST" action="{{ route('services.activate', $service) }}">
            @csrf
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm">Activate</button>
        </form>
        @endif
        @if($service->status === 'active')
        <form method="POST" action="{{ route('services.suspend', $service) }}">
            @csrf
            <button class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm">Suspend</button>
        </form>
        @endif
        <form method="POST" action="{{ route('services.destroy', $service) }}" onsubmit="return confirm('Terminate this service?')">
            @csrf @method('DELETE')
            <button class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm">Terminate</button>
        </form>
    </div>
</div>
</div>
@endsection
