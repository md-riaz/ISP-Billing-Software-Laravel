<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller {
    public function company() {
        $tenant = app('currentTenant');
        $settings = Setting::get();
        return view('settings.company', compact('tenant','settings'));
    }

    public function updateCompany(Request $request) {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'invoice_prefix' => 'nullable|string|max:20',
            'invoice_due_days' => 'nullable|integer|min:1',
            'currency_symbol' => 'nullable|string|max:5',
            'sms_api_key' => 'nullable|string|max:500',
            'sms_sender_id' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Settings saved.');
    }
}
