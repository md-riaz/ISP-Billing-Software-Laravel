@extends('layouts.platform')
@section('title', 'Platform Dashboard')
@section('page-title', 'Platform Dashboard')

@section('content')
<div class="space-y-6">

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
    @foreach([
        ['label'=>'Total ISPs','value'=>$stats['total_tenants'],'icon'=>'fas fa-building','color'=>'indigo'],
        ['label'=>'Active','value'=>$stats['active_tenants'],'icon'=>'fas fa-check-circle','color'=>'green'],
        ['label'=>'Trial','value'=>$stats['trial_tenants'],'icon'=>'fas fa-clock','color'=>'yellow'],
        ['label'=>'Suspended','value'=>$stats['suspended_tenants'],'icon'=>'fas fa-ban','color'=>'red'],
        ['label'=>'Monthly Revenue','value'=>taka($stats['monthly_revenue']),'icon'=>'fas fa-money-bill-wave','color'=>'blue'],
    ] as $stat)
    <div class="bg-white rounded-xl shadow-sm p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $stat['label'] }}</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stat['value'] }}</p>
    </div>
    @endforeach
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b">
        <h3 class="font-semibold text-gray-700">Recent Tenants</h3>
        <a href="{{ route('platform.tenants.index') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($recentTenants as $tenant)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('platform.tenants.show', $tenant) }}" class="font-medium text-indigo-600 hover:underline">{{ $tenant->name }}</a>
                        <p class="text-xs text-gray-400">{{ $tenant->email }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $tenant->subscription?->plan?->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $tenant->status === 'active' ? 'bg-green-100 text-green-700' :
                               ($tenant->status === 'trial' ? 'bg-yellow-100 text-yellow-700' :
                               'bg-red-100 text-red-700') }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $tenant->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection
