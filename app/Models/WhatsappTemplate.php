<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsappTemplate extends Model
{
    use HasFactory;
    protected $table = 'whatsapp_templates';
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

    public function createdby() { return $this->belongsTo(User::class, 'created_by'); }

    public function updatedby() { return $this->belongsTo(User::class, 'updated_by'); }

}
