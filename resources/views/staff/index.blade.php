@extends('layouts.app')
@section('title', 'Staff Management')
@section('page-title', 'Staff Management')

@section('content')
<div class="space-y-4">
<div class="flex justify-between items-center">
    <p class="text-sm text-gray-600">Manage staff users and their roles for your ISP.</p>
    <a href="{{ route('staff.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 flex items-center gap-2">
        <i class="fas fa-plus"></i>Add Staff
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Login</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($staff as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="font-medium text-gray-800">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $user->phone ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @foreach($user->roles as $role)
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">{{ $role->name }}</span>
                        @endforeach
                        @if($user->roles->isEmpty())<span class="text-gray-400 text-xs">No role</span>@endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('staff.edit', $user) }}" class="text-indigo-600 hover:text-indigo-800 mr-2 text-sm"><i class="fas fa-edit"></i></a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('staff.destroy', $user) }}" class="inline" onsubmit="return confirm('Remove this staff member?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400"><i class="fas fa-user-tie text-3xl mb-2 block"></i>No staff members yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($staff->hasPages())
    <div class="px-4 py-3 border-t">{{ $staff->links() }}</div>
    @endif
</div>
</div>
@endsection
