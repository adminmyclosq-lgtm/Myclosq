<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResetProfile;
use App\Models\ResetReentryRequest;
use App\Models\SafetyFlag;
use App\Models\Testimonial;
use App\Services\Phase2DAnalyticsService;
use App\Services\ResetJourneyService;
use Illuminate\Http\Request;

class ResetOperationsController extends Controller
{
    public function index(Phase2DAnalyticsService $analytics)
    {
        return view('admin.reset.index', $analytics->resetDashboard());
    }

    public function show(ResetProfile $resetProfile)
    {
        $resetProfile->load([
            'user.customerProfile', 'day0Baseline.priorityAreas', 'day0Baseline.triggers', 'startPlan',
            'dailyAdherence', 'resetCardUsage', 'gutSignalCheckpoints', 'milestoneCheckins.answers',
            'finalReview', 'griScores', 'grsScores', 'finalClassification', 'day30Decision',
            'unusualEvents', 'safetyFlags', 'testimonial', 'dropoffEvents', 'adminNotes',
        ]);

        return view('admin.reset.show', compact('resetProfile'));
    }

    public function reentry()
    {
        $requests = ResetReentryRequest::with([
            'user.customerProfile', 'previousResetProfile', 'newResetProfile', 'reviewer',
        ])->latest('id')->paginate(30);

        return view('admin.reset.reentry', compact('requests'));
    }

    public function approveReentry(Request $request, ResetReentryRequest $reentryRequest, ResetJourneyService $journey)
    {
        $data = $request->validate(['review_notes' => ['nullable', 'string', 'max:1000']]);
        $journey->approveReentry($reentryRequest, $request->user(), $data['review_notes'] ?? null);

        return back()->with('success', 'Re-entry approved and a new reset cycle created.');
    }

    public function rejectReentry(Request $request, ResetReentryRequest $reentryRequest, ResetJourneyService $journey)
    {
        $data = $request->validate(['review_notes' => ['nullable', 'string', 'max:1000']]);
        $journey->rejectReentry($reentryRequest, $request->user(), $data['review_notes'] ?? null);

        return back()->with('success', 'Re-entry request rejected.');
    }

    public function resolveSafety(Request $request, SafetyFlag $safetyFlag, ResetJourneyService $journey)
    {
        $data = $request->validate(['resolution' => ['required', 'string', 'max:1000']]);
        $journey->resolveSafety($safetyFlag, $request->user(), $data['resolution']);

        return back()->with('success', 'Safety review has been resolved.');
    }

    public function testimonials()
    {
        $testimonials = Testimonial::with('resetprofile.user.customerProfile')->latest('id')->paginate(30);
        return view('admin.reset.testimonials', compact('testimonials'));
    }

    public function moderateTestimonial(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'moderation_status' => ['required', 'in:pending,approved,rejected'],
            'published' => ['nullable', 'boolean'],
        ]);

        $testimonial->update([
            'moderation_status' => $data['moderation_status'],
            'published_at' => $data['moderation_status'] === 'approved' && $request->boolean('published') && $testimonial->consent === 'yes' ? now() : null,
        ]);

        return back()->with('success', 'Testimonial moderation updated.');
    }
}
