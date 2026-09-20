<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PositiveEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'positive_events';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

}
