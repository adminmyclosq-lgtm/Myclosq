<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinalClassification extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'final_classifications';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'safety_override' => 'boolean',
            'classified_at' => 'datetime',
        ];
    }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

    public function griscore() { return $this->belongsTo(GriScore::class, 'gri_score_id'); }

    public function grsscore() { return $this->belongsTo(GrScore::class, 'grs_score_id'); }

}
