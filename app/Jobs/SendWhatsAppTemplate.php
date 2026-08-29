<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable as FoundationQueueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppTemplate implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public int $tries=5;
    public int $timeout=30;

    public function __construct(
        public User $user,
        public string $templateName,
        public string $language='en',
        public array $parameters=[]
    ) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        $whatsapp->sendTemplate($this->user,$this->templateName,$this->language,$this->parameters);
    }
}
