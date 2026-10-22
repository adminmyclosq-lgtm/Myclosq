<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerNote extends Model
{
    use HasFactory;

    protected $table = 'customer_notes';

    protected $fillable = [
        'customer_id',
        'author_id',
        'note',
        'category',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
