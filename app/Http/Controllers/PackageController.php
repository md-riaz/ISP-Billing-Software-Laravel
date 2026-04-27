<?php
namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller {
    public function index() {
        $packages = Package::orderByDesc('created_at')->paginate(20);
        return view('packages.index', compact('packages'));
    }

    public function create() {
        return view('packages.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'package_code' => 'required|string|max:50',
            'package_name' => 'required|string|max:255',
            'speed_label' => 'required|string|max:100',
            'package_type' => 'required|in:home,business,corporate',
            'monthly_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'service_profile_label' => 'nullable|string|max:100',
            'line_profile_label' => 'nullable|string|max:100',
        ]);

        $package = Package::create(array_merge($validated, ['is_active' => true]));
        logActivity('package_created', 'Package', $package->id);
        return redirect()->route('packages.index')->with('success', 'Package created.');
    }

    public function edit(Package $package) {
        return view('packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package) {
        $validated = $request->validate([
            'package_code' => 'required|string|max:50',
            'package_name' => 'required|string|max:255',
            'speed_label' => 'required|string|max:100',
            'package_type' => 'required|in:home,business,corporate',
            'monthly_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'service_profile_label' => 'nullable|string|max:100',
            'line_profile_label' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);
        $package->update($validated);
        return redirect()->route('packages.index')->with('success', 'Package updated.');
    }

    public function destroy(Package $package) {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Package deleted.');
    }
}
