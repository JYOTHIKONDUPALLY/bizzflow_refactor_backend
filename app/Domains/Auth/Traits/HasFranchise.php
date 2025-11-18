<?php

namespace App\Domains\Auth\Traits;

use App\Domains\Auth\Models\Franchise;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasFranchise
{
    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public function scopeForFranchise($query, $franchiseId)
    {
        return $query->where('franchise_id', $franchiseId);
    }
}
