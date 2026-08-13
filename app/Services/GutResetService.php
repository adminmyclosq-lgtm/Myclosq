<?php

namespace App\Services;

use App\Models\DailyAdherence;
use App\Models\ResetProfile;
use Illuminate\Support\Facades\DB;

class GutResetService
{
    public function recordCheckin(ResetProfile $profile, int $day, array $answers=[], ?string $notes=null): DailyAdherence
    {
        if ($day<0 || $day>30) abort(422,'Day must be between 0 and 30.');

        return DB::transaction(function() use($profile,$day) {
            return DailyAdherence::updateOrCreate(
                ['reset_profile_id'=>$profile->id,'reset_day'=>$day],
                [
                    'calendar_date'=>now()->toDateString(),
                    'response_status'=>'completed',
                    'responded_at'=>now(),
                ]
            );
        });
    }

    public function processDailyWorkflows(): void
    {
        app(GutResetWhatsAppService::class)->processDailyWorkflows();
        app(Day30WorkflowService::class)->process();
    }
}
