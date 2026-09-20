<?php

namespace App\Services;

use App\Models\Day30Decision;
use App\Models\FinalClassification;
use App\Models\ResetProfile;

class Day30WorkflowService
{
    public function process(): void
    {
        ResetProfile::with([
            'user.customerProfile',
            'gutSignalCheckpoints',
            'finalReview',
            'day30Decision',
        ])->where('current_reset_day', 30)
            ->whereNull('day30_completed_at')
            ->chunkById(100, function ($profiles) {
                foreach ($profiles as $profile) {
                    if (!$profile->user || !$profile->finalReview) {
                        continue;
                    }

                    $classification = FinalClassification::where('reset_profile_id', $profile->id)->first();
                    if (!$classification) {
                        continue;
                    }

                    Day30Decision::firstOrCreate(
                        ['reset_profile_id' => $profile->id],
                        [
                            'classification_id' => $classification->id,
                            'user_recommendation' => 'Your 30-day response brief is complete. Review the observed changes and choose your next step based on your own experience.',
                            'commercial_action' => 'No automated purchase decision is made from the score. Any new cycle is started through the re-entry workflow.',
                            'next_step_code' => 'REVIEW_BRIEF',
                            'continuation_allowed' => !$profile->safety_flag_active,
                            'upsell_allowed' => false,
                            'testimonial_request_allowed' => !$profile->safety_flag_active,
                            'restart_allowed' => !$profile->safety_flag_active,
                            'doctor_guidance_required' => $profile->safety_flag_active,
                        ]
                    );

                    $profile->update([
                        'current_reset_day' => 30,
                        'day30_completed_at' => now(),
                        'status' => 'completed',
                    ]);
                }
            });
    }
}
