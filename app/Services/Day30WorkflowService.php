<?php

namespace App\Services;

use App\Jobs\SendWhatsAppTemplate;
use App\Models\Day30Decision;
use App\Models\ResetProfile;
use Illuminate\Support\Facades\DB;

class Day30WorkflowService
{
    public function process(): void
    {
        ResetProfile::with('user.customerProfile')
            ->where('current_reset_day',30)
            ->whereNull('day30_completed_at')
            ->chunkById(100,function($profiles) {
                foreach($profiles as $profile) {
                    if (!$profile->user) continue;

                    // Do not invent a health classification. The decision is
                    // only created when an existing final classification is
                    // available from the approved business workflow.
                    $classificationId=\App\Models\FinalClassification::where('reset_profile_id',$profile->id)->value('id');
                    if (!$classificationId) continue;

                    $decision=Day30Decision::firstOrCreate(
                        ['reset_profile_id'=>$profile->id],
                        [
                            'classification_id'=>$classificationId,
                            'user_recommendation'=>'Review your Day-30 Gut Response Brief and follow the recommended next step.',
                            'commercial_action'=>'',
                            'next_step_code'=>'REVIEW_BRIEF',
                            'continuation_allowed'=>false,
                            'upsell_allowed'=>false,
                            'testimonial_request_allowed'=>false,
                            'restart_allowed'=>false,
                            'doctor_guidance_required'=>$profile->safety_flag_active,
                        ]
                    );

                    $profile->update(['day30_completed_at'=>now(),'status'=>'completed']);

                    if ($profile->user->customerProfile?->whatsapp_opt_in) {
                        SendWhatsAppTemplate::dispatch($profile->user,'day30_complete','en',[$profile->user->customerProfile->display_name ?? 'Customer']);
                    }
                }
            });
    }
}
