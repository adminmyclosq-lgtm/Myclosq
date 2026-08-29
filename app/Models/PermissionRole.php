<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionRole extends Model
{
    use HasFactory;
    protected $table = 'permission_role';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function role() { return $this->belongsTo(Role::class, 'role_id'); }

    public function permission() { return $this->belongsTo(Permission::class, 'permission_id'); }

}
