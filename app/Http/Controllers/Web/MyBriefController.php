<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyBriefController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->resetProfile()
            ->with(['dailyAdherence', 'gutSignalCheckpoints', 'day30Decision'])
            ->first();

        $completedDays = $profile
            ? $profile->dailyAdherence->where('response_status', 'completed')->pluck('reset_day')->all()
            : [];
        $currentDay = $profile ? min(30, max((int) $profile->current_reset_day, empty($completedDays) ? 0 : max($completedDays))) : 0;

        return view('my-brief', compact('profile', 'completedDays', 'currentDay'));
    }
}
