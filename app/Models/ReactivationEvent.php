<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReactivationEvent extends Model
{
    use HasFactory;
    protected $table = 'reactivation_events';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'restart_requested' => 'boolean',
            'prompt_sent_at' => 'datetime',
            'response_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

    public function dropoffevent() { return $this->belongsTo(DropoffEvent::class, 'dropoff_event_id'); }

}
