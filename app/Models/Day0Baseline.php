<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Day0Baseline extends Model
{
    use HasFactory;
    protected $table = 'day0_baselines';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'recent_disruption_yes_no' => 'boolean',
            'safety_acknowledged' => 'boolean',
            'medical_disclaimer_acknowledged' => 'boolean',
            'whatsapp_opt_in' => 'boolean',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

}
