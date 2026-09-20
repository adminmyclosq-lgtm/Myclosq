<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QrScanEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'qr_scan_events';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'scan_timestamp' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function qrcode() { return $this->belongsTo(QrCode::class, 'qr_code_id'); }

    public function resetprofile() { return $this->belongsTo(ResetProfile::class, 'reset_profile_id'); }

    public function user() { return $this->belongsTo(User::class, 'user_id'); }

}
