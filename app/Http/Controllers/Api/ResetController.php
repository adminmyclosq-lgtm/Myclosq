<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reset\CheckinRequest;
use App\Services\GutResetService;
use Illuminate\Http\Request;

class ResetController extends Controller
{
    public function show(Request $request)
    {
        return response()->json(
            $request->user()->resetProfile?->load([
                'day0Baseline','gutSignalCheckpoints','dailyAdherence',
                'milestoneCheckins','griScores','grsScores','finalClassification'
            ])
        );
    }

    public function checkin(CheckinRequest $request, GutResetService $service)
    {
        $profile = $request->user()->resetProfile;
        abort_unless($profile, 404, 'Reset profile not found.');

        return response()->json(
            $service->recordCheckin(
                $profile,
                $request->integer('day_number'),
                $request->array('answers'),
                $request->input('notes')
            ),
            201
        );
    }
}
