<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminNote extends Model
{
    use HasFactory;
    protected $table = 'admin_notes';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

    public function user() { return $this->belongsTo(User::class, 'user_id'); }

}
