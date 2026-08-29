<?php

namespace App\Services;

use App\Models\DailyAdherence;
use App\Models\ResetProfile;
use App\Models\User;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\DB;

class GutResetWhatsAppService
{
    public function processInbound(User $user, WhatsappMessage $message): void
    {
        $text=trim((string)$message->message_body);
        if ($text==='') return;

        $profile=$user->resetProfile;
        if (!$profile || !in_array($profile->status,['created','active','started','in_progress'],true)) return;

        $day=(int)$profile->current_reset_day;
        if ($day<0 || $day>30) return;

        $response=strtolower($text);
        $status=in_array($response,['yes','y','done','completed','1','ok','okay'],true) ? 'completed' : 'responded';

        DailyAdherence::updateOrCreate(
            ['reset_profile_id'=>$profile->id,'reset_day'=>$day],
            [
                'calendar_date'=>now()->toDateString(),
                'response_status'=>$status,
                'responded_at'=>now(),
                'updated_at'=>now(),
            ]
        );

        if ($status==='completed') {
            $profile->update(['current_reset_day'=>min(30,$day+1)]);
            app(\App\Jobs\SendWhatsAppTemplate::class)->dispatch($user,'daily_followup','en',[$day]);
        }
    }

    public function processDailyWorkflows(): void
    {
        ResetProfile::with('user.customerProfile')
            ->whereIn('status',['active','started','in_progress'])
            ->whereBetween('current_reset_day',[0,30])
            ->chunkById(100,function($profiles) {
                foreach($profiles as $profile) {
                    $user=$profile->user;
                    if (!($user->customerProfile?->whatsapp_opt_in ?? false)) continue;

                    $day=(int)$profile->current_reset_day;
                    $adherence=DailyAdherence::firstOrCreate(
                        ['reset_profile_id'=>$profile->id,'reset_day'=>$day],
                        ['calendar_date'=>now()->toDateString()]
                    );

                    if (!$adherence->reminder_sent) {
                        SendWhatsAppTemplate::dispatch($user,'daily_checkin','en',[$day]);
                        $adherence->update(['reminder_sent'=>true,'reminder_sent_at'=>now()]);
                    }
                }
            });
    }
}
