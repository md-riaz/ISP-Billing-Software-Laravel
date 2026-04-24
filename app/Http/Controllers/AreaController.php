<?php
namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Pop;
use Illuminate\Http\Request;

class AreaController extends Controller {
    public function index() {
        $areas = Area::withCount('pops','customers')->orderBy('name')->paginate(20);
        $pops = Pop::with('area')->orderBy('name')->paginate(20);
        return view('areas.index', compact('areas', 'pops'));
    }

    public function store(Request $request) {
        $validated = $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string']);
        Area::create($validated);
        return back()->with('success', 'Area created.');
    }

    public function update(Request $request, Area $area) {
        $validated = $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string']);
        $area->update($validated);
        return back()->with('success', 'Area updated.');
    }

    public function destroy(Area $area) {
        $area->delete();
        return back()->with('success', 'Area deleted.');
    }
}
