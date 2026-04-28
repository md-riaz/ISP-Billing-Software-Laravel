<?php
namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;

class SmsController extends Controller {
    public function templates() {
        $templates = SmsTemplate::orderBy('template_name')->get();
        return view('sms.templates', compact('templates'));
    }

    public function storeTemplate(Request $request) {
        $validated = $request->validate([
            'event_type' => 'required|string|max:100',
            'template_name' => 'required|string|max:255',
            'message_body' => 'required|string',
        ]);
        SmsTemplate::create($validated);
        return back()->with('success', 'Template saved.');
    }

    public function updateTemplate(Request $request, SmsTemplate $template) {
        $validated = $request->validate([
            'event_type' => 'required|string|max:100',
            'template_name' => 'required|string|max:255',
            'message_body' => 'required|string',
            'is_active' => 'boolean',
        ]);
        $template->update($validated);
        return back()->with('success', 'Template updated.');
    }

    public function destroyTemplate(SmsTemplate $template) {
        $template->delete();
        return back()->with('success', 'Template deleted.');
    }

    public function logs(Request $request) {
        $query = SmsLog::with('customer');
        if ($request->filled('status')) $query->where('status', $request->status);
        $logs = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('sms.logs', compact('logs'));
    }
}
