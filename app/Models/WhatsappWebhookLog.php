<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsappWebhookLog extends Model
{
    use HasFactory;
    protected $table = 'whatsapp_webhook_logs';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'signature_verified' => 'boolean',
            'processed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

}
