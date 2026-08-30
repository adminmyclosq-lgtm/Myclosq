<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Day0Baseline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MyPlanController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->resetProfile()
            ->with(['day0Baseline.priorityAreas', 'dailyAdherence', 'day30Decision'])
            ->first();
        $completedDays = $profile
            ? $profile->dailyAdherence->where('response_status', 'completed')->pluck('reset_day')->all()
            : [];
        $currentDay = $profile ? min(30, max((int) $profile->current_reset_day, empty($completedDays) ? 0 : max($completedDays))) : 0;
        $selectedAreas = $profile?->day0Baseline?->priorityAreas->pluck('priority_area')->all() ?? [];

        return view('my-plan', compact('profile', 'currentDay', 'selectedAreas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'priority_areas' => ['required', 'array', 'min:1', 'max:3'],
            'priority_areas.*' => ['required', 'string', 'in:bloating,gas_burping,heaviness,acidity,regularity,comfort'],
        ]);
        $profile = $request->user()->resetProfile;
        abort_unless($profile, 404, 'Reset profile not found.');

        DB::transaction(function () use ($profile, $data) {
            $baseline = Day0Baseline::firstOrCreate(['reset_profile_id' => $profile->id]);
            $baseline->priorityAreas()->delete();
            foreach ($data['priority_areas'] as $index => $area) {
                $baseline->priorityAreas()->create([
                    'priority_area' => $area,
                    'priority_rank' => $index + 1,
                ]);
            }
        });

        return redirect()->route('my-plan')->with('success', 'Your focus areas have been saved.');
    }
}
