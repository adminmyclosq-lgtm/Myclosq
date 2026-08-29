<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleUser extends Model
{
    use HasFactory;
    protected $table = 'role_user';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function role() { return $this->belongsTo(Role::class, 'role_id'); }

    public function user() { return $this->belongsTo(User::class, 'user_id'); }

}
