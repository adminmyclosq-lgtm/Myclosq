<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Phase2DAnalyticsService;

class ResetOperationsController extends Controller
{
    public function index(Phase2DAnalyticsService $analytics)
    {
        return view('admin.reset.index',$analytics->resetDashboard());
    }
}
