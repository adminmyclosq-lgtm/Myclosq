<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Day0PriorityArea extends Model
{
    use HasFactory;
    protected $table = 'day0_priority_areas';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function day0baseline() { return $this->belongsTo(Day0Baseline::class, 'day0_baseline_id'); }

}
