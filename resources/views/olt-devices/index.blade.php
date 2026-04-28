@extends('layouts.app')
@section('title', 'OLT Devices')
@section('page-title', 'OLT Devices')

@section('content')
<div class="space-y-4">
<div class="flex justify-between items-center">
    <p class="text-sm text-gray-600">Manage connected OLT devices for service provisioning.</p>
    <a href="{{ route('olt-devices.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 flex items-center gap-2">
        <i class="fas fa-plus"></i>Add OLT Device
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($devices as $device)
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-server text-indigo-600"></i>
            </div>
            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $device->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ ucfirst($device->status) }}
            </span>
        </div>
        <h3 class="font-semibold text-gray-800">{{ $device->device_name }}</h3>
        <p class="text-xs text-gray-500 mt-1">{{ $device->vendor }} {{ $device->model }}</p>
        <div class="mt-3 space-y-1 text-xs text-gray-600">
            @if($device->ip_address)<div><i class="fas fa-network-wired mr-1 text-gray-400"></i>{{ $device->ip_address }}@if($device->port):{{ $device->port }}@endif</div>@endif
            @if($device->area)<div><i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>{{ $device->area->name }}</div>@endif
            @if($device->last_synced_at)<div><i class="fas fa-sync mr-1 text-gray-400"></i>Last sync: {{ $device->last_synced_at->diffForHumans() }}</div>@endif
        </div>
        <div class="flex gap-2 mt-4 pt-3 border-t">
            <a href="{{ route('olt-devices.show', $device) }}" class="flex-1 text-center bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-indigo-100">
                <i class="fas fa-eye mr-1"></i>View
            </a>
            <a href="{{ route('olt-devices.edit', $device) }}" class="flex-1 text-center bg-gray-50 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-gray-100">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
        <i class="fas fa-server text-4xl mb-3 block"></i>
        <p>No OLT devices configured yet.</p>
        <a href="{{ route('olt-devices.create') }}" class="mt-3 inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Add Your First Device</a>
    </div>
    @endforelse
</div>
@if($devices->hasPages())
<div>{{ $devices->links() }}</div>
@endif
</div>
@endsection
