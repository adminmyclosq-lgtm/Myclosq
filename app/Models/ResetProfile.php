<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResetProfile extends Model
{
    use HasFactory;
    protected $table = 'reset_profiles';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'manual_review_required' => 'boolean',
            'safety_flag_active' => 'boolean',
            'actual_start_date' => 'date',
            'day0_completed_at' => 'datetime',
            'day30_completed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class, 'user_id'); }

    public function productBatch() { return $this->belongsTo(ProductBatch::class, 'product_batch_id'); }

    public function day0Baseline() { return $this->hasOne(Day0Baseline::class, 'reset_profile_id'); }

    public function dailyAdherence() { return $this->hasMany(DailyAdherence::class, 'reset_profile_id'); }

    public function gutSignalCheckpoints() { return $this->hasMany(GutSignalCheckpoint::class, 'reset_profile_id'); }

    public function day30Decision() { return $this->hasOne(Day30Decision::class, 'reset_profile_id'); }

}
