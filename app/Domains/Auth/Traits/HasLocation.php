<?php

namespace App\Domains\Auth\Traits;

use App\Domains\Auth\Models\Location;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasLocation
{
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function scopeForLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }
}
