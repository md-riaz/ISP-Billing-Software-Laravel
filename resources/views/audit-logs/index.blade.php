@extends('layouts.app')
@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<div class="space-y-4">
<form class="flex flex-wrap gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search action..."
           class="border rounded-lg px-3 py-2 text-sm w-48 focus:ring-2 focus:ring-indigo-400 outline-none">
    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Filter</button>
    @if(request('search'))
    <a href="{{ route('audit-logs.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b"><tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entity</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs font-medium">{{ str_replace('_',' ', $log->action) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        @if($log->entity_type)
                        <span class="font-medium">{{ $log->entity_type }}</span>
                        @if($log->entity_id) <span class="text-gray-400">#{{ $log->entity_id }}</span> @endif
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $log->user?->name ?? 'System' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->ip_address }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->created_at?->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400"><i class="fas fa-history text-3xl mb-2 block"></i>No audit logs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-4 py-3 border-t">{{ $logs->withQueryString()->links() }}</div>
    @endif
</div>
</div>
@endsection
