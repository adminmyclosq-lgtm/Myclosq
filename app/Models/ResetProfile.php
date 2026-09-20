<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public function parentResetProfile() { return $this->belongsTo(self::class, 'parent_reset_profile_id'); }
    public function childResetProfiles() { return $this->hasMany(self::class, 'parent_reset_profile_id'); }
    public function productBatch() { return $this->belongsTo(ProductBatche::class, 'product_batch_id'); }
    public function day0Baseline() { return $this->hasOne(Day0Baseline::class, 'reset_profile_id'); }
    public function dailyAdherence() { return $this->hasMany(DailyAdherence::class, 'reset_profile_id'); }
    public function gutSignalCheckpoints() { return $this->hasMany(GutSignalCheckpoint::class, 'reset_profile_id')->orderBy('checkpoint_day'); }
    public function startPlan() { return $this->hasOne(StartPlan::class, 'reset_profile_id'); }
    public function resetCardUsage() { return $this->hasMany(ResetCardUsage::class, 'reset_profile_id'); }
    public function milestoneCheckins() { return $this->hasMany(MilestoneCheckin::class, 'reset_profile_id')->orderBy('milestone_day'); }
    public function unusualEvents() { return $this->hasMany(UnusualEvent::class, 'reset_profile_id')->latest('id'); }
    public function positiveEvents() { return $this->hasMany(PositiveEvent::class, 'reset_profile_id')->latest('id'); }
    public function safetyFlags() { return $this->hasMany(SafetyFlag::class, 'reset_profile_id')->latest('id'); }
    public function dropoffEvents() { return $this->hasMany(DropoffEvent::class, 'reset_profile_id')->latest('id'); }
    public function reactivationEvents() { return $this->hasMany(ReactivationEvent::class, 'reset_profile_id')->latest('id'); }
    public function finalReview() { return $this->hasOne(FinalReview::class, 'reset_profile_id'); }
    public function griScores() { return $this->hasMany(GriScore::class, 'reset_profile_id')->latest('id'); }
    public function grsScores() { return $this->hasMany(GrScore::class, 'reset_profile_id')->latest('id'); }
    public function finalClassification() { return $this->hasOne(FinalClassification::class, 'reset_profile_id'); }
    public function day30Decision() { return $this->hasOne(Day30Decision::class, 'reset_profile_id'); }
    public function commercialIntent() { return $this->hasOne(CommercialIntent::class, 'reset_profile_id'); }
    public function testimonial() { return $this->hasOne(Testimonial::class, 'reset_profile_id'); }
    public function feedback() { return $this->hasOne(Feedback::class, 'reset_profile_id'); }
    public function adminNotes() { return $this->hasMany(AdminNote::class, 'reset_profile_id')->latest('id'); }

    public function getLatestGriAttribute(): ?GriScore
    {
        return $this->relationLoaded('griScores') ? $this->griScores->first() : $this->griScores()->first();
    }

    public function getLatestGrsAttribute(): ?GrScore
    {
        return $this->relationLoaded('grsScores') ? $this->grsScores->first() : $this->grsScores()->first();
    }
}
