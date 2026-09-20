<?php

namespace App\Services;

use App\Models\DailyAdherence;
use App\Models\ResetProfile;

class GutResetService
{
    public function recordCheckin(ResetProfile $profile, int $day, array $answers = [], ?string $notes = null): DailyAdherence
    {
        if ($day < 1 || $day > 30) {
            abort(422, 'Day must be between 1 and 30.');
        }

        $data = [
            'capsule_taken' => (bool) ($answers['capsule_taken'] ?? $answers['completed'] ?? true),
            'notes' => $notes,
        ];

        foreach (['bloating_score', 'gas_burping_score', 'heaviness_score', 'acidity_score', 'overall_comfort_score', 'capsule_consistency', 'day_context', 'what_changed', 'needs_support', 'final_tracking_usage', 'product_comfort', 'areas_improved_count', 'areas_unresolved_count', 'trigger_pattern', 'user_verdict', 'continue_repeat_intent', 'recommendation_intent', 'usefulness_rating', 'most_valuable_element_1', 'most_valuable_element_2', 'least_useful_element', 'final_feedback_text'] as $key) {
            if (array_key_exists($key, $answers)) {
                $data[$key] = $answers[$key];
            }
        }

        app(ResetJourneyService::class)->completeDaily($profile, $day, $data);

        return DailyAdherence::where('reset_profile_id', $profile->id)
            ->where('reset_day', $day)
            ->firstOrFail();
    }

    public function processDailyWorkflows(): void
    {
        app(ResetJourneyService::class)->activatePlannedProfiles();
        app(ResetJourneyService::class)->detectDropoffs();
        app(GutResetWhatsAppService::class)->processDailyWorkflows();
        app(Day30WorkflowService::class)->process();
    }
}
