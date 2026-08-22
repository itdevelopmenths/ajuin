<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'store_id',
    'ticket_number',
    'submitted_by',
    'jabatan',
    'type',
    'estimated_price',
    'payment_amount',
    'recommendation_links',
    'maintenance_type_id',
    'maintenance_tier',
    'maintenance_deadline_days',
    'source',
    'description',
    'status',
    'handled_by',
    'attachments',
    'completion_attachments',
    'resolved_at',
])]
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = ['SCREENING', 'IN_PROGRESS', 'PEMBAYARAN', 'EKSEKUSI', 'SELESAI', 'REJECTED'];

    public const TYPES = ['MAINTENANCE', 'PEMBELIAN_PERALATAN'];

    public const FINAL_STATUSES = ['SELESAI', 'REJECTED'];

    protected function casts(): array
    {
        return [
            'attachments'                => 'array',
            'estimated_price'            => 'integer',
            'payment_amount'             => 'integer',
            'recommendation_links'       => 'array',
            'maintenance_deadline_days'  => 'integer',
            'completion_attachments'     => 'array',
            'resolved_at'                => 'datetime',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TicketLog::class);
    }

    public function maintenanceType(): BelongsTo
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    /**
     * Batas waktu (deadline) maintenance, dihitung dari snapshot deadline_days
     * saat ticket dibuat — bukan dari data master yang bisa berubah belakangan.
     */
    public function maintenanceDeadlineAt(): ?\Carbon\Carbon
    {
        if ($this->maintenance_deadline_days === null) {
            return null;
        }

        return $this->created_at->copy()->addDays($this->maintenance_deadline_days);
    }

    public function isMaintenanceOverdue(): bool
    {
        $deadline = $this->maintenanceDeadlineAt();
        if ($deadline === null) {
            return false;
        }

        $comparedAt = $this->resolved_at ?? now();

        return $comparedAt->greaterThan($deadline);
    }

    /**
     * Status deadline untuk kebutuhan highlight di tabel: 'overdue', 'soon' (≤24 jam lagi
     * dan belum selesai), 'ok', atau null jika ticket ini bukan tipe maintenance.
     */
    public function maintenanceDeadlineStatus(): ?string
    {
        $deadline = $this->maintenanceDeadlineAt();
        if ($deadline === null) {
            return null;
        }

        if ($this->isMaintenanceOverdue()) {
            return 'overdue';
        }

        if ($this->resolved_at === null && now()->addDay()->greaterThanOrEqualTo($deadline)) {
            return 'soon';
        }

        return 'ok';
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        $scopes = $user->scopes;
        if ($scopes->where('scope_type', 'ALL')->isNotEmpty()) {
            return $query;
        }

        $storeIds = $scopes->where('scope_type', 'STORE')->pluck('store_id')->filter()->values();

        return $query->where(function (Builder $inner) use ($storeIds): void {
            if ($storeIds->isNotEmpty()) {
                $inner->whereIn('store_id', $storeIds);
            }

            if ($storeIds->isEmpty()) {
                $inner->whereRaw('1 = 0');
            }
        });
    }

    public static function nextNumber(): string
    {
        $prefix = 'AJN-'.now()->format('Ymd').'-';
        $next = self::query()->withTrashed()->where('ticket_number', 'like', "{$prefix}%")->count() + 1;

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
