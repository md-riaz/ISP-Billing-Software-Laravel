<?php
namespace App\Http\Controllers;

use App\Models\Pop;
use App\Models\Area;
use Illuminate\Http\Request;

class PopController extends Controller {
    public function store(Request $request) {
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        Pop::create($validated);
        return back()->with('success', 'POP created.');
    }

    public function update(Request $request, Pop $pop) {
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        $pop->update($validated);
        return back()->with('success', 'POP updated.');
    }

    public function destroy(Pop $pop) {
        $pop->delete();
        return back()->with('success', 'POP deleted.');
    }
}
