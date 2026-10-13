<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Marketing Campaigns list & management.
     */
    public function index(Request $request)
    {
        // Sample campaigns data for enterprise marketing hub
        $campaigns = collect([
            [
                'id' => 1,
                'name' => 'Spring Gut Reset Launch 2026',
                'channel' => 'WhatsApp & Email',
                'status' => 'active',
                'audience' => 'All Registered Customers',
                'target_count' => 1250,
                'sent_count' => 1250,
                'opened_rate' => '68.4%',
                'clicks_rate' => '24.1%',
                'conversions' => 142,
                'revenue' => 45600.00,
                'start_date' => '2026-09-15',
            ],
            [
                'id' => 2,
                'name' => 'Gut Health 30-Day Check-in',
                'channel' => 'WhatsApp Automated',
                'status' => 'active',
                'audience' => 'Day 15-30 Participants',
                'target_count' => 480,
                'sent_count' => 480,
                'opened_rate' => '89.2%',
                'clicks_rate' => '42.0%',
                'conversions' => 88,
                'revenue' => 28900.00,
                'start_date' => '2026-10-01',
            ],
            [
                'id' => 3,
                'name' => 'Festive Wellness Bundle Offer',
                'channel' => 'Broadcast SMS & Web Banner',
                'status' => 'scheduled',
                'audience' => 'Inactive Customers (>60 days)',
                'target_count' => 850,
                'sent_count' => 0,
                'opened_rate' => '—',
                'clicks_rate' => '—',
                'conversions' => 0,
                'revenue' => 0.00,
                'start_date' => '2026-10-25',
            ],
        ]);

        return view('admin.marketing.campaigns', compact('campaigns'));
    }

    public function store(Request $request)
    {
        return back()->with('success', 'Marketing campaign scheduled successfully.');
    }
}
