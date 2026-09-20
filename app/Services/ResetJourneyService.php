<?php

namespace App\Services;

use App\Models\CommercialIntent;
use App\Models\DailyAdherence;
use App\Models\Day0Baseline;
use App\Models\Day30Decision;
use App\Models\DropoffEvent;
use App\Models\Feedback;
use App\Models\FinalClassification;
use App\Models\FinalReview;
use App\Models\GrScore;
use App\Models\GriScore;
use App\Models\GutSignalCheckpoint;
use App\Models\MilestoneAnswer;
use App\Models\MilestoneCheckin;
use App\Models\ResetCardUsage;
use App\Models\ResetProfile;
use App\Models\ResetReentryRequest;
use App\Models\SafetyFlag;
use App\Models\StartPlan;
use App\Models\UnusualEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResetJourneyService
{
    public const MILESTONES = [3, 7, 14, 21, 30];

    public function latestProfile(User $user): ?ResetProfile
    {
        return $user->resetProfiles()->latest('id')->first();
    }

    public function activeProfile(User $user): ?ResetProfile
    {
        return $user->resetProfiles()
            ->whereIn('status', ['created', 'active', 'started', 'in_progress', 'paused', 'dropoff'])
            ->latest('id')
            ->first();
    }

    public function ensureProfile(User $user, ?string $sourceChannel = null): ResetProfile
    {
        return $this->activeProfile($user) ?? DB::transaction(function () use ($user, $sourceChannel) {
            $latest = $this->latestProfile($user);
            if ($latest && $latest->status === 'completed') {
                throw ValidationException::withMessages([
                    'cycle' => 'Your previous reset is complete. Please request re-entry before starting another cycle.',
                ]);
            }

            $nextCycle = ((int) $user->resetProfiles()->max('cycle_number')) + 1;

            return $user->resetProfiles()->create([
                'cycle_number' => $nextCycle,
                'parent_reset_profile_id' => $latest?->id,
                'source_channel' => $sourceChannel ?? 'manual_activation',
                'current_reset_day' => 0,
                'status' => 'created',
            ]);
        });
    }

    public function createCycleFromDelivery(User $user, ?int $productBatchId = null): ?ResetProfile
    {
        if ($this->activeProfile($user)) {
            return $this->activeProfile($user);
        }

        return DB::transaction(function () use ($user, $productBatchId) {
            $latest = $this->latestProfile($user);

            // A later delivered pack must go through the same re-entry approval
            // workflow rather than silently creating a new cycle.
            if ($latest && $latest->status === 'completed') {
                $pending = ResetReentryRequest::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->exists();
                if (!$pending) {
                    ResetReentryRequest::create([
                        'user_id' => $user->id,
                        'previous_reset_profile_id' => $latest->id,
                        'status' => 'pending',
                        'request_reason' => 'New product delivery received; awaiting re-entry approval.',
                        'requested_at' => now(),
                    ]);
                }
                return null;
            }

            $nextCycle = ((int) $user->resetProfiles()->max('cycle_number')) + 1;

            return $user->resetProfiles()->create([
                'cycle_number' => $nextCycle,
                'parent_reset_profile_id' => $latest?->id,
                'source_channel' => 'order_delivered',
                'product_batch_id' => $productBatchId,
                'current_reset_day' => 0,
                'status' => 'created',
            ]);
        });
    }

    public function activatePlannedProfiles(): int
    {
        $count = 0;

        ResetProfile::with('startPlan')
            ->where('status', 'created')
            ->whereHas('startPlan', fn ($query) => $query->whereDate('selected_start_date', '<=', today()))
            ->chunkById(100, function ($profiles) use (&$count) {
                foreach ($profiles as $profile) {
                    $profile->update([
                        'status' => 'active',
                        'current_reset_day' => 1,
                        'actual_start_date' => $profile->startPlan?->actual_start_date ?? today(),
                    ]);
                    $profile->startPlan?->update(['actual_start_date' => $profile->actual_start_date]);
                    $count++;
                }
            });

        return $count;
    }

    public function completeDay0(ResetProfile $profile, array $data): void
    {
        if ($profile->status === 'completed') {
            throw ValidationException::withMessages(['cycle' => 'This reset cycle is already complete.']);
        }
        if ($profile->safety_flag_active) {
            throw ValidationException::withMessages(['cycle' => 'This reset is paused for safety review.']);
        }

        DB::transaction(function () use ($profile, $data) {
            $baseline = Day0Baseline::updateOrCreate(
                ['reset_profile_id' => $profile->id],
                [
                    'success_expectation_primary' => $data['success_expectation_primary'] ?? null,
                    'success_expectation_open_text' => $data['success_expectation_open_text'] ?? null,
                    'regularity_frequency' => $data['regularity_frequency'] ?? null,
                    'rescue_remedy_used' => $data['rescue_remedy_used'] ?? null,
                    'rescue_remedy_detail' => $data['rescue_remedy_detail'] ?? null,
                    'recent_disruption_yes_no' => (bool) ($data['recent_disruption_yes_no'] ?? false),
                    'recent_disruption_detail' => $data['recent_disruption_detail'] ?? null,
                    'safety_acknowledged' => true,
                    'medical_disclaimer_acknowledged' => true,
                    'whatsapp_opt_in' => (bool) ($data['whatsapp_opt_in'] ?? false),
                    'completed_at' => now(),
                ]
            );

            $baseline->priorityAreas()->delete();
            foreach (array_values($data['priority_areas'] ?? []) as $index => $area) {
                $baseline->priorityAreas()->create([
                    'priority_area' => $area,
                    'priority_rank' => $index + 1,
                ]);
            }

            $baseline->triggers()->delete();
            foreach (array_values($data['triggers'] ?? []) as $index => $trigger) {
                $baseline->triggers()->create([
                    'trigger_name' => $trigger,
                    'trigger_rank' => $index + 1,
                ]);
            }

            GutSignalCheckpoint::updateOrCreate(
                ['reset_profile_id' => $profile->id, 'checkpoint_day' => 0],
                [
                    'bloating_score' => $data['bloating_score'],
                    'gas_burping_score' => $data['gas_burping_score'],
                    'heaviness_score' => $data['heaviness_score'],
                    'acidity_score' => $data['acidity_score'],
                    'overall_comfort_score' => $data['overall_comfort_score'],
                    'recorded_via' => 'web',
                    'recorded_at' => now(),
                ]
            );

            StartPlan::updateOrCreate(
                ['reset_profile_id' => $profile->id],
                [
                    'selected_start_date' => $data['selected_start_date'],
                    'actual_start_date' => $data['selected_start_date'] === today()->toDateString() ? today() : null,
                    'capsule_timing' => $data['capsule_timing'] ?? null,
                    'custom_capsule_time' => $data['custom_capsule_time'] ?? null,
                    'capsule_card_location' => $data['capsule_card_location'] ?? null,
                    'custom_location' => $data['custom_location'] ?? null,
                    'reminder_style' => $data['reminder_style'] ?? 'milestone_only',
                    'reminder_time' => $data['reminder_time'] ?? null,
                    'daily_reminder_enabled' => (bool) ($data['daily_reminder_enabled'] ?? false),
                    'milestone_reminder_enabled' => (bool) ($data['milestone_reminder_enabled'] ?? true),
                    'missed_day_rule_confirmed' => true,
                    'unusual_day_rule_confirmed' => true,
                    'positive_shift_rule_confirmed' => true,
                    'completed_at' => now(),
                ]
            );

            $whatsappOptIn = (bool) ($data['whatsapp_opt_in'] ?? false);
            if ($profile->user) {
                $profile->user->customerProfile()->update([
                    'whatsapp_opt_in' => $whatsappOptIn,
                    'whatsapp_opt_in_at' => $whatsappOptIn ? now() : null,
                ]);
            }

            $startsToday = $data['selected_start_date'] === today()->toDateString();
            $profile->update([
                'current_reset_day' => $startsToday ? 1 : 0,
                'status' => $startsToday ? 'active' : 'created',
                'actual_start_date' => $startsToday ? today() : null,
                'day0_completed_at' => now(),
                'manual_review_required' => false,
            ]);
        });
    }

    public function scheduledDate(ResetProfile $profile, int $day): ?string
    {
        $start = $profile->actual_start_date?->copy() ?? $profile->startPlan?->selected_start_date?->copy();
        if (!$start) {
            return null;
        }
        return $start->copy()->addDays(max(0, $day - 1))->toDateString();
    }

    public function dailyPageData(ResetProfile $profile, int $day): array
    {
        $adherence = $profile->dailyAdherence()->where('reset_day', $day)->first();
        $card = $profile->resetCardUsage()->where('reset_day', $day)->first();
        $checkpoint = $profile->gutSignalCheckpoints()->where('checkpoint_day', $day)->first();
        $milestone = $profile->milestoneCheckins()->where('milestone_day', $day)->with('answers')->first();

        return compact('adherence', 'card', 'checkpoint', 'milestone');
    }

    public function completeDaily(ResetProfile $profile, int $day, array $data): void
    {
        if ($profile->safety_flag_active) {
            throw ValidationException::withMessages([
                'day' => 'This reset is paused for review. Please follow the programme team follow-up before continuing.',
            ]);
        }

        if ($profile->status === 'created') {
            throw ValidationException::withMessages(['day' => 'Your selected start date has not arrived yet.']);
        }

        if ((int) $profile->current_reset_day !== $day) {
            throw ValidationException::withMessages([
                'day' => 'Please complete your current day before opening another day.',
            ]);
        }

        DB::transaction(function () use ($profile, $day, $data) {
            DailyAdherence::updateOrCreate(
                ['reset_profile_id' => $profile->id, 'reset_day' => $day],
                [
                    'calendar_date' => now()->toDateString(),
                    'response_status' => 'completed',
                    'responded_at' => now(),
                    'followup_sent' => false,
                ]
            );

            ResetCardUsage::updateOrCreate(
                ['reset_profile_id' => $profile->id, 'reset_day' => $day],
                [
                    'usage_date' => now()->toDateString(),
                    'marked_yes_no' => (bool) ($data['capsule_taken'] ?? false),
                    'tracking_method' => 'web',
                    'notes' => $data['notes'] ?? null,
                ]
            );

            if (in_array($day, self::MILESTONES, true)) {
                GutSignalCheckpoint::updateOrCreate(
                    ['reset_profile_id' => $profile->id, 'checkpoint_day' => $day],
                    [
                        'bloating_score' => $data['bloating_score'],
                        'gas_burping_score' => $data['gas_burping_score'],
                        'heaviness_score' => $data['heaviness_score'],
                        'acidity_score' => $data['acidity_score'],
                        'overall_comfort_score' => $data['overall_comfort_score'],
                        'recorded_via' => 'web',
                        'recorded_at' => now(),
                    ]
                );
            }

            if ($day < 30) {
                $this->completeMilestoneIfNeeded($profile, $day, $data);
                $profile->update([
                    'current_reset_day' => $day + 1,
                    'status' => 'active',
                ]);
                return;
            }

            $this->completeFinalReview($profile, $data);
        });
    }

    private function completeMilestoneIfNeeded(ResetProfile $profile, int $day, array $data): void
    {
        if (!in_array($day, [3, 7, 14, 21], true)) {
            return;
        }

        $checkin = MilestoneCheckin::updateOrCreate(
            ['reset_profile_id' => $profile->id, 'milestone_day' => $day],
            [
                'status' => 'completed',
                'started_at' => $data['milestone_started_at'] ?? now(),
                'completed_at' => now(),
                'completion_channel' => 'web',
                'summary_generated' => true,
            ]
        );

        $answers = [
            'capsule_consistency' => $data['capsule_consistency'] ?? null,
            'day_context' => $data['day_context'] ?? null,
            'what_changed' => $data['what_changed'] ?? null,
            'needs_support' => $data['needs_support'] ?? false,
        ];

        foreach ($answers as $code => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            MilestoneAnswer::updateOrCreate(
                ['milestone_checkin_id' => $checkin->id, 'question_code' => $code],
                [
                    'answer_type' => is_bool($value) ? 'boolean' : (is_numeric($value) ? 'number' : 'text'),
                    'answer_text' => is_scalar($value) && !is_bool($value) ? (string) $value : null,
                    'answer_number' => is_numeric($value) ? (float) $value : null,
                    'answer_boolean' => is_bool($value) ? $value : null,
                ]
            );
        }
    }

    private function completeFinalReview(ResetProfile $profile, array $data): void
    {
        $checkpoint = $profile->gutSignalCheckpoints()->where('checkpoint_day', 30)->first();
        if (!$checkpoint) {
            throw ValidationException::withMessages(['overall_comfort_score' => 'Please complete your Day 30 response scores.']);
        }

        FinalReview::updateOrCreate(
            ['reset_profile_id' => $profile->id],
            [
                'capsule_consistency' => $data['capsule_consistency'] ?? null,
                'final_tracking_usage' => $data['final_tracking_usage'] ?? null,
                'product_comfort' => $data['product_comfort'] ?? null,
                'areas_improved_count' => (int) ($data['areas_improved_count'] ?? 0),
                'areas_unresolved_count' => (int) ($data['areas_unresolved_count'] ?? 0),
                'trigger_pattern' => $data['trigger_pattern'] ?? null,
                'user_verdict' => $data['user_verdict'] ?? null,
                'continue_repeat_intent' => $data['continue_repeat_intent'] ?? null,
                'recommendation_intent' => $data['recommendation_intent'] ?? null,
                'completed_at' => now(),
            ]
        );

        Feedback::updateOrCreate(
            ['reset_profile_id' => $profile->id],
            [
                'usefulness_rating' => $data['usefulness_rating'] ?? null,
                'most_valuable_element_1' => $data['most_valuable_element_1'] ?? null,
                'most_valuable_element_2' => $data['most_valuable_element_2'] ?? null,
                'least_useful_element' => $data['least_useful_element'] ?? null,
                'final_feedback_text' => $data['final_feedback_text'] ?? null,
                'submitted_at' => now(),
            ]
        );

        CommercialIntent::updateOrCreate(
            ['reset_profile_id' => $profile->id],
            [
                'paid_repeat_intent' => $data['continue_repeat_intent'] ?? null,
                'recommendation_intent' => $data['recommendation_intent'] ?? null,
                'phase2_interest' => false,
                'continue_repeat_intent' => $data['continue_repeat_intent'] ?? null,
                'referral_intent' => null,
            ]
        );

        $scores = $this->calculateScores($profile, $checkpoint);
        $classification = FinalClassification::updateOrCreate(
            ['reset_profile_id' => $profile->id],
            [
                'classification_code' => $profile->safety_flag_active ? 'SAFETY_REVIEW' : 'GUT_RESPONSE_REVIEW',
                'gri_score_id' => $scores['gri']->id,
                'grs_score_id' => $scores['grs']->id,
                'adherence_assessment' => $this->adherenceAssessment($scores['capsules_taken']),
                'product_comfort_assessment' => $data['product_comfort'] ?? null,
                'priority_area_movement' => $scores['grs']->movement_classification,
                'user_verdict' => $data['user_verdict'] ?? null,
                'trigger_pattern' => $data['trigger_pattern'] ?? null,
                'safety_override' => $profile->safety_flag_active,
                'rule_version' => 'v1',
            ]
        );

        Day30Decision::updateOrCreate(
            ['reset_profile_id' => $profile->id],
            [
                'classification_id' => $classification->id,
                'user_recommendation' => $profile->safety_flag_active
                    ? 'Your response brief is complete. Review the safety follow-up noted by the programme team before considering another cycle.'
                    : 'Your 30-day response brief is complete. Review the observed changes and choose your next step based on your own experience.',
                'commercial_action' => 'No automated purchase decision is made from the score. Any new cycle is started through the re-entry workflow.',
                'next_step_code' => 'REVIEW_BRIEF',
                'continuation_allowed' => !$profile->safety_flag_active,
                'upsell_allowed' => false,
                'testimonial_request_allowed' => !$profile->safety_flag_active,
                'restart_allowed' => !$profile->safety_flag_active,
                'doctor_guidance_required' => $profile->safety_flag_active,
                'generated_at' => now(),
            ]
        );

        $profile->update([
            'current_reset_day' => 30,
            'status' => 'completed',
            'day30_completed_at' => now(),
        ]);
    }

    public function calculateScores(ResetProfile $profile, GutSignalCheckpoint $day30Checkpoint): array
    {
        $capsulesTaken = (int) ResetCardUsage::where('reset_profile_id', $profile->id)->where('marked_yes_no', true)->count();
        $day30Comfort = (int) $day30Checkpoint->overall_comfort_score;
        $gri = min(100, ($capsulesTaken * 2) + ($day30Comfort * 4));
        $band = match (true) {
            $gri >= 80 => '80-100',
            $gri >= 60 => '60-79',
            $gri >= 40 => '40-59',
            default => '0-39',
        };

        $day0 = $profile->gutSignalCheckpoints()->where('checkpoint_day', 0)->first();
        if (!$day0) {
            throw ValidationException::withMessages(['overall_comfort_score' => 'Day 0 scores are required before final scoring.']);
        }

        $day0Burden = (float) $day0->bloating_score + (float) $day0->gas_burping_score + (float) $day0->heaviness_score + (float) $day0->acidity_score;
        $day30Burden = (float) $day30Checkpoint->bloating_score + (float) $day30Checkpoint->gas_burping_score + (float) $day30Checkpoint->heaviness_score + (float) $day30Checkpoint->acidity_score;
        $movement = $day0Burden - $day30Burden;
        $comfortDelta = (float) $day30Comfort - (float) $day0->overall_comfort_score;

        $griScore = GriScore::updateOrCreate(
            ['reset_profile_id' => $profile->id],
            [
                'capsules_taken' => $capsulesTaken,
                'day30_comfort_rating' => $day30Comfort,
                'gri_score' => $gri,
                'gri_band' => $band,
                'formula_version' => 'v1',
                'calculated_at' => now(),
            ]
        );

        $grsScore = GrScore::updateOrCreate(
            ['reset_profile_id' => $profile->id],
            [
                'day0_symptom_burden' => $day0Burden,
                'day30_symptom_burden' => $day30Burden,
                'grs_movement' => $movement,
                'day0_comfort' => $day0->overall_comfort_score,
                'day30_comfort' => $day30Comfort,
                'comfort_delta' => $comfortDelta,
                'movement_classification' => $movement > 0 ? 'improved' : ($movement < 0 ? 'worsened' : 'unchanged'),
                'formula_version' => 'v1',
                'calculated_at' => now(),
            ]
        );

        return [
            'gri' => $griScore,
            'grs' => $grsScore,
            'capsules_taken' => $capsulesTaken,
            'gri_band' => $band,
        ];
    }

    private function adherenceAssessment(int $capsulesTaken): string
    {
        return match (true) {
            $capsulesTaken >= 27 => 'high',
            $capsulesTaken >= 21 => 'regular',
            $capsulesTaken >= 15 => 'mixed',
            default => 'limited',
        };
    }

    public function detectDropoffs(): int
    {
        $count = 0;

        ResetProfile::whereIn('status', ['active', 'started', 'in_progress'])
            ->whereBetween('current_reset_day', [1, 30])
            ->chunkById(100, function ($profiles) use (&$count) {
                foreach ($profiles as $profile) {
                    $dueDate = $this->scheduledDate($profile, (int) $profile->current_reset_day);
                    if (!$dueDate || today()->diffInDays($dueDate, false) < -1) {
                        $already = DropoffEvent::where('reset_profile_id', $profile->id)
                            ->where('detected_day', $profile->current_reset_day)
                            ->where('created_at', '>=', now()->subDays(3))
                            ->exists();
                        if ($already) {
                            continue;
                        }

                        DropoffEvent::create([
                            'reset_profile_id' => $profile->id,
                            'detected_day' => $profile->current_reset_day,
                            'last_activity_at' => $profile->dailyAdherence()->latest('responded_at')->value('responded_at') ?? $profile->updated_at,
                            'dropoff_reason' => 'missed_checkin',
                            'detected_automatically' => true,
                        ]);
                        $profile->update(['status' => 'dropoff']);
                        $count++;
                    }
                }
            });

        return $count;
    }

    public function requestReentry(User $user, string $reason): ResetReentryRequest
    {
        $active = $this->activeProfile($user);
        if ($active) {
            throw ValidationException::withMessages(['reason' => 'Complete or resolve the current reset before requesting a new cycle.']);
        }

        $previous = $this->latestProfile($user);
        if (!$previous || $previous->status !== 'completed') {
            throw ValidationException::withMessages(['reason' => 'A completed reset cycle is required before requesting a new cycle.']);
        }

        $pending = ResetReentryRequest::where('user_id', $user->id)->where('status', 'pending')->exists();
        if ($pending) {
            throw ValidationException::withMessages(['reason' => 'A re-entry request is already pending review.']);
        }

        return ResetReentryRequest::create([
            'user_id' => $user->id,
            'previous_reset_profile_id' => $previous->id,
            'status' => 'pending',
            'request_reason' => $reason,
            'requested_at' => now(),
        ]);
    }

    public function approveReentry(ResetReentryRequest $request, User $reviewer, ?string $notes = null): ResetProfile
    {
        if ($request->status !== 'pending') {
            throw ValidationException::withMessages(['request' => 'Only pending requests can be approved.']);
        }

        return DB::transaction(function () use ($request, $reviewer, $notes) {
            $user = User::whereKey($request->user_id)->lockForUpdate()->firstOrFail();
            if ($this->activeProfile($user)) {
                throw ValidationException::withMessages(['request' => 'This user already has an active reset cycle.']);
            }

            $previous = $request->previousResetProfile;
            $nextCycle = ((int) $user->resetProfiles()->max('cycle_number')) + 1;

            $newProfile = $user->resetProfiles()->create([
                'cycle_number' => $nextCycle,
                'parent_reset_profile_id' => $previous->id,
                'source_channel' => 'reentry_approved',
                'product_batch_id' => $previous->product_batch_id,
                'current_reset_day' => 0,
                'status' => 'created',
            ]);

            $request->update([
                'status' => 'approved',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_notes' => $notes,
                'new_reset_profile_id' => $newProfile->id,
            ]);

            return $newProfile;
        });
    }

    public function rejectReentry(ResetReentryRequest $request, User $reviewer, ?string $notes = null): void
    {
        if ($request->status !== 'pending') {
            throw ValidationException::withMessages(['request' => 'Only pending requests can be rejected.']);
        }

        $request->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    public function resolveSafety(SafetyFlag $flag, User $reviewer, string $resolution): void
    {
        DB::transaction(function () use ($flag, $reviewer, $resolution) {
            $flag->update([
                'automation_paused' => false,
                'manual_review_required' => false,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'resolution' => $resolution,
            ]);

            $profile = $flag->resetProfile;
            $stillOpen = $profile->safetyFlags()->where('manual_review_required', true)->exists();
            if (!$stillOpen) {
                $profile->update([
                    'safety_flag_active' => false,
                    'manual_review_required' => false,
                    'status' => $profile->status === 'paused' ? 'active' : $profile->status,
                ]);
            }
        });
    }
}
