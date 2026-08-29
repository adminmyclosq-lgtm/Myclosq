<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyAdherence extends Model
{
    use HasFactory;
    protected $table = 'daily_adherence';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'reminder_sent' => 'boolean',
            'followup_sent' => 'boolean',
            'reminder_sent_at' => 'datetime',
            'responded_at' => 'datetime',
            'followup_sent_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function resetProfile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

}
