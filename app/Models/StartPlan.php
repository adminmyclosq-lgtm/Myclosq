<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StartPlan extends Model
{
    use HasFactory;
    protected $table = 'start_plans';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'daily_reminder_enabled' => 'boolean',
            'milestone_reminder_enabled' => 'boolean',
            'missed_day_rule_confirmed' => 'boolean',
            'unusual_day_rule_confirmed' => 'boolean',
            'positive_shift_rule_confirmed' => 'boolean',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

}
