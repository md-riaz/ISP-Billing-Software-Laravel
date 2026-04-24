<?php
namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller {
    public function index(Request $request) {
        $query = ActivityLog::with('user')
            ->where('tenant_id', app('currentTenant')->id);

        if ($request->filled('search')) {
            $query->where('action','like','%'.$request->search.'%');
        }

        $logs = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        return view('audit-logs.index', compact('logs'));
    }
}
