@extends('layouts.app')
@section('title', 'SMS Templates')
@section('page-title', 'SMS & Notifications')

@section('content')
<div class="space-y-4" x-data="{ showForm: false }">

<div class="flex items-center justify-between">
    <p class="text-sm text-gray-600">Manage SMS templates for billing notifications.</p>
    <div class="flex gap-2">
        <a href="{{ route('sms.logs') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 flex items-center gap-2">
            <i class="fas fa-history"></i>Send Logs
        </a>
        <button @click="showForm=!showForm" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 flex items-center gap-2">
            <i class="fas fa-plus"></i>Add Template
        </button>
    </div>
</div>

<!-- Add Form -->
<div x-show="showForm" x-cloak class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="font-semibold text-gray-800 mb-4">New SMS Template</h3>
    <form method="POST" action="{{ route('sms.templates.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                <input type="text" name="template_name" value="{{ old('template_name') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                <select name="event_type" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                    @foreach(['invoice_generated','payment_received','due_reminder','suspension_warning','reconnection_success'] as $e)
                    <option value="{{ $e }}" {{ old('event_type') == $e ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$e)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Message Body</label>
                <textarea name="message_body" rows="3" required
                          class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                          placeholder="Use {name}, {amount}, {due_date}, {month}, {company}, {due}, {receipt_no} as variables">{{ old('message_body') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Available variables: {name} {amount} {due_date} {month} {company} {due} {receipt_no}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Save Template</button>
            <button type="button" @click="showForm=false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">Cancel</button>
        </div>
    </form>
</div>

<!-- Templates List -->
<div class="space-y-3">
    @forelse($templates as $template)
    <div class="bg-white rounded-xl shadow-sm p-5" x-data="{ edit: false }">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <span class="font-semibold text-gray-800">{{ $template->template_name }}</span>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">{{ ucwords(str_replace('_',' ',$template->event_type)) }}</span>
                    @if($template->is_active)
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs">Active</span>
                    @else
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded text-xs">Inactive</span>
                    @endif
                </div>
                <p x-show="!edit" class="text-sm text-gray-600 bg-gray-50 rounded p-3">{{ $template->message_body }}</p>
                <form x-show="edit" x-cloak method="POST" action="{{ route('sms.templates.update', $template) }}" class="space-y-2">
                    @csrf @method('PUT')
                    <textarea name="message_body" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">{{ $template->message_body }}</textarea>
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}> Active
                        </label>
                        <button type="submit" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-indigo-700">Save</button>
                        <button type="button" @click="edit=false" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm">Cancel</button>
                    </div>
                </form>
            </div>
            <div class="flex gap-2 ml-4">
                <button @click="edit=!edit" class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-edit"></i></button>
                <form method="POST" action="{{ route('sms.templates.destroy', $template) }}" onsubmit="return confirm('Delete template?')">
                    @csrf @method('DELETE')
                    <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
        <i class="fas fa-sms text-3xl mb-2 block"></i>
        No SMS templates yet. Add your first template above.
    </div>
    @endforelse
</div>
</div>
@endsection
