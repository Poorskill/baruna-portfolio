<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Skill;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalProjects' => Project::count(),
                'publishedProjects' => Project::where('status', 'published')->count(),
                'draftProjects' => Project::where('status', 'draft')->count(),
                'totalSkills' => Skill::count(),
                'unreadMessages' => ContactMessage::where('status', 'unread')->count(),
            ],
        ]);
    }
}
