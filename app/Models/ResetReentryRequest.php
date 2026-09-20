<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResetReentryRequest extends Model
{
    protected $table = 'reset_reentry_requests';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function previousResetProfile(): BelongsTo
    {
        return $this->belongsTo(ResetProfile::class, 'previous_reset_profile_id');
    }

    public function newResetProfile(): BelongsTo
    {
        return $this->belongsTo(ResetProfile::class, 'new_reset_profile_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
