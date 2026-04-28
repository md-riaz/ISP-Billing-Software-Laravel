@extends('layouts.platform')
@section('title', $tenant->name)
@section('page-title', $tenant->name)

@section('content')
<div class="space-y-6">
<a href="{{ route('platform.tenants.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
    <i class="fas fa-arrow-left mr-2"></i>Back to Tenants
</a>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Info -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700">Tenant Information</h3>
                <a href="{{ route('platform.tenants.edit', $tenant) }}" class="text-sm text-indigo-600 hover:underline">Edit</a>
            </div>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500">Name</dt><dd class="font-medium text-gray-800">{{ $tenant->name }}</dd></div>
                <div><dt class="text-gray-500">Slug</dt><dd class="font-medium text-gray-800">{{ $tenant->slug }}</dd></div>
                <div><dt class="text-gray-500">Email</dt><dd class="font-medium text-gray-800">{{ $tenant->email }}</dd></div>
                <div><dt class="text-gray-500">Phone</dt><dd class="font-medium text-gray-800">{{ $tenant->phone ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Status</dt>
                    <dd><span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' :
                           ($tenant->status === 'trial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ ucfirst($tenant->status) }}</span></dd>
                </div>
                <div><dt class="text-gray-500">Customers</dt><dd class="font-medium text-gray-800">{{ $tenant->customers_count }}</dd></div>
                <div><dt class="text-gray-500">Trial Ends</dt><dd class="font-medium text-gray-800">{{ $tenant->trial_ends_at?->format('d M Y') ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Created</dt><dd class="font-medium text-gray-800">{{ $tenant->created_at->format('d M Y') }}</dd></div>
            </dl>
        </div>

        <!-- Users -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b"><h3 class="font-semibold text-gray-700">Users</h3></div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr></thead>
                <tbody class="divide-y">
                    @foreach($tenant->users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Subscriptions -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b"><h3 class="font-semibold text-gray-700">Subscriptions</h3></div>
            <div class="p-5 space-y-3">
                @foreach($tenant->subscriptions as $sub)
                <div class="border rounded-lg p-3 text-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">{{ $sub->plan?->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($sub->billing_cycle) }} · {{ taka($sub->price) }}</p>
                            <p class="text-xs text-gray-500">Expires: {{ $sub->expires_at?->format('d M Y') ?? '—' }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs {{ $sub->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Record Payment -->
        @if($tenant->subscriptions->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-gray-700 mb-4">Record Payment</h3>
            <form method="POST" action="{{ route('platform.tenants.payment', $tenant) }}" class="space-y-3">
                @csrf
                <select name="subscription_id" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    @foreach($tenant->subscriptions as $sub)
                    <option value="{{ $sub->id }}">{{ $sub->plan?->name }} ({{ ucfirst($sub->status) }})</option>
                    @endforeach
                </select>
                <input type="number" name="amount" placeholder="Amount" step="0.01" required class="w-full border rounded-lg px-3 py-2 text-sm">
                <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="w-full border rounded-lg px-3 py-2 text-sm">
                <input type="text" name="method" placeholder="Method (bank, bkash...)" class="w-full border rounded-lg px-3 py-2 text-sm">
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg text-sm hover:bg-green-700">Record Payment</button>
            </form>
        </div>
        @endif
    </div>
</div>
</div>
@endsection
