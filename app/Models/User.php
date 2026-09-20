<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $guarded = [];

    protected $hidden = ['password_hash'];

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }

    protected function casts(): array
    {
        return [
            'mobile_verified_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function customerProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function addresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function carts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function resetProfiles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ResetProfile::class);
    }

    public function resetProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ResetProfile::class)->latestOfMany();
    }

    public function activeResetProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ResetProfile::class)->whereIn('status', ['created', 'active', 'started', 'in_progress', 'paused', 'dropoff'])->latestOfMany();
    }

    public function whatsappContact(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(WhatsappContact::class);
    }

    public function getNameAttribute($value): string
    {
        return $value ?: ($this->customerProfile?->display_name ?: trim(($this->customerProfile?->first_name ?? '').' '.($this->customerProfile?->last_name ?? '')) ?: (string) $this->email);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('code', $role)->exists();
    }

    public function hasPermission(string $permissionCode): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn ($q) => $q->where('code', $permissionCode))
            ->exists();
    }
}
