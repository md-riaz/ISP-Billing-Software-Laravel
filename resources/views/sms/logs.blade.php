@extends('layouts.app')
@section('title', 'SMS Logs')
@section('page-title', 'SMS Send Logs')

@section('content')
<div class="space-y-4">
<div class="flex justify-between items-center">
    <a href="{{ route('sms.templates') }}" class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center gap-1">
        <i class="fas fa-arrow-left"></i> Back to Templates
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b"><tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent At</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $log->phone }}</td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ $log->message }}</td>
                    <td class="px-4 py-3">
                        @php $c = ['sent'=>'green','failed'=>'red','queued'=>'yellow'][$log->status] ?? 'gray'; @endphp
                        <span class="px-2 py-1 bg-{{ $c }}-100 text-{{ $c }}-700 rounded-full text-xs font-medium">{{ ucfirst($log->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $log->created_at?->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400"><i class="fas fa-inbox text-3xl mb-2 block"></i>No SMS logs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-4 py-3 border-t">{{ $logs->links() }}</div>
    @endif
</div>
</div>
@endsection
