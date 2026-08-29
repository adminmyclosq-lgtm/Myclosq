<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmPage extends Model
{
    use HasFactory;
    protected $table = 'cms_pages';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function createdby() { return $this->belongsTo(User::class, 'created_by'); }

    public function updatedby() { return $this->belongsTo(User::class, 'updated_by'); }

    public function sections()
    {
        return $this->hasMany(CmsSection::class, 'page_id')->orderBy('sort_order', 'asc');
    }
}
