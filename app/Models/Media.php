<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;
    protected $table = 'media';
    protected $guarded = [];

    protected $casts = ['file_size'=>'integer','width'=>'integer','height'=>'integer'];

    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->storage_path);
    }
}
