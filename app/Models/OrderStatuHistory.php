<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatuHistory extends Model
{
    use HasFactory;
    protected $table = 'order_status_history';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function order() { return $this->belongsTo(Order::class, 'order_id'); }

    public function changedby() { return $this->belongsTo(User::class, 'changed_by'); }

}
