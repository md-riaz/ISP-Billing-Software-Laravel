<?php
namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\OltDevice;
use App\Models\Pop;
use Illuminate\Http\Request;

class OltDeviceController extends Controller {
    public function index() {
        $devices = OltDevice::with('area','pop')->orderByDesc('created_at')->paginate(20);
        return view('olt-devices.index', compact('devices'));
    }

    public function create() {
        $areas = Area::orderBy('name')->get();
        $pops = Pop::orderBy('name')->get();
        return view('olt-devices.create', compact('areas','pops'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'vendor' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'base_url' => 'nullable|url|max:255',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'auth_type' => 'required|in:api_key,basic,token',
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string|max:255',
            'api_token' => 'nullable|string|max:500',
            'area_id' => 'nullable|exists:areas,id',
            'pop_id' => 'nullable|exists:pops,id',
            'notes' => 'nullable|string',
        ]);

        OltDevice::create($validated);
        return redirect()->route('olt-devices.index')->with('success', 'OLT Device added.');
    }

    public function show(OltDevice $oltDevice) {
        $oltDevice->load('area','pop','syncedOnus','actionLogs.executor');
        $oltDevice->loadCount('customerServices','syncedOnus');
        return view('olt-devices.show', compact('oltDevice'));
    }

    public function edit(OltDevice $oltDevice) {
        $areas = Area::orderBy('name')->get();
        $pops = Pop::orderBy('name')->get();
        return view('olt-devices.edit', compact('oltDevice','areas','pops'));
    }

    public function update(Request $request, OltDevice $oltDevice) {
        $validated = $request->validate([
            'device_name' => 'required|string|max:255',
            'vendor' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'base_url' => 'nullable|url|max:255',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer|min:1|max:65535',
            'auth_type' => 'required|in:api_key,basic,token',
            'username' => 'nullable|string|max:100',
            'password' => 'nullable|string|max:255',
            'api_token' => 'nullable|string|max:500',
            'area_id' => 'nullable|exists:areas,id',
            'pop_id' => 'nullable|exists:pops,id',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);
        // Only update password/token if provided
        if (empty($validated['password'])) unset($validated['password']);
        if (empty($validated['api_token'])) unset($validated['api_token']);

        $oltDevice->update($validated);
        return redirect()->route('olt-devices.show', $oltDevice)->with('success', 'OLT Device updated.');
    }

    public function destroy(OltDevice $oltDevice) {
        $oltDevice->delete();
        return redirect()->route('olt-devices.index')->with('success', 'OLT Device deleted.');
    }
}
