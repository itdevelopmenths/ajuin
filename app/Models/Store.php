<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $code
 * @property string      $public_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
#[Fillable(['name', 'code', 'category', 'public_token'])]
class Store extends Model
{
    use HasFactory;

    public const CATEGORIES = ['TOKO', 'GUDANG', 'MESS'];

    protected static function booted(): void
    {
        static::creating(function (Store $store): void {
            if (blank($store->public_token)) {
                $store->public_token = (string) Str::uuid();
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function submitUrl(): string
    {
        return route('public.submit.form');
    }

    /**
     * Scope toko yang boleh dilihat oleh user.
     * Hanya 2 tipe scope: ALL (Super Admin) dan STORE (SPV per toko).
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        $scopes = $user->scopes()->get();

        if ($scopes->contains(fn (UserScope $s): bool => $s->scope_type === 'ALL')) {
            return $query;
        }

        $storeIds = $scopes
            ->filter(fn (UserScope $s): bool => $s->scope_type === 'STORE')
            ->map(fn (UserScope $s) => $s->store_id)
            ->filter()
            ->values();

        if ($storeIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('id', $storeIds);
    }
}
