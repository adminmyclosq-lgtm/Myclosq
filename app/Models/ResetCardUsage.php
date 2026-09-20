<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResetCardUsage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'reset_card_usage';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'marked_yes_no' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

}
