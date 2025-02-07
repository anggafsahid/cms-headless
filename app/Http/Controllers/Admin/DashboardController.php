<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Media;
use App\Models\Team;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetching basic stats for the dashboard
        $pageCount = Page::count();
        $mediaCount = Media::count();
        $teamMemberCount = Team::count();

        return view('admin.dashboard', compact('pageCount', 'mediaCount', 'teamMemberCount'));
    }
}
