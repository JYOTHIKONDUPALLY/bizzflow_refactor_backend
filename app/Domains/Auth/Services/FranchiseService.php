<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Models\Franchise;

class FranchiseService
{
    public function getFranchiseById(int $id): ?Franchise
    {
        return Franchise::with(['country', 'currency', 'locations'])->find($id);
    }

    public function getAllActiveFranchises()
    {
        return Franchise::where('is_active', true)->get();
    }

    public function getFranchiseBySlug(string $slug): ?Franchise
    {
        return Franchise::where('slug', $slug)->first();
    }
}
