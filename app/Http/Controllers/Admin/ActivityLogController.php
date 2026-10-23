<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/ActivityLog/Index', [
            'logs' => ActivityLog::with('user:id,name')
                ->orderByDesc('created_at')
                ->paginate(30),
        ]);
    }
}
