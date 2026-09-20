<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MilestoneAnswer extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'milestone_answers';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'answer_json' => 'array',
            'answer_boolean' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function milestonecheckin() { return $this->belongsTo(MilestoneCheckin::class, 'milestone_checkin_id'); }

}
