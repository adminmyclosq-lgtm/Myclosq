<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnusualEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'unusual_events';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'product_related_yes_no' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

}
