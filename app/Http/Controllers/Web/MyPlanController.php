<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ResetJourneyService;
use Illuminate\Http\Request;

class MyPlanController extends Controller
{
    public function show(Request $request, ResetJourneyService $journey)
    {
        $user = $request->user();
        $active = $journey->activeProfile($user);
        $latest = $journey->latestProfile($user);

        if ($latest && $latest->status === 'completed') {
            return redirect()->route('reset.reentry');
        }

        if ($active) {
            $profile = $active;
            $selectedAreas = $active->day0Baseline?->priorityAreas()->pluck('priority_area')->all() ?? [];
            $readOnly = !empty($active->day0Baseline) && $active->status !== 'created';

            return view('my-plan', compact('profile', 'selectedAreas', 'readOnly'));
        }

        return redirect()->route('shop');
    }

    public function store(Request $request, ResetJourneyService $journey)
    {
        $user = $request->user();
        $active = $journey->activeProfile($user);
        $latest = $journey->latestProfile($user);

        if ($latest && $latest->status === 'completed') {
            return redirect()->route('reset.reentry');
        }

        if ($active) {
            return redirect()->route('my-plan')->with('success', 'Your plan is already active for this cycle.');
        }

        return redirect()->route('shop');
    }
}
