<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsappContact extends Model
{
    use HasFactory;
    protected $table = 'whatsapp_contacts';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'opt_in' => 'boolean',
            'opt_in_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class, 'user_id'); }

}
