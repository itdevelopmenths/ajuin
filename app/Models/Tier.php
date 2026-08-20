<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'deadline_days'])]
class Tier extends Model
{
    protected function casts(): array
    {
        return [
            'deadline_days' => 'integer',
        ];
    }

    public function maintenanceTypes(): HasMany
    {
        return $this->hasMany(MaintenanceType::class);
    }
}
