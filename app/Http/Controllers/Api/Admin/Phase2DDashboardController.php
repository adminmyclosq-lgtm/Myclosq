<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Phase2DAnalyticsService;

class Phase2DDashboardController extends Controller
{
    public function payments(Phase2DAnalyticsService $a){ return response()->json($a->paymentDashboard()['totals']); }
    public function fulfilment(Phase2DAnalyticsService $a){ return response()->json($a->fulfilmentDashboard()['totals']); }
    public function whatsapp(Phase2DAnalyticsService $a){ return response()->json($a->whatsappDashboard()); }
    public function reset(Phase2DAnalyticsService $a){ return response()->json($a->resetDashboard()); }
}
