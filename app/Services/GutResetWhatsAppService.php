<?php

namespace App\Services;

use App\Jobs\SendWhatsAppTemplate;
use App\Models\DailyAdherence;
use App\Models\DropoffEvent;
use App\Models\ResetCardUsage;
use App\Models\ResetProfile;
use App\Models\ReactivationEvent;
use App\Models\User;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\DB;

class GutResetWhatsAppService
{
    public function processInbound(User $user, WhatsappMessage $message): void
    {
        $text = trim((string) $message->message_body);
        if ($text === '') {
            return;
        }

        $profile = app(ResetJourneyService::class)->activeProfile($user);
        if (!$profile || $profile->safety_flag_active || !in_array($profile->status, ['created', 'active', 'started', 'in_progress', 'dropoff'], true)) {
            return;
        }

        $day = (int) $profile->current_reset_day;
        if ($day < 1 || $day > 30) {
            return;
        }

        $response = strtolower($text);
        $status = in_array($response, ['yes', 'y', 'done', 'completed', '1', 'ok', 'okay'], true) ? 'completed' : 'responded';
        $wasDropoff = $profile->status === 'dropoff';

        DB::transaction(function () use ($profile, $day, $status, $wasDropoff, $text) {
            DailyAdherence::updateOrCreate(
                ['reset_profile_id' => $profile->id, 'reset_day' => $day],
                [
                    'calendar_date' => now()->toDateString(),
                    'response_status' => $status,
                    'responded_at' => now(),
                ]
            );

            if ($status !== 'completed') {
                return;
            }

            ResetCardUsage::updateOrCreate(
                ['reset_profile_id' => $profile->id, 'reset_day' => $day],
                [
                    'usage_date' => now()->toDateString(),
                    'marked_yes_no' => true,
                    'tracking_method' => 'whatsapp',
                ]
            );

            if ($wasDropoff) {
                $dropoff = DropoffEvent::where('reset_profile_id', $profile->id)->where('detected_day', $day)->latest('id')->first();
                if ($dropoff) {
                    ReactivationEvent::create([
                        'reset_profile_id' => $profile->id,
                        'dropoff_event_id' => $dropoff->id,
                        'response' => $text,
                        'response_at' => now(),
                        'new_status' => 'active',
                        'restart_requested' => false,
                    ]);
                }
            }

            if ($day < 30) {
                $profile->update(['current_reset_day' => $day + 1, 'status' => 'active']);
            } else {
                $profile->update(['status' => 'active']);
            }
        });

        if ($status === 'completed') {
            SendWhatsAppTemplate::dispatch($user, 'daily_followup', 'en', [$day]);
        }
    }

    public function processDailyWorkflows(): void
    {
        $journey = app(ResetJourneyService::class);
        $journey->activatePlannedProfiles();

        ResetProfile::with(['user.customerProfile', 'startPlan'])
            ->whereIn('status', ['active', 'started', 'in_progress', 'dropoff'])
            ->whereBetween('current_reset_day', [1, 30])
            ->chunkById(100, function ($profiles) use ($journey) {
                foreach ($profiles as $profile) {
                    $user = $profile->user;
                    if (!($user?->customerProfile?->whatsapp_opt_in ?? false) || $profile->safety_flag_active) {
                        continue;
                    }

                    $day = (int) $profile->current_reset_day;
                    $scheduledDate = $journey->scheduledDate($profile, $day);
                    if ($scheduledDate && now()->toDateString() < $scheduledDate) {
                        continue;
                    }

                    $adherence = DailyAdherence::firstOrCreate(
                        ['reset_profile_id' => $profile->id, 'reset_day' => $day],
                        ['calendar_date' => now()->toDateString()]
                    );

                    $isDue = !$scheduledDate || now()->toDateString() >= $scheduledDate;
                    if ($profile->status === 'dropoff' && !$adherence->response_status) {
                        $dropoff = DropoffEvent::where('reset_profile_id', $profile->id)
                            ->where('detected_day', $day)
                            ->latest('id')->first();
                        if ($dropoff && !$dropoff->reactivation_prompt_sent) {
                            SendWhatsAppTemplate::dispatch($user, 'reactivation_prompt', 'en', [$day]);
                            $dropoff->update(['reactivation_prompt_sent' => true]);
                        }
                    } elseif ($isDue && !$adherence->reminder_sent) {
                        if ($day === 30) {
                            SendWhatsAppTemplate::dispatch($user, 'day30_review', 'en', [url('/reset/day/30')]);
                        } else {
                            SendWhatsAppTemplate::dispatch($user, 'daily_checkin', 'en', [$day]);
                        }
                        $adherence->update(['reminder_sent' => true, 'reminder_sent_at' => now()]);
                    }
                }
            });
    }
}
