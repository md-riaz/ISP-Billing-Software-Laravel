@extends('layouts.app')
@section('title', $oltDevice->device_name)
@section('page-title', 'OLT Device: ' . $oltDevice->device_name)

@section('content')
<div class="space-y-4">
<div class="flex items-center justify-between">
    <a href="{{ route('olt-devices.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center gap-1">
        <i class="fas fa-arrow-left"></i> Back to OLT Devices
    </a>
    <div class="flex gap-2">
        <a href="{{ route('olt-devices.edit', $oltDevice) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
            <i class="fas fa-edit mr-1"></i>Edit
        </a>
        <form method="POST" action="{{ route('olt-devices.destroy', $oltDevice) }}" onsubmit="return confirm('Delete this device?')">
            @csrf @method('DELETE')
            <button class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm hover:bg-red-200"><i class="fas fa-trash mr-1"></i>Delete</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Device Info -->
    <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-1">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-server text-indigo-600 text-xl"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">{{ $oltDevice->device_name }}</h3>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $oltDevice->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst($oltDevice->status) }}
                </span>
            </div>
        </div>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Vendor</span><span class="font-medium">{{ $oltDevice->vendor ?: '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Model</span><span class="font-medium">{{ $oltDevice->model ?: '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">IP Address</span><span class="font-medium">{{ $oltDevice->ip_address ?: '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Port</span><span class="font-medium">{{ $oltDevice->port ?: '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Auth Type</span><span class="font-medium">{{ strtoupper($oltDevice->auth_type) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Area</span><span class="font-medium">{{ $oltDevice->area?->name ?: '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">POP</span><span class="font-medium">{{ $oltDevice->pop?->name ?: '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Last Sync</span><span class="font-medium">{{ $oltDevice->last_synced_at?->diffForHumans() ?: 'Never' }}</span></div>
        </div>
        @if($oltDevice->notes)
        <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-600">{{ $oltDevice->notes }}</div>
        @endif
    </div>

    <!-- Stats & Actions -->
    <div class="lg:col-span-2 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-indigo-500">
                <p class="text-sm text-gray-500">Mapped Services</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $oltDevice->customer_services_count ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
                <p class="text-sm text-gray-500">Synced ONUs</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $oltDevice->synced_onus_count ?? 0 }}</p>
            </div>
        </div>

        <!-- Recent Action Logs -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Recent Action Logs</h3>
            @if($oltDevice->actionLogs->count() > 0)
            <div class="space-y-2">
                @foreach($oltDevice->actionLogs->take(10) as $log)
                <div class="flex items-start justify-between p-3 bg-gray-50 rounded-lg text-sm">
                    <div>
                        <span class="font-medium text-gray-700">{{ str_replace('_',' ', $log->action_type) }}</span>
                        @if($log->executed_by)
                        <span class="text-gray-400 text-xs ml-2">by {{ $log->executor?->name }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $log->status === 'success' ? 'bg-green-100 text-green-700' : ($log->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($log->status) }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-400 text-sm text-center py-4">No action logs yet.</p>
            @endif
        </div>
    </div>
</div>

<!-- Synced ONUs -->
@if($oltDevice->syncedOnus->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-3 border-b">
        <h3 class="font-semibold text-gray-800">Synced ONUs ({{ $oltDevice->syncedOnus->count() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50"><tr>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">ONU ID</th>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Serial</th>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Name</th>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">PON Port</th>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Status</th>
                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Last Seen</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($oltDevice->syncedOnus->take(20) as $onu)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-mono text-xs">{{ $onu->onu_identifier }}</td>
                    <td class="px-4 py-2 font-mono text-xs">{{ $onu->onu_serial ?: '-' }}</td>
                    <td class="px-4 py-2">{{ $onu->onu_name ?: '-' }}</td>
                    <td class="px-4 py-2">{{ $onu->pon_port ?: '-' }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-0.5 bg-{{ $onu->status === 'online' ? 'green' : 'gray' }}-100 text-{{ $onu->status === 'online' ? 'green' : 'gray' }}-700 rounded text-xs">{{ ucfirst($onu->status ?? 'unknown') }}</span>
                    </td>
                    <td class="px-4 py-2 text-xs text-gray-500">{{ $onu->last_seen_at?->diffForHumans() ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
</div>
@endsection
