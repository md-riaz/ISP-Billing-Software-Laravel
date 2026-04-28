<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller {
    public function index() {
        $staff = User::where('tenant_id', app('currentTenant')->id)->orderBy('name')->paginate(20);
        return view('staff.index', compact('staff'));
    }

    public function create() {
        return view('staff.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string',
        ]);

        $user = User::create([
            'tenant_id' => app('currentTenant')->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return redirect()->route('staff.index')->with('success', 'Staff member created.');
    }

    public function show(User $staff) {
        $this->authorizeTenantStaff($staff);
        return redirect()->route('staff.edit', $staff);
    }

    public function edit(User $staff) {
        $this->authorizeTenantStaff($staff);
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff) {
        $this->authorizeTenantStaff($staff);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = ['name' => $validated['name'], 'phone' => $validated['phone'] ?? null, 'status' => $validated['status']];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }
        $staff->update($data);
        return redirect()->route('staff.index')->with('success', 'Staff member updated.');
    }

    public function destroy(User $staff) {
        $this->authorizeTenantStaff($staff);

        if ($staff->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff member deleted.');
    }

    private function authorizeTenantStaff(User $staff): void {
        if ($staff->tenant_id !== app('currentTenant')->id) {
            abort(403, 'Access denied.');
        }
    }
}
