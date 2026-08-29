<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmSection extends Model
{
    use HasFactory;
    protected $table = 'cms_sections';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function page() { return $this->belongsTo(CmPage::class, 'page_id'); }

    public function media() { return $this->belongsTo(Media::class, 'media_id'); }

}
