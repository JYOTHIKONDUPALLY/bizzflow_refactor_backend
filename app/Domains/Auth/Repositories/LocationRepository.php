<?php

namespace App\Domains\Auth\Repositories;

use App\Domains\Auth\Models\Location;

class LocationRepository
{
    public function findById(int $id): ?Location
    {
        return Location::with(['franchise', 'country', 'currency'])->find($id);
    }

    public function findByFranchise(int $franchiseId)
    {
        return Location::where('franchise_id', $franchiseId)
            ->where('is_active', true)
            ->get();
    }

    public function findByCode(string $code): ?Location
    {
        return Location::where('code', $code)->first();
    }
}
