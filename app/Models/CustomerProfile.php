<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerProfile extends Model
{
    use HasFactory;
    protected $table = 'customer_profiles';
    protected $guarded = [];
    protected $casts = ['date_of_birth'=>'date','whatsapp_opt_in'=>'boolean','whatsapp_opt_in_at'=>'datetime','marketing_opt_in'=>'boolean'];
    public function user() { return $this->belongsTo(User::class); }
}
