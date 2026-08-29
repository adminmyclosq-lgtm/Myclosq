<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Phase2CHealthCheck extends Command
{
    protected $signature='phase2c:health';
    protected $description='Check Phase 2C configuration without calling external providers';

    public function handle(): int
    {
        $checks=[
            'Razorpay key id'=>config('services.razorpay.key_id'),
            'Razorpay key secret'=>config('services.razorpay.key_secret'),
            'Razorpay webhook secret'=>config('services.razorpay.webhook_secret'),
            'WhatsApp phone number id'=>config('services.whatsapp.phone_number_id'),
            'WhatsApp access token'=>config('services.whatsapp.access_token'),
            'WhatsApp verify token'=>config('services.whatsapp.verify_token'),
            'WhatsApp app secret'=>config('services.whatsapp.app_secret'),
            'Queue connection'=>config('queue.default'),
        ];

        foreach($checks as $label=>$value) {
            $this->line(sprintf('%-32s %s',$label,$value ? 'CONFIGURED' : 'MISSING'));
        }

        return self::SUCCESS;
    }
}
