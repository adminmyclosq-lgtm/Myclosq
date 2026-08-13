<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationTemplate extends Model
{
    use HasFactory;
    protected $table = 'notification_templates';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'variables_json' => 'array',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }

    public function updatedBy() { return $this->belongsTo(User::class, 'updated_by'); }

}
