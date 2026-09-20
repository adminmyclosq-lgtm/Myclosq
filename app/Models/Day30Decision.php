<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Day30Decision extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'day30_decisions';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'continuation_allowed' => 'boolean',
            'upsell_allowed' => 'boolean',
            'testimonial_request_allowed' => 'boolean',
            'restart_allowed' => 'boolean',
            'doctor_guidance_required' => 'boolean',
            'generated_at' => 'datetime',
        ];
    }

    public function resetProfile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

    public function classification() { return $this->belongsTo(FinalClassification::class, 'classification_id'); }

}
