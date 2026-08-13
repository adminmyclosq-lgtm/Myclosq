<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DropoffEvent extends Model
{
    use HasFactory;
    protected $table = 'dropoff_events';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'reminder_fatigue' => 'boolean',
            'product_discomfort' => 'boolean',
            'detected_automatically' => 'boolean',
            'reactivation_prompt_sent' => 'boolean',
            'last_activity_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

}
