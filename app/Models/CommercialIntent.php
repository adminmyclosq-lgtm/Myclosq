<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommercialIntent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'commercial_intents';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'phase2_interest' => 'boolean',
            'captured_at' => 'datetime',
        ];
    }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

}
