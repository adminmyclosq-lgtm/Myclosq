<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\Phase2DAnalyticsService;

class WhatsAppDashboardController extends Controller
{
    public function index(Phase2DAnalyticsService $analytics)
    {
        return view('admin.whatsapp.dashboard',$analytics->whatsappDashboard());
    }
}
