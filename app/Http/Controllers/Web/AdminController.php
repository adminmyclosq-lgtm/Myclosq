<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class AdminController extends Controller
{
    public function __construct(private AnalyticsService $analytics) {}

    public function index()
    {
        return view('admin.dashboard', ['stats' => $this->analytics->dashboard()]);
    }
}
