<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'is_active', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class);
    }

    public function scopes(): HasMany
    {
        return $this->hasMany(UserScope::class);
    }

    /**
     * Cek apakah user boleh melihat/mengakses toko ini.
     * Super Admin → semua toko. SPV → hanya toko yang di-assign.
     */
    public function canSeeStore(Store|int $store): bool
    {
        $storeId = $store instanceof Store ? $store->id : $store;

        if ($this->hasRole('Super Admin')) {
            return true;
        }

        $scopes = $this->relationLoaded('scopes') ? $this->scopes : $this->scopes()->get();

        if ($scopes->contains(fn (UserScope $s): bool => $s->scope_type === 'ALL')) {
            return true;
        }

        return $scopes
            ->filter(fn (UserScope $s): bool => $s->scope_type === 'STORE')
            ->map(fn (UserScope $s) => $s->store_id)
            ->contains($storeId);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
        ];
    }
}
