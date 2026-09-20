<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MilestoneCheckin extends Model
{
    use HasFactory;
    protected $table = 'milestone_checkins';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'summary_generated' => 'boolean',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function resetProfile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

    public function answers() { return $this->hasMany(MilestoneAnswer::class, 'milestone_checkin_id'); }

}
