<?php

namespace App\Services;

use App\Models\User;
use App\Models\WhatsappContact;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function sendTemplate(User $user, string $templateName, string $language = 'en', array $parameters = []): ?WhatsappMessage
    {
        $profile=$user->customerProfile;
        $contact=WhatsappContact::firstOrCreate(
            ['user_id'=>$user->id],
            [
                'phone_number'=>$user->mobile ?? '',
                'status'=>'active',
                'opt_in'=>(bool)($profile?->whatsapp_opt_in ?? false),
                'opt_in_at'=>$profile?->whatsapp_opt_in_at,
            ]
        );

        if (!$contact->phone_number || !$contact->opt_in) return null;

        $url=sprintf(
            '%s/%s/%s/messages',
            rtrim(config('services.whatsapp.graph_url'),'/'),
            config('services.whatsapp.api_version'),
            config('services.whatsapp.phone_number_id')
        );

        $components=[];
        if ($parameters) {
            $components[]=[
                'type'=>'body',
                'parameters'=>array_map(fn($value)=>['type'=>'text','text'=>(string)$value],array_values($parameters)),
            ];
        }

        $payload=[
            'messaging_product'=>'whatsapp',
            'to'=>$contact->phone_number,
            'type'=>'template',
            'template'=>[
                'name'=>$templateName,
                'language'=>['code'=>$language],
                'components'=>$components,
            ],
        ];

        $message=WhatsappMessage::create([
            'whatsapp_contact_id'=>$contact->id,
            'direction'=>'outbound',
            'message_type'=>'template',
            'template_name'=>$templateName,
            'message_body'=>json_encode($payload,JSON_UNESCAPED_UNICODE),
            'provider_message_id'=>null,
            'status'=>'queued',
            'raw_payload'=>$payload,
        ]);

        if (!config('services.whatsapp.access_token') || !config('services.whatsapp.phone_number_id')) {
            $message->update(['status'=>'failed','failed_at'=>now(),'error_code'=>'WHATSAPP_NOT_CONFIGURED']);
            return $message;
        }

        try {
            $response=Http::withToken(config('services.whatsapp.access_token'))
                ->acceptJson()->asJson()->timeout(15)->post($url,$payload);

            if ($response->successful()) {
                $message->update([
                    'status'=>'sent',
                    'provider_message_id'=>data_get($response->json(),'messages.0.id'),
                    'sent_at'=>now(),
                    'raw_payload'=>array_merge($payload,['response'=>$response->json()]),
                ]);
            } else {
                $message->update([
                    'status'=>'failed',
                    'failed_at'=>now(),
                    'error_code'=>(string)(data_get($response->json(),'error.code') ?? $response->status()),
                    'raw_payload'=>array_merge($payload,['response'=>$response->json()]),
                ]);
            }
        } catch(\Throwable $e) {
            Log::error('WhatsApp send failed',['error'=>$e->getMessage()]);
            $message->update(['status'=>'failed','failed_at'=>now(),'error_code'=>'TRANSPORT_ERROR']);
        }

        return $message;
    }

    public function sendText(User $user,string $text): ?WhatsappMessage
    {
        $contact=WhatsappContact::where('user_id',$user->id)->where('opt_in',true)->first();
        if (!$contact || !$contact->phone_number) return null;

        $url=sprintf('%s/%s/%s/messages',rtrim(config('services.whatsapp.graph_url'),'/'),config('services.whatsapp.api_version'),config('services.whatsapp.phone_number_id'));
        $payload=['messaging_product'=>'whatsapp','to'=>$contact->phone_number,'type'=>'text','text'=>['body'=>$text]];

        $message=WhatsappMessage::create([
            'whatsapp_contact_id'=>$contact->id,'direction'=>'outbound','message_type'=>'text',
            'message_body'=>$text,'status'=>'queued','raw_payload'=>$payload,
        ]);

        $response=Http::withToken(config('services.whatsapp.access_token'))->acceptJson()->asJson()->timeout(15)->post($url,$payload);
        $message->update([
            'status'=>$response->successful()?'sent':'failed',
            'provider_message_id'=>data_get($response->json(),'messages.0.id'),
            'sent_at'=>$response->successful()?now():null,
            'failed_at'=>$response->successful()?null:now(),
            'raw_payload'=>array_merge($payload,['response'=>$response->json()]),
        ]);
        return $message;
    }
}
