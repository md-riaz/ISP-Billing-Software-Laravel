@extends('layouts.platform')
@section('title', 'Subscription Plans')
@section('page-title', 'Subscription Plans')

@section('content')
<div class="space-y-4">
<div class="flex justify-end">
    <a href="{{ route('platform.plans.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 flex items-center gap-2">
        <i class="fas fa-plus"></i>Create Plan
    </a>
</div>

<div class="grid lg:grid-cols-3 gap-4">
    @foreach($plans as $plan)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden {{ !$plan->is_active ? 'opacity-60' : '' }}">
        <div class="p-5 border-b">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-lg">{{ $plan->name }}</h3>
                <span class="text-xs px-2 py-1 rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <p class="text-2xl font-bold text-indigo-600 mt-2">{{ taka($plan->price_monthly) }}<span class="text-sm text-gray-400 font-normal">/mo</span></p>
            <p class="text-sm text-gray-500">{{ taka($plan->price_yearly) }}/year</p>
        </div>
        <div class="p-4 text-sm space-y-2">
            <div class="flex justify-between"><span class="text-gray-500">Customers</span><span class="font-medium">{{ number_format($plan->max_customers) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Staff</span><span class="font-medium">{{ $plan->max_staff }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">OLT Devices</span><span class="font-medium">{{ $plan->max_olt_devices }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">SMS/month</span><span class="font-medium">{{ number_format($plan->max_sms_monthly) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Subscriptions</span><span class="font-medium text-indigo-600">{{ $plan->subscriptions_count }}</span></div>
        </div>
        <div class="px-4 pb-4 flex gap-2">
            <a href="{{ route('platform.plans.edit', $plan) }}" class="flex-1 text-center border border-indigo-600 text-indigo-600 py-1.5 rounded-lg text-sm hover:bg-indigo-50">Edit</a>
            <form method="POST" action="{{ route('platform.plans.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?')">
                @csrf @method('DELETE')
                <button class="px-3 py-1.5 border border-red-300 text-red-600 rounded-lg text-sm hover:bg-red-50">Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
</div>
@endsection
