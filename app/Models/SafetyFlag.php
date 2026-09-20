<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SafetyFlag extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'safety_flags';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'automation_paused' => 'boolean',
            'manual_review_required' => 'boolean',
            'reviewed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

    public function unusualevent() { return $this->belongsTo(UnusualEvent::class, 'unusual_event_id'); }

    public function reviewedby() { return $this->belongsTo(User::class, 'reviewed_by'); }

}
