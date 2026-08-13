<?php

namespace App\Jobs;

use App\Models\WhatsappContact;
use App\Models\WhatsappMessage;
use App\Models\WhatsappWebhookLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable as FoundationQueueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class ProcessWhatsAppWebhook implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public function __construct(public string $rawBody, public ?string $signature) {}

    public function handle(): void
    {
        $secret=(string)config('services.whatsapp.app_secret');
        $verified=false;
        if ($secret && $this->signature) {
            $expected='sha256='.hash_hmac('sha256',$this->rawBody,$secret);
            $verified=hash_equals($expected,$this->signature);
        }

        if ($secret && !$verified) throw new \RuntimeException('Invalid WhatsApp webhook signature.');

        $payload=json_decode($this->rawBody,true,512,JSON_THROW_ON_ERROR);
        $entryId=data_get($payload,'entry.0.id') ?: hash('sha256',$this->rawBody);
        $eventType='whatsapp';

        $log=WhatsappWebhookLog::firstOrCreate(
            ['provider_event_id'=>$entryId],
            [
                'event_type'=>$eventType,
                'phone_number'=>data_get($payload,'entry.0.changes.0.value.messages.0.from'),
                'payload'=>$payload,
                'signature_verified'=>$verified || !$secret,
                'processing_status'=>'received',
            ]
        );

        if ($log->processing_status==='processed') return;

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value=$change['value'] ?? [];

                foreach ($value['messages'] ?? [] as $incoming) {
                    $phone=(string)($incoming['from'] ?? '');
                    if (!$phone) continue;

                    $contact=WhatsappContact::where('phone_number',$phone)->first();
                    if (!$contact) continue;

                    $message=WhatsappMessage::firstOrCreate(
                        ['provider_message_id'=>$incoming['id'] ?? null],
                        [
                            'whatsapp_contact_id'=>$contact->id,
                            'direction'=>'inbound',
                            'message_type'=>$incoming['type'] ?? 'unknown',
                            'message_body'=>$this->extractBody($incoming),
                            'status'=>'received',
                            'raw_payload'=>$incoming,
                        ]
                    );

                    app(\App\Services\GutResetWhatsAppService::class)->processInbound($contact->user,$message);
                }

                foreach ($value['statuses'] ?? [] as $status) {
                    $providerId=$status['id'] ?? null;
                    if (!$providerId) continue;

                    $message=WhatsappMessage::where('provider_message_id',$providerId)->first();
                    if (!$message) continue;

                    $message->update([
                        'status'=>$status['status'] ?? $message->status,
                        'delivered_at'=>($status['status'] ?? null)==='delivered' ? now() : $message->delivered_at,
                        'read_at'=>($status['status'] ?? null)==='read' ? now() : $message->read_at,
                        'failed_at'=>($status['status'] ?? null)==='failed' ? now() : $message->failed_at,
                        'error_code'=>(string)(data_get($status,'errors.0.code') ?? $message->error_code),
                        'raw_payload'=>$status,
                    ]);
                }
            }
        }

        $log->update(['processing_status'=>'processed','processed_at'=>now()]);
    }

    private function extractBody(array $message): ?string
    {
        return match($message['type'] ?? null) {
            'text'=>data_get($message,'text.body'),
            'button'=>data_get($message,'button.text'),
            'interactive'=>data_get($message,'interactive.button_reply.title') ?? data_get($message,'interactive.list_reply.title'),
            default=>null,
        };
    }
}
