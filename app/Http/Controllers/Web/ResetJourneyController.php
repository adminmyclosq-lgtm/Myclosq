<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\ResetProfile;
use App\Models\Testimonial;
use App\Models\UnusualEvent;
use App\Models\PositiveEvent;
use App\Services\ResetJourneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResetJourneyController extends Controller
{
    public function __construct(private ResetJourneyService $journey) {}

    public function home(Request $request)
    {
        $user = $request->user();
        $profile = $this->journey->activeProfile($user) ?? $this->journey->latestProfile($user);
        $history = $user->resetProfiles()->latest('cycle_number')->get();
        $currentDay = $profile?->status === 'completed' ? 30 : (int) ($profile?->current_reset_day ?? 0);
        $completedDays = $profile ? $profile->dailyAdherence()->where('response_status', 'completed')->pluck('reset_day')->all() : [];

        return view('reset.home', compact('profile', 'history', 'currentDay', 'completedDays'));
    }

    public function day0(Request $request)
    {
        $user = $request->user();
        $profile = $this->journey->activeProfile($user);
        if (!$profile) {
            $latest = $this->journey->latestProfile($user);
            if ($latest && $latest->status === 'completed') {
                return redirect()->route('reset.reentry');
            }
            $profile = $this->journey->ensureProfile($user);
        }

        $profile->load(['day0Baseline.priorityAreas', 'day0Baseline.triggers', 'startPlan', 'gutSignalCheckpoints']);
        $currentDay = $profile->status === 'completed' ? 30 : (int) $profile->current_reset_day;

        return view('reset.day0', compact('profile', 'currentDay'));
    }

    public function storeDay0(Request $request)
    {
        $data = $request->validate([
            'success_expectation_primary' => ['required', 'string', 'max:100'],
            'success_expectation_open_text' => ['nullable', 'string', 'max:1000'],
            'regularity_frequency' => ['required', 'string', 'max:40'],
            'rescue_remedy_used' => ['required', 'string', 'max:50'],
            'rescue_remedy_detail' => ['nullable', 'string', 'max:255'],
            'recent_disruption_yes_no' => ['required', 'boolean'],
            'recent_disruption_detail' => ['nullable', 'string', 'max:1000'],
            'priority_areas' => ['required', 'array', 'min:1', 'max:3'],
            'priority_areas.*' => ['required', 'string', 'in:bloating,gas_burping,heaviness,acidity,regularity,comfort'],
            'triggers' => ['nullable', 'array', 'max:3'],
            'triggers.*' => ['required', 'string', 'max:100'],
            'bloating_score' => ['required', 'integer', 'between:0,10'],
            'gas_burping_score' => ['required', 'integer', 'between:0,10'],
            'heaviness_score' => ['required', 'integer', 'between:0,10'],
            'acidity_score' => ['required', 'integer', 'between:0,10'],
            'overall_comfort_score' => ['required', 'integer', 'between:1,10'],
            'selected_start_date' => ['required', 'date', 'after_or_equal:today'],
            'capsule_timing' => ['nullable', 'string', 'max:80'],
            'custom_capsule_time' => ['nullable', 'date_format:H:i'],
            'capsule_card_location' => ['nullable', 'string', 'max:100'],
            'custom_location' => ['nullable', 'string', 'max:255'],
            'reminder_style' => ['nullable', 'string', 'max:40'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
            'daily_reminder_enabled' => ['boolean'],
            'milestone_reminder_enabled' => ['boolean'],
            'whatsapp_opt_in' => ['boolean'],
            'safety_acknowledged' => ['accepted'],
            'medical_disclaimer_acknowledged' => ['accepted'],
        ]);

        $user = $request->user();
        $profile = $this->journey->activeProfile($user);
        if (!$profile) {
            $latest = $this->journey->latestProfile($user);
            if ($latest && $latest->status === 'completed') {
                return redirect()->route('reset.reentry');
            }
            $profile = $this->journey->ensureProfile($user);
        }

        $this->journey->completeDay0($profile, $data);

        return redirect()->route('reset.home')->with('success', 'Day 0 is complete. Your 30-day course is ready.');
    }

    public function day(Request $request, int $day)
    {
        abort_unless($day >= 1 && $day <= 30, 404);
        $profile = $this->journey->activeProfile($request->user()) ?? $this->journey->latestProfile($request->user());
        abort_unless($profile, 404, 'Reset profile not found.');
        if ($profile->status === 'completed') {
            return redirect()->route('reset.report', ['cycle' => $profile->cycle_number]);
        }

        $profile->load(['day0Baseline.priorityAreas', 'startPlan']);
        $currentDay = $profile->status === 'completed' ? 30 : (int) $profile->current_reset_day;
        $state = $this->journey->dailyPageData($profile, $day);
        $isCurrent = $profile->status !== 'completed' && (int) $profile->current_reset_day === $day;
        $milestone = in_array($day, ResetJourneyService::MILESTONES, true);

        $scheduledDate = $this->journey->scheduledDate($profile, $day);

        return view('reset.day', array_merge(compact('profile', 'day', 'currentDay', 'isCurrent', 'milestone', 'scheduledDate'), $state));
    }

    public function storeDay(Request $request, int $day)
    {
        abort_unless($day >= 1 && $day <= 30, 404);
        $rules = [
            'capsule_taken' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        if (in_array($day, ResetJourneyService::MILESTONES, true)) {
            $rules += [
                'bloating_score' => ['required', 'integer', 'between:0,10'],
                'gas_burping_score' => ['required', 'integer', 'between:0,10'],
                'heaviness_score' => ['required', 'integer', 'between:0,10'],
                'acidity_score' => ['required', 'integer', 'between:0,10'],
                'overall_comfort_score' => ['required', 'integer', 'between:1,10'],
            ];
        }

        if (in_array($day, [3, 7, 14, 21], true)) {
            $rules += [
                'capsule_consistency' => ['required', 'string', 'max:50'],
                'day_context' => ['nullable', 'string', 'max:500'],
                'what_changed' => ['nullable', 'string', 'max:1000'],
                'needs_support' => ['boolean'],
            ];
        }

        if ($day === 30) {
            $rules += [
                'bloating_score' => ['required', 'integer', 'between:0,10'],
                'gas_burping_score' => ['required', 'integer', 'between:0,10'],
                'heaviness_score' => ['required', 'integer', 'between:0,10'],
                'acidity_score' => ['required', 'integer', 'between:0,10'],
                'overall_comfort_score' => ['required', 'integer', 'between:1,10'],
                'capsule_consistency' => ['required', 'string', 'max:50'],
                'final_tracking_usage' => ['required', 'string', 'max:50'],
                'product_comfort' => ['required', 'string', 'max:50'],
                'areas_improved_count' => ['required', 'integer', 'between:0,3'],
                'areas_unresolved_count' => ['required', 'integer', 'between:0,3'],
                'trigger_pattern' => ['nullable', 'string', 'max:100'],
                'user_verdict' => ['required', 'string', 'max:100'],
                'continue_repeat_intent' => ['required', 'string', 'max:50'],
                'recommendation_intent' => ['required', 'string', 'max:30'],
                'usefulness_rating' => ['nullable', 'string', 'max:30'],
                'most_valuable_element_1' => ['nullable', 'string', 'max:80'],
                'most_valuable_element_2' => ['nullable', 'string', 'max:80'],
                'least_useful_element' => ['nullable', 'string', 'max:80'],
                'final_feedback_text' => ['nullable', 'string', 'max:3000'],
            ];
        }

        $data = $request->validate($rules);
        $profile = $this->journey->activeProfile($request->user());
        abort_unless($profile, 404, 'Reset profile not found.');
        $this->journey->completeDaily($profile, $day, $data);

        return $day === 30
            ? redirect()->route('reset.report')->with('success', 'Day 30 is complete. Your Gut Response Brief is ready.')
            : redirect()->route('reset.day', ['day' => $day + 1])->with('success', 'Day '.$day.' is complete.');
    }

    public function unusual(Request $request, int $day)
    {
        $data = $request->validate([
            'symptom_type' => ['required', 'string', 'max:100'],
            'severity' => ['required', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:2000'],
            'product_related_yes_no' => ['nullable', 'boolean'],
            'action_taken' => ['nullable', 'string', 'max:50'],
        ]);

        $profile = $this->journey->activeProfile($request->user());
        abort_unless($profile, 404);
        abort_unless($day >= 1 && $day <= 30, 404);

        DB::transaction(function () use ($profile, $day, $data) {
            $event = UnusualEvent::create([
                'reset_profile_id' => $profile->id,
                'reset_day' => $day,
                'symptom_type' => $data['symptom_type'],
                'severity' => $data['severity'],
                'description' => $data['description'] ?? null,
                'product_related_yes_no' => (bool) ($data['product_related_yes_no'] ?? false),
                'action_taken' => $data['action_taken'] ?? null,
                'recorded_via' => 'web',
            ]);

            if (in_array(strtolower($data['severity']), ['high', 'severe', 'critical'], true)) {
                \App\Models\SafetyFlag::create([
                    'reset_profile_id' => $profile->id,
                    'unusual_event_id' => $event->id,
                    'flag_type' => 'USER_REPORTED_UNUSUAL_EVENT',
                    'trigger_source' => 'web_form',
                    'detected_keyword' => null,
                    'severity' => $data['severity'],
                    'automation_paused' => true,
                    'manual_review_required' => true,
                ]);

                $profile->update([
                    'safety_flag_active' => true,
                    'manual_review_required' => true,
                    'status' => 'paused',
                ]);
            }
        });

        return back()->with('success', 'Your unusual event has been recorded for review.');
    }

    public function positive(Request $request, int $day)
    {
        $data = $request->validate([
            'event_type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
        $profile = $this->journey->activeProfile($request->user());
        abort_unless($profile, 404);
        abort_unless($day >= 1 && $day <= 30, 404);

        PositiveEvent::create([
            'reset_profile_id' => $profile->id,
            'reset_day' => $day,
            'event_type' => $data['event_type'],
            'context_type' => 'user_reported',
            'notes' => $data['description'] ?? null,
            'recorded_via' => 'web',
            'event_date' => today()->toDateString(),
        ]);

        return back()->with('success', 'Positive change recorded.');
    }

    public function report(Request $request, ?int $cycle = null)
    {
        $user = $request->user();
        $profile = $cycle
            ? $user->resetProfiles()->where('cycle_number', $cycle)->firstOrFail()
            : ($this->journey->latestProfile($user));

        abort_unless($profile, 404, 'No reset cycle is available yet.');
        $profile->load([
            'day0Baseline.priorityAreas', 'day0Baseline.triggers', 'startPlan',
            'dailyAdherence', 'resetCardUsage', 'gutSignalCheckpoints',
            'milestoneCheckins.answers', 'finalReview', 'griScores', 'grsScores',
            'finalClassification', 'day30Decision', 'unusualEvents', 'safetyFlags',
            'testimonial',
        ]);

        $gri = $profile->griScores->sortByDesc('id')->first();
        $grs = $profile->grsScores->sortByDesc('id')->first();
        $completedDays = $profile->dailyAdherence->where('response_status', 'completed')->pluck('reset_day')->all();
        $history = $user->resetProfiles()->latest('cycle_number')->get();
        $currentDay = $profile->status === 'completed' ? 30 : (int) $profile->current_reset_day;

        return view('reset.report', compact('profile', 'gri', 'grs', 'completedDays', 'history', 'currentDay'));
    }

    public function testimonial(Request $request)
    {
        $profile = $this->journey->latestProfile($request->user());
        abort_unless($profile && $profile->status === 'completed', 404);
        $currentDay = 30;

        return view('reset.testimonial', compact('profile', 'currentDay'));
    }

    public function storeTestimonial(Request $request)
    {
        $data = $request->validate([
            'consent' => ['required', 'in:yes,no'],
            'display_name' => ['nullable', 'string', 'max:150'],
            'testimonial_text' => ['nullable', 'string', 'max:3000'],
            'usage_permission' => ['nullable', 'string', 'max:30'],
        ]);

        $profile = $this->journey->latestProfile($request->user());
        abort_unless($profile && $profile->status === 'completed', 404);

        Testimonial::updateOrCreate(
            ['reset_profile_id' => $profile->id],
            $data + ['moderation_status' => 'pending']
        );

        return redirect()->route('reset.report')->with('success', 'Thank you. Your testimonial is saved for review.');
    }

    public function reentry(Request $request)
    {
        $user = $request->user();
        $latest = $this->journey->latestProfile($user);
        $profile = $latest;
        $pending = \App\Models\ResetReentryRequest::where('user_id', $user->id)->where('status', 'pending')->latest('id')->first();

        $currentDay = $latest?->status === 'completed' ? 30 : (int) ($latest?->current_reset_day ?? 0);

        return view('reset.reentry', compact('latest', 'profile', 'pending', 'currentDay'));
    }

    public function storeReentry(Request $request)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);
        $this->journey->requestReentry($request->user(), $data['reason']);

        return back()->with('success', 'Your request has been submitted for admin approval.');
    }

    public function activateFromQr(Request $request, QrCode $qrCode)
    {
        if (!$request->user()) {
            $request->session()->put('pending_reset_qr', $qrCode->code);
            return redirect()->route('login')->with('success', 'Please sign in to activate your 30-day Myclosq.');
        }

        abort_unless($qrCode->is_active, 404);
        $user = $request->user();
        $profile = $this->journey->activeProfile($user);
        if (!$profile) {
            $latest = $this->journey->latestProfile($user);
            if ($latest && $latest->status === 'completed') {
                return redirect()->route('reset.reentry')->with('success', 'Your previous cycle is complete. Please request re-entry for another cycle.');
            }
            $profile = $this->journey->ensureProfile($user, $qrCode->source_channel ?? 'qr');
        }

        DB::table('qr_scan_events')->insert([
            'qr_code_id' => $qrCode->id,
            'reset_profile_id' => $profile->id,
            'user_id' => $request->user()->id,
            'qr_source' => $qrCode->source_channel,
            'qr_location' => $qrCode->qr_location,
            'scan_timestamp' => now(),
            'source_channel' => $qrCode->source_channel,
            'milestone_context' => (string) ($qrCode->milestone_day ?? 'activation'),
            'action_taken' => 'activate_or_resume_reset',
            'new_user_or_existing' => $profile->cycle_number > 1 ? 'existing' : 'new',
            'device_type' => str_contains(strtolower((string) $request->userAgent()), 'mobile') ? 'mobile' : 'desktop',
            'ip_hash' => hash('sha256', $request->ip().'|'.config('app.key')),
            'created_at' => now(),
        ]);

        return redirect()->route('reset.day0');
    }
}
